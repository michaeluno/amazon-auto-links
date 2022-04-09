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
 * Searches products by the given ASIN and locale.
 *
 * This is a plural version of `AmazonAutoLinks_Event___Action_APIRequestSearchProducts` which queries multiple products at a time.
 *
 * @since 3.7.7
 */
class AmazonAutoLinks_Event___Action_APIRequestSearchProducts extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName     = 'aal_action_api_get_products_info';
    protected $_iCallbackParameters = 5;

    /**
     * Sets up hooks.
     */
    protected function _construct() {
        add_filter( 'aal_filter_disallowed_http_request_types_for_background_cache_renewal', array( $this, 'replyToAddExceptedRequestType' ) );
        add_action( 'aal_action_cache_update_products_with_paapi_response', array( $this, 'replyToUpdateProductsCache' ), 10, 2 );  // 5.2.4
    }
        /**
         * Adds the request type for excepted types.
         *
         * This way, cache renewal events of HTTP requests of the type do not get processed in the background.
         * If the caches are expired, they will be fetched at the time the request is made.
         *
         * @return array
         * @param  array $aExceptedRequestTypes
         */
        public function replyToAddExceptedRequestType( $aExceptedRequestTypes ) {
            $aExceptedRequestTypes[] = 'api';   // the HTTP request type used in PAAPI requests.
            return $aExceptedRequestTypes;
        }


    /**
     * @return boolean
     * @since  4.3.0
     */
    protected function _shouldProceed() {
        $_aParameters = func_get_args();
        if ( $this->hasBeenCalled( get_class( $this ) . '::' . __METHOD__ . '_' . serialize( $_aParameters ) ) ) {
            new AmazonAutoLinks_Error( 'UPDATE_PRODUCTS', 'A same HTTP request is made.', $_aParameters, true );
            return false;
        }
        if ( $this->_isLocked( $_aParameters ) ) {
            return false;
        }
        return true;
    }

    /**
     * Searches passed products and saves their data.
     */
    protected function _doAction( /* $aArguments */ ) {

        $_aParams      = func_get_args() + array( array(), '', '', '', '' );
        $_aList        = $_aParams[ 0 ];
        $_sAssociateID = $this->___getAssociateTag( $_aParams[ 1 ], $_aParams );
        $_sLocale      = $_aParams[ 2 ];
        $_sCurrency    = $_aParams[ 3 ];
        $_sLanguage    = $_aParams[ 4 ];
        $_aASINs       = $this->___getASINs( $_aList ); // $_aList will be updated to have keys of ASIN
        $_aResponse    = $this->___getAPIResponse( $_aASINs, $_sAssociateID, $_sLocale, $_sCurrency, $_sLanguage );

        /**
         * If there are item-specific errors, insert the error in the Items element
         * so the row will be updated and empty values will be inserted
         * then it will avoid triggering this background routine over and over again for not setting values.
         */
        if ( isset( $_aResponse[ 'Errors' ] ) ) {
            $this->___setErroredItems( $_aResponse );
        }
        /**
         * Cases:
         *  - only errors
         *  - errors with found items
         *  - only found items
         */
        if ( ! isset( $_aResponse[ 'ItemsResult' ][ 'Items' ] ) ) {
            $_sMessage = isset( $_aResponse[ 'Error' ][ 'Message' ] )
                ? $_aResponse[ 'Error' ][ 'Message' ]
                : 'Response does not contain items';
            new AmazonAutoLinks_Error( 'UPDATE_PRODUCTS_FAILURE', $_sMessage, array( 'associate_id' => $_sAssociateID, 'asins'=> $_aASINs, 'locale' => $_sLocale, 'currency' => $_sCurrency, 'language' => $_sLanguage, 'response' => $_aResponse ), '' );
            return;
        }

        do_action( 'aal_action_cache_update_products_with_paapi_response', $this->getElementAsArray( $_aResponse, array( 'ItemsResult', 'Items' ) ), $_aList );

    }
        /**
         * Applies a fallback when an Associate tag is not passed.
         * @remark Although this should not occur, while developing there was a case that an Associate tag was npt passed and it was hard to debug. So trigger an error log and apply a default one.
         * @param  string $sPassedAssociateTag
         * @param  array  $aParams
         * @return string
         * @since  4.4.0
         */
        private function ___getAssociateTag( $sPassedAssociateTag, array $aParams ) {
            if ( $sPassedAssociateTag ) {
                return $sPassedAssociateTag;
            }
            $_sLocale       = $aParams[ 2 ];
            $_oOption       = AmazonAutoLinks_Option::getInstance();
            $_sAssociateTag = $_oOption->getAssociateID( $_sLocale );
            new AmazonAutoLinks_Error( 'EVENT_SEARCH_PRODUCTS', "The associate tag is not given. The fallback, {$_sAssociateTag}, is applied.", $aParams );
            return $_sAssociateTag;
        }

        /**
         * @param $aResponse
         */
        private function ___setErroredItems( &$aResponse ) {
            $_aErroredItem = array();
            foreach( $this->getAsArray( $aResponse[ 'Errors' ] ) as $_aError ) {
                $_sMessage = $this->getElement( $_aError, array( 'Message' ), '' );
                $_sASINs   = $this->getASINsExtracted( $_sMessage, ',' );
                $_aASINs   = explode( ',', $_sASINs );
                foreach( $_aASINs as $_sASIN ) {
                    $_aErroredItem[ $_sASIN ] = array(
                        'ASIN'  => $_sASIN,
                        'Error' => $_aError,
                    );
                }
            }
            if ( ! count( $_aErroredItem ) ) {
                return;
            }
            if ( ! isset( $aResponse[ 'ItemsResult' ] ) ) {
                $aResponse[ 'ItemsResult' ] = array();
            }
            if ( ! isset( $aResponse[ 'ItemsResult' ][ 'Items' ] ) ) {
                $aResponse[ 'ItemsResult' ][ 'Items' ] = array();
            }
            foreach( $_aErroredItem as $_aError ) {
                $aResponse[ 'ItemsResult' ][ 'Items' ][] = $_aError;
            }
        }
        /**
         * Constructs the ASIN parameter.
         * @remark It is assumed that the passed list contains only up to 10 products
         * as the `ItemLookup` operation API parameter only accepts up to 10 items.
         * @param  array &$aList
         * @return array An array holding ASINs.
         * @see    AmazonAutoLinks_Event_Scheduler::scheduleProductInformation()
         */
        private function ___getASINs( &$aList ) {
            // Extract the ASINs
            $_aASINs   = array();
            $_aNewList = array();
            foreach( $aList as $_iIndex => $_aArguments ) {

                // For the argument structure, see AmazonAutoLinks_Event_Scheduler::scheduleProductInformation()
                // Extract ASIN and the loaded scheduled action may be of an old format.
                if ( ! isset( $_aArguments[ 1 ] ) && is_string( $_aArguments[ 1 ] ) ) {
                    continue;
                }
                $_sASIN = $_aArguments[ 1 ];
                $_aNewList[ $_sASIN ] = $_aArguments;
                $_aASINs[] = $_sASIN;

                // This should not happen but third-parties may use this action.
                if ( $_iIndex > 10 ) {
                    break;
                }
            }
            $aList = $_aNewList;    // update the list
            return $_aASINs;
        }

        /**
         * @param  array  $aASINs
         * @param  string $sAssociateID
         * @param  string $sLocale
         * @param  string $sCurrency
         * @param  string $sLanguage
         * @return array
         */
        private function ___getAPIResponse( array $aASINs, $sAssociateID, $sLocale, $sCurrency, $sLanguage ) {

            $_oOption     = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->getPAAPIStatus( $sLocale ) ) {
                return array();
            }
            $_sPublicKey  = $_oOption->getPAAPIAccessKey( $sLocale );
            $_sPrivateKey = $_oOption->getPAAPISecretKey( $sLocale );
            if ( empty( $_sPublicKey ) || empty( $_sPrivateKey ) ) {
                return array();
            }

            /**
             * Perform a PA-API request.
             * Here the item_lookup unit is used so that the payload should resemble the ones loaded in front-end. This prevents duplicated requests.
             * Search unit types which includes PA-API requests do not allow default options to be set by the user so it's safe to use the unit payload rather than manually writing one here.
             */
            $_iCacheDuration = $_oOption->get( 'unit_default', 'cache_duration' );
            $_iCacheDuration = ( integer ) round( $_iCacheDuration * 0.96 );  // a big shorter than the unit cache duration as this item look-up cache should expire by the next called time.
            $_oUnit          = new AmazonAutoLinks_UnitOutput_item_lookup( array(
                'ItemIds'              => $aASINs,
                'preferred_currency'   => $sCurrency,
                'language'             => $sLanguage,
                'country'              => $sLocale,
                'associate_id'         => $sAssociateID,
                'cache_duration'       => $_iCacheDuration,
                '_force_cache_renewal' => false,
            ) );
            return $_oUnit->getAPIResponse( PHP_INT_MAX );

        }

    /**
     * @callback add_action() aal_action_cache_update_products_with_paapi_response
     * @param    array $aResponseItems The products-data array extracted from the direct response array.
     * @param    array $aListQueried   A list of queried items.
     * @since    5.2.4
     */
    public function replyToUpdateProductsCache( $aResponseItems, $aListQueried ) {

        if ( empty( $aResponseItems ) ) {
            return;
        }

        $_aRows = array();
        foreach( $aResponseItems as $_aItem ) {

            $_sASIN = $this->getElement( $_aItem, array( 'ASIN' ), '' );
            if ( ! $_sASIN || ! isset( $aListQueried[ $_sASIN ] ) ) {
                continue;
            }

            /**
             * @see AmazonAutoLinks_Event_Scheduler::scheduleProductInformation()
             * @var array $_aParameters
             * Structure: array( 
             *  0 => $sAssociateID|Locale|Cur|Lang, 
             *  1 => $sASIN, 
             *  2 => $iCacheDuration, 
             *  3 => $bForceRenew, 
             *  4 => $sItemFormat 
             * ),
             */
            $_aParameters     = $aListQueried[ $_sASIN ];
            $_aQueriedItem    = explode( '|', $_aParameters[ 0 ] );
            $_sLocale         = $this->getElement( $_aQueriedItem, array( 1 ), '' );
            $_sCurrency       = $this->getElement( $_aQueriedItem, array( 2 ), '' );
            $_sLanguage       = $this->getElement( $_aQueriedItem, array( 3 ), '' );
            $_iCacheDuration  = ( integer ) $_aParameters[ 2 ];
            $_bForceRenewal   = ( boolean ) $_aParameters[ 3 ];
            $_sItemFormat     = $_aParameters[ 4 ];

            $_sKey            = "{$_sASIN}|{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
            $_aRows[ $_sKey ] = $this->___getRowFormatted( $_aItem, $_sASIN, $_sLocale, $_iCacheDuration, $_sCurrency, $_sLanguage );

            $this->___handleCustomerReview( $_sASIN, $_sLocale, $_iCacheDuration, $_bForceRenewal, $_sCurrency, $_sLanguage, $_sItemFormat );

        }
        $this->setProductDatabaseRows( $_aRows );

    }

        /**
         * @param  string  $sASIN
         * @param  string  $sLocale
         * @param  integer $iCacheDuration
         * @param  boolean $bForceRenewal
         * @param  string  $sCurrency
         * @param  string  $sLanguage
         * @param  string  $sItemFormat
         * @since  4.3.0
         */
        private function ___handleCustomerReview( $sASIN, $sLocale, $iCacheDuration, $bForceRenewal, $sCurrency, $sLanguage, $sItemFormat ) {

            // Reviews
            if ( AmazonAutoLinks_UnitOutput_Utility::hasItemFormatTagsIn( $sItemFormat, array( '%review%' ) ) ) {
                AmazonAutoLinks_Event_Scheduler::scheduleReview( "{$sASIN}|{$sLocale}|{$sCurrency}|{$sLanguage}", $iCacheDuration, $bForceRenewal );
            }

            // Ratings
            if ( ! AmazonAutoLinks_UnitOutput_Utility::hasItemFormatTagsIn( $sItemFormat, array( '%rating%', '%_review_rate%' ) ) ) { // 4.3.4 removed '%_discount_rate%' as it should be nothing to do with ratings.
                return;
            }
            AmazonAutoLinks_Event_Scheduler::scheduleRating( "{$sASIN}|{$sLocale}|{$sCurrency}|{$sLanguage}", $iCacheDuration, $bForceRenewal );

        }

        /**
         * @param  array   $aItem
         * @param  string  $sASIN
         * @param  string  $sLocale
         * @param  integer $iCacheDuration
         * @param  string  $sCurrency
         * @param  string  $sLanguage
         * @return array
         * @since  3.9.0
         */
        private function ___getRowFormatted( array $aItem, $sASIN, $sLocale, $iCacheDuration, $sCurrency, $sLanguage ) {

            $_aPrices = AmazonAutoLinks_Unit_Utility::getPrices( $aItem );
            $_aRow    = array(
                'asin_locale'        => $sASIN . '_' . $sLocale,
                'locale'             => $sLocale,
                'modified_time'      => date( 'Y-m-d H:i:s' ),
                'links'              => $this->getElement( $aItem, array( 'DetailPageURL' ), '' ),
                'sales_rank'         => ( integer ) $this->getElement(
                    $aItem,
                    array( 'BrowseNodeInfo', 'WebsiteSalesRank', 'SalesRank' ), 0
                ),
                'title'              => $this->getElement(
                    $aItem,
                    array( 'ItemInfo', 'Title', 'DisplayValue' ),
                    ''
                ),
                'images'             => AmazonAutoLinks_Unit_Utility::getImageSet( $aItem ),    // 3.8.11
                'editorial_reviews'  => AmazonAutoLinks_Unit_Utility::getContent( $aItem ), // 3.9.0 Editorial reviews are no longer available in PA-API 5 so use features.
                'currency'           => $_aPrices[ 'currency' ],
                'price'              => $_aPrices[ 'price_amount' ],
                'price_formatted'    => $_aPrices[ 'price_formatted' ],
                'lowest_new_price'   => $_aPrices[ 'lowest_new_price_amount' ],
                'lowest_new_price_formatted'  => $_aPrices[ 'lowest_new_price_formatted' ],
                'lowest_used_price'           => $_aPrices[ 'lowest_used_price_amount' ],
                'lowest_used_price_formatted' => $_aPrices[ 'lowest_used_price_formatted' ],
                'count_new'          => -1, // 3.9.0 not available in PA-API5
                'count_used'         => -1, // 3.9.0 not available in PA-API5
                'discounted_price'   => $_aPrices[ 'discounted_price_amount' ],
                'discounted_price_formatted'   => $_aPrices[ 'discounted_price_formatted' ],

                // Similar products will be set with a separate routine. <--- @deprecated
                // Once an empty value is set, it will no longer trigger the value retrieval background routine
                // 'similar_products'   => '',
                // 'description'        => null,   // (string) product details
            );

            // if `0` is passed for the cache duration, it just renews the cache and do not update the expiration time.
            if ( $iCacheDuration ) {
                $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
            }

            // 4.3.2 There are cases that rating information is included.
            $_inReviewCount = AmazonAutoLinks_Unit_Utility::getReviewCountFromItem( $aItem );
            $_inRating      = AmazonAutoLinks_Unit_Utility::getRatingFromItem( $aItem );
            if ( isset( $_inReviewCount, $_inRating ) ) {
                $_aRow[ 'rating' ]                  = $_inRating;
                $_aRow[ 'rating_image_url' ]        = AmazonAutoLinks_Unit_Utility::getRatingStarImageURL( $_inRating );
                $_aRow[ 'rating_html' ]             = ''; // deprecated 3.9.0
                $_aRow[ 'number_of_reviews' ]       = $_inReviewCount;
            }

            // With PA-API5, we don't know whether a customer review exists or not.
            // Regardless, fill these elements with an empty value.
            // If the element value is null when retrieved from the front-end,
            // it schedules a background task. So in order to avoid it, set a value.
            // @deprecated  3.9.0   Now when %review% or %rating% is not set, it won't trigger the background routine.
            // If these are set, even the user places those variables later on,
            // the background routine won't be triggered and the values will not be shown.
            // $_aRow[ 'rating' ]                  = 0;
            // $_aRow[ 'rating_image_url' ]        = '';
            // $_aRow[ 'rating_html' ]             = '';
            // $_aRow[ 'number_of_reviews' ]       = 0;
            // $_aRow[ 'customer_review_url' ]     = '';
            // $_aRow[ 'customer_review_charset' ] = '';
            // $_aRow[ 'customer_reviews' ]        = '';

            // 3.8.0+ If the table version is 1.1.0b01 or above,
            $_sCurrentVersion = get_option( "aal_products_version", '0' );
            if ( version_compare( $_sCurrentVersion, '1.1.0b01', '>=') ) {
                $_aFeatures = $this->getElementAsArray( $aItem, array( 'ItemInfo', 'Features', 'DisplayValues' ) );
                $_aRow[ 'features' ]   = AmazonAutoLinks_Unit_Utility::getFeatures( $_aFeatures );
                $_aNodes = $this->getElementAsArray( $aItem, array( 'BrowseNodeInfo', 'BrowseNodes', ) );
                $_aRow[ 'categories' ] = AmazonAutoLinks_Unit_Utility::getCategories( $_aNodes );
            }

            // 3.9.0+ If the table version is 1.2.0b01 or above,
            if ( version_compare( $_sCurrentVersion, '1.2.0b01', '>=') ) {
                $_aRow[ 'is_prime' ] = AmazonAutoLinks_Unit_Utility::isPrime( $aItem );
                $_aRow[ 'is_adult' ] = ( boolean ) $this->getElement(
                    $aItem,
                    array( 'ItemInfo', 'ProductInfo', 'IsAdultProduct', 'DisplayValue' ),
                    false
                );
                $_aRow[ 'language' ] = $sLanguage;
                $_aRow[ 'preferred_currency' ] = $sCurrency;
                $_sError = $this->getElement( $aItem, array( 'Error', 'Message', ), '' );
                $_aRow[ 'error' ] = $_sError
                    ? $this->getElement( $aItem, array( 'Error', 'Code', ), '' ) . ': ' . $_sError
                    : '';
            }

            // 3.10.0+
            if ( version_compare( $_sCurrentVersion, '1.3.0b01', '>=') ) {
                $_aRow[ 'delivery_free_shipping' ] = AmazonAutoLinks_Unit_Utility::isDeliveryEligible( $aItem, array( 'DeliveryInfo', 'IsFreeShippingEligible' )  );
                $_aRow[ 'delivery_fba' ] = AmazonAutoLinks_Unit_Utility::isDeliveryEligible( $aItem, array( 'DeliveryInfo', 'IsAmazonFulfilled' )  );
            }

            // 4.3.0
            if ( version_compare( $_sCurrentVersion, '1.4.0b01', '>=' ) ) {
                $_aRow[ 'asin' ]       = $sASIN;
                $_aRow[ 'product_id' ] = "{$sASIN}|{$sLocale}|{$sCurrency}|{$sLanguage}";
            }

            return $_aRow;

        }

}