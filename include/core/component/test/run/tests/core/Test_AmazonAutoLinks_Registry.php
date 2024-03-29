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
 * Tests AmazonAutoLinks_AdminPageFramework_WPUtility_URL methods.
 *  
 * @see     AmazonAutoLinks_Registry
 * @since   4.7.0
*/
class Test_AmazonAutoLinks_Registry extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags url
     */
    public function test_getPluginURL() {
        $this->_outputDetails( 'AmazonAutoLinks_Registry::getPluginURL( __FILE__ , true )',  AmazonAutoLinks_Registry::getPluginURL( __FILE__, true ) );
        $this->_outputDetails( '$this->getSRCFromPath( __FILE__ )', $this->getSRCFromPath( __FILE__ ) );
        $this->_assertTrue( AmazonAutoLinks_Registry::getPluginURL( __FILE__, true ) === $this->getSRCFromPath( __FILE__ ) );
    }

}