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
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOption_contextual extends AmazonAutoLinks_UnitOption_search {
    
    /**
     * Stores the unit type.
     */
    public $sUnitType = 'contextual';
    
    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */    
    static public $aStructure_Default = array(

        'criteria'      => array(
            'post_title'        => true,
            'taxonomy_terms'    => true,
            'breadcrumb'        => false,
         ),
        'additional_keywords'   => '',
        'excluding_keywords'    => '',  // 3.12.0
        'country'               => 'US',

//        'search_per_keyword'    => true,
//        '_sort'                 => 'raw',

    );
    
    /**
     * 
     * @return  array
     */
    protected function getDefaultOptionStructure() {
        return self::$aStructure_Default
            + parent::getDefaultOptionStructure();       
    }
    
    /**
     * 
     * @since       3.5.0
     * @since       4.0.0   Renamed from `format()` as it was too general.
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults ) {
        return parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults );
    }

}