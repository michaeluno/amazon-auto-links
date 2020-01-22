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
 * Creates Amazon product links by Similarity Look-up.
 * 
 * @package         Amazon Auto Links
 */
class AmazonAutoLinks_UnitOutput_similarity_lookup extends AmazonAutoLinks_UnitOutput_search {
    
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */    
    public $sUnitType = 'similarity_lookup';
    
    /**
     * Stores the unit option key that is used for the search.
     * This is needed for the `search_per_keyword` option.
     * @since       3.2.0
     */
    public $sSearchTermKey = 'ItemId';
    
    /**
     * Represents the array structure of the API request arguments.
     * @since            2.0.2
     */
    public static $aStructure_APIParameters = array(    
        'Operation'         => 'SimilarityLookup',        
        'Condition'         => 'Any',
        'ItemId'            => null,
        'MerchantId'        => null,
        'SimilarityType'    => 'Intersection',
        'ResponseGroup'     => 'Large',
    );
   
    /**
     * Performs an Amazon Product API request.
     * 
     * @since            2.0.2
     */
    protected function getRequest( $iCount ) {
        
        $_oAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 
            $this->oUnitOption->get( 'country' ), 
            $this->oOption->get( 'authentication_keys', 'access_key' ),
            $this->oOption->get( 'authentication_keys', 'access_key_secret' ),
            $this->oUnitOption->get( 'associate_id' )
        );
            
        // Perform the search for the first page regardless the specified count (number of items).
        // Keys with an empty value will be filtered out when performing the request.        
        return $_oAPI->request( 
            $this->getAPIParameterArray( 
                $this->oUnitOption->get( 'Operation' ) 
            ),
            $this->oUnitOption->get( 'cache_duration' ),
            $this->oUnitOption->get( '_force_cache_renewal' )
        );    
                 
    }
    
    /**
     * 
     * 'Operation' => 'SimilarityLookup''
     * @see              http://docs.aws.amazon.com/AWSECommerceService/latest/DG/SimilarityLookup.html
     * @since            2.0.3
     */
    protected function getAPIParameterArray( $sOperation='SimilarityLookup', $iItemPage=null ) {

        $_aUnitOptions = $this->oUnitOption->get()
            + self::$aStructure_APIParameters;
        return array(
            'Operation'         => $sOperation,
            'MerchantId'        => 'Amazon' === $_aUnitOptions['MerchantId'] 
                ? 'Amazon'
                : null,
            'SimilarityType'    => $_aUnitOptions['SimilarityType'],        
            'Condition'         => $_aUnitOptions['Condition'],    // (optional) Used | Collectible | Refurbished | New | Any
            'ItemId'            => $_aUnitOptions['ItemId'],    // (required)  If ItemIdis an ASIN, a SearchIndex cannot be specified in the request.
            'ResponseGroup'     => 'Large', // (optional)
        );
    }
    
    
}