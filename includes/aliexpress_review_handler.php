<?php
namespace ERA\Handler\ERAI_Aliexpress_Review_Handler {
    
    class ERAI_Aliexpress_Review_Handler {

        private $reviews;

        private $alixproductId;

        function __construct($alixproductId = "", $reviews = []) {
            $this->alixproductId = $alixproductId;
            $this->reviews = $reviews;
        }

        public function load_reviews() {
            $url = erai()->get_settings()->getReviewUrl();
        
            $url = str_replace("{product_id}", $this->alixproductId, $url);
            // Perform the API request to extract reviews
            $response = wp_remote_get($url,array('timeout' => 10));
    
            if (is_wp_error($response)) {
                // Log error or handle it (you might want to add some error handling here)
                error_log("[EARI] Faild Requesting Product. Respone Error " . $response->error_data);
                return false;
            }
    
            // Decode the response body to an array
            $data = json_decode(wp_remote_retrieve_body($response), true);
    
            $this->reviews =$data["data"]["evaViewList"] ?? [];
            return $this->reviews;
        }

        function exclude_existing_reviews($postid) {
            $excludedIds = [];
            $args = array(
                'post_id' => $postid,
                'meta_query' => array(
                    array(
                        'key' => 'erai-aliexpress-reviewid',
                        'compare' => 'EXISTS'
                    ),
                ),
            );
            
            // Perform the query
            $comments_query = new \WP_Comment_Query;  // Notice the backslash here
            $comments = $comments_query->query($args);
            
            // Check if comments are found
            if ($comments) {
                foreach ($comments as $comment) {
                    $excludedIds[] = get_comment_meta($comment->comment_ID, 'erai-aliexpress-reviewid', true);
                }
            }
            $reviews = [];
            for(!$index = 0;$index < count($this->reviews);$index++) {
                if(!in_array($this->reviews[$index]['evaluationId'], $excludedIds)) {
                    $reviews[] = $this->reviews[$index];
                }
            }
            $this->reviews = $reviews;
            return $this->reviews;
        }

        /**
         * Get the value of reviews
         */
        public function getReviews() {
            return $this->reviews;
        }

        /**
         * Set the value of reviews
         */
        public function setReviews($reviews) {
            $this->reviews = $reviews;
        }

        /**
         * Get the value of alixproductId
         */
        public function getAlixproductId() {
            return $this->alixproductId;
        }

        /**
         * Set the value of alixproductId
         */
        public function setAlixproductId($alixproductId) {
            $this->alixproductId = $alixproductId;
        }
    }

    function earai_load_review($productId, $postid) {
        $handler = new ERAI_Aliexpress_Review_Handler($productId);
        $handler->load_reviews();
        return $handler->exclude_existing_reviews($postid);
    }
}