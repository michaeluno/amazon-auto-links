<?php
/**
 * Auto Amazon Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
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
            'site_title'        => false,   // 5.4.0
            'url_query'         => false,   // 5.4.0
            'post_meta'         => false,   // 5.4.0
         ),
        'http_query_parameters' => array(), // 5.4.0
        'additional_keywords'   => '',
        'excluding_keywords'    => '',  // 3.12.0
        'country'               => 'US',

       // 'search_per_keyword'    => true,
       // '_sort'                 => 'raw',

    );
    
    /**
     * @return  array
     */
    protected function getDefaultOptionStructure() {
        return self::$aStructure_Default
            + AmazonAutoLinks_UnitOption_search::$aStructure_Default;
    }

    /**
     * @param  array $aUnitOptions
     * @param  array $aDefaults
     * @param  array $aRawOptions
     * @return array
     * @since  5.4.0
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults, array $aRawOptions ) {
        $aUnitOptions[ 'http_query_parameters' ] = array_values( array_filter( $this->getElementAsArray( $aUnitOptions, 'http_query_parameters' ) ) ); // drop empty elements & reorder the keys
        return parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults, $aRawOptions );
    }

}