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
 * Loads the units component.
 *
 * @since 4.1.0
 * @since 5.0.0 Renamed from `AmazonAutoLinks_UnitTypeLoader_scratchpad_payload`.
*/
final class AmazonAutoLinks_Unit_UnitType_Loader_scratchpad_payload extends AmazonAutoLinks_Unit_UnitType_Loader_search {

    /**
     * Stores each unit type component directory path.
     *
     * Component specific assets are placed inside the component directory and to load them the component path needs to be known.
     * @remark  Without this declaration, the value refers to the parent one.
     * @var     string
     * @since   4.2.0
     */
    static public $sDirPath = '';

    /**
     * Stores the unit type slug.
     * @remark Each extended class should assign own unique unit type slug here.
     * @since  4.1.0
     */
    public $sUnitTypeSlug = 'scratchpad_payload';

    /**
     * @var   string
     * @since 5.0.0
     */
    protected $_sPAAPIOperation = 'payload';

    /**
     * @param string $sScriptPath
     * @since 4.1.0
     */
    protected function _construct( $sScriptPath ) {
        self::$sDirPath = dirname( __FILE__ );

        if ( 'scratchpad_payload' === $this->sUnitTypeSlug ) {
            new AmazonAutoLinks_Unit_PAAPICustomPayload_Event_Filter_ProductsFetcher;
            new AmazonAutoLinks_Unit_PAAPICustomPayload_Event_Filter_ProductsFormatter;
            new AmazonAutoLinks_Unit_PAAPICustomPayload_Event_Filter_ProductsSorter;
        }
        parent::_construct( $sScriptPath );
    }

    /**
     * @param    string $sURL
     * @param    array  $aInputs
     * @since    5.0.0
     * @return   string
     * @callback add_filter() aal_filter_admin_unit_paapi_unit_types_unit_creation_page_url
     */
    public function replyToGetUnitCreationPageURL( $sURL, $aInputs ) {
        if ( $aInputs[ 'Operation' ] !== $this->_sPAAPIOperation ) {
            return $sURL;
        }
        return add_query_arg(
            array(
                'tab'          => 'custom_payload',
                'transient_id' => $aInputs[ 'transient_id' ],
           ) + $this->getHTTPQueryGET(),
           $aInputs[ 'bounce_url' ]
        );
    }

    /**
     * Adds post meta boxes.
     * 
     * @since 4.1.0
     */
    protected function _loadAdminComponents( $sScriptPath ) {

        // Admin pages

        // @deprecated 5.0.0
        // new AmazonAutoLinks_ScratchPadPayloadUnitAdminPage(
        //     array(
        //         'type'      => 'transient',
        //         'key'       => $GLOBALS[ 'aal_transient_id' ],
        //         'duration'  => 60*60*24*2,
        //     ),
        //     $sScriptPath
        // );

        new AmazonAutoLinks_ScratchPadUnit_Admin_Page;

        // Post meta boxes
        new AmazonAutoLinks_UnitPostMetaBox_Main_scratchpad_payload(
            null,   // meta box ID - null for auto-generate
            __( 'Custom Payload Main', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),
            'normal', // context - e.g. 'normal', 'advanced', or 'side'
            'high'  // priority - e.g. 'high', 'core', 'default' or 'low'
        );
       // new AmazonAutoLinks_UnitPostMetaBox_Advanced_search(
       //     null,   // meta box ID - null for auto-generate
       //     __( 'Product Search Advanced', 'amazon-auto-links' ),
       //     array( // post type slugs: post, page, etc.
       //         AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
       //     ),
       //     'normal', // context - e.g. 'normal', 'advanced', or 'side'
       //     'low' // priority - e.g. 'high', 'core', 'default' or 'low'
       // );
        
    }     

    /**
     * Determines the unit type from given output arguments.
     * @param  string $sUnitTypeSlug
     * @param  array  $aArguments
     * @return string
     * @since  4.1.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
        return $this->_getOperationArgument( $aArguments ) === $this->_sPAAPIOperation
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return string
     * @since  4.1.0
     */
    protected function _getLabel() {
        return __( 'PA-API Custom Payload', 'amazon-auto-links' );
    }

    /**
     * @since  5.0.0
     * @return string
     */
    protected function _getShortName() {
        return __( 'Custom Payload', 'amazon-auto-links' );
    }

    /**
     * @return string
     * @since  5.0.0
     */
    protected function _getDescription() {
        return sprintf(
            __( 'Returns items using a custom payload generated with <a target="_blank" href="%1$s">ScratchPad</a>.', 'amazon-auto-links' ),
            esc_url( 'https://webservices.amazon.com/paapi5/scratchpad/' )
        );
    }

}