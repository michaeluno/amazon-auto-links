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
 * Utility tests.
 *  
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_FrameworkUtility extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @return bool
     * @tags number
     */
    public function test_getNumberFixed() {
        return 100 === $this->getNumberFixed( 156, 4, 3, 100 );
    }
    /**
     * @return bool
     * @tags number
     */
    public function test_getNumberFixed2() {
        return 3 === $this->getNumberFixed( 1, 4, 3, 100 );
    }

    /**
     * @return bool
     * @tags number
     */
    public function test_getNumberFixed3() {
        return 4 === $this->getNumberFixed( '0xFFF', 4, 3, 100 );
    }

    /**
     * @purpose Checking if the deprecated name can still be used.
     * @return bool
     * @tags number
     */
    public function test_fixNumber() {
        return 4 === $this->fixNumber( '0xFFF', 4, 3, 100 );
    }

    /**
     * @return bool
     * @tags path
     */
    public function test_getRelativePath() {
        $_sDirBaseName  = basename( dirname( __FILE__ ) );
        $_sFileBaseName = basename( __FILE__ );
        return "./{$_sDirBaseName}/{$_sFileBaseName}" === $this->getRelativePath( dirname( dirname( __FILE__ ) ), __FILE__ );
    }


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