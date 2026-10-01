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
                    'add_new_item'  => 'Add New Book',
                    'edit_item'     => 'Edit Book',
                ],

                'public'       => true,
                'show_in_rest' => true,

                'supports' => [
                    'title',
                    'editor',
                    'thumbnail',
                    'excerpt',
                ],

                'menu_icon' => 'dashicons-book',
            ]);
        }
}