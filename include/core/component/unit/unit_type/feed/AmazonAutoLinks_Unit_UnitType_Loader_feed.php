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
 * Loads the Feed unit type components.
 *
 * The Feed unit type allows the user to display products of units created in another WordPress site installing Auto Amazon Links.
 *
 * @since 4.0.0
 * @since 5.0.0 Renamed from `AmazonAutoLinks_UnitTypeLoader_feed`.
 */
final class AmazonAutoLinks_Unit_UnitType_Loader_feed extends AmazonAutoLinks_Unit_UnitType_Loader_Base {

    /**
     * Stores each unit type component directory path.
     *
     * Component specific assets are placed inside the component directory and to load them the component path needs to be known.
     * @remark Without this declaration, the value refers to the parent one.
     * @var    string
     * @since  4.0.0
     */
    static public $sDirPath = '';

    /**
     * Stores the unit type slug.
     * @remark Each extended class should assign own unique unit type slug here.
     * @since  4.0.0
     */
    public $sUnitTypeSlug = 'feed';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array();
    
    /**
     * Stores protected meta key names.
     */    
    public $aProtectedMetaKeys = array();

    /**
     * @param string $sScriptPath
     */
    protected function _construct( $sScriptPath ) {

        self::$sDirPath = dirname( __FILE__ );

        // Events
        new AmazonAutoLinks_Unit_Feed_Event_RenewCacheAction;
        new AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsFetcher;   // 5.0.0+
        new AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsSorter;    // 5.0.0+
        new AmazonAutoLinks_Unit_Feed_Event_Filter_ProductsFormatter; // 5.0.0+

    }

    /**
     * Adds post meta boxes.
     * 
     * @since 4.0.0
     */
    protected function _loadAdminComponents( $sScriptPath ) {

        // Admin pages
        new AmazonAutoLinks_Unit_UnitType_Feed_Admin_Pages(
            array(
                'type'      => 'transient',
                'key'       => $GLOBALS[ 'aal_transient_id' ],
                'duration'  => 60*60*24*2,
            ),
            $sScriptPath
        );

        // Post meta boxes
        new AmazonAutoLinks_UnitPostMetaBox_Main_feed(
            null,
            __( 'Main', 'amazon-auto-links' ), // meta box title
            array(     // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),
            'normal', // context (what kind of metabox this is)
            'high'    // priority - e.g. 'high', 'core', 'default' or 'low'
        );

    }

    /**
     * Determines the unit type from given output arguments.
     * @param       string      $sUnitTypeSlug
     * @param       array       $aArguments
     * @return      string
     * @since       4.0.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
        return isset( $aArguments[ 'feed_urls' ] )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return      string
     * @since       4.0.0
     */
    protected function _getLabel() {
        return __( 'Feed', 'amazon-auto-links' );
    }

}