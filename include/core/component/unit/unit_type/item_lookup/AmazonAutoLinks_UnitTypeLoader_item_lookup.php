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
 * Loads the units component.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypeLoader_item_lookup extends AmazonAutoLinks_UnitTypeLoader_Base {
        
    /**
     * Stores the unit type slug.
     * @remark      Each extended class should assign own unique unit type slug here.
     * @since       3.3.0
     */
    public $sUnitTypeSlug = 'item_lookup';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array(
        'AmazonAutoLinks_FormFields_ItemLookupUnit_Main',
        'AmazonAutoLinks_FormFields_ItemLookupUnit_Advanced',
    );    
    
    /**
     * Adds post meta boxes.
     * 
     * @since       3.3.0
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {
        
        new AmazonAutoLinks_UnitPostMetaBox_Main_item_lookup(
            null,   // meta box ID - null for auto-generate
            __( 'Item Look-up Main', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ),                 
            'normal', // context - e.g. 'normal', 'advanced', or 'side'
            'high'  // priority - e.g. 'high', 'core', 'default' or 'low'
        );                 
        new AmazonAutoLinks_UnitPostMetaBox_Advanced_item_lookup(
            null,   // meta box ID - null for auto-generate
            __( 'Item Look-up Advanced', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ),                 
            'normal', // context - e.g. 'normal', 'advanced', or 'side'
            'low' // priority - e.g. 'high', 'core', 'default' or 'low'            
        );         
        
    }

    /**
     * Determines the unit type from given output arguments.
     * @param       string      $sUnitTypeSlug
     * @param       array       $aArguments
     * @return      string
     * @since       3.5.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
        return 'ItemLookup' === $this->_getOperationArgument( $aArguments )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return      string
     * @since       3.5.0
     */
    protected function _getLabel() {
        return __( 'Item Look-up', 'amazon-auto-links' );
    }

   
}