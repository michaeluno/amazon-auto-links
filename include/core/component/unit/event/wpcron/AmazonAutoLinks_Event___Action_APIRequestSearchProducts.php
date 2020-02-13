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
 * Searches products by the given ASIN and locale.
 *
 * This is a plural version of `AmazonAutoLinks_Event___Action_APIRequestSearchProducts` which queries multiple products at a time.
 *
 * @since       3.7.7
 */
class AmazonAutoLinks_Event___Action_APIRequestSearchProducts extends AmazonAutoLinks_Event___Action_APIRequestSearchProduct {

    protected $_sActionHookName     = 'aal_action_api_get_products_info';
    protected $_iCallbackParameters = 5;

    /**
     * Searches passed products and saves their data.
     */
    protected function _doAction( /* $aArguments */ ) {

        $_aParams        = func_get_args() + array( array(), '', '' );
        if ( $this->_isLocked( $_aParams ) ) {
            return;
        }

        $_aList          = $_aParams[ 0 ];
        $_sAssociateID   = $_aParams[ 1 ];
        $_sLocale        = $_aParams[ 2 ];
        $_sCurrency      = $_aParams[ 3 ];
        $_sLanguage      = $_aParams[ 4 ];
        $_sASINs         = $this->___getASINs( $_aList ); // $_aList will be updated to have keys of ASIN
        $_aResponse      = $this->___getAPIResponse( $_sASINs, $_sAssociateID, $_sLocale, $_sCurrency, $_sLanguage );

        /**
         * if there are item-specific errors, insert the error in the Items element
         * so the row will be updated and empty values will be inserted
         * then it will avoid triggering this background routine over and over again for not setting values.
         */
        if ( isset( $_aResponse[ 'Errors' ] ) ) {
            $this->___setErroredItems( $_aResponse );
        }
        /**
         * There are cases of
         * - only errors
         * - errors with found items
         * - only found items
         */
        if ( isset( $_aResponse[ 'ItemsResult' ][ 'Items' ] ) ) {
            $this->___setItemsIntoDatabase( $_aResponse, $_aList, $_sLocale, $_sCurrency, $_sLanguage );
            return;
        }

        // @todo Create a temporary file that indicates that an API request got an error and further API requests should hold back.
    }
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
         * @remark  It is assumed that the passed list contains only up to 10 products
         * as the `ItemLookup` operation API parameter only accepts up to 10 items.
         * @param array &$aList
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
            return implode( ',', $_aASINs );
        }

        private function ___getAPIResponse( $sASINs, $sAssociateID, $sLocale, $sCurrency, $sLanguage ) {

            $_oOption     = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->isAPIConnected() ) {
                return array();
            }
            $_sPublicKey  = $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
            if ( empty( $_sPublicKey ) || empty( $_sPrivateKey ) ) {
                return array();
            }

            /**
             * @remark  A short cache duration is set here because this request is versatile.
             * And the important information (product data) is stored in the products table with the expiration time which is checked when the item is drawn.
             * -> [3.9.0+] Changed it to 1 day to save API calls.
             */
            $_oAPI      = new AmazonAutoLinks_PAAPI50( $sLocale, $_sPublicKey, $_sPrivateKey, $sAssociateID );
            $_aPayload  = array(
                'ItemIds'               => explode( ',', $sASINs ),
                'Operation'             => 'GetItems',
                'CurrencyOfPreference'  => $sCurrency,
                'LanguagesOfPreference' => array( $sLanguage ),
                'Resources'             => AmazonAutoLinks_PAAPI50___Payload::$aResources,
            );
            return $_oAPI->request( $_aPayload, 60 * 60 * 24, false );

        }

        /**
         * @param $aItems
         * @param $aList
         * @since   3.7.7
         */
        private function ___setItemsIntoDatabase( array $aResponse, array $aList, $sLocale, $sCurrency, $sLanguage ) {

            $_aItems = $this->getElementAsArray( $aResponse, array( 'ItemsResult', 'Items' ) );
            foreach ( $_aItems as $_iIndex => $_aItem ) {
                $this->___setItemIntoDatabase( $_aItem, $aList, $sLocale, $sCurrency, $sLanguage );
            }

        }
            private function ___setItemIntoDatabase( array $aItem, array $aList, $sLocale, $sCurrency, $sLanguage ) {

                $_sASIN = $this->getElement( $aItem, array( 'ASIN' ), '' );

                if ( ! $_sASIN ) {
                    return;
                }
                if ( ! isset( $aList[ $_sASIN ] ) ) {
                    return;
                }

                // Structure: array( 0 => $sAssociateID|Locale|Cur|Lang, 1 => $sASIN,  2 => $sLocale, 3 => $iCacheDuration, 4 => $bForceRenew, 5 => $sItemFormat ),
                $_aParameters = $aList[ $_sASIN ];

                // Retrieve similar products in a separate routine
                // Do this only `%similar%` is present in the Item Format option.
                // @deprecated 3.9.0
//                if ( false !== strpos( $_aParameters[ 5 ], '%similar%' ) ) {
//                    $this->_scheduleFetchingSimilarProducts(
//                        $aItem, // product data in `Items` -> `Item` in returned from API.
//                        $_sASIN,
//                        $_aParameters[ 2 ], // locale
//                        $_aParameters[ 0 ], // associate id
//                        $_aParameters[ 3 ], // cache duration
//                        $_aParameters[ 4 ]  // whether to force cache renewal
//                    );
//                }

                $this->___setProductData(
                    $aItem, // product data in `Items` -> `Item` in returned from API.
                    $_sASIN,
                    $sLocale,   // locale
                    $_aParameters[ 2 ], // cache duration
                    $_aParameters[ 3 ], // whether to force cache renewal
                    $_aParameters[ 4 ],  // item format
                    $sCurrency,
                    $sLanguage
                );

            }
                private function ___setProductData( array $aItem, $sASIN, $sLocale, $iCacheDuration, $bForceRenewal, $sItemFormat, $sCurrency, $sLanguage ) {

                    // Customer reviews
                    $_oLocale       = new AmazonAutoLinks_PAAPI50___Locales;
                    $_sMarketPlace  = isset( $_oLocale->aMarketPlaces[ $sLocale ] ) ? $_oLocale->aMarketPlaces[ $sLocale ] : $_oLocale->aMarketPlaces[ 'US' ];
                    $_sReviewURL    = 'https://' . $_sMarketPlace . '/product-reviews/' . $sASIN;

                    if (
                        AmazonAutoLinks_UnitOutput_Utility::hasCustomVariable( $sItemFormat, array( '%review%', '%rating%', '%_discount_rate%', '%_review_rate%' ) )
                    ) {
                        AmazonAutoLinks_Event_Scheduler::scheduleCustomerReviews2(
                            $_sReviewURL,
                            $sASIN, $sLocale, $iCacheDuration, $bForceRenewal, $sCurrency, $sLanguage );
                    }

                    $_aRow          = $this->___getRowFormatted( $aItem, $sASIN, $sLocale, $iCacheDuration, $sCurrency, $sLanguage );

                    $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
                    $_oProductTable->setRowByASINLocale(
                        $sASIN . '_' . strtoupper( $sLocale ),  // asin _ locale
                        $_aRow, // row data to set
                        $sCurrency,
                        $sLanguage
                    );

                }
                    /**
                     * @param array $aItem
                     * @param $sASIN
                     * @param $sLocale
                     * @param $iCacheDuration
                     * @param $sCurrency
                     * @param $sLanguage
                     *
                     * @return array
                     * @since   3.9.0
                     * @todo    Test
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

                        // With PA-API5, we don't know whether a customer review exists or not.
                        // Regardless, fill these elements with an empty value.
                        // If the element value is null when retrieved from the front-end,
                        // it schedules a background task. So in order to avoid it, set a value.
                        // @deprecated  3.9.0   Now when %review% or %rating% is not set, it won't trigger the background routine.
                        // If these are set, even the user places those variables later on,
                        // the background routine won't be triggered and the values will not be shown.
//                        $_aRow[ 'rating' ]                  = 0;
//                        $_aRow[ 'rating_image_url' ]        = '';
//                        $_aRow[ 'rating_html' ]             = '';
//                        $_aRow[ 'number_of_reviews' ]       = 0;
//                        $_aRow[ 'customer_review_url' ]     = '';
//                        $_aRow[ 'customer_review_charset' ] = '';
//                        $_aRow[ 'customer_reviews' ]        = '';

                        // 3.8.0+ If the table version is 1.1.0b01 or above,
                        $_sCurrentVersion = get_option( "aal_products_version", '0' );
                        if ( version_compare( $_sCurrentVersion, '1.1.0b01', '>=')) {
                            $_aFeatures = $this->getElementAsArray( $aItem, array( 'ItemInfo', 'Features', 'DisplayValues' ) );
                            $_aRow[ 'features' ]   = AmazonAutoLinks_Unit_Utility::getFeatures( $_aFeatures );
                            $_aNodes = $this->getElementAsArray( $aItem, array( 'BrowseNodeInfo', 'BrowseNodes', ) );
                            $_aRow[ 'categories' ] = AmazonAutoLinks_Unit_Utility::getCategories( $_aNodes );
                        }

                        // 3.9.0+ If the table version is 1.2.0b01 or above,
                        if ( version_compare( $_sCurrentVersion, '1.2.0b01', '>=')) {
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
                        if ( version_compare( $_sCurrentVersion, '1.3.0b01', '>=')) {
                            $_aRow[ 'delivery_free_shipping' ] = AmazonAutoLinks_Unit_Utility::isDeliveryEligible( $aItem, array( 'DeliveryInfo', 'IsFreeShippingEligible' )  );
                            $_aRow[ 'delivery_fba' ] = AmazonAutoLinks_Unit_Utility::isDeliveryEligible( $aItem, array( 'DeliveryInfo', 'IsAmazonFulfilled' )  );;
                        }

                        return $_aRow;

                    }

}