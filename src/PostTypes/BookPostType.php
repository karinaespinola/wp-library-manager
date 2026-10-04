<?php

namespace WLM\PostTypes;

use WLM\Contracts\Bootable;

defined('ABSPATH') || exit;

class BookPostType implements Bootable
{
    public function boot(): void
    {
        add_action(
            'init',
            [$this, 'register']
        );
    }
    public function register(): void
    {
        register_post_type('book', [
            'labels' => [
                'name'          => 'Books',
                'singular_name' => 'Book',
            ],

            'public' => true,
            'show_in_rest' => true,

            'capability_type' => ['book', 'books'],

            'map_meta_cap' => true,

            'supports' => [
                'title',
                'editor',
                'thumbnail',
            ],
        ]);
    }
}
