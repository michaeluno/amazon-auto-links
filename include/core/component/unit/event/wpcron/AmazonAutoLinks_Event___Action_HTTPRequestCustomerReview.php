<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Handles  SimplePie cache renewal events.
 * @package      Amazon Auto Links
 * @since        3
 * @since        3.5.0      Renamed from `AmazonAutoLinks_Event_Action_CustomerReview`.
 */
class AmazonAutoLinks_Event___Action_HTTPRequestCustomerReview extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_api_get_customer_review';

    protected function _construct() {
        add_filter( 'aal_filter_http_request_cache_name', array( $this, 'replyToModifyHTTPRequestCacheName' ), 10, 3 );
    }
        /**
         * Remove request specific query argument in the URL query string to construct a cache name.
         * @callback    add_filter  aal_filter_http_request_cache_name
         * @param       string      $sCacheName
         * @param       string      $sURL
         * @param       string      $sRequestType
         * @return      string
         * @since       3.5.0
         */
        public function replyToModifyHTTPRequestCacheName( $sCacheName, $sURL, $sRequestType ) {

            if ( 'customer_review' !== $sRequestType ) {
                return $sCacheName;
            }
            // e.g. https://www.amazon.com/reviews/iframe?akid=AKIAIUOXXAXPYUKNVPVA&alinkCode=xm2&asin=B00DBYBNEE&atag=amazon-auto-links-20&exp=2017-01-08T17%3A14%3A23Z&summary=1&v=2&sig=Ey0%2FaqdtYsl1kf8PyUA4hst9SJQBfjYX2EBJsvMEVAU%3D
            $sURL = remove_query_arg(
                array(
                    'akid',         //=AKIAIUOXXAXPYUKNVPVA
                    'alinkCode',    //=xm2
                    'atag',         //=amazon-auto-links-20
                    'exp',          //=2017-01-08T17%3A14%3A23Z
                    'summary',      //=1
                    'v',            //=2
                    'sig',          //=Ey0%2FaqdtYsl1kf8PyUA4hst9SJQBfjYX2EBJsvMEVAU%3D
                ),
                $sURL
            );
            return AmazonAutoLinks_Registry::TRANSIENT_PREFIX
                . '_'
                . md5( $sURL );

        }

    /**
     *
     */
    protected function _doAction( /* $aArguments=array( 0 => url, 1 => asin, 2 => locale, 3 => cache_duration, 4 => force renew  ) */ ) {
        
        $_aParams        = func_get_args() + array( null );
        $_aArguments     = $_aParams[ 0 ] + array( null, null, null, null, false );
        $_sURL           = $_aArguments[ 0 ];
        $_sASIN          = $_aArguments[ 1 ];
        $_sLocale        = $_aArguments[ 2 ];
        $_iCacheDuration = $_aArguments[ 3 ];
        $_bForceRenew    = $_aArguments[ 4 ];

        $_oHTTP          = new AmazonAutoLinks_HTTPClient( $_sURL, $_iCacheDuration, null, 'customer_review' );
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
            $_aRow
        );  

    }   
        /**
         * 
         * @return      array
         */
        private function ___getRowFormatted( $sURL, $sHTML, $iCacheDuration, $sReviewCharSet ) {
                     
            $_oScraper = new AmazonAutoLinks_ScraperPSHDP_CustomerReview(
                $sHTML
            );        
            if ( ! $_oScraper->oSimpleDOM ) {
                return array();  
            }

            $_aRow = array(
                'rating'                  => $_oScraper->getRating(),
                'rating_image_url'        => $_oScraper->getRatingImageURL(),
                'rating_html'             => $_oScraper->getRatingHTML(),
                'number_of_reviews'       => $_oScraper->getNumberOfReviews(),
                'customer_review_url'     => $sURL,
                'customer_review_charset' => $sReviewCharSet,
                // 'customer_review_charset' => $oHTTP->getCharacterSet( $sURL ),
                'customer_reviews'        => $_oScraper->getCustomerReviews(),
                'modified_time'           => date( 'Y-m-d H:i:s' ),
            );

            // if `0` is passed for the cache duration, it just renews the cache and do not update the expiration time.
            if ( $iCacheDuration ) {
                $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
            }
            return $_aRow;
            
        }


}