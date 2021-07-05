<?php
/**
 * Amazon Auto Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * 
 */

/**
 * Outputs the 'url' unit.
 * 
 * @since 3.2.0
 */
class AmazonAutoLinks_UnitOption_url extends AmazonAutoLinks_UnitOption_item_lookup {
    
    /**
     * Stores the unit type.
     */
    public $sUnitType = 'url';    
    
    /**
     * Stores the default structure and key-values of the unit.
     * @remark Accessed from the base class constructor to construct a default option array.
     */    
    static public $aStructure_Default = array(
        'urls'               => '',  // (string|array)
        'search_per_keyword' => false, // [4.4.0] Changed it to false to save API calls.
        '_found_items'       => '',  // (string)
        '_sort'              => 'raw',  // [3.2.1] (string)
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
     * @param  array $aUnitOptions
     * @param  array $aDefaults
     * @param  array $aRawOptions
     * @return array
     * @since  3
     * @since  4.0.0 Renamed from format() as it was too general.
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults, array $aRawOptions ) {
        $aUnitOptions = parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults, $aRawOptions );
        $aUnitOptions[ 'urls' ]  = $this->getAsArray( $aUnitOptions[ 'urls' ] );
        return $aUnitOptions;
    }    

}