<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_Model___DefaultValues extends AmazonAutoLinks_AdminPageFramework_Form_Base {
    public $aFieldsets = array();
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aFieldsets, );
        $this->aFieldsets = $_aParameters[ 0 ];
    }
    public function get()
    {
        return $this->___getDefaultValues($this->aFieldsets, array());
    }
    private function ___getDefaultValues($aFieldsets, $aDefaultOptions)
    {
        foreach ($aFieldsets as $_sSectionPath => $_aItems) {
            $_aSectionPath = explode('|', $_sSectionPath);
            foreach ($_aItems as $_sFieldPath => $_aFieldset) {
                $_aFieldPath = explode('|', $_sFieldPath);
                $this->setMultiDimensionalArray($aDefaultOptions, '_default' === $_sSectionPath ? array( $_sFieldPath ) : array_merge($_aSectionPath, $_aFieldPath), $this->___getDefaultValue($_aFieldset));
            }
        }
        return $aDefaultOptions;
    }
    private function ___getDefaultValue($aFieldset)
    {
        $_aSubFields = $this->getIntegerKeyElements($aFieldset);
        if (count($_aSubFields) === 0) {
            return $this->getElement($aFieldset, 'value', $this->getElement($aFieldset, 'default', null));
        }
        $_aDefault = array();
        array_unshift($_aSubFields, $aFieldset);
        foreach ($_aSubFields as $_iIndex => $_aField) {
            $_aDefault[ $_iIndex ] = $this->getElement($_aField, 'value', $this->getElement($_aField, 'default', null));
        }
        return $_aDefault;
    }
}
