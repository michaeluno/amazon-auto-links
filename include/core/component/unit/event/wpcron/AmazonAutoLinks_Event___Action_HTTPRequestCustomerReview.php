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
 * Retrieves customer reviews and the rating of the given product and updates the product cache.
 * @package      Amazon Auto Links
 * @since        3.9.0
 * @since        4.3.3  Renamed from `AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview2`.
 */
class AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_api_get_customer_review2';

    /**
     * @return bool
     * @since 4.3.0
     */
    protected function _shouldProceed( /* $aArguments */ ) {
        if ( $this->hasBeenCalled( get_class( $this ) . '::' . __METHOD__ . '_' . serialize( func_get_args() ) ) ) {
            return false;
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
        $_sURL           = AmazonAutoLinks_Unit_Utility::getCustomerReviewURL( $_sASIN, $_sLocale );
        
        $_oHTTP          = new AmazonAutoLinks_HTTPClient(
            $_sURL,
            $_iCacheDuration,
            array(  // http arguments
                'timeout'     => 20,
                'redirection' => 20,
            ),
            'customer_review2'
        );
        if ( $_bForceRenew ) {
            $_oHTTP->deleteCache();
        }
        $_aRow           = $this->___getRowFormatted(
            $_sURL,
            $_oHTTP->get(),
            $_iCacheDuration,
            $_oHTTP->getCharacterSet(), // empty parameter value will retrieve the last set character set. This works as only one USL is parsed.
            $_sASIN,
            $_sLocale,
            $_sCurrency,
            $_sLanguage
        );
        if ( empty( $_aRow ) ) {
            return;
        }

        $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        if ( version_compare( get_option( 'aal_products_version', '0' ), '1.4.0b01', '<' ) ) {
            $_oProductTable->setRowByASINLocale( $_sASIN . '_' . strtoupper( $_sLocale ), $_aRow, $_sCurrency, $_sLanguage );
            return;
        }
        $_mResult = $_oProductTable->setRow( $_aRow );

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

            $_aRow = array(
                'customer_review_url'     => $sURL,
                'customer_review_charset' => $sReviewCharSet,
                'customer_reviews'        => $_oScraper->getCustomerReviews(),
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

}