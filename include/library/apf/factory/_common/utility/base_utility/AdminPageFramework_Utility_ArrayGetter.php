<?php
/**
 Admin Page Framework v3.7.5b01 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/amazon-auto-links>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
abstract class AmazonAutoLinks_AdminPageFramework_Utility_ArrayGetter extends AmazonAutoLinks_AdminPageFramework_Utility_Array {
    static public function getFirstElement(array $aArray) {
        foreach ($aArray as $_mElement) {
            return $_mElement;
        }
    }
    static public function getElement($aSubject, $aisKey, $mDefault = null, $asToDefault = array(null)) {
        $_aToDefault = is_null($asToDefault) ? array(null) : self::getAsArray($asToDefault, true);
        $_mValue = self::getArrayValueByArrayKeys($aSubject, self::getAsArray($aisKey, true), $mDefault);
        return in_array($_mValue, $_aToDefault, true) ? $mDefault : $_mValue;
    }
    static public function getElementAsArray($aSubject, $aisKey, $mDefault = null, $asToDefault = array(null)) {
        return self::getAsArray(self::getElement($aSubject, $aisKey, $mDefault, $asToDefault), true);
    }
    static public function getIntegerKeyElements(array $aParse) {
        foreach ($aParse as $_isKey => $_v) {
            if (!is_numeric($_isKey)) {
                unset($aParse[$_isKey]);
                continue;
            }
            $_isKey = $_isKey + 0;
            if (!is_int($_isKey)) {
                unset($aParse[$_isKey]);
            }
        }
        return $aParse;
    }
    static public function getNonIntegerKeyElements(array $aParse) {
        foreach ($aParse as $_isKey => $_v) {
            if (is_numeric($_isKey) && is_int($_isKey + 0)) {
                unset($aParse[$_isKey]);
            }
        }
        return $aParse;
    }
    static public function getArrayValueByArrayKeys($aArray, $aKeys, $vDefault = null) {
        $_sKey = array_shift($aKeys);
        if (isset($aArray[$_sKey])) {
            if (empty($aKeys)) {
                return $aArray[$_sKey];
            }
            if (is_array($aArray[$_sKey])) {
                return self::getArrayValueByArrayKeys($aArray[$_sKey], $aKeys, $vDefault);
            }
            return $vDefault;
        }
        return $vDefault;
    }
    static public function getAsArray($mValue, $bPreserveEmpty = false) {
        if (is_array($mValue)) {
            return $mValue;
        }
        if ($bPreserveEmpty) {
            return ( array )$mValue;
        }
        if (empty($mValue)) {
            return array();
        }
        return ( array )$mValue;
    }
    static public function getArrayElementsByKeys(array $aSubject, array $aKeys) {
        return array_intersect_key($aSubject, array_flip($aKeys));
    }
}