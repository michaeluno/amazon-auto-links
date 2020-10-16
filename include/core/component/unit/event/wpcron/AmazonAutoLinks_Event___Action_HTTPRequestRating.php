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
 * @since        4.3.4
 * @remark       Not used at the moment and still being tested.
 */
class AmazonAutoLinks_Event___Action_HTTPRequestRating extends AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview {

    protected $_sActionHookName = 'aal_action_api_get_product_rating';
    protected $_iCallbackParameters = 3;

    /**
     *
     */
    protected function _doAction( /* $sProductID, $iCacheDuration, $bForceRenew */ ) {

        $sASIN       = $sLocale = $sCurrency = $sLanguage = $iCacheDuration = $bForceRenew = null;
        $this->___setParameters( func_get_args(), $sASIN, $sLocale, $sCurrency, $sLanguage, $iCacheDuration, $bForceRenew );
        $_sURL       = $this->___getRatingWidgetPageURL( $sASIN, $sLocale );
        $_sHTML      = $this->___getWidgetPage( $_sURL, $iCacheDuration, $bForceRenew, $sLocale, $sLanguage );
        $_aRow       = $this->___getRowFormatted( $_sHTML, $iCacheDuration, $sASIN, $sLocale, $sCurrency, $sLanguage );
        if ( empty( $_aRow ) ) {
            return;
        }
        $this->___setRow( $_aRow, $sASIN, $sLocale, $sCurrency, $sLanguage );

    }

        /**
         * @param array  $aRow
         * @param string $sASIN
         * @param string $sLocale
         * @param string $sCurrency
         * @param string $sLanguage
         *
         * @since 4.3.4
         */
        private function ___setRow( array $aRow, $sASIN, $sLocale, $sCurrency, $sLanguage ) {
            $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
            if ( version_compare( get_option( 'aal_products_version', '0' ), '1.4.0b01', '<' ) ) {
                $_oProductTable->setRowByASINLocale( $sASIN . '_' . strtoupper( $sLocale ), $aRow, $sCurrency, $sLanguage );
                return;
            }
            $_oProductTable->setRow( $aRow );
        }

        /**
         * @param array   $aParameters
         * @param string  $sASIN
         * @param string  $sLocale
         * @param string  $sCurrency
         * @param string  $sLanguage
         * @param integer $iCacheDuration
         * @param boolean $bForceRenew
         */
        private function ___setParameters( $aParameters, &$sASIN, &$sLocale, &$sCurrency, &$sLanguage, &$iCacheDuration, &$bForceRenew ) {
            $aParameters    = $aParameters + array( null, null, null );
            $iCacheDuration = $aParameters[ 1 ];
            $bForceRenew    = $aParameters[ 2 ];
            $_aProductID    = explode( '|', $aParameters[ 0 ] ) + array( null, null, null, null );
            $sASIN          = $_aProductID[ 0 ];
            $sLocale        = $_aProductID[ 1 ];
            $sCurrency      = $_aProductID[ 2 ];
            $sLanguage      = $_aProductID[ 3 ];
        }
        /**
         * @param  string  $sURL
         * @param  integer $iCacheDuration
         * @param  boolean $bForceRenew
         * @param  string  $sLocale           This is needed to generate cookies.
         * @param  string  $sLanguage         This is also needed to generate cookies.           
         * @see    https://stackoverflow.com/questions/8279478/amazon-product-advertising-api-get-average-customer-rating/31329604#31329604
         * @return string
         */
        private function ___getWidgetPage( $sURL, $iCacheDuration, $bForceRenew, $sLocale, $sLanguage ) {

            /**
             * Perform an HTTP request
             * This handles character encoding conversion.
             * @var AmazonAutoLinks_HTTPClient $_oHTTP
             */
            $this->___getWidgetPageResponse( $_oHTTP, $sURL, $iCacheDuration, $bForceRenew, $sLocale, $sLanguage );
            return $_oHTTP->getBody();

        }
            /**
             * @param  null|AmazonAutoLinks_HTTPClient $oHTTP
             * @param  string  $sURL
             * @param  boolean $iCacheDuration
             * @param  boolean $bForceRenew
             * @param  string  $sLocale
             * @param  string  $sLanguage
             * @return array|WP_Error
             */
            private function ___getWidgetPageResponse( &$oHTTP, $sURL, $iCacheDuration, $bForceRenew, $sLocale, $sLanguage ) {

                $_oLocale           = new AmazonAutoLinks_Locale( $sLocale );
                $_oVersatileCookies = new AmazonAutoLinks_VersatileFileManager_AmazonCookies( $sLocale );
                $_aRequestCookies   = $_oVersatileCookies->get();
                $_aRequestCookies   = empty( $_aRequestCookies )
                    ? $_oLocale->getHTTPRequestCookies()
                    : $_aRequestCookies;

                $_aArguments      = array(
                    'timeout'     => 20,
                    'redirection' => 20,
                    'cookies'     => $_aRequestCookies,
                    'interval'    => 10,
                    'renew_cache' => ( boolean ) $bForceRenew,
                );
                $_oHTTP           = new AmazonAutoLinks_HTTPClient( $sURL, $iCacheDuration, $_aArguments, 'rating' );
                $_aoResponse      = $_oHTTP->getResponse();

                // If 404, try again
                if ( 404 === $_oHTTP->getStatusCode() ) {
                    $_aArguments[ 'renew_cache' ] = true;
                    $_aArguments[ 'cookies' ]     = array_merge( $_oHTTP->getCookies(), $_aRequestCookies );
                    $_oHTTP                       = new AmazonAutoLinks_HTTPClient(
                        $sURL,
                        $iCacheDuration,
                        $_aArguments,
                        'rating'
                    );
                    $_aoResponse  = $_oHTTP->getResponse();
                }
                $oHTTP = $_oHTTP;
                return $_aoResponse;

            }

        /**
         * @param string $sASIN
         * @param string $sLocale
         * @param boolean $bTest    Enable this only for testing.
         * @return string a URL
         */
        private function ___getRatingWidgetPageURL( $sASIN, $sLocale, $bTest=false ) {
            $_oLocale = new AmazonAutoLinks_Locale( $sLocale );
            $_sURL    = $_oLocale->getProductRatingWidgetURL( $sASIN );
            return $bTest
                ? add_query_arg(
                    array(
                        'test' => mt_rand( 1, PHP_INT_MAX )
                    ),
                    $_sURL
                )
                : $_sURL;
        }
        /**
         * @param  string  $sHTML
         * @param  integer $iCacheDuration
         * @param  string  $sASIN
         * @param  string  $sLocale
         * @param  string  $sCurrency
         * @param  string  $sLanguage
         * @return array
         * @since  4.3.4
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