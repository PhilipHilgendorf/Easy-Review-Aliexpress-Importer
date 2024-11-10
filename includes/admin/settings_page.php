<?php
namespace ERAI\Admin\Pages {

    class ERAI_Admin_Settings {

        private $settings;

        function __construct() {
            $this->settings = erai()->get_settings();
            if(($_GET['page'] ?? '') == "erai-woocommerce-settings") {
                add_action('admin_enqueue_scripts', [$this, 'load_assets']);
                add_action('erai_admin_page', [$this, 'load_page']);
            }

            add_action("wp_ajax_erai_update_settings", [$this, 'update_settings']);
        }

        function update_settings() {
            $this->settings->update_settings(
                isset($_POST['hide_anonymous_buyer']),
                (int) sanitize_text_field($_POST['minimum_stars']),
                sanitize_text_field($_POST['review_url']),
                isset($_POST['translate']),
                sanitize_text_field($_POST['deeplapikey']),
                sanitize_text_field($_POST['translate_to']),
                isset($_POST['freedeeplapi'])
            );
            exit();
        }

        function load_page() {
            print('<div id="erai-settings-container"></div>');
        }
        
        function load_assets() {
            wp_enqueue_script('erai_settings', erai()->plugin_dir_url("node/dist/bundle.js"), [],erai()->get_version(), true);
            wp_localize_script('erai_settings', "erai_settings", [
                "ajax_url" => admin_url( 'admin-ajax.php?action=erai_update_settings' ), 
                "action" => "erai_update_settings",
                "hide_anonymous_buyer" => $this->settings->isHideAnonymousBuyer(), 
                "minimum_stars" => $this->settings->getMinimumStars(), 
                "review_url" => $this->settings->getReviewUrl(), 
                "translate" => $this->settings->isTranslate(), 
                "deeplapikey" => $this->settings->getDeeplapikey(), 
                "translateTo" => $this->settings->getTranslateTo(),
                "free_deepl_api" => $this->settings->isFreeDeeplApi()
            ]);
            wp_enqueue_style('erai_settings', erai()->plugin_dir_url("node/dist/style.css"), [], erai()->get_version());
        }
    }

    new ERAI_Admin_Settings();
}

