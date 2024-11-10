<?php
namespace ERAI\Admin\Menu {

    class ERAI_Admin_Menu {

        function __construct() {
            add_action('admin_menu', [$this, 'register_menu']);
        }

        function register_menu() {
            add_submenu_page(
                'woocommerce',    // Parent slug (Settings menu)
                'Easy Aliexpress',         // Page title
                'Easy Aliexpress',                // Menu title
                'manage_woocommerce',         // Capability
                'erai-woocommerce-settings',         // Menu slug
                [$this, 'admin_page'],     // Callback function),
                9999
            );
        }

        

        function admin_page() {
            do_action('erai_admin_page');
        }


    }

    new ERAI_Admin_Menu();
}