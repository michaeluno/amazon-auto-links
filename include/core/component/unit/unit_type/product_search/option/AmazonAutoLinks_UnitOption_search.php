<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Handles search unit options.
 * 
 * @since       3

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
        
        // 'nodes' => 0,    // 0 is for all nodes.    Comma delimited strings will be passed. e.g. 12345,12425,5353
        
        // These are used for API parameters as well
        'Keywords'              => null,      // the keyword to search
        // @deprecated 3.9.0 'Power'                 => null,        // @see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/PowerSearchSyntax.html
        'Operation'             => 'SearchItems',    // ItemSearch, ItemLookup, SimilarityLookup // 3.9.0 PA-API changed the parameter structure
        'Title'                 => '',      // for the advanced Title option
        'Sort'                  => 'salesrank',        // pricerank, inversepricerank, sales_rank, relevancerank, reviewrank
        'SearchIndex'           => 'All',        
        'BrowseNode'            => '',    // ( optional )
        'Availability'          => 'Available',    // ( optional ) 
        'Condition'             => 'Any',
        'MaximumPrice'          => null,
        'MinimumPrice'          => null,
        'MinPercentageOff'      => null,
        'MerchantId'            => null,    // 2.0.7+
        'MarketplaceDomain'     => null,    // 2.1.0+
        'ItemPage'              => null,

        'MinReviewsRating'      => 0,       // 3.9.1
//        'MinSavingPercent'      => 0,       // 3.9.1 - same as `MinPercentageOff`
//        'Merchant'              => null,    // 3.9.1 - same as `MerchantId`
        'DeliveryFlags'         => null,    // (array) 3.10.0
    );
    
    /**
     * @since       3.4.6
     */
    public static $aShortcodeArgumentKeys = array(
        'keywords'              => 'Keywords',
        'power'                 => 'Power',             
        'operation'             => 'Operation',
        'title'                 => 'Title',            
        'sort'                  => 'Sort',                
        'searchindex'           => 'SearchIndex',
        'browsenode'            => 'BrowseNode',          
        'availability'          => 'Availability',
        'condition'             => 'Condition',         
        'maximumprice'          => 'MaximumPrice',
        'minimumprice'          => 'MinimumPrice',         
        'minpercentageoff'      => 'MinPercentageOff',
        'merchantid'            => 'MerchantId',     
        'marketplacedomain'     => 'MarketplaceDomain',
        'itempage'              => 'ItemPage',
    );

    /**
     * @param array $aUnitOptions
     *
     * @return array
     * @since       3.4.6
     * @since       4.0.0   Renamed from format() as it is too general.
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults ) {

        $aUnitOptions = $this->_getShortcodeArgumentKeysSanitized( $aUnitOptions, self::$aShortcodeArgumentKeys );
        return parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults );
        
    }
    
}