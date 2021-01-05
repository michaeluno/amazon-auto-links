<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Creates Amazon product links by Item Look-up.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_UnitOutput_item_lookup extends AmazonAutoLinks_UnitOutput_search {
    
    /**
     * Stores the unit type.
     * @remark Note that the base constructor will create a unit option object based on this value.
     * @var    string
     */    
    public $sUnitType = 'item_lookup';
    
    /**
     * Stores the unit option key that is used for the search.
     * This is needed for the `search_per_keyword` option.
     * @since 3.2.0
     * @var   string
     */
    public $sSearchTermKey = 'ItemId';

    /**
     * The array element key name that contains `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     * @var   string
     * @since 3.9.0
     */
    protected $_sResponseItemsParentKey = 'ItemsResult';


    /**
     * Represents the array structure of the API request arguments.
     * @since 2.0.2
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
     * Sorts items.
     * @remark      Overriding the method in the `AmazonutoLinks_Unit_search` class.
     * @since       3.2.1
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_url`.
     * @return      array
     * @param       array $aResponse
     */
    protected function getProducts( $aResponse ) {

        $_sSortType = $this->oUnitOption->get(
            array( '_sort' ),  // dimensional path
            'raw'   // default
        );

        $_aProducts = parent::getProducts( $aResponse );

        /*
         * 'title'             => __( 'Title', 'amazon-auto-links' ),
         * 'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
         * 'random'            => __( 'Random', 'amazon-auto-links' ),
         * 'raw'               => __( 'Raw', 'amazon-auto-links' ),
         */
        $_sMethodName = "_getItemsSorted_{$_sSortType}";
        return $this->{$_sMethodName}( $_aProducts );

    }

    /**
     * Performs an Amazon Product API request.
     * 
     * @since  2.0.2
     * @since  4.3.1   Changed the scope to public from protected. This is for the background routine to fetch products.
     * @param  integer $iCount
     * @return array
     */
    public function getRequest( $iCount ) {

        $_sAssociateID = $this->oUnitOption->get( 'associate_id' );
        if ( ! $_sAssociateID ) {
            return array(
                'Error' => array(
                    'Code'    => $this->sUnitType,
                    'Message' => 'An Associate tag is not set.',
                )
            );
        }

        $_sLocale = $this->oUnitOption->get( 'country' );
        $_oAPI    = new AmazonAutoLinks_PAAPI50(
            $_sLocale,
            $this->oOption->getPAAPIAccessKey( $_sLocale ),
            $this->oOption->getPAAPISecretKey( $_sLocale ),
            $_sAssociateID
        );

        $_aItemIDs           = array_merge(
            $this->getAsArray( $this->oUnitOption->get( 'ItemIds' ) ),
            explode( ',', $this->oUnitOption->get( 'ItemId' ) )
        );
        // @todo Filter with timed black list items
        $_aItemIDs           = array_filter( array_unique( $_aItemIDs ), array( $this, 'isASINAllowed' ) );

        $_aResponse          = array();
        $_aChunksItemIDs     = array_chunk( $_aItemIDs, 10 );   // the maximum number of items that can be queried is 10
        $_iCacheDuration     = $this->oUnitOption->get( 'cache_duration' );
        $_bForceCacheRenewal = $this->oUnitOption->get( '_force_cache_renewal' );
        foreach( $_aChunksItemIDs as $_iIndex => $_aChunkBy10 ) {
            if ( empty( $_aChunkBy10 ) ) {
                break;
            }
            $_aChunkBy10 = array_filter( $_aChunkBy10 ); // sometimes an empty element gets inserted so drop them
            $_aPayload   = $this->getAPIParameters( $this->oUnitOption->get( 'Operation' ) );
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
         * @sinece 4.4.0
         */
        private function ___getResponsesMerged( array $aMainResponse, array $aSubResponse ) {

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
     * @since  4.3.1        Renamed from `getAPIParameterArray()`. Made the scope public from protected. This is for the background routine of getting products data that need to replicate API payload of the item_lookup unit.
     * @param  string       $sOperation
     * @param  integer      $iItemPage
     * @return array
     */
    public function getAPIParameters( $sOperation='GetItems', $iItemPage=0 ) {

        $_aUnitOptions = $this->oUnitOption->get() + self::$aStructure_APIParameters;
        $_aPayload     = array(
            'Operation'             => 'ItemLookup' === $sOperation
                ? 'GetItems'
                : $sOperation,
            'Condition'             => 'All' === $_aUnitOptions[ 'Condition' ]
                ? 'Any'
                : $_aUnitOptions[ 'Condition' ],    // (optional) Used | Collectible | Refurbished, Any
            'ItemIds'               => array_filter( array_merge(
                $_aUnitOptions[ 'ItemIds' ],
                explode( ',', $_aUnitOptions[ 'ItemId' ] )
            ) ),
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