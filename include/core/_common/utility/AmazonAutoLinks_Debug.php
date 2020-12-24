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
 * Provides utility methods that uses WordPerss built-in functions.
 *
 * @since   1
 * @since   3     Made it extend `AmazonAutoLinks_AdminPageFramework_Debug`.
 */
class AmazonAutoLinks_Debug extends AmazonAutoLinks_AdminPageFramework_Debug {

    /**
     * Logs the given variable output to a file.
     *
     * @param  mixed   $mValue The value to log.
     * @param  string  $sFilePath The log file path.
     * @param  boolean $bStackTrace The log file path.
     * @param  integer $iTrace
     * @param  integer $iStringLengthLimit
     * @param  integer $iArrayDepthLimit
     * @return void
     */
    static public function log( $mValue, $sFilePath=null, $bStackTrace=false, $iTrace=0, $iStringLengthLimit=99999, $iArrayDepthLimit=50 ) {

        self::$iLegibleStringCharacterLimit = PHP_INT_MAX;
        self::$iLegibleArrayDepthLimit = PHP_INT_MAX;
        parent::log( $mValue, $sFilePath, $bStackTrace, $iTrace + 1, $iStringLengthLimit, $iArrayDepthLimit );

    }

}