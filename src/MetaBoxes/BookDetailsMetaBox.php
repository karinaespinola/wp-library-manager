<?php

namespace WLM\MetaBoxes;

use WP_Post;
use WLM\Contracts\Bootable;

defined('ABSPATH') || exit;

class BookDetailsMetaBox implements Bootable
{
    public function boot(): void
    {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post_book', [$this, 'save']);
    }

    public function register(): void
    {
        add_meta_box(
            'wlm_book_details',
            'Book Details',
            [$this, 'render'],
            'book',
            'normal',
            'default'
        );
    }

    public function render(WP_Post $post): void
    {
        wp_nonce_field(
            'wlm_save_book_meta',
            'wlm_book_nonce'
        );

        $isbn = get_post_meta(
            $post->ID,
            'wlm_isbn',
            true
        );

        $year = get_post_meta(
            $post->ID,
            'wlm_year',
            true
        );

        $pages = get_post_meta(
            $post->ID,
            'wlm_pages',
            true
        );

        ?>
        <p>
            <label for="wlm_isbn">ISBN</label>

            <input
                type="text"
                id="wlm_isbn"
                name="wlm_isbn"
                value="<?php echo esc_attr($isbn); ?>"
            >
        </p>

        <p>
            <label for="wlm_year">Publication Year</label>

            <input
                type="number"
                id="wlm_year"
                name="wlm_year"
                value="<?php echo esc_attr($year); ?>"
            >
        </p>

        <p>
            <label for="wlm_pages">Pages</label>

            <input
                type="number"
                id="wlm_pages"
                name="wlm_pages"
                value="<?php echo esc_attr($pages); ?>"
            >
        </p>
        <?php
    }

    public function save(int $post_id): void
    {
        if (
            ! isset($_POST['wlm_book_nonce']) ||
            ! wp_verify_nonce(
                $_POST['wlm_book_nonce'],
                'wlm_save_book_meta'
            )
        ) {
            return;
        }

        if (! current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['wlm_isbn'])) {
            update_post_meta(
                $post_id,
                'wlm_isbn',
                sanitize_text_field($_POST['wlm_isbn'])
            );
        }

        if (isset($_POST['wlm_year'])) {
            update_post_meta(
                $post_id,
                'wlm_year',
                absint($_POST['wlm_year'])
            );
        }

        if (isset($_POST['wlm_pages'])) {
            update_post_meta(
                $post_id,
                'wlm_pages',
                absint($_POST['wlm_pages'])
            );
        }
    }
}