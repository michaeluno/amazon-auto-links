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
 * Loads the ad-widget search unit type component.
 *
 * @since 5.0.0
 */
final class AmazonAutoLinks_Unit_UnitType_Loader_ad_widget_search extends AmazonAutoLinks_Unit_UnitType_Loader_Base {

    /**
     * Stores each unit type component directory path.
     *
     * Component specific assets are placed inside the component directory and to load them the component path needs to be known.
     * @remark Without this declaration, the value refers to the parent one.
     * @var    string
     * @since  5.0.0
     */
    static public $sDirPath = '';

    /**
     * Stores the unit type slug.
     * @remark Each extended class should assign own unique unit type slug here.
     * @since  5.0.0
     */
    public $sUnitTypeSlug = 'ad_widget_search';
    
    /**
     * Stores class names of form fields.
     * @since 5.0.0
     */
    public $aFieldClasses = array(
        'AmazonAutoLinks_FormFields_AdWidgetSearchUnit_Main',
    );    
    
    /**
     * Stores protected meta key names.
     */    
    public $aProtectedMetaKeys = array();

    /**
     * Determines whether the unit type requires the PA API access.
     * @var   boolean
     * @since 5.0.0
     */
    public $bRequirePAAPI = false;

    /**
     * @param string $sScriptPath
     * @since 5.0.0
     */
    protected function _construct( $sScriptPath ) {
        self::$sDirPath = dirname( __FILE__ );
        new AmazonAutoLinks_Unit_UnitType_AdWidgetSearch_Event_Filter_ProductsFetcher;
        new AmazonAutoLinks_Unit_UnitType_AdWidgetSearch_Event_Filter_ProductsFormatter;
        new AmazonAutoLinks_Unit_UnitType_AdWidgetSearch_Event_Filter_ProductsSorter;
    }

    /**
     * @param    boolean $bRequired
     * @since    5.0.0
     * @callback add_filter()      aal_filter_unit_type_is_api_access_required_{unit type slug}
     * @return   bool
     */
    public function replyToDetermineAPIRequirement( $bRequired ) {
        return false;
    }

    /**
     * Adds post meta boxes.
     * 
     * @since 5.0.0
     */
    protected function _loadAdminComponents( $sScriptPath ) {

        new AmazonAutoLinks_Unit_UnitType_AdminPages_ad_widget_search(
            array(
                'type'      => 'transient',
                'key'       => $GLOBALS[ 'aal_transient_id' ],
                'duration'  => 60*60*24*2,
            ),
            $sScriptPath
        );

        new AmazonAutoLinks_Unit_UnitType_Admin_PostMetaBox_Main_ad_widget_search(
            null,  // meta box ID - can be null. If null is passed, the ID gets automatically generated and the class name with all lower case characters will be applied.
            __( 'Main', 'amazon-auto-links' ), // meta box title
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),
            'normal', // context (what kind of meta-box this is)
            'high' // priority
        );

    }

    /**
     * Determines the unit type from given output arguments.
     * @param  string $sUnitTypeSlug
     * @param  array  $aArguments
     * @return string
     * @since  5.0.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
        return isset( $aArguments[ 'Keywords' ] )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return string
     * @since  3.5.0
     */
    protected function _getLabel() {
        return __( 'Product Search', 'amazon-auto-links' );
    }

}