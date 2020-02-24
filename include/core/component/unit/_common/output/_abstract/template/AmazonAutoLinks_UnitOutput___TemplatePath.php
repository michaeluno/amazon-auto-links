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
 * A class that provides method to retrieve the template path.
 *
 * @since       3.5.0
 * @deprecated  4.0.2   Use `AmazonAutoLinks_UnitOutput__Template` instead.
 */
class AmazonAutoLinks_UnitOutput__TemplatePath extends AmazonAutoLinks_PluginUtility {

    private $___aUnitArguments = array();

    /**
     * Sets up properties.
     *
     * @param array $aUnitArguments
     */
    public function __construct( array $aUnitArguments ) {

        $this->___aUnitArguments = $aUnitArguments;

    }

    public function get( $sTemplatePath=null ) {
        $_sTemplatePath = isset( $sTemplatePath )
            ? $sTemplatePath
            : $this->___getTemplatePath( $this->___aUnitArguments );
        $_sTemplatePath = wp_normalize_path( $_sTemplatePath );
        return apply_filters( "aal_filter_template_path", $_sTemplatePath, $this->___aUnitArguments );
    }
    /**
     * Finds the template path from the given arguments(unit options).
     *
     * The keys that can determine the template path are template, template_id, template_path.
     *
     * The template_id key is automatically assigned when creating a unit. If the template_path is explicitly set and the file exists, it will be used.
     *
     * The template key is a user friendly one and it should point to the name of the template. If multiple names exist, the first item will be used.
     *
     * @return      string
     * @since       unknown
     * @since       3.5.0       Renamed from `getTemplatePath()`.
     * @since       3.5.0       Changed the visibility scope from protected.
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base`.
     */
    private function ___getTemplatePath( $aArguments ) {

        // Case: a template path is given.
        if ( isset( $aArguments[ 'template_path' ] ) && file_exists( $aArguments[ 'template_path' ] ) ) {
            return $aArguments[ 'template_path' ];
        }

        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();

        // Case: a template name is given.
        if ( isset( $aArguments[ 'template' ] ) && $aArguments[ 'template' ] ) {
            foreach( $_oTemplateOption->getActiveTemplates() as $_aTemplate ) {
                if ( strtolower( $_aTemplate[ 'name' ] ) === strtolower( trim( $aArguments[ 'template' ] ) ) ) {
                    return $_aTemplate[ 'template_path' ];
                }
            }
        }

        // Case: a template ID is given.
        // @remark even if the template is found, if it is not activated, return the default template
        // For JSON and RSS feed outputs, they are given the path with the `template_path` argument, in that case, it does not matter whether the template is activated via the UI.
        $_snTemplateID = $this->getElement( $aArguments, array( 'template_id' ) );
        if ( $_oTemplateOption->isActive( $_snTemplateID ) ) {
            $_sTemplatePath = $_oTemplateOption->getPathFromID( $_snTemplateID );
            if ( $_sTemplatePath && file_exists( $_sTemplatePath ) ) {
                return $_sTemplatePath;
            }
        }

        // Not found. In that case, use the default one.
        $_sUnitType   = $this->getElement( $aArguments, 'unit_type' );
        $_oOption     = AmazonAutoLinks_Option::getInstance();
        $_sTemplateID = $_oOption->get( array( 'unit_default', 'template_id' ), '' );
        $_sTemplateID = $_sTemplateID
            ? $_sTemplateID
            : $_oTemplateOption->getDefaultTemplateIDByUnitType( $_sUnitType );
        return $_oTemplateOption->getPathFromID( $_sTemplateID );

    }


}
