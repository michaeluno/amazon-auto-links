<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Handles Similarity Look-up unit options.
 * 
 * @since       3

 */
class AmazonAutoLinks_UnitOption_similarity_lookup extends AmazonAutoLinks_UnitOption_item_lookup {
    
    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */
    public static $aStructure_Default = array(
        
        // Main fields
        'ItemId'            => null,
        'SimilarityType'    => 'Intersection',
        'Operation'         => 'SimilarityLookup',
        
        // Advanced fields
        'MerchantId'    => 'All',
        'Condition'     => 'New',
        
    );

    /**
     * 
     * @return  array
     */
    protected function getDefaultOptionStructure() {
        return self::$aStructure_Default
            + parent::$aStructure_Default
            + AmazonAutoLinks_UnitOption_search::$aStructure_Default;
    }    
    
}