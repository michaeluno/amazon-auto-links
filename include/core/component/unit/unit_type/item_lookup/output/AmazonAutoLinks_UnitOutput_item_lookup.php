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
 * Creates Amazon product links by Item Look-up.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_UnitOutput_item_lookup extends AmazonAutoLinks_UnitOutput_search {
    
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */    
    public $sUnitType = 'item_lookup';
    
    /**
     * Stores the unit option key that is used for the search.
     * This is needed for the `search_per_keyword` option.
     * @since       3.2.0
     */
    public $sSearchTermKey = 'ItemId';

    /**
     * The array element key name that containes `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     * @var string
     * @since   3.9.0
     */
    protected $_sResponseItemsParentKey = 'ItemsResult';


    /**
     * Represents the array structure of the API request arguments.
     * @since            2.0.2
     * @see https://webservices.amazon.com/paapi5/documentation/get-items.html
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
     * @since            2.0.2
     */
    protected function getRequest( $iCount ) {

        $_oAPI = new AmazonAutoLinks_PAAPI50(
            $this->oUnitOption->get( 'country' ),
            $this->oOption->get( 'authentication_keys', 'access_key' ),
            $this->oOption->get( 'authentication_keys', 'access_key_secret' ),
            $this->oUnitOption->get( 'associate_id' )
        );
        $_aResponse = $_oAPI->request(
            $this->getAPIParameterArray( $this->oUnitOption->get( 'Operation' ) ),
            $this->oUnitOption->get( 'cache_duration' ),
            $this->oUnitOption->get( '_force_cache_renewal' )
        );

        return $_aResponse;
                 
    }

    /**
     *
     * @see              http://docs.aws.amazon.com/AWSECommerceService/latest/DG/ItemLookup.html
     * @since            2.0.2
     */
    protected function getAPIParameterArray( $sOperation='GetItems', $iItemPage=null ) {

        $_aUnitOptions = $this->oUnitOption->get()
            + self::$aStructure_APIParameters;
        $_aPayload = array(
            'Operation'             => 'ItemLookup' === $sOperation
                ? 'GetItems' : $sOperation,
            'Condition'             => 'All' === $_aUnitOptions[ 'Condition' ]
                ? 'Any' : $_aUnitOptions[ 'Condition' ],    // (optional) Used | Collectible | Refurbished, Any
            'ItemIds'               => explode( ',', $_aUnitOptions[ 'ItemId' ] ),
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
        return $_aPayload;
        
    }
    
}