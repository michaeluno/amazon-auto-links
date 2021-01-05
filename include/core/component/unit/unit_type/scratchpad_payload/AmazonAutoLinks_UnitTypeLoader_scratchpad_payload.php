<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the units component.
 *  
 * @package     Amazon Auto Links
 * @since       4.1.0
*/
class AmazonAutoLinks_UnitTypeLoader_scratchpad_payload extends AmazonAutoLinks_UnitTypeLoader_Base {

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
     * @since       4.1.0
     */
    public $sUnitTypeSlug = 'scratchpad_payload';


    /**
     * @param $sScriptPath
     * @since   4.1.0
     */
    protected function _construct( $sScriptPath ) {
        self::$sDirPath = dirname( __FILE__ );
    }

    /**
     * Adds post meta boxes.
     * 
     * @since       4.1.0
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {

        // Admin pages
        new AmazonAutoLinks_ScratchPadPayloadUnitAdminPage(
            array(
                'type'      => 'transient',
                'key'       => $GLOBALS[ 'aal_transient_id' ],
                'duration'  => 60*60*24*2,
            ),
            $sScriptPath
        );
        
        // Post meta boxes
        new AmazonAutoLinks_UnitPostMetaBox_Main_scratchpad_payload(
            null,   // meta box ID - null for auto-generate
            __( 'ScratchPad Payload Main', 'amazon-auto-links' ),
            array( // post type slugs: post, page, etc.
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            ),
            'normal', // context - e.g. 'normal', 'advanced', or 'side'
            'high'  // priority - e.g. 'high', 'core', 'default' or 'low'
        );
//        new AmazonAutoLinks_UnitPostMetaBox_Advanced_search(
//            null,   // meta box ID - null for auto-generate
//            __( 'Product Search Advanced', 'amazon-auto-links' ),
//            array( // post type slugs: post, page, etc.
//                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
//            ),
//            'normal', // context - e.g. 'normal', 'advanced', or 'side'
//            'low' // priority - e.g. 'high', 'core', 'default' or 'low'
//        );
        
    }     

    /**
     * Determines the unit type from given output arguments.
     * @param       string      $sUnitTypeSlug
     * @param       array       $aArguments
     * @return      string
     * @since       4.1.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
        return in_array( $this->_getOperationArgument( $aArguments ), array( 'payload', ) )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return      string
     * @since       4.1.0
     */
    protected function _getLabel() {
        return __( 'ScratchPad Payload', 'amazon-auto-links' );
    }

}