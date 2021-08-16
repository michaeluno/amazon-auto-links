<?php 
/**
	Admin Page Framework v3.9.0b04 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Debug extends AmazonAutoLinks_AdminPageFramework_Debug_Log {
    static public function dump($asArray, $sFilePath = null, $bStackTrace = false, $iStringLengthLimit = 0, $iArrayDepthLimit = 0) {
        echo self::get($asArray, $sFilePath, true, $bStackTrace, $iStringLengthLimit, $iArrayDepthLimit);
    }
    static public function getDetails($mValue, $bEscape = true, $bStackTrace = false, $iStringLengthLimit = 0, $iArrayDepthLimit = 0) {
        $_sValueWithDetails = self::_getArrayRepresentationSanitized(self::_getLegibleDetails($mValue, $iStringLengthLimit, $iArrayDepthLimit));
        $_sValueWithDetails = $bStackTrace ? $_sValueWithDetails . PHP_EOL . self::getStackTrace() : $_sValueWithDetails;
        return $bEscape ? "<pre class='dump-array'>" . htmlspecialchars($_sValueWithDetails) . "</pre>" : $_sValueWithDetails;
    }
    static public function get($asArray, $sFilePath = null, $bEscape = true, $bStackTrace = false, $iStringLengthLimit = 0, $iArrayDepthLimit = 0) {
        if ($sFilePath) {
            self::log($asArray, $sFilePath);
        }
        $_sContent = self::_getLegible($asArray, $iStringLengthLimit, $iArrayDepthLimit) . ($bStackTrace ? PHP_EOL . self::getStackTrace() : '');
        return $bEscape ? "<pre class='dump-array'>" . htmlspecialchars($_sContent) . "</pre>" : $_sContent;
    }
    static public function log($mValue, $sFilePath = null, $bStackTrace = false, $iTrace = 0, $iStringLengthLimit = 99999, $iArrayDepthLimit = 50) {
        self::_log($mValue, $sFilePath, $bStackTrace, $iTrace, $iStringLengthLimit, $iArrayDepthLimit);
    }
    static public function dumpArray($asArray, $sFilePath = null) {
        self::showDeprecationNotice('AmazonAutoLinks_AdminPageFramework_Debug::' . __FUNCTION__, 'AmazonAutoLinks_AdminPageFramework_Debug::dump()');
        AmazonAutoLinks_AdminPageFramework_Debug::dump($asArray, $sFilePath);
    }
    static public function getArray($asArray, $sFilePath = null, $bEscape = true) {
        self::showDeprecationNotice('AmazonAutoLinks_AdminPageFramework_Debug::' . __FUNCTION__, 'AmazonAutoLinks_AdminPageFramework_Debug::get()');
        return AmazonAutoLinks_AdminPageFramework_Debug::get($asArray, $sFilePath, $bEscape);
    }
    static public function logArray($asArray, $sFilePath = null) {
        self::showDeprecationNotice('AmazonAutoLinks_AdminPageFramework_Debug::' . __FUNCTION__, 'AmazonAutoLinks_AdminPageFramework_Debug::log()');
        AmazonAutoLinks_AdminPageFramework_Debug::log($asArray, $sFilePath);
    }
    static public function getAsString($mValue) {
        self::showDeprecationNotice('AmazonAutoLinks_AdminPageFramework_Debug::' . __FUNCTION__);
        return self::_getLegible($mValue);
    }
    }
    