<?php

namespace WLM\Shortcodes;

use WLM\Contracts\Bootable;
use WLM\Repositories\BookRepository;

class BooksShortcode implements Bootable
{
    public function __construct(
        private BookRepository $repository
    ) {
    }

    public function boot(): void
    {
        add_shortcode(
            'wlm_books',
            [$this, 'render']
        );
    }

    public function render(array $atts = []): string
    {
        $atts = shortcode_atts(
            [
                'limit' => 10,
                'genre' => '',
            ],
            $atts,
            'wlm_books'
        );

        $limit = absint($atts['limit']);
        $genre = sanitize_text_field($atts['genre']);

       if (!empty($genre)) {
            $query = $this->repository->byGenre($genre, $limit);
        } else {
            $query = $this->repository->all($limit);
        }
  

        if (! $query->have_posts()) {
            return '<p>No books found.</p>';
        }

        $html = '<ul>';

        while ($query->have_posts()) {
            $query->the_post();
            $year = get_post_meta(get_the_ID(), 'year', true);
            $pages = get_post_meta(get_the_ID(), 'pages', true);
            
            $html .= '<article class="wlm-book">';

            $html .= '<h3>';
            $html .= esc_html(get_the_title());
            $html .= '</h3>';

            if ($year) {
                $html .= '<p>';
                $html .= 'Year: ';
                $html .= esc_html($year);
                $html .= '</p>';
            }

            if ($pages) {
                $html .= '<p>';
                $html .= 'Pages: ';
                $html .= esc_html($pages);
                $html .= '</p>';
            }

            $html .= '</article>';
        }

        $html .= '</ul>';

        wp_reset_postdata();

        return $html;
    }
}