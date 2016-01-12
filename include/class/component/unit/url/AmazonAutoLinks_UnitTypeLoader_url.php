<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Loads the units component.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypeLoader_url extends AmazonAutoLinks_UnitTypeLoader_Base {
        
    /**
     * Stores the unit type slug.
     * @remark      Each extended class should assign own unique unit type slug here.
     * @since       3.3.0
     */
    public $sUnitTypeSlug = 'url';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array(
        'AmazonAutoLinks_FormFields_URLUnit_Main',
    );    
    
    /**
     * Stores protected meta key names.
     */    
    public $aProtectedMetaKeys = array(
    );    
    
    /**
     * Adds post meta boxes.
     * 
     * @since       3.3.0
     * @return      void
     */
    public function loadAdminComponents( $sScriptPath ) {

        // Admin pages
        new AmazonAutoLinks_URLUnitAdminPage(
            array(
                'type'      => 'transient',
                'key'       => $GLOBALS[ 'aal_transient_id' ],
                'duration'  => 60*60*24*2,
            ),
            $sScriptPath
        );         
              
        // Post meta boxes
        new AmazonAutoLinks_UnitPostMetaBox_Main_url(
            null,
            __( 'Main', 'amazon-auto-links' ), // meta box title
            array(     // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] 
            ), 
            'normal', // context (what kind of metabox this is)
            'high'    // priority - e.g. 'high', 'core', 'default' or 'low'
        );   
        new AmazonAutoLinks_PostMetaBox_URLUnit_Advanced(
            null,     // meta box ID - null for auto-generate
            __( 'URL Unit Advanced', 'amazon-auto-links' ),
            array(    // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),                 
            'normal', // context - e.g. 'normal', 'advanced', or 'side'
            'low'     // priority - e.g. 'high', 'core', 'default' or 'low'
        );
        
    }    


}