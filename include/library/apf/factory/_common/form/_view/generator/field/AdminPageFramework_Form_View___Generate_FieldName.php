<?php 
/**
	Admin Page Framework v3.9.0b08 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Form_View___Generate_FieldName extends AmazonAutoLinks_AdminPageFramework_Form_View___Generate_Field_Base {
    public function get() {
        $_sResult = $this->_getFiltered($this->_getFieldName());
        return $_sResult;
    }
    public function getModel() {
        return $this->get() . '[' . $this->sIndexMark . ']';
    }
    protected function _getFieldName() {
        $_aFieldPath = $this->aArguments['_field_path_array'];
        if (!$this->_isSectionSet()) {
            return $this->_getInputNameConstructed($_aFieldPath);
        }
        $_aSectionPath = $this->aArguments['_section_path_array'];
        if ($this->_isSectionSet() && isset($this->aArguments['_section_index'])) {
            $_aSectionPath[] = $this->aArguments['_section_index'];
        }
        $_sFieldName = $this->_getInputNameConstructed(array_merge($_aSectionPath, $_aFieldPath));
        return $_sFieldName;
    }
    }
    