<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Retrieves customer reviews and the rating of the given product and updates the product cache.
 * @since        3.9.0
 * @since        4.3.4  Renamed from `AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview2`. Changed the action hook name from `aal_action_api_get_customer_review2`.
 */
class AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName     = 'aal_action_api_get_customer_review';
    protected $_iCallbackParameters = 3;


    /**
     * @var  integer
     * @sine 4.3.6
     */
    protected $_iHTTPRequestInterval = 10;

    /**
     * @since 4.5.0
     */
    protected function _construct() {
        $this->_iHTTPRequestInterval = apply_filters( 'aal_filter_http_request_interval_customer_reviews', $this->_iHTTPRequestInterval );
    }

    /**
     * @return bool
     * @since 4.3.0
     */
    protected function _shouldProceed( /* $aArguments */ ) {
        if ( $this->hasBeenCalled( get_class( $this ) . '::' . __METHOD__ . '_' . serialize( func_get_args() ) ) ) {
            return false;
        }
        if ( ! $this->_hasEnoughTime() ) {
            $_bScheduled = AmazonAutoLinks_Event_Scheduler::scheduleTaskCheckResume( time(), true );
            exit;   // avoid reaching the maximum execution time
        }
        return true;
    }

    /**
     *
     */
    protected function _doAction( /* $sProductID, $iCacheDuration, $bForceRenew */ ) {

        $_aParams        = func_get_args() + array( null, null, null );
        $_iCacheDuration = $_aParams[ 1 ];
        $_bForceRenew    = $_aParams[ 2 ];
        $_aProductID     = explode( '|', $_aParams[ 0 ] );
        $_sASIN          = $_aProductID[ 0 ];
        $_sLocale        = $_aProductID[ 1 ];
        $_sCurrency      = $_aProductID[ 2 ];
        $_sLanguage      = $_aProductID[ 3 ];
        $_sURL           = $this->___getReviewPageURL( $_sASIN, $_sLocale );
        $_sCharacterSet  = '';
        $_sHTML          = $this->___getReviewPage( $_sURL, $_sCharacterSet, $_iCacheDuration, $_bForceRenew, $_sLocale, $_sLanguage );
        $_aRow           = $this->___getRowFormatted( $_sURL, $_sHTML, $_iCacheDuration, $_sCharacterSet, $_sASIN, $_sLocale, $_sCurrency, $_sLanguage );
        $this->___updateRow( $_aRow, $_sASIN, $_sLocale, $_sCurrency, $_sLanguage );

    }
        /**
         * @param $sASIN
         * @param $sLocale
         * @return string
         * @since 4.3.4
         */
        private function ___getReviewPageURL( $sASIN, $sLocale ) {
            $_oLocale = new AmazonAutoLinks_Locale( $sLocale );
            return $_oLocale->getCustomerReviewURL( $sASIN );
        }
        private function ___updateRow( array $aRow, $sASIN, $sLocale, $sCurrency, $sLanguage ) {
            if ( empty( $aRow ) ) {
                return;
            }
            $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
            if ( version_compare( get_option( 'aal_products_version', '0' ), '1.4.0b01', '<' ) ) {
                $_oProductTable->setRowByASINLocale( $sASIN . '_' . strtoupper( $sLocale ), $aRow, $sCurrency, $sLanguage );
                return;
            }
            $_oProductTable->setRow( $aRow );
        }
        /**
         * @param  string  $sURL
         * @param  string  $sCharacterSet
         * @param  integer $iCacheDuration
         * @param  boolean $bForceRenew
         * @param  string  $sLocale
         * @param  string  $sLanguage
         * @return string
         * @since  4.3.4
         */
        private function ___getReviewPage( $sURL, &$sCharacterSet, $iCacheDuration, $bForceRenew, $sLocale, $sLanguage ) {
            $this->___getReviewPageResponse( $_oHTTP, $sURL, $sLocale, $iCacheDuration, $bForceRenew, $sLanguage );
            $sCharacterSet = $_oHTTP->getCharacterSet(); // empty parameter value will retrieve the last set character set. This works as only one USL is parsed.
            return $_oHTTP->getBody();
        }
            /**
             * @param  AmazonAutoLinks_HTTPClient $oHTTP
             * @param  string  $sURL
             * @param  string  $sLocale
             * @param  integer $iCacheDuration
             * @param  boolean $bForceRenew
             * @param  string  $sLanguage
             * @return array|WP_Error
             * @since  4.3.4
             */
            private function ___getReviewPageResponse( &$oHTTP, $sURL, $sLocale, $iCacheDuration, $bForceRenew, $sLanguage ) {
                $oHTTP          = new AmazonAutoLinks_HTTPClient(
                    $sURL,
                    $iCacheDuration,
                    array(
                        'timeout'     => 20,
                        'redirection' => 20,
                        'interval'    => $this->_iHTTPRequestInterval,
                        'renew_cache' => ( boolean ) $bForceRenew,
                    ),
                    'customer_review'
                );
                return $oHTTP->getResponse();
            }

        /**
         *
         * @param string $sURL
         * @param string $sHTML
         * @param integer $iCacheDuration
         * @param string $sReviewCharSet
         * @param string $sASIN
         * @param string $sLocale
         * @param string $sCurrency
         * @param string $sLanguage
         * @return      array
         * @since   3.9.0
         * @since   4.3.0   Added the `$sASIN`, `$sLocale`, `$sCurrency`, and `$sLanguage` parameters.
         */
        private function ___getRowFormatted( $sURL, $sHTML, $iCacheDuration, $sReviewCharSet, $sASIN, $sLocale, $sCurrency, $sLanguage ) {

            $_oScraper      = new AmazonAutoLinks_ScraperDOM_CustomerReview2( $sHTML );
            $_inRating      = $_oScraper->getRating();
            $_inReviewCount = $_oScraper->getNumberOfReviews();
            $_snReviews     = $_oScraper->getCustomerReviews();
            $_aRow          = array(
                'customer_review_url'     => $sURL,
                'customer_review_charset' => $sReviewCharSet,
                'customer_reviews'        => is_null( $_snReviews ) ? '' : $_snReviews,
                'modified_time'           => date( 'Y-m-d H:i:s' ),
            );

            // Ratings are handled separately with a different event but if they are retrieved here, update them as well as reviews.
            if ( isset( $_inRating, $_inReviewCount ) ) {
                $_aRow = array(
                    'rating'                  => $_inRating,
                    'rating_image_url'        => AmazonAutoLinks_Unit_Utility::getRatingStarImageURL( $_inRating ),
                    'rating_html'             => '',    // @deprecated 3.9.0
                    'number_of_reviews'       => $_inReviewCount,                        
                ) + $_aRow;
            }
            
            if ( version_compare( get_option( 'aal_products_version', '0' ), '1.4.0b01', '>=' ) ) {
                $_aRow[ 'product_id' ] = "{$sASIN}|{$sLocale}|{$sCurrency}|{$sLanguage}";
            }

            // if `0` is passed for the cache duration, it just renews the cache and does not update the expiration time.
            if ( $iCacheDuration ) {
                $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
            }
            return $_aRow;
            
        }

    /**
     * Checks if there is an enough time to perform an action that involves an HTTP request.
     * @return boolean
     * @since  4.3.6
     */
    protected function _hasEnoughTime() {

        $_iMaxExecutionTime = $this->getMaxExecutionTime();
        if ( ! $_iMaxExecutionTime ) {
            return true;
        }
        $_fElapsed          = microtime( true ) - $this->getObjectCache( 'load_time' );
        $_iRemained         = $_iMaxExecutionTime - round( $_fElapsed ); // remained seconds
        $_iExpectedTime     = 5; // maybe 5 seconds to perform one HTTP request.
        return $this->_iHTTPRequestInterval + $_iExpectedTime < $_iRemained;

    }

}