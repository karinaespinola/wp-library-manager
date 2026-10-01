<?php

namespace WLM\Taxonomies;

use WLM\Contracts\Bootable;

defined('ABSPATH') || exit;

class LanguageTaxonomy implements Bootable
{
    public function boot(): void
    {
        add_action('init', [$this, 'register']);
    }    
    public function register(): void
    {
        register_taxonomy(
            'language',
            ['book'],
            [
                'labels' => [
                    'name'          => 'Languages',
                    'singular_name' => 'Language',
                ],

                'public'       => true,
                'hierarchical' => true,
                'show_in_rest' => true,
            ]
        );
    }
}