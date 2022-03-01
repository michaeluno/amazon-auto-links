<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___FieldsetRows extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $aFieldsetsPerSection = array();
    public $iSectionIndex = null;
    public $aSavedData = array();
    public $aFieldErrors = array();
    public $aFieldTypeDefinitions = array();
    public $aCallbacks = array();
    public $oMsg;
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aFieldsetsPerSection, $this->iSectionIndex, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg, );
        $this->aFieldsetsPerSection = $_aParameters[ 0 ];
        $this->iSectionIndex = $_aParameters[ 1 ];
        $this->aSavedData = $_aParameters[ 2 ];
        $this->aFieldErrors = $_aParameters[ 3 ];
        $this->aFieldTypeDefinitions = $_aParameters[ 4 ];
        $this->aCallbacks = $_aParameters[ 5 ] + $this->aCallbacks;
        $this->oMsg = $_aParameters[ 6 ];
    }
    public function get($bTableRow=true)
    {
        $_sMethodName = $this->getAOrB($bTableRow, '_getFieldsetRow', '_getFieldset');
        $_sOutput = '';
        foreach ($this->aFieldsetsPerSection as $_aFieldset) {
            $_oFieldsetOutputFormatter = new AmazonAutoLinks_AdminPageFramework_Form_Model___Format_FieldsetOutput($_aFieldset, $this->iSectionIndex, $this->aFieldTypeDefinitions);
            $_aFieldset = $_oFieldsetOutputFormatter->get();
            if (! $this->callBack($this->aCallbacks[ 'is_fieldset_visible' ], array( true, $_aFieldset ))) {
                continue;
            }
            $_sOutput .= call_user_func_array(array( $this, $_sMethodName ), array( $_aFieldset ));
        }
        return $_sOutput;
    }
    private function _getFieldsetRow($aFieldset)
    {
        $_oFieldsetRow = new AmazonAutoLinks_AdminPageFramework_Form_View___FieldsetTableRow($aFieldset, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        return $_oFieldsetRow->get();
    }
    private function _getFieldset($aFieldset)
    {
        $_oFieldsetRow = new AmazonAutoLinks_AdminPageFramework_Form_View___FieldsetRow($aFieldset, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        return $_oFieldsetRow->get();
    }
}
