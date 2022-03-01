<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___FieldsetRow extends AmazonAutoLinks_AdminPageFramework_Form_View___FieldsetTableRow {
    public $aFieldset = array();
    public $aSavedData = array();
    public $aFieldErrors = array();
    public $aFieldTypeDefinitions = array();
    public $aCallbacks = array();
    public $oMsg;
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aFieldset, $this->aSavedData, $this->aFieldErrors, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg, );
        $this->aFieldset = $_aParameters[ 0 ];
        $this->aSavedData = $_aParameters[ 1 ];
        $this->aFieldErrors = $_aParameters[ 2 ];
        $this->aFieldTypeDefinitions = $_aParameters[ 3 ];
        $this->aCallbacks = $_aParameters[ 4 ];
        $this->oMsg = $_aParameters[ 5 ];
    }
    public function get()
    {
        $aFieldset = $this->aFieldset;
        if (! $this->isNormalPlacement($aFieldset)) {
            return '';
        }
        $_oFieldrowAttribute = new AmazonAutoLinks_AdminPageFramework_Form_View___Attribute_Fieldrow($aFieldset);
        return $this->_getFieldByContainer($aFieldset, array( 'open_main' => "<div " . $_oFieldrowAttribute->get() . ">", 'close_main' => "</div>", ));
    }
}
