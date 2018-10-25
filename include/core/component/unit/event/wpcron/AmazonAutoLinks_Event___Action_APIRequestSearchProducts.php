<<?php
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

    protected $_sActionHookName = 'aal_action_api_get_products_info';
    
    /**
     * Searches passed products and saves their data.
     */
    protected function _doAction( /* $aArguments */ ) {

        $_aParams        = func_get_args() + array( array(), '', '' );
        $_aList          = $_aParams[ 0 ];
        $_sAssociateID   = $_aParams[ 1 ];
        $_sLocale        = $_aParams[ 2 ];
        $_sASINs         = $this->___getASINs( $_aList );
        $_aResponse      = $this->___getAPIResponse( $_sASINs, $_sAssociateID, $_sLocale );

        if ( $this->___getErrorType( $_aResponse ) ) {
           // @todo for the response Throttling error that the API gives, schedule the same request with an interval.
            return;
        }

        $this->___setItemsIntoDatabase( $_aResponse, $_aList );

    }
        /**
         * @return  string|integer     The found error code
         */
        private function ___getErrorType( array $aResponse ) {

            /**
            <?xml version="1.0" ?>
            <ItemLookupResponse
                xmlns="http://webservices.amazon.com/AWSECommerceService/2011-08-01">
                <OperationRequest>
                    <HTTPHeaders>
                        <Header Name="UserAgent" Value="Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36"></Header>
                    </HTTPHeaders>
                    <RequestId>9c7f8542-2876-4710-9df2-456456cc97f2</RequestId>
                    <Arguments>
                        <Argument Name="AWSAccessKeyId" Value="AKIAIUOXXAXPYUKNVPVA"></Argument>
                        <Argument Name="AssociateTag"></Argument>
                        <Argument Name="IdType" Value="ASIN"></Argument>
                        <Argument Name="ItemId" Value="B01HUJEPZO,B000WLIK5Y,B0000C91EK"></Argument>
                        <Argument Name="Operation" Value="ItemLookup"></Argument>
                        <Argument Name="ResponseGroup" Value="Large"></Argument>
                        <Argument Name="Service" Value="AWSECommerceService"></Argument>
                        <Argument Name="Timestamp" Value="2018-10-25T11:33:40.000Z"></Argument>
                        <Argument Name="Signature" Value="XMTsRFcyNVqf8rWDELEaEslrMvIW4bkwN50eowQasmc="></Argument>
                    </Arguments>
                    <RequestProcessingTime>0.0011089160000000</RequestProcessingTime>
                </OperationRequest>
                <Items>
                    <Request>
                        <IsValid>False</IsValid>
                        <ItemLookupRequest>
                            <IdType>ASIN</IdType>
                            <ItemId>B01HUJEPZO</ItemId>
                            <ItemId>B000WLIK5Y</ItemId>
                            <ItemId>B0000C91EK</ItemId>
                            <ResponseGroup>Large</ResponseGroup>
                            <VariationPage>All</VariationPage>
                        </ItemLookupRequest>
                        <Errors>
                            <Error>
                                <Code>AWS.MissingParameters</Code>
                                <Message>Required parameters are missing.</Message>
                            </Error>
                        </Errors>
                    </Request>
                </Items>
            </ItemLookupResponse>
             */
            $_sResponseStatus = $this->getElement( $aResponse, array( 'Items', 'Request', 'IsValid' ) );
            if ( 'false' === strtolower( $_sResponseStatus ) ) {
                return $this->getElement( $aResponse, array( 'Items', 'Request', 'Errors', 'Error' ) )
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
         *
         * @param $aList
         */
        private function ___getASINs( $aList ) {
            // Extract the ASINs
            $_aASINs = array();
            foreach( $aList as $_iIndex => $_aArguments ) {

                // For the argument structure, see AmazonAutoLinks_Event_Scheduler::scheduleProductInformation()
                // Extract ASIN and the loaded scheduled action may be of an old format.
                if ( ! isset( $_aArguments[ 1 ] ) && is_string( $_aArguments[ 1 ] ) ) {
                    continue;
                }

                $_aASINs[] = $_aArguments[ 1 ];

                // This should not happen but third-parties may use this action.
                if ( $_iIndex > 10 ) {
                    break;
                }
            }
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
                600,    // cache duration
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
        private function ___setItemsIntoDatabase( array $aItems, array $aList ) {
            foreach ( $aItems as $_sKey => $_aItem ) {
                if ( 'Item' !== $_sKey ) {
                    continue;
                }
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
                // array( 0 => $sAssociateID, 1 => $sASIN,  2 => $sLocale, 3 => $iCacheDuration, 4 => $bForceRenew, 5 => $sItemFormat ),
                $_aParameters = $aList[ $_sASIN ];

                // Retrieve similar products in a separate routine
                // Do this only `%similar%` is present in the Item Format option.
                if ( false !== strpos( $_aParameters[ 5 ], '%similar%' ) ) {
                    $this->___scheduleFetchingSimilarProducts(
                        $aItem, // product data in `Items` -> `Item` in returned from API.
                        $_sASIN,
                        $_aParameters[ 2 ], // locale
                        $_aParameters[ 0 ], // associate id
                        $_aParameters[ 3 ], // cache duration
                        $_aParameters[ 4 ]  // whether to force cache renewal
                    );
                }

            }

}