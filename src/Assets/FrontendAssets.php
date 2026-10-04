<?php

namespace WLM\Assets;

use WLM\Contracts\Bootable;

class FrontendAssets implements Bootable
{
    public function boot(): void
    {
        add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueue']
        );
    }

    public function enqueue(): void
    {
        wp_register_style(
            'wlm-books',
            WLM_PLUGIN_URL . 'assets/css/books.css',
            [],
            filemtime(WLM_PLUGIN_PATH . 'assets/css/books.css')
        );

        wp_localize_script(
            'wlm-books',
            'wlmData',
            [
                'restUrl' => rest_url('wlm/v1/'),
                'nonce'   => wp_create_nonce('wp_rest'),
            ]
        );

        wp_register_script(
            'wlm-books',
            WLM_PLUGIN_URL . 'assets/js/books.js',
            [],
            filemtime(WLM_PLUGIN_PATH . 'assets/js/books.js'),
            true
        );
    }
}
