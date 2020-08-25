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
class AmazonAutoLinks_UnitTest_FrameworkUtility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @purpose Checks if the site debug mode is enabled.
     * @return bool
     */
    public function test_isDebugModeEnabled() {
        return AmazonAutoLinks_PluginUtility::isDebugModeEnabled();
    }
    /**
     * @purpose Checks if the site debug log is enabled.
     * @return bool
     */
    public function test_isDebugLogEnabled() {
        return AmazonAutoLinks_PluginUtility::isDebugLogEnabled();
    }
    /**
     * @purpose Checks if the site debug display is enabled.
     * @return bool
     */
    public function test_isDebugDisplayEnabled() {
        return AmazonAutoLinks_PluginUtility::isDebugDisplayEnabled();
    }


}