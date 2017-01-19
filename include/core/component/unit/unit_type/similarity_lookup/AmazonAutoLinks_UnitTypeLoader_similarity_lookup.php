<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Loads the units component.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypeLoader_similarity_lookup extends AmazonAutoLinks_UnitTypeLoader_Base {
        
    /**
     * Stores the unit type slug.
     * @remark      Each extended class should assign own unique unit type slug here.
     * @since       3.3.0
     */
    public $sUnitTypeSlug = 'similarity_lookup';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array(
        'AmazonAutoLinks_FormFields_SimilarityLookupUnit_Main',    
        'AmazonAutoLinks_FormFields_SimilarityLookupUnit_Advanced',
    );    
    
    /**
     * Adds post meta boxes.
     * 
     * @since       3.3.0
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {
        
        new AmazonAutoLinks_UnitPostMetaBox_Main_similarity_lookup(
            null,   // meta box ID - null for auto-generate
            __( 'Similarity Look-up Main', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ),                 
            'normal', // context - e.g. 'normal', 'advanced', or 'side'
            'core'  // priority - e.g. 'high', 'core', 'default' or 'low'
        );   
        new AmazonAutoLinks_UnitPostMetaBox_Advanced_similarity_lookup(
            null,   // meta box ID - null for auto-generate
            __( 'Similarity Look-up Advanced', 'amazon-auto-links' ),
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
        return 'SimilarityLookup' === $this->_getOperationArgument( $aArguments )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return      string
     * @since       3.5.0
     */
    protected function _getLabel() {
        return __( 'Similarity Look-up', 'amazon-auto-links' );
    }

}