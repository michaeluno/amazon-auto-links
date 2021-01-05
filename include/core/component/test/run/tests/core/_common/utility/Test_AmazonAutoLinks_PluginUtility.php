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
 * Tests the `AmazonAutoLinks_PluginUtility` class.
 *  
 * @package Amazon Auto Links
 * @see     AmazonAutoLinks_PluginUtility
 * @since   4.3.0
*/
class Test_AmazonAutoLinks_PluginUtility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @see  AmazonAutoLinks_PluginUtility::isAmazonURL()
     * @tags URL, url
     */
    public function test_isAmazonURL() {
        $_sURL = 'https://www.amazon.com';
        $this->_assertTrue( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
        $_sURL = 'https://www.amazon.co.uk';
        $this->_assertTrue( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
        $_sURL = 'https://www.google.com';
        $this->_assertFalse( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
        $_sURL = 'https://www.foo.amazon';
        $this->_assertFalse( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
        $_sURL = 'https://amazon.com';
        $this->_assertTrue( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
        $_sURL = 'http://amazon.com';
        $this->_assertTrue( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
        foreach( AmazonAutoLinks_Locales::getLocaleObjects() as $_oLocale ) {
            $_sURL = $_oLocale->getMarketPlaceURL();
            $this->_assertTrue( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
            $_sURL = $_oLocale->getCustomerReviewURL( '1234567890' );
            $this->_assertTrue( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
            $_sURL = $_oLocale->getProductRatingWidgetURL( '1234567890' );
            $this->_assertTrue( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
            $_sURL = $_oLocale->getAssociatesURL();
            $this->_assertTrue( AmazonAutoLinks_PluginUtility::isAmazonURL( $_sURL ), $_sURL );
        }
    }

    /**
     * @see AmazonAutoLinks_PluginUtility::scheduleTask()
     * @tags task
     */
    public function test_scheduleTask() {

        $_sActionName = 'aal_action_test';
        $_aArguments  = array( 'foo', 'bar' );
        $_bScheduled  = AmazonAutoLinks_PluginUtility::scheduleTask( $_sActionName, $_aArguments, time() );
        $this->_assertTrue( $_bScheduled, 'The task should be scheduled' );
        $this->_assertTrue( AmazonAutoLinks_PluginUtility::isTaskScheduled( $_sActionName, $_aArguments ), 'Check if the task is scheduled.' );
        AmazonAutoLinks_PluginUtility::unscheduleTask( $_sActionName, $_aArguments );
        $this->_assertFalse( AmazonAutoLinks_PluginUtility::isTaskScheduled( $_sActionName, $_aArguments ), 'Check if the task is removed.' );
    }


    /**
     * @purpose Checks if the site debug mode is enabled.
     * @return  bool
     */
    public function test_isPluginAdminPage() {
        return ! AmazonAutoLinks_PluginUtility::isPluginAdminPage();
    }

    public function test_getASINsExtracted() {
        $_sText  = 'teries-Count/dp/B00MNV8E0C/ref=sr_1_3?dchild=1' . PHP_EOL
            . '-Set/dp/B00R3Z49G6/ref=sr_1_10?dchild' . PHP_EOL
            . 'Pre-charged/dp/B00HZV9WTM/ref=sr_1_46?dchild=1&keywords=amazonbasics&pf_rd_p=9349ffb9-3aaa-476f-8532-6a4a5c3da3' . PHP_EOL;
        $_sASINs = AmazonAutoLinks_PluginUtility::getASINsExtracted( $_sText, '|' );
        if ( 'B00MNV8E0C|B00R3Z49G6|B00HZV9WTM' !== $_sASINs ) {
            throw new Exception('Could not extract ASINs correctly. ' . $_sASINs );
        }
        return true;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function test_getDegree() {
        $_sDegree = AmazonAutoLinks_PluginUtility::getDegree(
            'width',
            array(
                'width' => 50,
                'width_unit' => '%',
            )
        );
        if ( ! is_string( $_sDegree ) ) {
            throw new Exception('The degree value must be a string' );
        }
        if ( '50%' === $_sDegree ) {
            return 'Got the correct result, 50%.';
        }
        return false;
    }

}