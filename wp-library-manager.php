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

$plugin = new WLM\Plugin();
$plugin->boot();