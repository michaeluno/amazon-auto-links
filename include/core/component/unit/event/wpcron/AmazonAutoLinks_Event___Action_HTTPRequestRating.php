<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Retrieve rating information of the given product and updates the product cache.
 *
 * @package      Amazon Auto Links
 * @since        4.3.3
 * @remark       Not used at the moment and still being tested.
 */
class AmazonAutoLinks_Event___Action_HTTPRequestRating extends AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview2 {

    protected $_sActionHookName = 'aal_action_api_get_rating';

    /**
     *
     */
    protected function _doAction( /* $sURL, $sASIN, $sLocale, $sCurrency, $sLanguage, $iCacheDuration, $bForceRenew */ ) {

        $sURL   = $sASIN = $sLocale = $sCurrency = $sLanguage = $iCacheDuration = $bForceRenew = null;
        $this->___setParameters( func_get_args(), $sURL, $sASIN, $sLocale, $sCurrency, $sLanguage, $iCacheDuration, $bForceRenew );
        $_sURL  = $this->___getRatingWidgetPageURL( $sASIN, $sLocale );
        $_sHTML = $this->___getDataFromWidgetPage( $_sURL, $iCacheDuration, $bForceRenew );
        $_aRow  = $this->___getRowFormatted( $_sHTML, $iCacheDuration, $sASIN, $sLocale, $sCurrency, $sLanguage );
        if ( empty( $_aRow ) ) {
            return;
        }

        $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        if ( version_compare( get_option( 'aal_products_version', '0' ), '1.4.0b01', '<' ) ) {
            $_oProductTable->setRowByASINLocale( $sASIN . '_' . strtoupper( $sLocale ), $_aRow, $sCurrency, $sLanguage );
            return;
        }
        $_oProductTable->setRow( $_aRow );

    }
        /**
         * @param array $aParameters
         * @param string $sURL
         * @param string $sASIN
         * @param string $sLocale
         * @param string $sCurrency
         * @param string $sLanguage
         * @param integer $iCacheDuration
         * @param boolean $bForceRenew
         */
        private function ___setParameters( $aParameters, &$sURL, &$sASIN, &$sLocale, &$sCurrency, &$sLanguage, &$iCacheDuration, &$bForceRenew ) {
            $aParameters    = $aParameters + array( null, null, null, null, null, null, null );
            $sURL           = $aParameters[ 0 ];
            $sASIN          = $aParameters[ 1 ];
            $sLocale        = $aParameters[ 2 ];
            $sCurrency      = $aParameters[ 3 ];
            $sLanguage      = $aParameters[ 4 ];
            $iCacheDuration = $aParameters[ 5 ];
            $bForceRenew    = $aParameters[ 6 ];
        }
        /**
         * @param string $sURL
         * @param integer $iCacheDuration
         * @param boolean $bForceRenew
         * @see https://stackoverflow.com/questions/8279478/amazon-product-advertising-api-get-average-customer-rating/31329604#31329604
         * @return string
         */
        private function ___getDataFromWidgetPage( $sURL, $iCacheDuration, $bForceRenew ) {

            $_oHTTP          = new AmazonAutoLinks_HTTPClient(
                $sURL,
                $iCacheDuration,
                array(  // http arguments
                    'timeout'     => 20,
                    'redirection' => 20,
                    'cookies' => array(
                        'ubid-main' => $this->___getCookie_ubid_main(),
                    ),
                ),
                'rating'
            );
            if ( $bForceRenew ) {
                $_oHTTP->deleteCache();
            }
            return $_oHTTP->get();

        }
            /**
             * Generates a string for the `ubid-main` cookie item.
             * @return string Format: XXX-XXXXXXX-XXXXXXX e.g. 001-0000000-0000000
             * @since 4.3.3
             */
            private function ___getCookie_ubid_main() {
                return sprintf( '%03d', mt_rand( 1, 999 ) )
                    . '-'
                    . sprintf( '%07d', mt_rand( 1, 9999999 ) )
                    . '-'
                    . sprintf( '%07d', mt_rand( 1, 9999999 ) );
            }
        /**
         * @param string $sASIN
         * @param string $sLocale
         * @return string a URL
         */
        private function ___getRatingWidgetPageURL( $sASIN, $sLocale ) {
            // e.g. https://www.amazon.com/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=B01B8R6V2E
            $_sSchemeDomain  = AmazonAutoLinks_Property::getStoreDomainByLocale( $sLocale ); // with http(s) prefixed
            return $_sSchemeDomain . '/gp/customer-reviews/widgets/average-customer-review/popover/ref=dpx_acr_pop_?contextId=dpx&asin=' . $sASIN;
        }
        /**
         * @param  string  $sHTML
         * @param  integer $iCacheDuration
         * @param  string  $sASIN
         * @param  string  $sLocale
         * @param  string  $sCurrency
         * @param  string  $sLanguage
         * @return array
         * @since  4.3.3
         */
        private function ___getRowFormatted( $sHTML, $iCacheDuration, $sASIN, $sLocale, $sCurrency, $sLanguage ) {

            $_oScraper      = new AmazonAutoLinks_ScraperDOM_WidgetUserRating( $sHTML );
            $_iRating       = $_oScraper->getRating();
            $_inReviewCount = $_oScraper->getNumberOfReviews();
            $_aRow          = array(
                'rating'                  => $_iRating,
                'rating_image_url'        => AmazonAutoLinks_Unit_Utility::getRatingStarImageURL( $_iRating ),
                'rating_html'             => '',    // @deprecated 3.9.0
                'number_of_reviews'       => $_inReviewCount,
                'modified_time'           => date( 'Y-m-d H:i:s' ),
            );

            if ( version_compare( get_option( 'aal_products_version', '0' ), '1.4.0b01', '>=' ) ) {
                $_aRow[ 'product_id' ] = "{$sASIN}|{$sLocale}|{$sCurrency}|{$sLanguage}";
            }

            // if `0` is passed for the cache duration, it just renews the cache and does not update the expiration time.
            if ( $iCacheDuration ) {
                $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
            }
            return $_aRow;
            
        }

}