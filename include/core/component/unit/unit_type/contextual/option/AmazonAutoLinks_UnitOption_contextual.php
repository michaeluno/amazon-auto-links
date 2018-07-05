<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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
     */
    protected function format( array $aUnitOptions ) {
        return parent::format( $aUnitOptions );
    }

}