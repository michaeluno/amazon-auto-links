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
 * A class that injects debug information in an overall unit output.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput__DebugInformation_Unit extends AmazonAutoLinks_UnitOutput__DebugInformation_Base {

    /**
     * @return array
     */
    protected function _getFilterArguments() {
        return array(
            array(
                'aal_filter_unit_output',
                array( $this, '_replyToInsertDebugOutput' ),
                10,  // priority
                5    // number of parameters
            ),
        );
    }

    /**
     * @return          string
     * @callback        add_filter      aal_filter_unit_output
     */
    public function _replyToInsertDebugOutput( $sContent, $aUnitOptions, $sTemplatePath, $aPluginOptions, $aProducts ) {
        return $sContent . $this->___getUnitDebugInformation( $aUnitOptions, $sTemplatePath, $aPluginOptions, $aProducts );
    }
        /**
        * Prints out unit debug information.
        * @since       3.5.0
        */
       private function ___getUnitDebugInformation( $aUnitArguments, $sTemplatePath, $aPluginOptions, $aProducts ) {
           return "<pre style='height: 300px; overflow-y: scroll; overflow-x: auto; padding: 0 1em'>"
               . "<h3>Debug Info</h3>"
               . "<h4>Template Path</h4>"
               . AmazonAutoLinks_Debug::get( $sTemplatePath )
               . "<h4>Unit Arguments</h4>"
               . AmazonAutoLinks_Debug::get( $aUnitArguments )
               . "<h4>Plugin Options</h4>"
               . AmazonAutoLinks_Debug::get( $aPluginOptions )
               . "<h4>Fetched Data</h4>"
               . AmazonAutoLinks_Debug::get( $aProducts )
               . "<span style='float:right;'>"
                   . AmazonAutoLinks_Registry::NAME
                   . ' ' . AmazonAutoLinks_Registry::VERSION
                   . "</span>"
               . "</pre>";
       }

}