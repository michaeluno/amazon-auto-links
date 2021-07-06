<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the `url` unit type components.
 *  
 * @package     Amazon Auto Links
 * @since       3.3.0
*/
class AmazonAutoLinks_UnitTypeLoader_url extends AmazonAutoLinks_UnitTypeLoader_Base {

    /**
     * Stores each unit type component directory path.
     *
     * Component specific assets are placed inside the component directory and to load them the component path needs to be known.
     * @remark  Without this declaration, the value refers to the parent one.
     * @var string
     * @since   4.2.0
     */
    static public $sDirPath = '';

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
     * URL unit specific event callbacks.
     * @param $sScriptPath
     */
    protected function _construct( $sScriptPath ) {
        // new AmazonAutoLinks_Unit_URL_Event_HTTPCacheDiminisher; // @deprecated 4.3.2
        new AmazonAutoLinks_Unit_URL_Event_RenewCacheAction;
        new AmazonAutoLinks_Unit_URL_Event_DebugOutput; // [4.4.0]
    }

    /**
     * Adds post meta boxes.
     * 
     * @since       3.3.0
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {

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

    /**
     * Determines the unit type from given output arguments.
     * @param       string      $sUnitTypeSlug
     * @param       array       $aArguments
     * @return      string
     * @since       3.5.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
        return isset( $aArguments[ 'urls' ] )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return      string
     * @since       3.5.0
     */
    protected function _getLabel() {
        return __( 'URL', 'amazon-auto-links' );
    }

}