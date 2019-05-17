<?php

/*
Plugin Name: Simple Easy Behance Portfolio Showcase
Plugin URI: #
Description: A Plugin to display your behance portfolio in a modern and user friendly way on your WordPress blog and or website. The plugin complies and uses the latest version of behance API.
Version: 1.0.0
Author: Sheraz Ahmed
Author URI: https://isheraz.com
Text Domain: behance-portfolio
Domain Path: /languages
License: GPLv2 or later
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  Sorry but you can\'t access me directly, not cool bro!';
    exit;
}

define('BEHANCE_PORTFOLIO_VERSION', '1.0.0');
define('BEHANCE_PORTFOLIO_MINIMUM_WP_VERSION', '4.0');
define('BEHANCE_PORTFOLIO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BEHANCE_PORTFOLIO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BEHANCE_PORTFOLIO_DELETE_LIMIT', 100000);

add_action('wp_enqueue_scripts', 'ecpt_replace_core_jquery_version');
function ecpt_replace_core_jquery_version()
{
    if (is_admin()) return;
   /* wp_deregister_script('jquery-core');
    wp_deregister_script('jquery-migrate');
    wp_register_script('jquery-core', BEHANCE_PORTFOLIO_PLUGIN_URL . "public/jquery-3.1.1.min.js", array(), '3.1.1');
    wp_register_script('jquery-migrate', BEHANCE_PORTFOLIO_PLUGIN_URL . "public/jquery-migrate-3.0.0.min.js", array(), '3.0.0');*/
}

register_activation_hook(__FILE__, array('BehancePortfolio', 'ecpt_activate_plugin'));
register_deactivation_hook(__FILE__, array('BehancePortfolio', 'ecpt_deactivate_plugin'));

require_once(BEHANCE_PORTFOLIO_PLUGIN_DIR . 'includes/class.behance-portfolio.php');
require_once(BEHANCE_PORTFOLIO_PLUGIN_DIR . 'includes/class.shortcode.php');
//require_once( BEHANCE_PORTFOLIO_PLUGIN_DIR . 'class.behance-portfolio-widget.php' );

add_action('init', array('BehancePortfolio', 'ecpt_init'));
new BehanceShortcode();