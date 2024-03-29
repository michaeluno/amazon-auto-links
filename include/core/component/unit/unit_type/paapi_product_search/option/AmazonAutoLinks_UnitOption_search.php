<?php
/**
 * Auto Amazon Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 * 
 */

/**
 * Handles search unit options.
 * 
 * @since 3
 */
class AmazonAutoLinks_UnitOption_search extends AmazonAutoLinks_UnitOption_Base {

    /**
     * Stores the unit type.
     */
    public $sUnitType = 'search';

    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */
    public static $aStructure_Default = array(

        'additional_attribute'  => null,
        'search_by'             => 'Author',

        'search_per_keyword'    => false,

        'shuffle'               => false,   // [4.7.0+]
        
        // 'nodes' => 0,    // 0 is for all nodes.    Comma delimited strings will be passed. e.g. 12345,12425,5353
        
        // These are used for API parameters as well
        'Keywords'              => null,      // the keyword to search
        // @deprecated 3.9.0 'Power'                 => null,        // @see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/PowerSearchSyntax.html
        'Operation'             => 'SearchItems',    // ItemSearch, ItemLookup, SimilarityLookup // 3.9.0 PA-API changed the parameter structure
        'Title'                 => '',      // for the advanced Title option
        'Sort'                  => 'Relevance',        // pricerank, inversepricerank, sales_rank, relevancerank, reviewrank
        'SearchIndex'           => 'All',        
        'BrowseNode'            => '',    // ( optional )
        'Availability'          => 'Available',    // ( optional ) 
        'Condition'             => 'Any',
        'MaximumPrice'          => null,
        'MinimumPrice'          => null,
        'MinPercentageOff'      => null,
        'MerchantId'            => 'All',    // 2.0.7+
        'MarketplaceDomain'     => null,    // 2.1.0+ @deprecated No longer available in PA-API 5
        'ItemPage'              => null,

        'MinReviewsRating'      => 0,       // 3.9.1
        'DeliveryFlags'         => null,    // (array) 3.10.0
    );
    
    /**
     * @since       3.4.6
     */
    public static $aShortcodeArgumentKeys = array(
        'search'                => 'Keywords',          // 5.0.0+ the shortcode argument
        'keywords'              => 'Keywords',
        'power'                 => 'Power',             
        'operation'             => 'Operation',
        'title'                 => 'Title',            
        'sort'                  => 'Sort',              // alias for `sortby`
        'searchindex'           => 'SearchIndex',
        'browsenode'            => 'BrowseNode',        // alias for `browsenodeid`
        'availability'          => 'Availability',
        'condition'             => 'Condition',         
        'maximumprice'          => 'MaximumPrice',      // alias for `maxprice`
        'minimumprice'          => 'MinimumPrice',
        'minpercentageoff'      => 'MinPercentageOff',  // alias for `minsavingpercent`
        'merchantid'            => 'MerchantId',
        'marketplacedomain'     => 'MarketplaceDomain', // @deprecated
        'itempage'              => 'ItemPage',
        // [4.8.0+] For PA-API5 parameters
        'minsavingpercent'      => 'MinPercentageOff',  // alias for `minpercentageoff`
        'minreviewsrating'      => 'MinReviewsRating',
        'merchant'              => 'MerchantId',        // alias for `merchantid`
        'minprice'              => 'MinimumPrice',      // alias for `minimumprice`
        'maxprice'              => 'MaximumPrice',      // alias for `maximumprice`
        'browsenodeid'          => 'BrowseNode',        // alias for `browsenode`
        'sortby'                => 'Sort',              // alias for `sort`
        'currencyofpreference'  => 'preferred_currency',
        'languagesofpreference' => 'language',
    );

    /**
     * @param  array $aUnitOptions
     * @param  array $aDefaults
     * @param  array $aRawOptions
     * @return array
     * @since  3.4.6
     * @since  4.0.0 Renamed from format() as it is too general.
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults, array $aRawOptions ) {
        $aUnitOptions[ 'Operation' ]     = 'SearchItems';
        $aUnitOptions = $this->_getShortcodeArgumentKeysSanitized( $aUnitOptions, self::$aShortcodeArgumentKeys );
        return parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults, $aRawOptions );
    }

}