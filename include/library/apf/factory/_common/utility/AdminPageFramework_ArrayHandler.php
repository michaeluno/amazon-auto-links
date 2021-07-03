<?php 
/**
	Admin Page Framework v3.8.30b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_FrameworkUtility extends AmazonAutoLinks_AdminPageFramework_WPUtility {
    static public function showDeprecationNotice($sDeprecated, $sAlternative = '', $sProgramName = '') {
        $sProgramName = $sProgramName ? $sProgramName : self::getFrameworkName();
        parent::showDeprecationNotice($sDeprecated, $sAlternative, $sProgramName);
    }
    static public function sortAdminSubMenu() {
        if (self::hasBeenCalled(__METHOD__)) {
            return;
        }
        foreach (( array )$GLOBALS['_apf_sub_menus_to_sort'] as $_sIndex => $_sMenuSlug) {
            if (!isset($GLOBALS['submenu'][$_sMenuSlug])) {
                continue;
            }
            ksort($GLOBALS['submenu'][$_sMenuSlug]);
            unset($GLOBALS['_apf_sub_menus_to_sort'][$_sIndex]);
        }
    }
    static public function getFrameworkVersion($bTrimDevVer = false) {
        $_sVersion = AmazonAutoLinks_AdminPageFramework_Registry::getVersion();
        return $bTrimDevVer ? self::getSuffixRemoved($_sVersion, '.dev') : $_sVersion;
    }
    static public function getFrameworkName() {
        return AmazonAutoLinks_AdminPageFramework_Registry::NAME;
    }
    static public function getFrameworkNameVersion() {
        return self::getFrameworkName() . ' ' . self::getFrameworkVersion();
    }
    }
    class AmazonAutoLinks_AdminPageFramework_ArrayHandler extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
        public $aData = array();
        public $aDefault = array();
        public function __construct() {
            $_aParameters = func_get_args() + array($this->aData, $this->aDefault,);
            $this->aData = $_aParameters[0];
            $this->aDefault = $_aParameters[1];
        }
        public function get() {
            $_mDefault = null;
            $_aKeys = func_get_args() + array(null);
            if (!isset($_aKeys[0])) {
                return $this->uniteArrays($this->aData, $this->aDefault);
            }
            if (is_array($_aKeys[0])) {
                $_aKeys = $_aKeys[0];
                $_mDefault = $this->getElement($_aKeys, 1);
            }
            return $this->getArrayValueByArrayKeys($this->aData, $_aKeys, $this->_getDefaultValue($_mDefault, $_aKeys));
        }
        private function _getDefaultValue($_mDefault, $_aKeys) {
            return isset($_mDefault) ? $_mDefault : $this->getArrayValueByArrayKeys($this->aDefault, $_aKeys);
        }
        public function set() {
            $_aParameters = func_get_args();
            if (!isset($_aParameters[0], $_aParameters[1])) {
                return;
            }
            $_asKeys = $_aParameters[0];
            $_mValue = $_aParameters[1];
            if (is_scalar($_asKeys)) {
                $this->aData[$_asKeys] = $_mValue;
                return;
            }
            $this->setMultiDimensionalArray($this->aData, $_asKeys, $_mValue);
        }
        public function delete() {
            $_aParameters = func_get_args();
            if (!isset($_aParameters[0], $_aParameters[1])) {
                return;
            }
            $_asKeys = $_aParameters[0];
            $_mValue = $_aParameters[1];
            if (is_scalar($_asKeys)) {
                $this->aData[$_asKeys] = $_mValue;
                return;
            }
            $this->unsetDimensionalArrayElement($this->aData, $_asKeys);
        }
        public function __toString() {
            return $this->getObjectInfo($this);
        }
    }
    