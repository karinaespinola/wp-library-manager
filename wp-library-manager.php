<?php

/**
 * Plugin Name: WP Library Manager
 * Description: A custom WordPress plugin for managing books.
 * Version: 1.0.0
 * Author: Karina
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook(
    __FILE__,
    ['WLM\\Plugin', 'activate']
);

register_deactivation_hook(
    __FILE__,
    ['WLM\\Plugin', 'deactivate']
);

define(
    'WLM_PLUGIN_PATH',
    plugin_dir_path(__FILE__)
);

define(
    'WLM_PLUGIN_URL',
    plugin_dir_url(__FILE__)
);

$plugin = new WLM\Plugin();
$plugin->boot();