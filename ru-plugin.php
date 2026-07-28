<?php
/**
 * Plugin Name: RU Plugin
 * Description: Custom RiseUp Consulting functionality — SEO/Schema audit tool, PDF reports, lead capture.
 * Version: 1.0
 * Author: Rise Up
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/cpt-register.php';
require_once plugin_dir_path(__FILE__) . 'includes/ai-helpers.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-helpers.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/verification.php';
require_once plugin_dir_path(__FILE__) . 'includes/email-report.php';
require_once plugin_dir_path(__FILE__) . 'includes/schema-email-report.php';
require_once plugin_dir_path(__FILE__) . 'includes/elementor-integration.php';
require_once plugin_dir_path(__FILE__) . 'includes/seo-audit-core.php';
require_once plugin_dir_path(__FILE__) . 'includes/pdf-report.php';
