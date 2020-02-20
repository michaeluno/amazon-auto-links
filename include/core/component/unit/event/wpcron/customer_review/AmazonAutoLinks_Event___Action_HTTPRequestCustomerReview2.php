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
 * Handles  SimplePie cache renewal events.
 * @package      Amazon Auto Links
 * @since        3.9.0
 */
class AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview2 extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_api_get_customer_review2';

    protected function _construct() {}

    /**
     *
     */
    protected function _doAction( /* $aArguments=array( 0 => url, 1 => asin, 2 => locale, 3 => cache_duration, 4 => force renew, 5 => currency, 6 => language  ) */ ) {
        
        $_aParams        = func_get_args() + array( null );
        $_aArguments     = $_aParams[ 0 ] + array( null, null, null, null, false, null, null );
        $_sURL           = $_aArguments[ 0 ];
        $_sASIN          = $_aArguments[ 1 ];
        $_sLocale        = $_aArguments[ 2 ];
        $_iCacheDuration = $_aArguments[ 3 ];
        $_bForceRenew    = $_aArguments[ 4 ];
        $_sCurrency      = $_aArguments[ 5 ];
        $_sLanguage      = $_aArguments[ 6 ];

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
        $_sHTMLBody      = $_oHTTP->get();

        if ( ! $_sHTMLBody ) {
            return;
        }        

        $_aRow           = $this->___getRowFormatted(
            $_sURL,
            $_sHTMLBody,
            $_iCacheDuration,
            $_oHTTP->getCharacterSet() // empty parameter value will retrieve the last set character set. This works as only one USL is parsed.
        );
        if ( empty( $_aRow ) ) {
            return;
        }

        $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_iSetObjectID  = $_oProductTable->setRowByASINLocale(
            $_sASIN . '_' . strtoupper( $_sLocale ),
            $_aRow,
            $_sCurrency,
            $_sLanguage
        );  

    }   
        /**
         * 
         * @return      array
         */
        private function ___getRowFormatted( $sURL, $sHTML, $iCacheDuration, $sReviewCharSet ) {

            $_oScraper      = new AmazonAutoLinks_ScraperDOM_CustomerReview2( $sHTML );
            $_iRating       = $_oScraper->getRating();
            $_inReviewCount = $_oScraper->getNumberOfReviews();

            $_aRow = array(
                'rating'                  => $_iRating,
                'rating_image_url'        => AmazonAutoLinks_Unit_Utility::getRatingStarImageURL( $_iRating ),
                'rating_html'             => '',    // @deprecated 3.9.0
                'number_of_reviews'       => $_inReviewCount,
                'customer_review_url'     => $sURL,
                'customer_review_charset' => $sReviewCharSet,
                // 'customer_review_charset' => $oHTTP->getCharacterSet( $sURL ),
                'customer_reviews'        => $_oScraper->getCustomerReviews(),
                'modified_time'           => date( 'Y-m-d H:i:s' ),
            );

            // if `0` is passed for the cache duration, it just renews the cache and does not update the expiration time.
            if ( $iCacheDuration ) {
                $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
            }
            return $_aRow;
            
        }


}