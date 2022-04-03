<?php
/*
 * Admin Page Framework v3.9.1b05 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_WPUtility_File extends AmazonAutoLinks_AdminPageFramework_WPUtility_Hook {
    public static function getScriptData($sPathOrContent, $sType='plugin', $aDefaultHeaderKeys=array())
    {
        $_aHeaderKeys = $aDefaultHeaderKeys + array( 'sName' => 'Name', 'sURI' => 'URI', 'sScriptName' => 'Script Name', 'sLibraryName' => 'Library Name', 'sLibraryURI' => 'Library URI', 'sPluginName' => 'Plugin Name', 'sPluginURI' => 'Plugin URI', 'sThemeName' => 'Theme Name', 'sThemeURI' => 'Theme URI', 'sVersion' => 'Version', 'sDescription' => 'Description', 'sAuthor' => 'Author', 'sAuthorURI' => 'Author URI', 'sTextDomain' => 'Text Domain', 'sDomainPath' => 'Domain Path', 'sNetwork' => 'Network', '_sitewide' => 'Site Wide Only', );
        $aData = file_exists($sPathOrContent) ? get_file_data($sPathOrContent, $_aHeaderKeys, $sType) : self::getScriptDataFromContents($sPathOrContent, $sType, $_aHeaderKeys);
        switch (trim($sType)) { case 'theme': $aData['sName'] = $aData['sThemeName']; $aData['sURI'] = $aData['sThemeURI']; break; case 'library': $aData['sName'] = $aData['sLibraryName']; $aData['sURI'] = $aData['sLibraryURI']; break; case 'script': $aData['sName'] = $aData['sScriptName']; break; case 'plugin': $aData['sName'] = $aData['sPluginName']; $aData['sURI'] = $aData['sPluginURI']; break; default: break; }
        return $aData;
    }
    public static function getScriptDataFromContents($sContent, $sType='plugin', $aDefaultHeaderKeys=array())
    {
        $sContent = str_replace("\r", "\n", $sContent);
        $_aHeaders = $aDefaultHeaderKeys;
        if ($sType) {
            $_aExtraHeaders = apply_filters("extra_{$sType}_headers", array());
            if (! empty($_aExtraHeaders)) {
                $_aExtraHeaders = array_combine($_aExtraHeaders, $_aExtraHeaders);
                $_aHeaders = array_merge($_aExtraHeaders, ( array ) $aDefaultHeaderKeys);
            }
        }
        foreach ($_aHeaders as $_sHeaderKey => $_sRegex) {
            $_bFound = preg_match('/^[ \t\/*#@]*' . preg_quote($_sRegex, '/') . ':(.*)$/mi', $sContent, $_aMatch);
            $_aHeaders[ $_sHeaderKey ] = $_bFound && $_aMatch[ 1 ] ? _cleanup_header_comment($_aMatch[ 1 ]) : '';
        }
        return $_aHeaders;
    }
    public static function download($sURL, $iTimeOut=300)
    {
        if (false === filter_var($sURL, FILTER_VALIDATE_URL)) {
            return false;
        }
        $_sTmpFileName = self::setTempPath(self::getBaseNameOfURL($sURL));
        if (! $_sTmpFileName) {
            return false;
        }
        $_aoResponse = wp_safe_remote_get($sURL, array( 'timeout' => $iTimeOut, 'stream' => true, 'filename' => $_sTmpFileName ));
        if (is_wp_error($_aoResponse)) {
            unlink($_sTmpFileName);
            return false;
        }
        if (200 != wp_remote_retrieve_response_code($_aoResponse)) {
            unlink($_sTmpFileName);
            return false;
        }
        $_sContent_md5 = wp_remote_retrieve_header($_aoResponse, 'content-md5');
        if ($_sContent_md5) {
            $_boIsMD5 = verify_file_md5($_sTmpFileName, $_sContent_md5);
            if (is_wp_error($_boIsMD5)) {
                unlink($_sTmpFileName);
                return false;
            }
        }
        return $_sTmpFileName;
    }
    public static function setTempPath($sFilePath='')
    {
        $_sDir = get_temp_dir();
        $sFilePath = basename($sFilePath);
        if (empty($sFilePath)) {
            $sFilePath = time() . '.tmp';
        }
        $sFilePath = $_sDir . wp_unique_filename($_sDir, $sFilePath);
        touch($sFilePath);
        return $sFilePath;
    }
    public static function getBaseNameOfURL($sURL)
    {
        $_sPath = parse_url($sURL, PHP_URL_PATH);
        $_sFileBaseName = basename($_sPath);
        return $_sFileBaseName;
    }
}
