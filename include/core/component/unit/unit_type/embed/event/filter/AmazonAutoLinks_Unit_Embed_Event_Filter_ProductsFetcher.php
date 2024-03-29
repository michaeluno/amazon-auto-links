<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Fetches product data from outside source.
 *
 * @since 5.0.0
 * @deprecated 5.0.0
 */
class AmazonAutoLinks_Unit_Embed_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_Category_Event_Filter_ProductsFetcher {

    /**
     * @var   string 
     * @since 5.0.0
     */
    public $sUnitType = 'embed';

    /**
     * @var AmazonAutoLinks_UnitOutput_embed
     * @since 5.0.0
     */
    public $oUnitOutput;

    /**
     * Represents the structure of each product array.
     * @var array
     */
    public static $aStructure_Product = array();

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {

        // Originally the glue was \s+ but not sure what this was for.
        // If it is split by a white space, search result URL cannot be parsed properly such as https://www.amazon.it/s?k=harry potter&...
        $_aURLs                  = preg_split( "/[\r\n]+/", trim( ( string ) $this->oUnitOutput->oUnitOption->get( 'uri' ) ), 0, PREG_SPLIT_NO_EMPTY );

        $_sLanguage              = $this->oUnitOutput->oUnitOption->get( 'language' );
        $_aASINsPerURL           = $this->___getASINsPerURL( $_aURLs, $_sLanguage, $_aASINsPerNonProductURL );
        $_iCount                 = ( integer ) $this->oUnitOutput->oUnitOption->get( 'count' );
        $_aProducts              = $this->___getProductsWithASINs( $_aASINsPerURL, $_iCount, $_sAssociateID, $_sLocale, $_sLanguage );
        $_aProducts              = $_aProducts + $this->___getProductsWithASINs( $_aASINsPerNonProductURL, $_iCount, $_sAssociateID, $_sLocale, $_sLanguage );
        $_aASINsOfNonProductURLs = $this->___getASINsOfNonProductURL( $_aASINsPerNonProductURL );

        $this->oUnitOutput->bNonProductURL = ! empty( $_aASINsPerNonProductURL ); // referred when displaying errors

        // Set the Associate ID and locale. These could be detected from the URL. If not detected, give an empty value so that a warning will be displayed.
        $this->oUnitOutput->oUnitOption->set( 'associate_id', $_sAssociateID ); // some elements are formatted based on this value
        if ( $_sLocale ) {  // for non-amazon sites, locale cannot be detected and empty
            $this->oUnitOutput->oUnitOption->set( 'country', $_sLocale ); // when no image is found, the alternative thumbnail is based on the default locale
        }
        $_sLocale = $this->oUnitOutput->oUnitOption->get( 'country' );   // re-retrieve the value as `$_sLocale` can be empty

        $_aProductsByPAAPI = $this->___getProductsByPAAPI( array_keys( $_aProducts ), $_sLocale );
        $_aProductsByURLs  = $this->___getProductsByPAAPI( $_aASINsOfNonProductURLs, $_sLocale );
        $_aProducts        = $this->___getProductsMerged( $_aProducts, $_aProductsByPAAPI );
        return array_merge( $_aProducts, $_aProductsByURLs );

    }

        /**
         * @remark The user might past an Amazon site url but not of the product page.
         * @param  array $aASINsPerNonProductURL
         * @return array
         * @since  4.4.0
         * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.  
         */
        private function ___getASINsOfNonProductURL( array $aASINsPerNonProductURL ) {
            $_aASINs = array();
            foreach( $aASINsPerNonProductURL as $_aASINsByURL ) {
                $_aASINs = array_merge( $_aASINs, $_aASINsByURL );
            }
            return array_unique( $_aASINs );
        }
        /**
         * @param  array $aURLs
         * @param  string $sLanguage
         * @param  array|null $aASINsPerNonProductURL
         * @return array An array holding ASINs by URL.
         * @since  ?
         * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
         */
        private function ___getASINsPerURL( array $aURLs, $sLanguage, &$aASINsPerNonProductURL ) {
            $aASINsPerNonProductURL = array();
            $aASINPerURL            = array();
            $aProductURLs           = array_unique( $aURLs );
            foreach( $aProductURLs as $_iIndex => $_sURL ) {
                // Multiple ASINs can be embedded in a single URL like https://amazon.com/dp/ABCDEFGHIJ?tag=my-associate-21,ABCDEFGHI2,ABCDEFGHI3
                $_aASINs = $this->getASINs( $_sURL );
                if ( ! empty( $_aASINs ) ) {
                    $aASINPerURL[ $_sURL ] = $_aASINs;
                    continue;
                }
                $aASINsPerNonProductURL[ $_sURL ] = $this->oUnitOutput->getASINsFromHTMLs( array( $this->___getProductPage( $_sURL, $sLanguage ) ) );
            }
            return $aASINPerURL;
        }

