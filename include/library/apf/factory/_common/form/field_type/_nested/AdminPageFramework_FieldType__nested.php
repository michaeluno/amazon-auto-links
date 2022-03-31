<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_FieldType__nested extends AmazonAutoLinks_AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array( '_nested' );
    protected $aDefaultKeys = array( );
    protected function getField($aField)
    {
        $_oCallerForm = $aField[ '_caller_object' ];
        $_aInlineMixedOutput = array();
        foreach ($this->getAsArray($aField[ 'content' ]) as $_aChildFieldset) {
            if (is_scalar($_aChildFieldset)) {
                continue;
            }
            if (! $this->isNormalPlacement($_aChildFieldset)) {
                continue;
            }
            $_aChildFieldset = $this->getFieldsetReformattedBySubFieldIndex($_aChildFieldset, ( integer ) $aField[ '_index' ], $aField[ '_is_multiple_fields' ], $aField);
            $_oFieldset = new AmazonAutoLinks_AdminPageFramework_Form_View___Fieldset($_aChildFieldset, $_oCallerForm->aSavedData, $_oCallerForm->getFieldErrors(), $_oCallerForm->aFieldTypeDefinitions, $_oCallerForm->oMsg, $_oCallerForm->aCallbacks);
            $_aInlineMixedOutput[] = $_oFieldset->get();
        }
        return implode('', $_aInlineMixedOutput);
    }
}
