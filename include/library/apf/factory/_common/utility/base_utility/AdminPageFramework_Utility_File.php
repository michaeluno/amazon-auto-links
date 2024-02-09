<?php
/*
 * Admin Page Framework v3.9.2b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2023, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_Utility_File extends AmazonAutoLinks_AdminPageFramework_Utility_URL {
    public static function getFileTailContents($asPath=array(), $iLines=1)
    {
        $_sPath = self::_getFirstItem($asPath);
        if (! @is_readable($_sPath)) {
            return '';
        }
        return trim(implode('', array_slice(file($_sPath), - $iLines)));
    }
    private static function _getFirstItem($asItems)
    {
        $_aItems = is_array($asItems) ? $asItems : array( $asItems );
        $_aItems = array_values($_aItems);
        return ( string ) array_shift($_aItems);
    }
    public static function sanitizeFileName($sFileName, $sReplacement='_')
    {
        $sFileName = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", $sReplacement, $sFileName);
        return preg_replace("([\.]{2,})", '', $sFileName);
    }
}
