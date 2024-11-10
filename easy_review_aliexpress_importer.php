<?php
/*
Plugin Name: Easy Review Aliexpress Importer
Version: 1.0.0
Author: Philip Hilgendorf
Description: Easily import authentic AliExpress reviews to your WooCommerce products for added trust and engagement.
Author URI: https://github.com/PhilipHilgendorf
License: GPL v3 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!defined('ERAI_PLUGIN_DIR')) {
    define('ERAI_PLUGIN_DIR',  plugin_dir_path( __FILE__ ));
}
if ( ! defined( 'ERAI_PLUGIN_FILE' ) ) {
	define( 'ERAI_PLUGIN_FILE', __FILE__ );
}

if(!defined('ERAI_VERSION')) {
    define('ERAI_VERSION', '1.0.0');
}

if(!defined('ERAI_WOOCOMMERCE')) {
    $erai_woocommerce = false;
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $erai_woocommerce = true;
    }
    define('ERAI_WOOCOMMERCE', $erai_woocommerce);
}

require_once 'src/ERAI.php';

erai();