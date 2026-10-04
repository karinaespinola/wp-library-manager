<?php

namespace WLM\Rest;

use WLM\Contracts\Bootable;
use WLM\Repositories\BookRepository;
use WP_REST_Request;
use WP_REST_Response;

class BooksController implements Bootable
{
    public function __construct(
        private BookRepository $repository
    ) {
    }

    public function boot(): void
    {
        add_action(
            'rest_api_init',
            [$this, 'registerRoutes']
        );
    }

    public function registerRoutes(): void
    {
        register_rest_route(
            'wlm/v1',
            '/books',
            [
                'methods'  => 'GET',
                'callback' => [$this, 'index'],
                'permission_callback' => '__return_true',
            ]
        );

        register_rest_route(
            'wlm/v1',
            '/books/(?P<id>\d+)',
            [
                'methods'  => 'GET',
                'callback' => [$this, 'show'],
                'permission_callback' => '__return_true',
            ]
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

        if (! $post || $post->post_type !== 'book') 
        {
            return new WP_REST_Response([
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
        ]);
    }
}