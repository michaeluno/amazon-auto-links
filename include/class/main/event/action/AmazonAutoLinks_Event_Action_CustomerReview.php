<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Handles  SimplePie cache renewal events.
 * @package      Amazon Auto Links
 * @since        3
 * @action       aal_action_api_get_customer_review
 */
class AmazonAutoLinks_Event_Action_CustomerReview extends AmazonAutoLinks_Event_Action_Base {
        
    /**
     * 
     * @callback        action        aal_action_api_get_customer_review
     */
    public function doAction( /* $aArguments=array( 0 => url, 1 => asin, 2 => locale, 3 => cache_duration  ) */ ) {
        
        $_aParams        = func_get_args() + array( null );
        $_aArguments     = $_aParams[ 0 ] + array( null, null, null, null );
        $_sURL           = $_aArguments[ 0 ];
        $_sASIN          = $_aArguments[ 1 ];
        $_sLocale        = $_aArguments[ 2 ];
        $_iCacheDuration = $_aArguments[ 3 ];

        $_oHTTP         = new AmazonAutoLinks_HTTPClient( $_sURL, $_iCacheDuration );
        $_sHTMLBody     = $_oHTTP->get();
        if ( ! $_sHTMLBody ) {
            return;
        }        

        $_aRow           = $this->_formatRow( 
            $_sURL,
            $_sHTMLBody,
            $_iCacheDuration,
            $_oHTTP->getCharacterSet() // empty parameter value will retrieve the last set character set. This works as only one USL is parsed.
        );
        if ( empty( $_aRow ) ) {
            return;
        }

        $_oProductTable = new AmazonAutoLinks_DatabaseTable_product(
            AmazonAutoLinks_Registry::$aDatabaseTables[ 'product' ]
        );             
        $_iSetObjectID  = $_oProductTable->setRowByASINLocale(
            $_sASIN . '_' . strtoupper( $_sLocale ),
            $_aRow
        );  

    }   
        /**
         * 
         * @return      array
         */
        private function _formatRow( $sURL, $sHTML, $iCacheDuration, $sReviewCharSet ) {
                     
            $_oScraper = new AmazonAutoLinks_ScraperPSHDP_CustomerReview(
                $sHTML
            );        
            if ( ! $_oScraper->oSimpleDOM ) {
                return array();  
            }

            return array(
                'rating'                  => $_oScraper->getRating(),
                'rating_image_url'        => $_oScraper->getRatingImageURL(),
                'rating_html'             => $_oScraper->getRatingHTML(),
                'number_of_reviews'       => $_oScraper->getNumberOfReviews(),
                'customer_review_url'     => $sURL,
                'customer_review_charset' => $sReviewCharSet,
                // 'customer_review_charset' => $oHTTP->getCharacterSet( $sURL ),
                'customer_reviews'        => $_oScraper->getCustomerReviews(),
                'modified_time'           => date( 'Y-m-d H:i:s' ), 
                'expiration_time'         => date( 'Y-m-d H:i:s', time() + $iCacheDuration ),
            );
            
        }

}