        /**
         * @param  array   $aASINsPerURL
         * @param  integer $iNumberOfItems
         * @param  string  $sAssociateID
         * @param  string  $sLocale
         * @param  string  $sLanguage
         * @return array
         * @since  4.4.0
         * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
         */
        private function ___getProductsWithASINs( array $aASINsPerURL, $iNumberOfItems, &$sAssociateID, &$sLocale, $sLanguage ) {
            $_aAdWidgetLocales  = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport();
            $_aProducts         = array();
            $_sLocale           = '';
            $_sAssociateID      = '';
            foreach( $aASINsPerURL as $_sURL => $_aASINs ) {
                $_sThisLocale  = AmazonAutoLinks_Locales::getLocaleFromURL( $_sURL, ( string ) $this->oUnitOutput->oUnitOption->get( array( 'country' ), 'US' ) );
                $_sLocale      = $_sLocale ? $_sLocale : $_sThisLocale;
                $_sAssociateID = $_sAssociateID ? $_sAssociateID : $this->___getAssociateIDFromURL( $_sURL, $_sThisLocale );
                if ( in_array( $_sLocale, $_aAdWidgetLocales, true ) ) {
                    $_aProducts = $_aProducts + $this->___getProductsByAdWidgetAPI( $_aASINs, $_sThisLocale, $_sAssociateID, $sLanguage );
                    continue;
                }
                $_aProducts    = $_aProducts + $this->___getProductsScraped( $_aASINs, $_sThisLocale, $_sAssociateID, $sLanguage );
            }
            $sAssociateID = $_sAssociateID ? $_sAssociateID : $sAssociateID;
            $sLocale      = $_sLocale ? $_sLocale : $sLocale;
            return $_aProducts;
        }
            /**
             * @param  array $aASINs
             * @param  string $sLocale
             * @param  string $sAssociateID
             * @param  string $sLanguage
             * @return array
             * @since  4.6.9
             * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
             */
            private function ___getProductsByAdWidgetAPI( array $aASINs, $sLocale, $sAssociateID, $sLanguage ) {

                // Capture the modified date of the response
                add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 1 );
                add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10, 2 );

                $_aProducts          = array();
                $_oAdWidgetAPISearch = new AmazonAutoLinks_AdWidgetAPI_Search( $sLocale, ( integer ) $this->oUnitOutput->oUnitOption->get( 'cache_duration' ) );
                $_aResponse          = $_oAdWidgetAPISearch->get( $aASINs );
                $_aChunks            = array_chunk( $aASINs, 20 );
                $_sAPIEndpoint       = $_oAdWidgetAPISearch->getEndpoint( reset( $_aChunks ) );

                remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
                remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10 );

                foreach( $this->getElementAsArray( $_aResponse, array( 'results' ) ) as $_aItem ) {
                    $_aStructure_Item = array(
                        'ASIN'          => null,    'Title'             => null,
                        'Price'         => null,    'ListPrice'         => null,
                        'ImageUrl'      => null,    'DetailPageURL'     => null,
                        'Rating'        => null,    'TotalReviews'      => null,
                        'Subtitle'      => null,    'IsPrimeEligible'   => null,
                    );
                    $_aItem    = $_aItem + $_aStructure_Item;
                    $_aProduct = array(
                        'ASIN'              => $_aItem[ 'ASIN' ],
                        'product_url'       => $_aItem[ 'DetailPageURL' ],
                        'thumbnail_url'     => $_aItem[ 'ImageUrl' ],
                        'title'             => $_aItem[ 'Title' ],
                        'rating'            => ( integer ) ( ( ( double ) $_aItem[ 'Rating' ] ) * 10 ),
                        'number_of_reviews' => ( integer ) $_aItem[ 'TotalReviews' ],
                        'formatted_price'   => AmazonAutoLinks_Unit_Utility::getPrice( $_aItem[ 'ListPrice' ], null, null, $_aItem[ 'Price' ], $_aItem[ 'Price' ] ),
                        'is_prime'          => ( boolean ) $_aItem[ 'IsPrimeEligible' ],
                        'updated_date'      => $this->getElement( $this->oUnitOutput->aModifiedDates, $_sAPIEndpoint ),
                    ) + $_aItem;
                    $_aProduct[ 'formatted_rating' ] = $this->___getFormattedRating( $_aProduct[ 'rating' ], $_aProduct[ 'number_of_reviews' ], $sLocale, $_aItem[ 'ASIN' ], $sAssociateID, $sLanguage );
                    $_aProducts[ $_aItem[ 'ASIN' ] ] = $_aProduct;
                }
                return $_aProducts;
            }
                /**
                 * @return string
                 * @since  4.6.9
                 * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
                 */
                private function ___getFormattedRating( $iRating, $iReviewCount, $sLocale, $sASIN, $sAssociateID, $sLanguage ) {
                    $_oLocale       = new AmazonAutoLinks_Locale( $sLocale );
                    $_sReviewURL    = $_oLocale->getCustomerReviewURL( $sASIN, $sAssociateID, $sLanguage );
                    return "<div class='amazon-customer-rating-stars'>"
                            . AmazonAutoLinks_Unit_Utility::getRatingOutput( $iRating, $_sReviewURL, $iReviewCount )
                        . "</div>";
                }

            /**
             * @return array
             * @since  4.6.9
             * @since  5.0.0  Moved from `AmazonAutoLinks_UnitOutput_embed`.
             */
            private function ___getProductsScraped( array $aASINs, $sLocale, $sAssociateID, $sLanguage ) {
                $_aProducts = array();
                foreach( $aASINs as $_sASIN ) {
                    $_aProduct = $this->___getProductScraped( $_sASIN, $sLocale, $sAssociateID, $sLanguage );
                    $_aProducts[ $_sASIN ] = $_aProduct;
                }
                return $_aProducts;
            }
                /**
                 * @return array
                 * @since  4.4.0
                 * @since  5.0.0  Moved from `AmazonAutoLinks_UnitOutput_embed`.
                 */
                private function ___getProductScraped( $sASIN, $sLocale, $sAssociateID, $sLanguage ) {

                    $_sProductURL = $this->___getProductURL( $sASIN, $sAssociateID, $sLocale, $sLanguage, $_sURLDomain );
                    $_sHTML       = $this->___getProductPage( $_sProductURL, $sLanguage );
                    $_oScraper    = new AmazonAutoLinks_ScraperDOM_Product( $_sHTML, $_sProductURL );
                    $_aProduct    = $_oScraper->get( $sAssociateID, $_sURLDomain );

                    // If the title is not set, it means failure of retrieving the product data.
                    if ( ! isset( $_aProduct[ 'title' ] ) ) { // it was 'thumbnail_url' before
                        unset( $_aProduct[ '_features' ] );
                        unset( $_aProduct[ '_sub_image_urls' ] );
                        return $_aProduct;
                    }

                    $_aProduct[ 'updated_date' ] = $this->getElement( $this->oUnitOutput->aModifiedDates, $_sProductURL );
                    $_aProduct[ 'content' ]      = ! empty( $_aProduct[ 'content' ] )
                        ? "<div class='amazon-product-content'>"
                            . $_aProduct[ 'content' ]
                        . "</div>"
                        : '';
                    $_sDescriptionExtracted      = $this->oUnitOutput->getDescriptionFormatted(
                        isset( $_aProduct[ 'description' ] ) ? $_aProduct[ 'description' ] : ( $_aProduct[ 'content' ] ? $_aProduct[ 'content' ] : implode( ' ', $_aProduct[ '_features' ] ) ),
                        $this->oUnitOutput->oUnitOption->get( 'description_length' ),
                        $this->oUnitOutput->getReadMoreText( $_aProduct[ 'product_url' ] )
                    );
                    $_aProduct[ 'description' ]  = $_sDescriptionExtracted
                        ? "<div class='amazon-product-description'>"
                            . $_sDescriptionExtracted
                        . "</div>"
                        : '';

                    $_aProduct[ 'image_set' ]           = $this->oUnitOutput->getSubImageOutput(
                        $_aProduct[ '_sub_image_urls' ],
                        $_aProduct[ 'title' ],
                        $_aProduct[ 'product_url' ],
                        ( boolean ) $this->oUnitOutput->oUnitOption->get( 'pop_up_image' )
                    );
                    if ( $_aProduct[ 'thumbnail_url' ] && ! $_aProduct[ 'image_set' ] ) {
                        $_aProduct[ 'image_set' ] = "<div class='sub-images'></div>"; // change the value from null to an empty tag so that further data inspection will not continue
                    }

                    unset( $_aProduct[ '_features' ] );
                    unset( $_aProduct[ '_sub_image_urls' ] );
                    return $_aProduct;

                }
                    /**
                     * @return string
                     * @since  ?
                     * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
                     */
                    private function ___getProductURL( $sASIN, $sAssociateID, $sLocale, $sLanguage, &$sURLDomain ) {
                        $_oLocale      = new AmazonAutoLinks_Locale( $sLocale );
                        $_sDomain      = $_oLocale->getDomain();
                        $sURLDomain    = 'https://' . $_sDomain;
                        return add_query_arg(
                            array(
                                'tag'      => $sAssociateID,
                                'language' => $sLanguage,
                            ),
                            $sURLDomain . '/dp/' . $sASIN . '/'
                        );
                    }

                    /**
                     * @param  string $sURL
                     * @param  string $sLanguage
                     * @return string
                     * @since  4.3.4
                     * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
                     */
                    private function ___getProductPage( $sURL, $sLanguage ) {

                        add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 4 );
                        add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10, 5 );
                        add_filter( 'aal_filter_http_request_result', array( $this, 'replyToCaptureError' ), 100, 2 );

                        $_oHTTP   = new AmazonAutoLinks_HTTPClient(
                            $sURL,
                            86400,
                            array(
                                'timeout'     => 20,    // 20 seconds as the default is 5 seconds and it often times out
                                'redirection' => 20,
                            ),
                            $this->sUnitType . '_unit_type' // request type
                        );

                        $_sHTTPBody = $_oHTTP->getBody();
                        remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
                        remove_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10 );
                        remove_filter( 'aal_filter_http_request_result', array( $this, 'replyToCaptureError' ), 100 );
                        return $_sHTTPBody;

                    }


        /**
         * @param  array $aProductsByScraping
         * @param  array $aProductsByPAAPI
         * @since  4.2.9
         * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
         * @return array
         */
        private function ___getProductsMerged( array $aProductsByScraping, array $aProductsByPAAPI ) {
            foreach( $aProductsByPAAPI as $_aProduct ) {
                $_sASIN = $this->getElement( $_aProduct, array( 'ASIN' ) );
                if ( ! isset( $aProductsByScraping[ $_sASIN ] ) ) {
                    continue;
                }
                $_aProductByScraping = $aProductsByScraping[ $_sASIN ];
                $_aOverride          = array_intersect_key(
                    $_aProductByScraping,
                    array(
                        'rating'            => null,
                        'number_of_reviews' => null,
                        'formatted_rating'  => null,
                        'is_prime'          => null,
                        'updated_date'      => null,
                    )
                );
                $aProductsByScraping[ $_sASIN ] = $_aOverride + $_aProduct + $_aProductByScraping;
            }
            return $aProductsByScraping;
        }

        /**
         * @param  array  $aASINs  A numerically indexed array of ASINs.
         * @param  string $sLocale
         * @since  4.2.9
         * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
         * @return array
         */
        private function ___getProductsByPAAPI( array $aASINs, $sLocale ) {

            if ( empty( $aASINs ) ) {
                return array();
            }

            // If the API keys are set, perform an API request
            if ( ! $this->oUnitOutput->oOption->isPAAPIKeySet( $sLocale ) ) {
                return array();
            }

            $_oItemLookUpUnit = new AmazonAutoLinks_UnitOutput_item_lookup( array( 'ItemIds' => $aASINs ) + $this->oUnitOutput->oUnitOption->get() );
            return $_oItemLookUpUnit->fetch();

        }

            /**
             * @return string
             * @since  ?
             * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
             */
            private function ___getAssociateIDFromURL( $sURL, $sLocale ) {

                $_bOverrideAssociatesIDOfURL = ( boolean ) $this->oUnitOutput->oOption->get( 'custom_oembed', 'override_associates_id_of_url' );
                if ( ! $_bOverrideAssociatesIDOfURL ) {
                    $_sQuery = parse_url( $sURL, PHP_URL_QUERY );
                    parse_str( $_sQuery, $_aQuery );
                    if ( isset( $_aQuery[ 'tag' ] ) ) {
                        return $_aQuery[ 'tag' ];
                    }
                }
                return $this->oUnitOutput->oOption->getAssociateID( $sLocale );

            }
            
    /**
     * @param  WP_Error|array $aoResponse
     * @param  string $sURL
     * @return array
     * @since  4.2.2
     * @since  4.3.4 Changed the parameters to be singular.
     * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_embed`.
     */
    public function replyToCaptureError( $aoResponse, $sURL ) {
        if ( ! is_wp_error( $aoResponse ) ) {
            return $aoResponse;
        }
        $this->oUnitOutput->aErrors[ $sURL ] = $aoResponse->get_error_message();
        return $aoResponse;
    }

}