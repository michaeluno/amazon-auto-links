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
 * Adds an in-page tab to an admin page.
 * 
 * @since       4.3.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_Test_AdminPage_Test_Scratch extends AmazonAutoLinks_Test_AdminPage_Test_Tests {

    /**
     * @return  array
     * @since   4.3.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'scratches',
            'title'     => 'Scratches',
        );
    }

    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    protected function _loadTab( $oAdminPage ) {
        parent::_loadTab( $oAdminPage );
    }

    /**
     * @return array
     */
    protected function _getTagLabelsForCheckBox() {
        return $this->_getTagLabels(
            AmazonAutoLinks_Test_Loader::$sDirPath . '/run/scratches',
            include( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/class-map.php' ),
            array( 'AmazonAutoLinks_Scratch_Base' )
        );
    }


    /**
     * Write scratches here to test something.
     * @callback        action      do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {
        parent::_doTab( $oFactory );

    }
        protected function _printFiles() {
            echo "<div class='files-container'>";
            echo "<h4>Files</h4>";
            $_oVerifier = new AmazonAutoLinks_Test_ClassLister(
                AmazonAutoLinks_Test_Loader::$sDirPath . '/run/scratches',
                include( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/class-map.php' ),
                array( 'AmazonAutoLinks_Scratch_Base' )
            );
            AmazonAutoLinks_Debug::dump( $_oVerifier->get() );
            echo "</div>";
        }
            
}