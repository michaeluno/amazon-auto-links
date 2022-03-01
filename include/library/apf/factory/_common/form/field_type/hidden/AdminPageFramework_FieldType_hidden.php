<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_FieldType_hidden extends AmazonAutoLinks_AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array( 'hidden' );
    protected $aDefaultKeys = array( 'hidden' => true, );
    protected function getField($aField)
    {
        return $aField[ 'before_label' ] . "<div class='amazon-auto-links-input-label-container'>" . "<label for='{$aField[ 'input_id' ]}'>" . $aField[ 'before_input' ] . ($aField[ 'label' ] ? "<span " . $this->getLabelContainerAttributes($aField, 'amazon-auto-links-input-label-string') . ">" . $aField[ 'label' ] . "</span>" : "") . "<input " . $this->getAttributes($aField[ 'attributes' ]) . " />" . $aField[ 'after_input' ] . "</label>" . "</div>" . $aField[ 'after_label' ];
    }
}
