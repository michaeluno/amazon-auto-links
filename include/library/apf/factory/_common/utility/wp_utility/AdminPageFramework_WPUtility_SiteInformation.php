<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_WPUtility_SiteInformation extends AmazonAutoLinks_AdminPageFramework_WPUtility_Meta {
    public static function getSiteData($asKeys=array())
    {
        $_sWPDebugClassFilePath = ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
        if (file_exists($_sWPDebugClassFilePath)) {
            include_once($_sWPDebugClassFilePath);
        }
        if (! class_exists('WP_Debug_Data')) {
            return array();
        }
        try {
            $_mCache = self::getObjectCache(__CLASS__ . '::' . __METHOD__);
            $_aDebugData = ! isset($_mCache) ? WP_Debug_Data::debug_data() : $_mCache;
            self::setObjectCache(__CLASS__ . '::' . __METHOD__, $_aDebugData);
            return empty($asKeys) ? $_aDebugData : self::getElement($_aDebugData, $asKeys);
        } catch (Exception $_oException) {
            return array();
        }
    }
    public static function isDebugModeEnabled()
    {
        return ( bool ) defined('WP_DEBUG') && WP_DEBUG;
    }
    public static function isDebugLogEnabled()
    {
        return ( bool ) defined('WP_DEBUG_LOG') && WP_DEBUG_LOG;
    }
    public static function isDebugDisplayEnabled()
    {
        return ( bool ) defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY;
    }
    public static function getSiteLanguage($sDefault='en_US')
    {
        return defined('WPLANG') && WPLANG ? WPLANG : $sDefault;
    }
}
