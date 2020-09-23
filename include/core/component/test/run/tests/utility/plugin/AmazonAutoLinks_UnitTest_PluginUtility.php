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
 * Utility tests.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_PluginUtility extends AmazonAutoLinks_UnitTest_Base {

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