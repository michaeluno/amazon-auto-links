<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___Fieldset___FieldError extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $aErrors = array();
    public $aSectionPath = array();
    public $aFieldPath = array();
    public $sHeadingMessage = '';
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aErrors, $this->aSectionPath, $this->aFieldPath, $this->sHeadingMessage, );
        $this->aErrors = $_aParameters[ 0 ];
        $this->aSectionPath = $_aParameters[ 1 ];
        $this->aFieldPath = $_aParameters[ 2 ];
        $this->sHeadingMessage = $_aParameters[ 3 ];
    }
    public function get()
    {
        return $this->_getFieldError($this->aErrors, $this->_getSectionPathSanitized($this->aSectionPath), $this->aFieldPath, $this->sHeadingMessage);
    }
    private function _getSectionPathSanitized($aSectionPath)
    {
        if ('_default' === $this->getElement($aSectionPath, 0)) {
            array_shift($aSectionPath);
        }
        return $aSectionPath;
    }
    private function _getFieldError($aErrors, $aSectionPath, $aFieldPath, $sHeadingMessage)
    {
        $_aErrorPath = array_merge($aSectionPath, $aFieldPath);
        if ($this->_hasFieldError($aErrors, $_aErrorPath)) {
            return "<span class='field-error'>*&nbsp;" . $sHeadingMessage . $this->getElement($aErrors, $_aErrorPath) . "</span>";
        }
        return '';
    }
    private function _hasFieldError($aErrors, array $aFieldAddress)
    {
        return is_scalar($this->getElement($aErrors, $aFieldAddress));
    }
}
