<?php
/**
 * 
 * 
 * @since 1.0.0
 */

namespace ERAI {

    final class ERAI {

        private $woocommerce = false;
    
		public $version = '';

		private static $instance;

        private $settings;

        /**
         * Primary class constructor
         * 
         * @since 1.0.0
         */

		public static function instance() {
            if(self::$instance === null || !self::$instance instanceof self) {
				self::$instance = new self();
				self::$instance->constants();
				self::$instance->includes();
            }
            return self::$instance;
        }

        /**
         * Set Default values
         * 
         * @since 1.0.0
         */

        private function constants() {
            $this->version = ERAI_VERSION;
            $this->woocommerce = ERAI_WOOCOMMERCE;
        }

        /**
         * Includes important Files
         * 
         * @since 1.0.0
         */

        private function includes() {
            // Global includes.

            require_once ERAI_PLUGIN_DIR . 'includes/erai_settings.php';
            require_once ERAI_PLUGIN_DIR . 'includes/aliexpress_review_handler.php';
            require_once ERAI_PLUGIN_DIR . 'src/Woocommerce/Review_Handler.php';

            // Admin only includes
			if ( is_admin() ) {
                include_once ERAI_PLUGIN_DIR . 'includes/admin/settings_page.php';
                include_once ERAI_PLUGIN_DIR . 'includes/admin/menu.php';
                include_once ERAI_PLUGIN_DIR . 'src/Woocommerce/Product_Handler.php';
            }
        }

        
        /**
         * Gets Plugin dir URL
         * 
         * @since 1.0.0
         */

        public function plugin_dir_url($path = "") {
            return plugin_dir_url(ERAI_PLUGIN_FILE). $path;
        } 

        /**
         * Gets Plugin Version
         * 
         * @since 1.0.0
         */

        public function get_version() {
            return $this->version;
        }

        /**
         * Gets Plugin Settings
         * 
         * @since 1.0.0
         */

        public function get_settings() {
            return $this->settings;
        }
        
        public function set_settings($settings) {
            self::$instance->settings = $settings;
        }
        

        /**
         * Gets Woocommerce Active
         * 
         * @since 1.0.0
         */

        public function get_woocommerce() {
            return $this->woocommerce;
        }

    }
}


namespace {


	/**
	 * The function which returns the one ERAI instance.
	 *
	 * @since 1.0.0
	 *
	 * @return ERAI\ERAI
	 */
    
    function erai() {
        return ERAI\ERAI::instance();
    }
	class_alias( 'ERAI\ERAI', 'ERAI' );
}