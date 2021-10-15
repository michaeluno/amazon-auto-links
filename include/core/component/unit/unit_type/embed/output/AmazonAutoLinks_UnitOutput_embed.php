<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Generates Amazon product links for oEmbed outputs.
 *
 * @since       4.0.0
 */
class AmazonAutoLinks_UnitOutput_embed extends AmazonAutoLinks_UnitOutput_category {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'embed';

    /**
     * Lists the tags (variables) used in the Item Format unit option that require to access the custom database.
     * @var array
     */
    protected $_aItemFormatDatabaseVariables = array(
        '%review%', '%similar%', '%category%', '%rank%', '%prime%',
        '%_discount_rate%', '%_review_rate%', // 3.9.2  - used for advanced filters
        '%discount%'                          // 4.7.8
    );

    /**
     * Represents the structure of each product array.
     * @var array
     */
    public static $aStructure_Product = array();

    /**
     * Indicates whether the given URLs contain a non-product URL.
     * @var   boolean
     * @since 4.4.0
     */
    private $___bNonProductURL = false;

    /**
     * Fetches product data and returns the associative array containing the output of product links.
     *
     * @param  array $aURLs
     * @return array An array contains products.
     * @since  ?
     * @since  5.0.0 Removed the first parameter of `$aURLs`.
     */
    public function fetch() {

        // Originally the glue was \s+ but not sure what this was for.
        // If it is split by a white space, search result URL cannot be parsed properly such as https://www.amazon.it/s?k=harry potter&...
        $_aURLs                  = preg_split( "/[\r\n]+/", trim( ( string ) $this->oUnitOption->get( 'uri' ) ), 0, PREG_SPLIT_NO_EMPTY );

        $_sLanguage              = $this->oUnitOption->get( 'language' );
        $_aASINsPerURL           = $this->___getASINsPerURL( $_aURLs, $_sLanguage, $_aASINsPerNonProductURL );
        $_iCount                 = ( integer ) $this->oUnitOption->get( 'count' );
        $_aProducts              = $this->___getProductsWithASINs( $_aASINsPerURL, $_iCount, $_sAssociateID, $_sLocale, $_sLanguage );
        $_aProducts              = $_aProducts + $this->___getProductsWithASINs( $_aASINsPerNonProductURL, $_iCount, $_sAssociateID, $_sLocale, $_sLanguage );
        $_aASINsOfNonProductURLs = $this->___getASINsOfNonProductURL( $_aASINsPerNonProductURL );

        $this->___bNonProductURL = ! empty( $_aASINsPerNonProductURL ); // referred when displaying errors

        // Set the Associate ID and locale. These could be detected from the URL. If not detected, give an empty value so that a warning will be displayed.
        $this->oUnitOption->set( 'associate_id', $_sAssociateID ); // some elements are formatted based on this value
        if ( $_sLocale ) {  // for non-amazon sites, locale cannot be detected and empty
            $this->oUnitOption->set( 'country', $_sLocale ); // when no image is found, the alternative thumbnail is based on the default locale
        }
        $_sLocale = $this->oUnitOption->get( 'country' );   // re-retrieve the value as `$_sLocale` can be empty

        $_aProductsByPAAPI = $this->___getProductsByPAAPI( array_keys( $_aProducts ), $_sLocale );
        $_aProductsByURLs  = $this->___getProductsByPAAPI( $_aASINsOfNonProductURLs, $_sLocale );
        $_aProducts        = $this->___getProductsMerged( $_aProducts, $_aProductsByPAAPI );
        $_aProducts        = array_merge( $_aProducts, $_aProductsByURLs );
        return $this->_getProducts( $_aProducts, $_sLocale, $_sAssociateID, $_iCount );
    }
        /**
         * @remark The user might past an Amazon site url but not of the product page.
         * @param  array   $aASINsPerNonProductURL
         * @return array
         * @since  4.4.0
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
                $aASINsPerNonProductURL[ $_sURL ] = $this->getASINsFromHTMLs( array( $this->___getProductPage( $_sURL, $sLanguage ) ) );
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
         */
        private function ___getProductsWithASINs( array $aASINsPerURL, $iNumberOfItems, &$sAssociateID, &$sLocale, $sLanguage ) {
            $_aAdWidgetLocales  = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport();
            $_aProducts         = array();
            $_sLocale           = '';
            $_sAssociateID      = '';
            foreach( $aASINsPerURL as $_sURL => $_aASINs ) {
                $_sThisLocale  = AmazonAutoLinks_Locales::getLocaleFromURL( $_sURL, ( string ) $this->oUnitOption->get( array( 'country' ), 'US' ) );
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
             */
            private function ___getProductsByAdWidgetAPI( array $aASINs, $sLocale, $sAssociateID, $sLanguage ) {

                // Capture the modified date of the response
                add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 1 );
                add_filter( 'aal_filter_http_request_response', array( $this, 'replyToCaptureUpdatedDateForNewRequest' ), 10, 2 );

                $_aProducts          = array();
                $_oAdWidgetAPISearch = new AmazonAutoLinks_AdWidgetAPI_Search( $sLocale, ( integer ) $this->oUnitOption->get( 'cache_duration' ) );
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
                        'updated_date'      => $this->getElement( $this->aModifiedDates, $_sAPIEndpoint ),
                    ) + $_aItem;
                    $_aProduct[ 'formatted_rating' ] = $this->___getFormattedRating( $_aProduct[ 'rating' ], $_aProduct[ 'number_of_reviews' ], $sLocale, $_aItem[ 'ASIN' ], $sAssociateID, $sLanguage );
                    $_aProducts[ $_aItem[ 'ASIN' ] ] = $_aProduct;
                }
                return $_aProducts;
            }
                /**
                 * @return string
                 * @since  4.6.9
                 */
                private function ___getFormattedRating( $iRating, $iReviewCount, $sLocale, $sASIN, $sAssociateID, $sLanguage ) {
                    $_oLocale       = new AmazonAutoLinks_Locale( $sLocale );
                    $_sReviewURL    = $_oLocale->getCustomerReviewURL( $sASIN, $sAssociateID, $sLanguage );
                    return "<div class='amazon-customer-rating-stars'>"
                            . AmazonAutoLinks_Unit_Utility::getRatingOutput( $iRating, $_sReviewURL, $iReviewCount )
                        . "</div>";
                }

            /**
             * @param  array  $aASINs
             * @param  string $sLocale
             * @param  string $sAssociateID
             * @param  string $sLanguage
             * @return array
             * @since  4.6.9
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
                 * @param  string $sASIN
                 * @param  string $sLocale
                 * @param  string $sAssociateID
                 * @param  string $sLanguage
                 * @return array
                 * @since  4.4.0
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

                    $_aProduct[ 'updated_date' ] = $this->getElement( $this->aModifiedDates, $_sProductURL );
                    $_aProduct[ 'content' ]      = ! empty( $_aProduct[ 'content' ] )
                        ? "<div class='amazon-product-content'>"
                            . $_aProduct[ 'content' ]
                        . "</div>"
                        : '';
                    $_sDescriptionExtracted      = $this->_getDescriptionSanitized(
                        isset( $_aProduct[ 'description' ] ) ? $_aProduct[ 'description' ] : ( $_aProduct[ 'content' ] ? $_aProduct[ 'content' ] : implode( ' ', $_aProduct[ '_features' ] ) ),
                        $this->oUnitOption->get( 'description_length' ),
                        $this->_getReadMoreText( $_aProduct[ 'product_url' ] )
                    );
                    $_aProduct[ 'description' ]  = $_sDescriptionExtracted
                        ? "<div class='amazon-product-description'>"
                            . $_sDescriptionExtracted
                        . "</div>"
                        : '';

                    $_aProduct[ 'image_set' ]           = $this->getSubImageOutput(
                        $_aProduct[ '_sub_image_urls' ],
                        $_aProduct[ 'title' ],
                        $_aProduct[ 'product_url' ],
                        ( boolean ) $this->oUnitOption->get( 'pop_up_image' )
                    );
                    if ( $_aProduct[ 'thumbnail_url' ] && ! $_aProduct[ 'image_set' ] ) {
                        $_aProduct[ 'image_set' ] = "<div class='sub-images'></div>"; // change the value from null to an empty tag so that further data inspection will not continue
                    }

                    unset( $_aProduct[ '_features' ] );
                    unset( $_aProduct[ '_sub_image_urls' ] );
                    return $_aProduct;

                }
                    /**
                     * @param string $sASIN
                     * @param string $sAssociateID
                     * @param string $sLocale
                     * @param string $sLanguage
                     * @param string $sURLDomain
                     * @return string
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
         * @return array
         */
        private function ___getProductsByPAAPI( array $aASINs, $sLocale ) {

            if ( empty( $aASINs ) ) {
                return array();
            }

            // If the API keys are set, perform an API request
            if ( ! $this->oOption->isPAAPIKeySet( $sLocale ) ) {
                return array();
            }

            $_oItemLookUpUnit = new AmazonAutoLinks_UnitOutput_item_lookup( array( 'ItemIds' => $aASINs ) + $this->oUnitOption->get() );
            return $_oItemLookUpUnit->fetch();

        }

            /**
             * @param  string $sURL
             * @param  string $sLocale
             * @return string
             */
            private function ___getAssociateIDFromURL( $sURL, $sLocale ) {

                $_bOverrideAssociatesIDOfURL = ( boolean ) $this->oOption->get( 'custom_oembed', 'override_associates_id_of_url' );
                if ( ! $_bOverrideAssociatesIDOfURL ) {
                    $_sQuery = parse_url( $sURL, PHP_URL_QUERY );
                    parse_str( $_sQuery, $_aQuery );
                    if ( isset( $_aQuery[ 'tag' ] ) ) {
                        return $_aQuery[ 'tag' ];
                    }
                }
                return $this->oOption->getAssociateID( $sLocale );

            }

    /**
     * Overrides parent method to return errors specific to this embed unit type.
     * @param  array  $aProducts
     * @return string
     * @since  4.2.2
     */
    protected function _getError( $aProducts ) {

        if ( $this->___bNonProductURL && ! empty( $this->___aErrors ) ) {
            $_sErrors = implode( ' ', $this->___aErrors );
            $this->___aErrors = array();
            return $_sErrors . ' ' . $this->getEnableHTTPProxyOptionMessage();
        }

        /**
         * For users not using PA-API keys, errors should be displayed.
         * Otherwise, even failing to access to the store page, API requests can be preformed with ASIN.
         */
        $_sLocale       = $this->oUnitOption->get( 'country' );
        if ( ! $this->oOption->isPAAPIKeySet( $_sLocale ) ) {
            if ( ! empty( $this->___aErrors ) ) {
                $this->___aErrors = array_map( array( $this, '___replyToSetASINToErrorMessage' ), $this->___aErrors, array_keys( $this->___aErrors ), array_fill(0, count( $this->___aErrors ), $_sLocale ) );
                $_sErrors = implode( ' ', $this->___aErrors );
                $this->___aErrors = array();
                return $_sErrors  . ' ' . $this->getEnableHTTPProxyOptionMessage();
            }
            $aProducts = $this->___getProductsFilteredForWithoutPAAPI( $aProducts );
        }

        $_sErrorMessage = parent::_getError( $aProducts );
        return $_sErrorMessage
            ? $_sErrorMessage . ' ' . $this->getEnableHTTPProxyOptionMessage()
            : $_sErrorMessage;

    }
        /**
         * @param    string $sErrorMessage
         * @param    string $sURL
         * @param    string $sLocale
         * @return   string
         * @callback array_map()
         * @since    4.4.6
         */
        private function ___replyToSetASINToErrorMessage( $sErrorMessage, $sURL, $sLocale ) {
            $_aASINs = $this->getASINs( $sURL );
            if ( empty( $_aASINs ) ) {
                return $sErrorMessage;
            }
            $_sASINs  = implode( ', ', $_aASINs );
            return $sErrorMessage . " ({$sLocale}: {$_sASINs})";
        }
        /**
         * Filters out unfinished product data.
         *
         * For some reasons, like cloudflare cache errors, the Amazon product page responds with the 200 status but shows an error.
         * In that case, products data become unfinished. So remove those.
         * @param  array $aProducts
         * @return array
         * @since  4.4.5
         */
        private function ___getProductsFilteredForWithoutPAAPI( $aProducts ) {
            foreach( $aProducts as $_iIndex => $_aProduct ) {
                if ( ! $this->getElement( $_aProduct, 'thumbnail_url' ) && ! isset( $aProducts[ 'title' ] ) ) {
                    unset( $aProducts[ $_iIndex ] );
                }
            }
            return $aProducts;
        }

    /**
     * Stores captured HTTP errors.
     * @var array
     */
    private $___aErrors = array();

    /**
     * @param  WP_Error|array $aoResponse
     * @param  string $sURL
     * @return array
     * @since  4.2.2
     * @since  4.3.4 Changed the parameters to be singular.
     */
    public function replyToCaptureError( $aoResponse, $sURL ) {
        if ( ! is_wp_error( $aoResponse ) ) {
            return $aoResponse;
        }
        $this->___aErrors[ $sURL ] = $aoResponse->get_error_message();
        return $aoResponse;
    }

    /**
     * Called when the unit has access to the plugin custom database table.
     *
     * Sets the 'content' and 'description' elements in the product (item) array which require plugin custom database table.
     *
     * @since    4.2.10
     * @return   array
     * @callback add_filter      aal_filter_unit_each_product_with_database_row
     * @param    array $aProduct
     * @param    array $aDBRow
     * @param    array $aScheduleIdentifier
     */
    public function replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier=array() ) {

        remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFormatProductWithDBRow' ), 10 );

        if ( empty( $aProduct ) ) {
            return array();
        }


        $aProduct[ 'content' ]          = empty( $aProduct[ 'content' ] )
            ? AmazonAutoLinks_Unit_Utility::getContent( $aProduct )
            : $aProduct[ 'content' ];
        if ( empty( $aProduct[ 'description' ] ) ) {
            $_sDescriptionExtracted         = $this->_getDescriptionSanitized(
                $aProduct[ 'content' ],
                $this->oUnitOption->get( 'description_length' ),
                $this->_getReadMoreText( $aProduct[ 'product_url' ] )
            );
            $aProduct[ 'description' ]      = $_sDescriptionExtracted
                ? "<div class='amazon-product-description'>"
                        . $_sDescriptionExtracted
                    . "</div>"
                : '';
        }

        $aProduct[ 'text_description' ] = strip_tags( $aProduct[ 'description' ] );
        if ( $this->isDescriptionBlocked( $aProduct[ 'text_description' ] ) ) {
            $this->aBlockedASINs[ $aProduct[ 'ASIN' ] ] = $aProduct[ 'ASIN' ];
            return array(); // will be dropped
        }
        return $aProduct;

    }

}