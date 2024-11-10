<?php
namespace ERAI\Config {

    class ERAI_Settings {

        private bool $hide_anonymous_buyer;

        private int $minimum_stars;

        private string $review_url;

        private bool $translate;

        private string $deeplapikey;

        private string $translate_to;

        private bool $free_deepl_api;
        
        function __construct() {
            $this->load_settings();
        }

        function load_settings() {
            $settings = json_decode(get_option('erai_settings'), true) ?? [];
            $this->hide_anonymous_buyer = (bool) ($settings['hide_anonymous_buyer'] ?? true);
            $this->minimum_stars = (int) ($settings['minimum_stars'] ?? 1);
            $this->review_url = $settings['review_url'] ?? "https://feedback.aliexpress.com/pc/searchEvaluation.do?productId={product_id}&pageSize=200";
            $this->translate = (bool) ($settings['translate'] ?? false);
            $this->deeplapikey = $settings['deeplapikey'] ?? "";
            $this->translate_to = $settings['translate_to'] ?? "";
            $this->free_deepl_api = (bool) ($settings['free_deepl_api'] ?? false);
        }

        function update_settings($hide_anonymous_buyer = true, $minimum_stars = "", $review_url = "", $translate = false, $deeplapikey = "", $translate_to = "", $free_deepl_api = false) {
            $settings = [];
            $settings['hide_anonymous_buyer'] = (bool) ($hide_anonymous_buyer);
            $settings['minimum_stars'] = (int) !empty($minimum_stars) ? $minimum_stars : $this->minimum_stars;
            $settings['review_url'] = !empty($review_url) ? $review_url : $this->review_url;
            $settings['translate'] = (bool) $translate;
            $settings['deeplapikey'] = !empty($deeplapikey) ? $deeplapikey: $this->deeplapikey;
            $settings['translate_to'] = !empty($translate_to) ? $translate_to :  $this->translate_to;
            $settings['free_deepl_api'] = !empty($free_deepl_api) ? $free_deepl_api :  $this->free_deepl_api;
            update_option('erai_settings', json_encode($settings));
            $this->load_settings();
        }

        /**
         * Get the value of hide_anonymous_buyer
         */
        public function isHideAnonymousBuyer() {
            return $this->hide_anonymous_buyer;
        }

        /**
         * Set the value of hide_anonymous_buyer
         */
        public function setHideAnonymousBuyer(bool $hide_anonymous_buyer) {
            $this->hide_anonymous_buyer = $hide_anonymous_buyer;
            return $this;
        }

        /**
         * Get the value of minimum_stars
         */
        public function getMinimumStars() {
            return $this->minimum_stars;
        }

        /**
         * Set the value of minimum_stars
         */
        public function setMinimumStars(int $minimum_stars) {
            $this->minimum_stars = $minimum_stars;
            return $this;
        }

        /**
         * Get the value of review_url
         */
        public function getReviewUrl() {
            return $this->review_url;
        }

        /**
         * Set the value of review_url
         */
        public function setReviewUrl(string $review_url) {
            $this->review_url = $review_url;
        }

        /**
         * Get the value of translate
         */
        public function isTranslate() {
            return $this->translate;
        }

        /**
         * Set the value of translate
         */
        public function setTranslate(bool $translate) {
            $this->translate = $translate;

            return $this;
        }

        /**
         * Get the value of deeplapikey
         */
        public function getDeeplapikey() {
            return $this->deeplapikey;
        }

        /**
         * Set the value of deeplapikey
         */
        public function setDeeplapikey(string $deeplapikey) {
            $this->deeplapikey = $deeplapikey;

            return $this;
        }

        /**
         * Get the value of translate_to
         */
        public function getTranslateTo() {
            return $this->translate_to;
        }

        /**
         * Set the value of translate_to
         */
        public function setTranslateTo(string $translate_to) {
            $this->translate_to = $translate_to;

            return $this;
        }

        
        /**
         * Get the value of free_deepl_api
         */
        public function isFreeDeeplApi() {
            return $this->free_deepl_api;
        }

        /**
         * Set the value of free_deepl_api
         */
        public function setFreeDeeplApi(string $free_deepl_api) {
            $this->free_deepl_api = $free_deepl_api;

            return $this;
        }
        
    }
    erai()->set_settings(new ERAI_Settings());
}

