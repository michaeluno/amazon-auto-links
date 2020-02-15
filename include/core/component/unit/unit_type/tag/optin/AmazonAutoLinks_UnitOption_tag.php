<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Handles 'tag' unit options.
 * 
 * @since       3
 */
class AmazonAutoLinks_UnitOption_tag extends AmazonAutoLinks_UnitOption_Base {
    
    /**
     * Stores the unit type.
     * @remark      Should be overridden in an extended class.
     */
    public $sUnitType = 'tag';    
    
    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */    
    static public $aStructure_Default = array(

        'tags'          => '',
        'customer_id'   => '',
        'feed_type'     => array(
            'new' => true,
        ),
        'threshold'     => 2,
        'sort'          => 'random',    // date, title, title_descending    
       
    );
    
    
    /**
     * 
     * @since       3
     * @since       4.0.0
     * @return      array   Renamed from format() as it was too general.
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults ) {

        $aUnitOptions = parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults );
        
        // If nothing is checked for the feed type, enable the 'new' item.
        $_aCheckedFeedTypes = array_filter( $aUnitOptions[ 'feed_type' ] );
        if ( empty( $_aCheckedFeedTypes ) ) {
            $aUnitOptions[ 'feed_type' ][ 'new' ] = true;
        }  
        
        
        return $aUnitOptions;
        
    }    
    

    

}