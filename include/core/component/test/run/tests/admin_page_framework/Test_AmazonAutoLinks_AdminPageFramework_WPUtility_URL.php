<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Tests AmazonAutoLinks_AdminPageFramework_WPUtility_URL methods.
 *  
 * @see     AmazonAutoLinks_AdminPageFramework_WPUtility_URL
 * @since   4.7.0
*/
class Test_AmazonAutoLinks_AdminPageFramework_WPUtility_URL extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags url
     */
    public function test_url() {
        $this->_assertTrue( plugins_url( basename( __FILE__ ), __FILE__ ) === $this->getSRCFromPath( __FILE__ ) );
    }

}