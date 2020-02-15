<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Handles category unit options.
 * 
 * @since       3

 */
class AmazonAutoLinks_UnitOption_category extends AmazonAutoLinks_UnitOption_Base {

    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */
    static public $aStructure_Default = array(

        'sort'                  => 'random', // date, title, title_descending    
        'keep_raw_title'        => false,    // this is for special sorting method.
        'feed_type'             => array(
            'bestsellers'           => true, 
            'new-releases'          => false,
            'movers-and-shakers'    => false,
            'top-rated'             => false,
            'most-wished-for'       => false,
            'most-gifted'           => false,    
        ),

        'categories'            => array(),    
        'categories_exclude'    => array(),
        
        // The below are retrieved separately and the default values will be assigned in a different process
        // So do not set these here.
        // 'item_format' => null,   // (string)
        // 'image_format' => null,  // (string)
        // 'title_format' => null,  // (string)

    );


    /**
     * 
     * @since   3
     * @since   4.0.0   Renamed from `format()` as it was too general.
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults ) {

        $aUnitOptions = parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults );
        
        // If nothing is checked for the feed type, enable the bestseller item.
        $_aCheckedFeedTypes = array_filter( $aUnitOptions[ 'feed_type' ] );
        if ( empty( $_aCheckedFeedTypes ) ) {
            $aUnitOptions[ 'feed_type' ][ 'bestsellers' ] = true;
        }  
        
        return $aUnitOptions;
        
    }    
    

    

}