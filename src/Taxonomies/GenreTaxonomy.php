<?php

namespace WLM\Taxonomies;

use WLM\Contracts\Bootable;

defined('ABSPATH') || exit;

class GenreTaxonomy implements Bootable
{
    public function boot(): void
    {
        add_action('init', [$this, 'register']);
    }    
    public function register(): void
    {
        register_taxonomy(
            'genre',
            ['book'],
            [
                'labels' => [
                    'name'          => 'Genres',
                    'singular_name' => 'Genre',
                ],

                'public'       => true,
                'hierarchical' => true,
                'show_in_rest' => true,
            ]
        );
    }
}