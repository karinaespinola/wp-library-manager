<?php

namespace WLM\Shortcodes;

use WLM\Contracts\Bootable;
use WLM\Repositories\BookRepository;

class BooksShortcode implements Bootable
{
    public function __construct(
        private BookRepository $repository
    ) {}

    public function boot(): void
    {
        add_shortcode(
            'wlm_books',
            [$this, 'render']
        );
    }

    public function render(array $atts = []): string
    {
        wp_enqueue_style('wlm-books');
        wp_enqueue_script('wlm-books');

        $default_limit = (int) get_option(
            'wlm_books_per_page',
            12
        );

        $atts = shortcode_atts(
            [
                'limit' => $default_limit,
                'genre' => '',
            ],
            $atts,
            'wlm_books'
        );

        $limit = absint($atts['limit']);
        $genre = sanitize_key($atts['genre']);

        $query = $genre
            ? $this->repository->byGenre($genre, $limit)
            : $this->repository->all($limit);

        ob_start();
        include WLM_PLUGIN_PATH . 'templates/books-list.php';
        wp_reset_postdata();

        return (string) ob_get_clean();
    }
}
