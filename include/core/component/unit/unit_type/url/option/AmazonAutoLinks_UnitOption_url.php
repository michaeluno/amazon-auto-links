<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Handles 'url' unit options.
 * 
 * @since       3.2.0
 */
class AmazonAutoLinks_UnitOption_url extends AmazonAutoLinks_UnitOption_item_lookup {
    
    /**
     * Stores the unit type.
     */
    public $sUnitType = 'url';    
    
    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */    
    static public $aStructure_Default = array(
    
        'urls'           => '',  // (string|array)
        'search_per_keyword'    => true,
        '_found_items'   => '',  // (string)
        '_sort'          => 'raw',  // 3.2.1+ (string)

    );
    
    /**
     * 
     * @return  array
     */
    protected function getDefaultOptionStructure() {
        return array(
                '_found_items' => __( 'Retrieving items...', 'amazon-auto-links' ),
            )
            + self::$aStructure_Default
            + parent::getDefaultOptionStructure();       
    }
    
    /**
     * 
     * @since       3
     * @since       4.0.0   Renamed from format() as it was too general.
     * @return      array
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults ) {

        $aUnitOptions = parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults );
        
        $aUnitOptions[ 'urls' ]  = $this->getAsArray( $aUnitOptions[ 'urls' ] );
                
        return $aUnitOptions;
        
    }    

}