<?php
/**
 * Plugin Name: RU Plugin
 * Description: Custom RiseUp Consulting functionality — SEO tools (PDF reports) + SEO/Schema audit tool.
 * Version: 1.0
 * Author: Rise Up
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'seo-tools/riseup-seo-tools.php';
require_once plugin_dir_path(__FILE__) . 'audit-tool/core-file.php';
