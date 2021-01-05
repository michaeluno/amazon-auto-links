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
 * Inserts a debug output below each unit output.
 * @since   4.3.5
 */
class AmazonAutoLinks_Unit_Event_Filter_Debug_UnitOutput extends AmazonAutoLinks_PluginUtility {

    /**
     * @since  4.3.5
     * @remark Already checked if the plugin debug mode is turned on.
     */
    public function __construct() {
        add_filter( 'aal_filter_unit_output', array( $this, 'replyToInsertDebugOutput' ), 10, 5 );
    }

    /**
     * @param    string $sOutputHTML
     * @param    array  $aUnitOptions
     * @param    string $sTemplatePath
     * @param    array  $aPluginOptions
     * @param    array  $aProducts
     * @return   string
     * @since    4.3.5
     * @callback add_filter() aal_filter_unit_output
     */
    public function replyToInsertDebugOutput( $sOutputHTML, $aUnitOptions, $sTemplatePath, $aPluginOptions, $aProducts ) {
        if ( $this->getElement( $aUnitOptions, array( 'is_preview' ) ) ) {
            return $sOutputHTML;
        }
        return $sOutputHTML . $this->___getUnitDebugInformation( $aUnitOptions, $sTemplatePath, $aPluginOptions, $aProducts );
    }
        /**
         * Prints out unit debug information.
         *
         * @param  array  $aUnitArguments
         * @param  string $sTemplatePath
         * @param  array  $aPluginOptions
         * @param  array  $aProducts
         * @return string
         * @since  4.3.5  Moved from `AmazonAutoLinks_UnitOutput__DebugInformation_Unit`.
         * @since  3.5.0
         */
        private function ___getUnitDebugInformation( $aUnitArguments, $sTemplatePath, $aPluginOptions, $aProducts ) {
            return "<pre style='height: 300px; overflow-y: scroll; overflow-x: auto; padding: 1em'>"
                . "<h4>Debug Info - Unit</h4>"
                . "<h5>Template Path</h5>"
                . AmazonAutoLinks_Debug::get( $sTemplatePath )
                . "<h5>Unit Arguments</h5>"
                . AmazonAutoLinks_Debug::getDetails( $aUnitArguments )
                . "<h5>Plugin Options</h5>"
                . AmazonAutoLinks_Debug::getDetails( $aPluginOptions )
                . "<span style='float:right;'>"
                    . AmazonAutoLinks_Registry::NAME
                    . ' ' . AmazonAutoLinks_Registry::VERSION
                    . "</span>"
                . "</pre>";
       }
}