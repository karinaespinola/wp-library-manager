<?php

namespace WLM\Filters;

use WLM\Contracts\Bootable;

defined('ABSPATH') || exit;

class BookTitleFilter implements Bootable
{
    public function boot(): void
    {
        add_filter('the_title', [$this, 'filterTitle'], 10, 2);
    }

    public function filterTitle(string $title, int $post_id): string
    {
        if (get_post_type($post_id) === 'book') {
            $title = 'Book: ' . $title;
        }
        return $title;
    }
}