<?php

namespace ERAI\WooCommerce\Review {

    class ERAI_WC_Review_Handler {
        
        private $deeplapikey;

        private $translateto;

        private $freeapi;

        private $minimumStars;

        function __construct() {
            $this->deeplapikey = erai()->get_settings()->getDeeplapikey();
            $this->translateto = erai()->get_settings()->getTranslateTo(); 
            $this->freeapi = erai()->get_settings()->isFreeDeeplApi();
            $this->minimumStars = erai()->get_settings()->getMinimumStars() -1;
            add_action('wp_ajax_erai_add_wc_reviews', [$this, 'add_reviews']);
            add_action('wp_ajax_erai_translate_review_text', [$this, 'translate_review_text']);
            
            if(!erai()->get_woocommerce()) {
                return;
            }
            add_filter('woocommerce_review_comment_text', [$this, 'display_images_meta_in_review'], 10, 1);

            add_action('wp', function() {
                
                if(is_product()) {
                    add_action('wp_enqueue_scripts', [$this, 'load_product_scripts']);
                    add_action('wp_footer', [$this, 'photoswipe_html_structure']);
                }
            });
        }

        function photoswipe_html_structure() {
            ?>
            <!-- PhotoSwipe Core HTML Structure -->
            <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="pswp__bg"></div>
                <div class="pswp__scroll-wrap">
                    <div class="pswp__container">
                        <div class="pswp__item"></div>
                        <div class="pswp__item"></div>
                        <div class="pswp__item"></div>
                    </div>
                    <div class="pswp__ui pswp__ui--hidden">
                        <div class="pswp__top-bar">
                            <div class="pswp__counter"></div>
                            <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                            <button class="pswp__button pswp__button--share" title="Share"></button>
                            <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                            <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                            <div class="pswp__preloader">
                                <div class="pswp__preloader__icn">
                                    <div class="pswp__preloader__cut">
                                        <div class="pswp__preloader__donut"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                            <div class="pswp__share-tooltip"></div>
                        </div>
                        <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                        <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                        <div class="pswp__caption">
                            <div class="pswp__caption__center"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        function load_product_scripts() {
            wp_enqueue_script( 'photoswipe-ui-default', WC()->plugin_url() . '/assets/js/photoswipe/photoswipe-ui-default.min.js', array( 'jquery' ), WC_VERSION, true );
            wp_enqueue_script( 'photoswipe', WC()->plugin_url() . '/assets/js/photoswipe/photoswipe.min.js', array( 'jquery' ), WC_VERSION, true );
            wp_enqueue_style( 'photoswipe-css', WC()->plugin_url() . '/assets/css/photoswipe/photoswipe.css', array(), WC_VERSION );
            wp_enqueue_style( 'photoswipe-skin', WC()->plugin_url() . '/assets/css/photoswipe/default-skin/default-skin.css', array(), WC_VERSION );
            wp_enqueue_script('erai-photoswipe-init', erai()->plugin_dir_url('assets/js/erai-product.js'), array('jquery', 'photoswipe','photoswipe-ui-default'), erai()->get_version(), true);
            wp_enqueue_style('erai-product', erai()->plugin_dir_url('assets/css/erai-product.css'), [], erai()->get_version());

        }

        function display_images_meta_in_review($comment) {
            // Retrieve the custom meta value for the review
            $images = get_comment_meta($comment->comment_ID, 'erai-reviews-images', true);

            if(!$images) {
                return $comment;
            }

            $images_html = '<div class="erai-product-review-image-container"><div class="woocommerce-product-gallery"><div class="photoswipe-gallery">';

            for ($index =0;$index < count($images);$index++) {
                $full_size_image  = wp_get_attachment_image_src( $images[$index], 'full' );
                $thumbnail        = wp_get_attachment_image_src( $images[$index], 'woocommerce_thumbnail' );
                $image_title      = get_post_field( 'post_excerpt', $images[$index] );
                $images_html .= '<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">';
                $images_html .= '<a href="'.esc_url( $full_size_image[0] ).'" itemprop="contentUrl" data-size="'. esc_attr( $full_size_image[1] ) .'x'. esc_attr( $full_size_image[2] ).'">';
                $images_html .= '<img src="'.esc_url( $thumbnail[0] ).'" itemprop="thumbnail" alt="'. esc_attr( $image_title ).'">';
                $images_html .= '</a>';
                $images_html .= '</figure>';
            }
             $images_html .= '</div></div></div>';
            $comment->comment_content .= $images_html;
        
            return $comment;
        }

        public function add_reviews() {
            $product_id = sanitize_text_field($_POST['product_id']);
            $reviews = $_POST['reviews'];
            $addedReviews = 0;
            $errormsg = "";
            $removedIds = [];
            for($index = 0; $index < count($reviews);$index++) {
                $images = array_map('sanitize_text_field', $reviews[$index]['images'] ?? []);
                if(erai()->get_settings()->isTranslate()) {
                    $translation_result = $this->translate_text_with_deepl($reviews[$index]['buyerFeedback'], $this->deeplapikey, $this->translateto, $this->freeapi);
                    if(!$translation_result['success']) {
                        $errormsg = $translation_result['content'];
                        break;
                    }
                    $reviews[$index]['buyerFeedback'] = $translation_result['content'];
                }

                $username = sanitize_text_field($reviews[$index]['buyerName']);
                if(erai()->get_settings()->isHideAnonymousBuyer() && !isset($reviews[$index]['translated'])) {
                    $username = $reviews[$index]['anonymous'] ? $this->generate_username() : $username;
                }

                $stars = (int) sanitize_text_field($reviews[$index]['buyerEval'])/20;

                if($stars < $this->minimumStars) {
                    $stars = $this->minimumStars;
                }

                $reviewadded = $this->add_woocommerce_review($product_id, 
                $username, 
                sanitize_text_field($reviews[$index]['buyerFeedback']), 
                $stars, 
                $images,
                sanitize_text_field($reviews[$index]['evaluationId']));
                if($reviewadded) {
                    $addedReviews++;
                    $removedIds[] = $index;
                }
            }

            if(empty($errormsg)) {
                echo json_encode(['success' => true, 'removedIds' => $removedIds, 'msg' => $addedReviews.' out of '.count($reviews).' reviews have been added']);
                exit();
            }
            echo json_encode(['success' => false, 'removedIds' => $removedIds, 'msg' => 'Something went wrong with the creation of the reviews - only '.$addedReviews.' reviews were added.'. $errormsg]);
            exit();
        }

        function generate_username() {
            // Generate one random uppercase letter
            $uppercase = chr(rand(65, 90)); // ASCII range for A-Z

            // Generate one random lowercase letter
            $lowercase = chr(rand(97, 122)); // ASCII range for a-z

            return $uppercase . "***" . $lowercase;
        }

        function translate_review_text() {
            $reviewtext = sanitize_text_field($_POST['review_text']);
            $result = $this->translate_text_with_deepl($reviewtext, $this->deeplapikey, $this->translateto, $this->freeapi);
            echo json_encode($result);
            exit();
        }

        function translate_text_with_deepl($text, $auth_key, $target_lang, $freeapi) {
            $apitype = $freeapi ? '-free' : '';
            $url = 'https://api'. $apitype .'.deepl.com/v2/translate';
        
            // Request headers
            $headers = array(
                'Authorization' => 'DeepL-Auth-Key ' . $auth_key,
                'Content-Type'  => 'application/json'
            );
        
            // Request body
            $body = json_encode(array(
                'text' => array($text),  
                'target_lang' => $target_lang
            ));
        
            // Sending the POST request
            $response = wp_remote_post($url, array(
                'headers' => $headers,
                'body'    => $body,
                'timeout' => 15
            ));
        
            // Check if the response is successful
            if (is_wp_error($response)) {
                return ['success' => false, 'content' => 'Request failed: ' . $response->get_error_message()];
            } else {
                $response_body = wp_remote_retrieve_body($response);
                $decoded_body = json_decode($response_body, true);
        
                // Check if the translation is successful
                if (isset($decoded_body['translations'][0]['text'])) {
                    return ['success' => true, 'content' => $decoded_body['translations'][0]['text']];
                } else {
                    return ['success' => false, 'content' => 'Translation error: ' . $response_body];
                }
            }
        }

        function add_woocommerce_review( $product_id, $username, $review_content, $rating, $images, $evaluationId = -1, $title = '' ) {
            // Check if the product exists
            if ( ! get_post( $product_id ) ) {
                return false;
            }
          
            // Prepare the comment data
            $comment_data = array(
                'comment_post_ID'      => $product_id,
                'comment_author'       => $username,
                'comment_author_email' => $username . "@gmail.com",
                'comment_content'      => $review_content,
                'comment_type'         => 'review',
                'comment_approved'     => 1, // Set to 1 to approve the review immediately
                'user_id'              => -1,
            );
          
            
    
            // Insert the comment (review)
            $comment_id = wp_insert_comment( $comment_data );
          
            if ( ! $comment_id ) {
                return false;
            }
          
            // Add rating as a comment meta
            if ( $rating && $rating >= 1 && $rating <= 5 ) {
                update_comment_meta( $comment_id, 'rating', $rating );
            }
    
            // Add a title for the review if provided
            if ( ! empty( $title ) ) {
                update_comment_meta( $comment_id, 'title', sanitize_text_field( $title ) );
            }
    
            add_comment_meta($comment_id, "erai-aliexpress-reviewid", $evaluationId);
            
            if(empty($images)) {
                return $comment_id;
            }

            $imageIds = [];
            for($index = 0;$index < count($images);$index++) {
                $imageId = $this->upload_file_from_url($images[$index]);
                if(!is_numeric($imageId) || !$imageId) {
                    continue;
                }
                $imageIds[] = $imageId;
            }
            if($imageIds) {
                add_comment_meta($comment_id, "erai-reviews-images", $imageIds);
            }
            
            return $comment_id;
        }

        private function upload_file_from_url($image_url) {
            // Get the image from the URL
            $response = wp_remote_get($image_url);
        
            // Check if the request was successful
            if (is_wp_error($response)) {
                return $response;
            }
        
            // Get the image body (the actual content of the image)
            $image_data = wp_remote_retrieve_body($response);
        
            // Get the file name from the URL
            $filename = basename($image_url);
        
            // Check if the uploads directory is writable
            $upload_dir = wp_upload_dir();
            if (wp_mkdir_p($upload_dir['path'])) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }
        
            // Save the image to the uploads directory
            file_put_contents($file, $image_data);
        
            // Check the file type (image/jpeg, image/png, etc.)
            $wp_filetype = wp_check_filetype($filename, null);
        
            // Create an attachment post in the media library
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name($filename),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );
        
            // Insert the attachment into the media library
            $attach_id = wp_insert_attachment($attachment, $file);
        
            // Include the file in the attachment metadata
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            wp_update_attachment_metadata($attach_id, $attach_data);
        
            return $attach_id;
          }
    }


    new ERAI_WC_Review_Handler();
}