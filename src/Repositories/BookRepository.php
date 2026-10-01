<?php

namespace WLM\Repositories;

use WP_Query;

class BookRepository
{
    public function all(int $limit = 10): WP_Query
    {
        return new WP_Query([
            'post_type'      => 'book',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
        ]);
    }

    public function byGenre(string $genre, int $limit = 10): WP_Query
    {
        return new WP_Query([
            'post_type'      => 'book',
            'posts_per_page' => $limit,
            'tax_query' => [
                [
                    'taxonomy' => 'genre',
                    'field'    => 'slug',
                    'terms'    => $genre,
                ],
            ],
        ]);
    }

    public function publishedAfter(int $year): WP_Query
    {
        return new WP_Query([
            'post_type' => 'book',
            'meta_query' => [
                [
                    'key'     => 'wlm_year',
                    'value'   => $year,
                    'compare' => '>',
                    'type'    => 'NUMERIC',
                ],
            ],
        ]);
    }
    public function byLanguage(string $language): WP_Query
    {
        return new WP_Query([
            'post_type' => 'book',
            'tax_query' => [
                [
                    'taxonomy' => 'language',
                    'field'    => 'slug',
                    'terms'    => $language,
                ],
            ],
        ]);
    }
}