<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Performs PA-API 5 requests with the GetItems operations.
 *
 * @since   5.0.0
 */
class AmazonAutoLinks_Unit_PAAPI5_Request_GetItems extends AmazonAutoLinks_Unit_PAAPI5_Request_Base {

    /**
     * The array element key name that contains `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     * @var   string
     * @since 3.9.0
     * @since 5.0.0  Moved from `AmazonAutoLinks_UnitOutput_item_lookup`.
     */
    protected $_sResponseItemsParentKey = 'ItemsResult';

    /**
     * Represents the array structure of the API request arguments.
     * @since 2.0.2
     * @since 5.0.0  Moved from `AmazonAutoLinks_UnitOutput_item_lookup`.
     * @var   array
     * @see   https://webservices.amazon.com/paapi5/documentation/get-items.html
     */
    public static $aStructure_APIParameters = array(
        'Operation'             => 'GetItems',  // string
        'Condition'             => 'Any',       // string default: Any
        'ItemIdType'            => null,    // string   default: ASIN
        'ItemIds'               => null,    // array
        'Merchant'              => null,    // string
        'OfferCount'            => null,    // integer
        'Resources'             => null,    // array
//        'CurrencyOfPreference'  => null,    // string
//        'LanguagesOfPreference' => null,    // array
        // @deprecated 3.9.0    The below parameters are not supported in PA-API 5
//        'IdType'                => null,
//        'IncludeReviewsSummary' => null,
//        'ItemId'                => null,
//        'MerchantId'            => null,
//        'RelatedItemPage'       => null,
//        'RelationshipType'      => null,
//        'SearchIndex'           => null,
//        'TruncateReviewsAt'     => null,
//        'VariationPage'         => null,
//        'ResponseGroup'         => null,
    );

    /**
     * Performs an Amazon Product API request.
     *
     * @since  2.0.2
     * @since  4.3.1   Changed the scope to public from protected. This is for the background routine to fetch products.
     * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_item_lookup`. Renamed from `getRequest()`.
     * @param  integer $iCount
     * @return array
     */
    public function getPAAPIResponse( $iCount ) {

        $_sAssociateID = $this->oUnitOption->get( 'associate_id' );
        if ( ! $_sAssociateID ) {
            return array(
                'Error' => array(
                    'Code'    => $this->oUnitOption->sUnitType,
                    'Message' => 'An Associate tag is not set.',
                )
            );
        }

        $_oAPI               = new AmazonAutoLinks_PAAPI50( $this->oUnitOption->get( 'country' ), $this->sPublicKey, $this->sSecretKey, $_sAssociateID );
        $_aItemIDs           = $this->oUnitOption->get( 'ItemIds' );

        $_aResponse          = array();
        $_aChunksItemIDs     = array_chunk( $_aItemIDs, 10 );   // the maximum number of items that can be queried is 10
        $_iCacheDuration     = $this->oUnitOption->get( 'cache_duration' );
        $_bForceCacheRenewal = $this->oUnitOption->get( '_force_cache_renewal' );
        foreach( $_aChunksItemIDs as $_iIndex => $_aChunkBy10 ) {
            if ( empty( $_aChunkBy10 ) ) {
                break;
            }
            $_aChunkBy10 = array_filter( $_aChunkBy10 ); // sometimes an empty element gets inserted so drop them
            $_aPayload   = $this->___getAPIParameters();
            $_aPayload[ 'ItemIds' ] = $_aChunkBy10;
            $_aThisResponse = $_oAPI->request( $_aPayload, $_iCacheDuration, $_bForceCacheRenewal );
            $_aResponse  = $this->___getResponsesMerged( $_aResponse, $_aThisResponse );
            if ( $iCount <= count( $this->getElementAsArray( $_aResponse, array( $this->_sResponseItemsParentKey, 'Items' ) ) ) ) {
                break;
            }
        }
        return $_aResponse;

    }
        /**
         * @param  array $aMainResponse
         * @param  array $aSubResponse
         * @return array
         * @since  4.4.0
         * @sicne  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_item_lookup`.
         */
        private function ___getResponsesMerged( array $aMainResponse, array $aSubResponse ) {

            if ( ! isset( $aMainResponse[ '_ResponseDate' ] ) && isset( $aSubResponse[ '_ResponseDate' ] ) ) {
                $aMainResponse[ '_ResponseDate' ] = $this->getElement( $aSubResponse, array( '_ResponseDate' ) );
            }

            $_aItems = array_merge(
                $this->getElementAsArray( $aMainResponse, array( $this->_sResponseItemsParentKey, 'Items' ) ),
                $this->getElementAsArray( $aSubResponse, array( $this->_sResponseItemsParentKey, 'Items' ) )
            );
            if ( ! empty( $_aItems ) ) {
                $this->setMultidimensionalArray( $aMainResponse, array( $this->_sResponseItemsParentKey, 'Items' ), $_aItems );
            }

            $_aErrors = array_merge(
                $this->getElementAsArray( $aMainResponse, array( 'Errors' ) ),
                $this->getElementAsArray( $aSubResponse, array( 'Errors' ) )
            );
            if ( ! empty( $_aErrors ) ) {
                $aMainResponse[ 'Errors' ] = $_aErrors;
            }

            $_sError = trim( $this->getElement( $aMainResponse, array( 'Error', 'Message' ) ) . ' ' . $this->getElement( $aSubResponse, array( 'Error', 'Message' ) ) );
            if ( $_sError ) {
                $this->setMultidimensionalArray( $aMainResponse, array( 'Error', 'Message' ), $_sError );
            }
            return $aMainResponse;

        }

    /**
     *
     * @see    http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
     * @since  2.0.2
     * @since  4.3.1   Renamed from `getAPIParameterArray()`. Made the scope public from protected. This is for the background routine of getting products data that need to replicate API payload of the item_lookup unit.
     * @since  5.0.0   Changed the visibility scope to private from public as this is not used from outside. Moved from `AmazonAutoLinks_UnitOutput_item_lookup`. Removed the `$sOperation` and `$iItemPage` parameters.
     * @return array
     */
    private function ___getAPIParameters() {

        $_sOperation   = $this->oUnitOption->get( 'Operation' );
        $_aUnitOptions = $this->oUnitOption->get() + self::$aStructure_APIParameters;
        $_aPayload     = array(
            'Operation'             => 'ItemLookup' === $_sOperation
                ? 'GetItems'
                : $_sOperation,
            'Condition'             => 'All' === $_aUnitOptions[ 'Condition' ]
                ? 'Any'
                : $_aUnitOptions[ 'Condition' ],    // (optional) Used | Collectible | Refurbished, Any
            'ItemIds'               => $_aUnitOptions[ 'ItemIds' ], // this is formatted in the unit option class
            'Merchant'              => 'Amazon' === $this->oUnitOption->get( 'MerchantId' )
                ? 'Amazon'
                : null,
            'CurrencyOfPreference'  => $_aUnitOptions[ 'preferred_currency' ]
                ? $_aUnitOptions[ 'preferred_currency' ]
                : null,
            'LanguagesOfPreference' => $_aUnitOptions[ 'language' ]
                ? array( $_aUnitOptions[ 'language' ] )
                : null,
            'Resources'             => AmazonAutoLinks_PAAPI50___Payload::$aResources,
        );
        $_aPayload = array_filter( $_aPayload, array( $this, 'isNotNull' ) ); // drop null elements.
        ksort( $_aPayload );  // important to generate identical caches.
        return $_aPayload;

    }

}