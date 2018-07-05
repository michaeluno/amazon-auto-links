<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * A class that provides method to retrieve the template path.
 *
 * @since       3.5.0
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
        return apply_filters(
            "aal_filter_template_path",
            isset( $sTemplatePath )
                ? $sTemplatePath
                : $this->___getTemplatePath( $this->___aUnitArguments ),
            $this->___aUnitArguments
        );
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

        // If it is set in a request, use it.
        if ( isset( $aArguments[ 'template_path' ] ) && file_exists( $aArguments[ 'template_path' ] ) ) {
            return $aArguments[ 'template_path' ];
        }

        $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();

        // If a template name is given in a request
        if ( isset( $aArguments[ 'template' ] ) && $aArguments[ 'template' ] ) {
            foreach( $_oTemplateOption->getActiveTemplates() as $_aTemplate ) {
                if ( strtolower( $_aTemplate[ 'name' ] ) == strtolower( trim( $aArguments[ 'template' ] ) ) ) {
                    return $_aTemplate[ 'template_path' ];
                }
            }
        }

        // If a template ID is given,
        if ( isset( $aArguments[ 'template_id' ] ) && $aArguments[ 'template_id' ] ) {
            foreach( $_oTemplateOption->getActiveTemplates() as $_sID => $_aTemplate ) {
                if ( $_sID == trim( $aArguments[ 'template_id' ] ) ) {
                    return $_aTemplate[ 'template_path' ];
                }
            }
        }

        // Not found. In that case, use the default one.
        return $this->___getDefaultTemplatePath();

    }
        /**
         *
         * @remark      Each unit has to define its own default template.
         * @since       3
         * @since       3.5.0       Renamed from `getDefaultTemplatePath()`.
         * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base`.
         * @return      string
         */
        private function ___getDefaultTemplatePath() {
            $_oTemplateOption = AmazonAutoLinks_TemplateOption::getInstance();
            $_aTemplate = $_oTemplateOption->getTemplateArrayByDirPath(
                AmazonAutoLinks_Registry::$sDirPath
                . DIRECTORY_SEPARATOR . 'template'
                . DIRECTORY_SEPARATOR . 'category',
                false       // no extra info
            );
            return $_aTemplate[ 'dir_path' ] . '/template.php' ;
        }

}
