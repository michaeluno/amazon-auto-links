<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
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
     * @return array
     * @since  4.6.21
     */
    protected function _getFileList() {
        $_oVerifier = new AmazonAutoLinks_Test_ClassLister(
            AmazonAutoLinks_Test_Loader::$sDirPath . '/run/scratches',
            include( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/class-map.php' ),
            array( 'AmazonAutoLinks_Scratch_Base' )
        );
        return $_oVerifier->get();
    }

}