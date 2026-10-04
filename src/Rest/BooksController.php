<?php

namespace WLM\Rest;

use WLM\Contracts\Bootable;
use WLM\Repositories\BookRepository;
use WLM\Services\BookService;
use WP_REST_Request;
use WP_REST_Response;

class BooksController implements Bootable
{
    public function __construct(
        private BookRepository $repository,
        private BookService $bookService
    ) {
        $this->bookService = $bookService;
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
            '/books/(?P<id>\d+)',
            [
                [
                    'methods'  => 'GET',
                    'callback' => [$this, 'show'],
                    'permission_callback' => '__return_true',
                ],
                [
                    'methods'             => 'PUT',
                    'callback'            => [$this->bookService, 'update'],
                    'permission_callback' => [$this, 'canUpdate'],
                ],
                [
                    'methods'             => 'DELETE',
                    'callback'            => [$this->bookService, 'destroy'],
                    'permission_callback' => [$this, 'canDelete'],
                ],
            ]
        );

        register_rest_route(
            'wlm/v1',
            '/books',
            [
                [
                    'methods'             => 'GET',
                    'callback'            => [$this->bookService, 'index'],
                    'permission_callback' => '__return_true',
                ],

                [
                    'methods'             => 'POST',
                    'callback'            => [$this->bookService, 'store'],
                    'permission_callback' => [$this, 'canCreate'],
                    'args' => [
                        'title' => [
                            'required' => true,
                            'sanitize_callback' => 'sanitize_text_field',
                        ],

                        'year' => [
                            'sanitize_callback' => 'absint',
                        ],

                        'pages' => [
                            'sanitize_callback' => 'absint',
                        ],

                        'isbn' => [
                            'sanitize_callback' => 'sanitize_text_field',
                        ],

                        'genre' => [
                            'sanitize_callback' => 'sanitize_key',
                        ],
                    ],
                ],
            ],

        );
    }



    public function canCreate(): bool
    {
        return current_user_can('edit_posts');
    }



    public function canUpdate(WP_REST_Request $request): bool
    {
        $id = absint(
            $request->get_param('id')
        );

        return current_user_can(
            'edit_post',
            $id
        );
    }

    public function canDelete(WP_REST_Request $request): bool
    {
        $id = absint(
            $request->get_param('id')
        );

        return current_user_can(
            'delete_post',
            $id
        );
    }
}
