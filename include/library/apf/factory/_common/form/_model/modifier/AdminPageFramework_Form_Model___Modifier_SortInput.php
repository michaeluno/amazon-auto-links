<?php 
/**
	Admin Page Framework v3.8.32b02 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Form_Model___Modifier_SortInput extends AmazonAutoLinks_AdminPageFramework_Form_Model___Modifier_Base {
    public $aInput = array();
    public $aFieldAddresses = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aInput, $this->aFieldAddresses,);
        $this->aInput = $_aParameters[0];
        $this->aFieldAddresses = $_aParameters[1];
    }
    public function get() {
        foreach ($this->_getFormattedDimensionalKeys($this->aFieldAddresses) as $_sFlatFieldAddress) {
            $_aDimensionalKeys = explode('|', $_sFlatFieldAddress);
            $_aDynamicElements = $this->getElement($this->aInput, $_aDimensionalKeys);
            if (!is_array($_aDynamicElements)) {
                continue;
            }
            $this->setMultiDimensionalArray($this->aInput, $_aDimensionalKeys, array_values($_aDynamicElements));
        }
        return $this->aInput;
    }
    private function _getFormattedDimensionalKeys($aFieldAddresses) {
        $aFieldAddresses = $this->getAsArray($aFieldAddresses);
        $aFieldAddresses = array_unique($aFieldAddresses);
        arsort($aFieldAddresses);
        return $aFieldAddresses;
    }
    }
    