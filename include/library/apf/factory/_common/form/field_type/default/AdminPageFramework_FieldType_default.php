<?php
/*
 * Admin Page Framework v3.9.1b05 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_FieldType_default extends AmazonAutoLinks_AdminPageFramework_FieldType {
    public $aDefaultKeys = array();
    public function _replyToGetField($aField)
    {
        return $aField['before_label'] . "<div class='amazon-auto-links-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ($aField['label'] && ! $aField['repeatable'] ? "<span " . $this->getLabelContainerAttributes($aField, 'amazon-auto-links-input-label-string') . ">" . $aField[ 'label' ] . "</span>" : "") . $aField['value'] . $aField['after_input'] . "</label>" . "</div>" . $aField['after_label'] ;
    }
}
