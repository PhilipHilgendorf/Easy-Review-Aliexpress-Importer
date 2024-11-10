<?php
namespace ERAI\WooCommerce\Handler {

    use ERA\Handler\ERAI_Aliexpress_Review_Handler as ERAI_Review_Handler;

    class ERAI_WC_Product_Handler {
        
        function __construct() {
            add_action('add_meta_boxes', [$this, 'add_product_metabox']);
            add_action('save_post', [$this, 'save_product_metabox_data']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
            add_action('wp_ajax_erai_load_wc_reviews', [$this, 'load_reviews']);
        }

        function load_reviews() {
            $aliexpress_url = sanitize_url(urldecode($_POST['aliexpress_url']));
    	    $postid = sanitize_text_field($_POST['product_id']);
            if (preg_match('/\/item\/(\d+)\.html/', $aliexpress_url, $matches)) {
                echo json_encode(['status' => 'success' ,'reviews' =>ERAI_Review_Handler\earai_load_review($matches[1], $postid)]);
                exit();
            }
            echo json_encode(['status' => 'faild']);
            exit();
        }

        function enqueue_admin_scripts() {
            global $post;
            if (($post->post_type ?? '') != 'product') {
                return;
            }
            wp_enqueue_style('erai-admin-product', erai()->plugin_dir_url('assets/css/erai-admin-product.css'), [], erai()->get_version());
            wp_enqueue_script('erai-admin-product', erai()->plugin_dir_url('assets/js/erai-admin-product.js'), array('jquery'), erai()->get_version(), true);
            // Localize the script to use the admin-ajax.php URL
            wp_localize_script('erai-admin-product', 'erai_admin_product', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'product_id' => $post->ID
            ));
        }

        function save_product_metabox_data($post_id) {
             // Verify the nonce
            if (!isset($_POST['custom_product_metabox_nonce']) || !wp_verify_nonce($_POST['custom_product_metabox_nonce'], 'save_custom_product_metabox_data')) {
                return;
            }

            // Verify user permissions
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }

            // Check if it's an autosave (don't save on autosave)
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // Save the custom field data
            if (isset($_POST['custom_product_field'])) {
                update_post_meta($post_id, '_custom_product_field', sanitize_text_field($_POST['custom_product_field']));
            } else {
                delete_post_meta($post_id, '_custom_product_field');
            }
        }


        function add_product_metabox() {
            // Add a new metabox
            add_meta_box(
                'erai_product_metabox', 
                'Review Importer', 
                [$this, 'display_product_metabox'], 
                'product',
                'side', 
                'core' 
            );
        }

        function display_product_metabox($post) {
            $erai_aliexpress_review_url = get_post_meta($post->ID, '_erai_aliexpress_review_url', true);

            // Nonce for security
            wp_nonce_field('save_custom_product_metabox_data', 'custom_product_metabox_nonce');
            ?>
            <div>
                <label for="custom_product_field">Aliexpress URL:</label><br>
                <input type="text" name="erai_aliexpress_review_url" id="erai_aliexpress_review_url" value="<?php echo esc_attr($erai_aliexpress_review_url); ?>" style="width: 100%;"><br><br>
                <a class="button" id="erai_submit"><?php _e('Get Reviews', 'erai') ?></a>
            </div>
            <div class="erai-popup" id="erai-popup-review">
                <div class="erai-popup-container">
                    <div class="erai-popup-inner">
                        <div class="erai-popup-head">
                            <p class="result-msg">Successfully Reviews added</p>
                            <div class="close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </div>
                        </div>
                            <div class="erai-controlls">
                            </div>
                        <div class="erai-popup-body">
                            <div class="erai-reviews">
                                
                            </div>
                        </div>
                        <div class="erai-bottom-controlls">
                            <div class="current-selected">
                                <p>Current Selected: <span id="erai-selected-reviews">0</span></p>
                            </div>
                            <div class="btn-container">
                                <a class="erai-btn" disabled id="erai_add_reviews">Add Reviews</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="erai-popup" id="erai-popup-loading">
                <div class="erai-popup-container">
                    <div class="erai-popup-inner">
                        <div class="erai-popup-head">
                            <h2>Loading</h2>
                            <div class="close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </div>
                        </div>
                        <div class="erai-popup-body">
                            <div class="erai-loading-container">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

    }

    new ERAI_WC_Product_Handler();
}