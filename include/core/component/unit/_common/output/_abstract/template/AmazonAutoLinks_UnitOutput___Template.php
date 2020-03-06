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
 * Determines the template to be used from unit arguments.
 *
 * @since       4.0.2
 */
class AmazonAutoLinks_UnitOutput__Template extends AmazonAutoLinks_PluginUtility {

    /**
     * @var AmazonAUtoLinks_UnitOption_Base
     */
    private $___oUnitOption = array();

    /**
     * Sets up properties.
     *
     * @param AmazonAUtoLinks_UnitOption_Base
     */
    public function __construct( AmazonAUtoLinks_UnitOption_Base $oUnitOption ) {

        $this->___oUnitOption = $oUnitOption;

    }

    /**
     * @return string the determined template ID.
     */
    public function get() {

        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();

        // Case: `template_path` is specified.
        $_sTemplatePath = isset( $this->___oUnitOption->aUnitOptions[ 'template_path' ] )
            ? $this->___oUnitOption->aUnitOptions[ 'template_path' ]
            : '';
        if ( $_sTemplatePath ) {
            $_sTemplateID = $_oTemplateOption->getIDFromPath( $_sTemplatePath );
            if ( $_sTemplateID ) {
                return $_sTemplateID;
            }
        }

        // Case: a template name is given.
        $_sTemplateName = isset( $this->___oUnitOption->aUnitOptions[ 'template' ] )
            ? $this->___oUnitOption->aUnitOptions[ 'template' ]
            : '';
        if ( $_sTemplateName ) {
            $_sTemplateID = $_oTemplateOption->getIDFromName(  $_sTemplateName );
            if ( $_sTemplateID ) {
                return $_sTemplateID;
            }
        }

        // Case: a template ID is given.
        // @remark even if the template is found, if it is not activated, return the default template
        // For JSON and RSS feed outputs, they are given the path with the `template_path` argument,
        // in that case, it does not matter whether the template is activated via the UI.
        $_sTemplateID  = isset( $this->___oUnitOption->aUnitOptions[ 'template_id' ] )
            ? ( string ) $this->___oUnitOption->aUnitOptions[ 'template_id' ]
            : '';
        $_sTemplateID  = untrailingslashit( $_sTemplateID );     // for backward compatibility
        if ( $_oTemplateOption->isActive( $_sTemplateID ) ) {
            return $_sTemplateID;
        }

        // Not found. In that case, use the default one.
        $_oOption     = AmazonAutoLinks_Option::getInstance();
        $_sTemplateID = $_oOption->get( array( 'unit_default', 'template_id' ), '' );
        return $_sTemplateID
            ? untrailingslashit( $_sTemplateID )    // for backward compatibility
            : $_oTemplateOption->getDefaultTemplateIDByUnitType( $this->___oUnitOption->sUnitType );

    }

}
