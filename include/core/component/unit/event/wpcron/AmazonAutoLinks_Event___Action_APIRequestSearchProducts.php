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
 * Searches products by the given ASIN and locale.
 *
 * This is a plural version of `AmazonAutoLinks_Event___Action_APIRequestSearchProducts` which queries multiple products at a time.
 *
 * @since       3.7.7
 */
class AmazonAutoLinks_Event___Action_APIRequestSearchProducts extends AmazonAutoLinks_Event___Action_APIRequestSearchProduct {

    protected $_sActionHookName     = 'aal_action_api_get_products_info';
    protected $_iCallbackParameters = 3;

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
        $_sASINs         = $this->___getASINs( $_aList ); // $_aList will be updated to have keys of ASIN
        $_aResponse      = $this->___getAPIResponse( $_sASINs, $_sAssociateID, $_sLocale );

        if ( $_isErrorCode = $this->___getErrorType( $_aResponse ) ) {

            // @deprecated 3.9.0
            // $this->___setAPIErrorFlagFile( $_isErrorCode );

            // @todo for the response Throttling error that the API gives, schedule the same request with an interval.
            // @todo also when scheduling a task, there should be a check for an individual product information retrieval task scheduled or not. If yes, it should not be scheduled.
            return;
        }

        $this->___setItemsIntoDatabase( $_aResponse, $_aList );

    }
        /**
         * Creates a temporary file that indicates that an API request got an error and further API requests should hold back.
         * @since   3.9.0
         * @deprecated
         */
/*        private function ___setAPIErrorFlagFile( $isErrorCode ) {

            if ( ! in_array( $isErrorCode, array( 'RequestThrottled', 'InvalidClientTokenId' ) ) ) {
                return;
            }
            $_oOption      = AmazonAutoLinks_Option::getInstance();
            $_sPublicKey   = $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey  = $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );

            $_oFileManager = new AmazonAutoLinks_VersatileFileManager(
                $_sPublicKey . '_' . $_sPrivateKey,
                60 * 60,    // 1 hour
                'AAL_PAAPI_ERROR_'
            );
            if ( $_oFileManager->exist() ) {
                return;
            }
            $_oFileManager->set();

        }*/
        /**
         * @return  string|integer     The found error code
         * @param   array   $aResponse
         */
        private function ___getErrorType( array $aResponse ) {

            if ( empty( $aResponse ) ) {
                return 1;
            }

            /**
             * @see ItemLookup.Error.MIssingParameters.example.xml
             */
            $_sResponseStatus = $this->getElement( $aResponse, array( 'Items', 'Request', 'IsValid' ) );
            if ( 'false' === strtolower( $_sResponseStatus ) ) {
                return $this->getElement( $aResponse, array( 'Items', 'Request', 'Errors', 'Error' ) );
            }

            /**
             * ## Case: invalid access key
             * ```
             * <ItemLookupErrorResponse
                xmlns="http://ecs.amazonaws.com/doc/2005-10-05/">
                <Error>
                    <Code>InvalidClientTokenId</Code>
                    <Message>The AWS Access Key Id you provided does not exist in our records.</Message>
                </Error>
                <RequestID>5fc4fd48-af1a-4500-b3fb-788884e2cece</RequestID>
                </ItemLookupErrorResponse>
             * ```
             */
            $_sErrorCode = $this->getElement( $aResponse, array( 'Error', 'Code' ) );
            if ( $_sErrorCode ) {
                return $_sErrorCode;
            }

            return 0;
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

        private function ___getAPIResponse( $sASINs, $sAssociateID, $sLocale ) {

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
             */
            $_aItems = $this->_getAPIResponse(
                $_sPublicKey,
                $_sPrivateKey,
                $sASINs,
                $sLocale,
                $sAssociateID,
                600,    // cache duration. Why giving a short one? See the above dock-block.
                false,   // prevent excessive API calls
                array()    // response element dimensional key to extract
            );
            return $_aItems;

        }

        /**
         * @param $aItems
         * @param $aList
         * @since   3.7.7
         */
        private function ___setItemsIntoDatabase( array $aResponse, array $aList ) {

            $_aItems = $this->getElementAsArray( $aResponse, array( 'Items', 'Item' ) );
            // Singular
            if ( ! isset( $_aItems[ 0 ] ) ) {
                $this->___setItemIntoDatabase( $_aItems, $aList );
                return;
            }
            // Multiple
            foreach ( $_aItems as $_iIndex => $_aItem ) {
                $this->___setItemIntoDatabase( $_aItem, $aList );
            }

        }
            private function ___setItemIntoDatabase( array $aItem, array $aList ) {

                $_sASIN = $this->getElement( $aItem, array( 'ASIN' ), '' );

                if ( ! $_sASIN ) {
                    return;
                }
                if ( ! isset( $aList[ $_sASIN ] ) ) {
                    return;
                }

                // Structure: array( 0 => $sAssociateID, 1 => $sASIN,  2 => $sLocale, 3 => $iCacheDuration, 4 => $bForceRenew, 5 => $sItemFormat ),
                $_aParameters = $aList[ $_sASIN ];

                // Retrieve similar products in a separate routine
                // Do this only `%similar%` is present in the Item Format option.
                if ( false !== strpos( $_aParameters[ 5 ], '%similar%' ) ) {
                    $this->_scheduleFetchingSimilarProducts(
                        $aItem, // product data in `Items` -> `Item` in returned from API.
                        $_sASIN,
                        $_aParameters[ 2 ], // locale
                        $_aParameters[ 0 ], // associate id
                        $_aParameters[ 3 ], // cache duration
                        $_aParameters[ 4 ]  // whether to force cache renewal
                    );
                }

                $this->_setProductData(
                    $aItem, // product data in `Items` -> `Item` in returned from API.
                    $_sASIN,
                    $_aParameters[ 2 ], // locale
                    $_aParameters[ 3 ], // cache duration
                    $_aParameters[ 4 ], // whether to force cache renewal
                    $_aParameters[ 5 ]  // item format
                );

            }



}