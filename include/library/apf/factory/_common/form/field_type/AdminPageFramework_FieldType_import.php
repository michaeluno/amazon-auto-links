<?php 
/**
	Admin Page Framework v3.8.22b06 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2020, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_FieldType_import extends AmazonAutoLinks_AdminPageFramework_FieldType_submit {
    public $aFieldTypeSlugs = array('import',);
    protected $aDefaultKeys = array('option_key' => null, 'format' => 'json', 'is_merge' => false, 'attributes' => array('class' => 'button button-primary', 'file' => array('accept' => 'audio/*|video/*|image/*|MIME_type', 'class' => 'import', 'type' => 'file',), 'submit' => array('class' => 'import button button-primary', 'type' => 'submit',),),);
    protected function setUp() {
    }
    protected function getScripts() {
        return "";
    }
    protected function getStyles() {
        return ".amazon-auto-links-field-import input {margin-right: 0.5em;}.amazon-auto-links-field-import label,.form-table td fieldset.amazon-auto-links-fieldset .amazon-auto-links-field-import label { display: inline; }";
    }
    protected function getField($aField) {
        $aField['attributes']['name'] = "__import[submit][{$aField['input_id']}]";
        $aField['label'] = $aField['label'] ? $aField['label'] : $this->oMsg->get('import');
        return parent::getField($aField);
    }
    protected function _getExtraFieldsBeforeLabel(&$aField) {
        return "<input " . $this->getAttributes(array('id' => "{$aField['input_id']}_file", 'type' => 'file', 'name' => "__import[{$aField['input_id']}]",) + $aField['attributes']['file']) . " />";
    }
    protected function _getExtraInputFields(&$aField) {
        $aHiddenAttributes = array('type' => 'hidden',);
        return "<input " . $this->getAttributes(array('name' => "__import[{$aField['input_id']}][input_id]", 'value' => $aField['input_id'],) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array('name' => "__import[{$aField['input_id']}][field_id]", 'value' => $aField['field_id'],) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array('name' => "__import[{$aField['input_id']}][section_id]", 'value' => isset($aField['section_id']) && $aField['section_id'] != '_default' ? $aField['section_id'] : '',) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array('name' => "__import[{$aField['input_id']}][is_merge]", 'value' => $aField['is_merge'],) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array('name' => "__import[{$aField['input_id']}][option_key]", 'value' => $aField['option_key'],) + $aHiddenAttributes) . "/>" . "<input " . $this->getAttributes(array('name' => "__import[{$aField['input_id']}][format]", 'value' => $aField['format'],) + $aHiddenAttributes) . "/>";
    }
    }
    