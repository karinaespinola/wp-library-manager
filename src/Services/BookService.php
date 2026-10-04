<?php

namespace WLM\Services;

use WP_REST_Request;
use WP_REST_Response;
use WLM\Repositories\BookRepository;

class BookService
{
    private BookRepository $repository;

    public function __construct()
    {
        $this->repository = new BookRepository();
    }

    public function store(WP_REST_Request $request): WP_REST_Response|\WP_Error
    {
        $title = sanitize_text_field($request->get_param('title') ?? '');

        if ($title === '') {
            return new \WP_Error(
                'wlm_missing_title',
                'Title is required.',
                [
                    'status' => 422,
                ]
            );
        }

        $post_id = wp_insert_post(
            [
                'post_type'   => 'book',
                'post_status' => 'publish',
                'post_title'  => $title,
            ],
            true
        );

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        $year = absint($request->get_param('year'));

        $pages = absint($request->get_param('pages'));

        $isbn = sanitize_text_field($request->get_param('isbn') ?? '');

        if ($year > 0) {
            update_post_meta(
                $post_id,
                'wlm_year',
                $year
            );
        }

        if ($pages > 0) {
            update_post_meta(
                $post_id,
                'wlm_pages',
                $pages
            );
        }

        if ($isbn !== '') {
            update_post_meta(
                $post_id,
                'wlm_isbn',
                $isbn
            );
        }

        $genre = sanitize_key(
            $request->get_param('genre') ?? ''
        );

        if ($genre !== '') {
            wp_set_object_terms(
                $post_id,
                $genre,
                'genre'
            );
        }

        return new WP_REST_Response(
            [
                'id'      => $post_id,
                'message' => 'Book created successfully.',
            ],
            201
        );
    }

    public function index(
        WP_REST_Request $request
    ): WP_REST_Response {
        $limit = absint(
            $request->get_param('limit') ?: 10
        );
        $query = $this->repository->all($limit);

        $books = [];

        while ($query->have_posts()) {
            $query->the_post();

            $post_id = get_the_ID();

            $books[] = [
                'id'    => $post_id,
                'title' => get_the_title(),
                'year'  => (int) get_post_meta(
                    $post_id,
                    'wlm_year',
                    true
                ),
                'pages' => (int) get_post_meta(
                    $post_id,
                    'wlm_pages',
                    true
                ),
            ];
        }

        wp_reset_postdata();

        return new WP_REST_Response(
            $books,
            200
        );
    }

    public function show(
        WP_REST_Request $request
    ): WP_REST_Response {
        $id = absint($request->get_param('id'));

        $post = get_post($id);

        if (! $post || $post->post_type !== 'book') {
            return new WP_REST_Response(
                [
                    'message' => 'Book not found',
                ],
                404
            );
        }

        return new WP_REST_Response([
            'id'    => $post->ID,
            'title' => $post->post_title,
            'year'  => (int) get_post_meta(
                $post->ID,
                'wlm_year',
                true
            ),
            'pages' => (int) get_post_meta(
                $post->ID,
                'wlm_pages',
                true
            ),
        ]);
    }

    public function update(
        WP_REST_Request $request
    ): WP_REST_Response|\WP_Error {
        $id = absint(
            $request->get_param('id')
        );

        $post = get_post($id);

        if (
            ! $post ||
            $post->post_type !== 'book'
        ) {
            return new \WP_Error(
                'wlm_book_not_found',
                'Book not found.',
                [
                    'status' => 404,
                ]
            );
        }

        $title = $request->get_param('title');

        if ($title !== null) {
            $result = wp_update_post(
                [
                    'ID'         => $id,
                    'post_title' => sanitize_text_field($title),
                ],
                true
            );

            if (is_wp_error($result)) {
                return $result;
            }
        }

        if ($request->has_param('year')) {
            $result = update_post_meta(
                $id,
                'wlm_year',
                absint($request->get_param('year'))
            );
            if (! $result) {
                return new \WP_Error(
                    'wlm_update_failed',
                    'Unable to update year.',
                    [
                        'status' => 500,
                    ]
                );
            }
        }

        return new WP_REST_Response(
            [
                'message' => 'Book updated successfully.',
            ],
            200
        );
    }

    public function destroy(
        WP_REST_Request $request
    ): WP_REST_Response|\WP_Error {
        $id = absint(
            $request->get_param('id')
        );

        $post = get_post($id);

        if (
            ! $post ||
            $post->post_type !== 'book'
        ) {
            return new \WP_Error(
                'wlm_book_not_found',
                'Book not found.',
                [
                    'status' => 404,
                ]
            );
        }

        $result = wp_delete_post(
            $id,
            true
        );

        if (! $result) {
            return new \WP_Error(
                'wlm_delete_failed',
                'Unable to delete book.',
                [
                    'status' => 500,
                ]
            );
        }

        return new WP_REST_Response(
            [
                'message' => 'Book deleted successfully.',
            ],
            200
        );
    }
}
