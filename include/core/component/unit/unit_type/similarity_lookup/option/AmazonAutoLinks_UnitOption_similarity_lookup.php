<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Handles Similarity Look-up unit options.
 * 
 * @since       3

 */
class AmazonAutoLinks_UnitOption_similarity_lookup extends AmazonAutoLinks_UnitOption_item_lookup {
    
    /**
     * Stores the unit type.
     */
    public $sUnitType = 'similarity_lookup';    
    
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
        'Condition'     => 'Any',
        
    );

    /**
     * @since       3.4.6
     */
    public static $aShortcodeArgumentKeys = array(
        'operation'             => 'Operation',
        'itemid'                => 'ItemId',
        'merchantid'            => 'MerchantId',     
        'condition'             => 'Condition',         
        'similaritytype'        => 'SimilarityType',         
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
    
    /**
     * 
     * @since       3.4.6
     * @since       4.0.0       Renamed from format() as it was too general.
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults ) {

        $aUnitOptions = $this->_getShortcodeArgumentKeysSanitized( $aUnitOptions, self::$aShortcodeArgumentKeys );
        return parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults );
        
    }        
    
}