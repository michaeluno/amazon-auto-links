<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_Utility_Deprecated {
    public static function minifyCSS($sCSSRules)
    {
        AmazonAutoLinks_AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__, 'getCSSMinified()');
        return AmazonAutoLinks_AdminPageFramework_Utility_String::getCSSMinified($sCSSRules);
    }
    public static function sanitizeLength($sLength, $sUnit='px')
    {
        AmazonAutoLinks_AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__, 'getLengthSanitized()');
        return AmazonAutoLinks_AdminPageFramework_Utility_String::getLengthSanitized($sLength, $sUnit);
    }
    public static function getCorrespondingArrayValue($vSubject, $sKey, $sDefault='', $bBlankToDefault=false)
    {
        AmazonAutoLinks_AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__, 'getElement()');
        if (! isset($vSubject)) {
            return $sDefault;
        }
        if ($bBlankToDefault && $vSubject == '') {
            return $sDefault;
        }
        if (! is_array($vSubject)) {
            return ( string ) $vSubject;
        }
        if (isset($vSubject[ $sKey ])) {
            return $vSubject[ $sKey ];
        }
        return $sDefault;
    }
    public static function getArrayDimension($array)
    {
        AmazonAutoLinks_AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__);
        return (is_array(reset($array))) ? self::getArrayDimension(reset($array)) + 1 : 1;
    }
    protected function getFieldElementByKey($asElement, $sKey, $asDefault='')
    {
        AmazonAutoLinks_AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__, 'getElement()');
        if (! is_array($asElement) || ! isset($sKey)) {
            return $asElement;
        }
        $aElements = &$asElement;
        return isset($aElements[ $sKey ]) ? $aElements[ $sKey ] : $asDefault;
    }
    public static function shiftTillTrue(array $aArray)
    {
        AmazonAutoLinks_AdminPageFramework_Utility::showDeprecationNotice(__FUNCTION__);
        foreach ($aArray as &$vElem) {
            if ($vElem) {
                break;
            }
            unset($vElem);
        }
        return array_values($aArray);
    }
}
