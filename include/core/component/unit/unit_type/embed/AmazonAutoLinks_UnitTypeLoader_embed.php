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
 * Loads the Embed unit type components.
 *
 * The Embed unit type is called from the plugin custom oEmbed outputs.
 * There is the `uri` argument and it holds encoded URL passed from the oEmbed iframe request.
 *
 * @package     Amazon Auto Links
 * @since       4.0.0
*/
class AmazonAutoLinks_UnitTypeLoader_embed extends AmazonAutoLinks_Unit_UnitType_Loader_Base {

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
     * @since       4.0.0
     */
    public $sUnitTypeSlug = 'embed';
    
    /**
     * Stores class names of form fields.
     */
    public $aFieldClasses = array();
    
    /**
     * Stores protected meta key names.
     */    
    public $aProtectedMetaKeys = array();

    protected function _construct( $sScriptPath ) {}

    /**
     * Adds post meta boxes.
     * 
     * @since       4.0.0
     * @return      void
     */
    protected function _loadAdminComponents( $sScriptPath ) {}

    /**
     * Determines the unit type from given output arguments.
     * @param       string      $sUnitTypeSlug
     * @param       array       $aArguments
     * @return      string
     * @since       4.0.0
     */
    protected function _getUnitTypeSlugByOutputArguments( $sUnitTypeSlug, $aArguments ) {
        return isset( $aArguments[ 'uri' ] )
            ? $this->sUnitTypeSlug
            : $sUnitTypeSlug;
    }

    /**
     * @return      string
     * @since       4.0.0
     */
    protected function _getLabel() {
        return __( 'Embed', 'amazon-auto-links' );
    }

}