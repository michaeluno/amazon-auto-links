<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Loads the `contextual` unit type.
 *
 * @since 3.5.0
 * @since 5.0.0 Renamed from `AmazonAutoLinks_UnitTypeLoader_contextual`.
*/
final class AmazonAutoLinks_Unit_UnitType_Loader_contextual extends AmazonAutoLinks_Unit_UnitType_Loader_Base {

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
     * @since       3.5.0
     */
    public $sUnitTypeSlug = 'contextual';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array();
    
    /**
     * Stores protected meta key names.
     */    
    public $aProtectedMetaKeys = array(
    );    
    
    /**
     * Adds post meta boxes.
     * 
     * @since       3.5.0
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {

        // Admin pages
        new AmazonAutoLinks_ContextualUnitAdminPage(
            array(
                'type'      => 'transient',
                'key'       => $GLOBALS[ 'aal_transient_id' ],
                'duration'  => 60*60*24*2,
            ),
            $sScriptPath
        );         
              
        // Post meta boxes
        new AmazonAutoLinks_UnitPostMetaBox_Main_contextual(
            null,
            __( 'Main', 'amazon-auto-links' ), // meta box title
            array(     // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),
            'normal', // context (what kind of metabox this is)
            'high'    // priority - e.g. 'high', 'core', 'default' or 'low'
        );

        new AmazonAutoLinks_UnitPostMetaBox_Advanced_contextual(
            null,
            __( 'Advanced', 'amazon-auto-links' ), // meta box title
            array(     // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),
            'normal', // context (what kind of metabox this is)
            'default'    // priority - e.g. 'high', 'core', 'default' or 'low'
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
        return isset( $aArguments[ 'criteria' ], $aArguments[ 'additional_keywords' ] )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return      string
     * @since       3.5.0
     */
    protected function _getLabel() {
        return __( 'Contextual', 'amazon-auto-links' );
    }

}