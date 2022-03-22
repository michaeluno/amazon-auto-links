<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_Controller extends AmazonAutoLinks_AdminPageFramework_Form_View {
    public function setFieldErrors($aErrors)
    {
        $this->oFieldError->set($aErrors);
    }
    public function hasFieldError()
    {
        return $this->oFieldError->hasError();
    }
    public function hasSubmitNotice($sType='')
    {
        return $this->oSubmitNotice->hasNotice($sType);
    }
    public function setSubmitNotice($sMessage, $sType='error', $asAttributes=array(), $bOverride=true)
    {
        $this->oSubmitNotice->set($sMessage, $sType, $asAttributes, $bOverride);
    }
    public function addSection(array $aSectionset)
    {
        $aSectionset = $aSectionset + array( 'section_id' => null, );
        $aSectionset[ 'section_id' ] = $this->sanitizeSlug($aSectionset[ 'section_id' ]);
        $this->aSectionsets[ $aSectionset[ 'section_id' ] ] = $aSectionset;
        $this->aFieldsets[ $aSectionset[ 'section_id' ] ] = $this->getElement($this->aFieldsets, $aSectionset[ 'section_id' ], array());
    }
    public function removeSection($sSectionID)
    {
        if ('_default' === $sSectionID) {
            return;
        }
        unset($this->aSectionsets[ $sSectionID ], $this->aFieldsets[ $sSectionID ]);
    }
    public function getResources($sKey)
    {
        return $this->getElement(self::$_aResources, $sKey);
    }
    public function unsetResources($aKeys)
    {
        $this->unsetDimensionalArrayElement(self::$_aResources, $aKeys);
    }
    public function setResources($sKey, $mValue)
    {
        return self::$_aResources[ $sKey ] = $mValue;
    }
    public function addResource($sKey, $sValue)
    {
        self::$_aResources[ $sKey ][] = $sValue;
    }
    protected $_asTargetSectionID = '_default';
    public function addField($asFieldset)
    {
        if (! $this->_isFieldsetDefinition($asFieldset)) {
            $this->_asTargetSectionID = $this->_getTargetSectionID($asFieldset);
            return $this->_asTargetSectionID;
        }
        $_aFieldset = $asFieldset;
        $this->_asTargetSectionID = $this->getElement($_aFieldset, 'section_id', $this->_asTargetSectionID);
        if (! isset($_aFieldset[ 'field_id' ])) {
            return null;
        }
        $this->_setFieldset($_aFieldset);
        return $_aFieldset;
    }
    private function _setFieldset(array $aFieldset)
    {
        $aFieldset = array( '_fields_type' => $this->aArguments[ 'structure_type' ], '_structure_type' => $this->aArguments[ 'structure_type' ], ) + $aFieldset + array( 'section_id' => $this->_asTargetSectionID, 'class_name' => $this->aArguments[ 'caller_id' ], ) ;
        $aFieldset[ 'field_id' ] = $this->getIDSanitized($aFieldset[ 'field_id' ]);
        $aFieldset[ 'section_id' ] = $this->getIDSanitized($aFieldset[ 'section_id' ]);
        $_aSectionPath = $this->getAsArray($aFieldset[ 'section_id' ]);
        $_sSectionPath = implode('|', $_aSectionPath);
        $_aFieldPath = $this->getAsArray($aFieldset[ 'field_id' ]);
        $_sFieldPath = implode('|', $_aFieldPath);
        $this->aFieldsets[ $_sSectionPath ][ $_sFieldPath ] = $aFieldset;
    }
    private function _isFieldsetDefinition($asFieldset)
    {
        if (is_scalar($asFieldset)) {
            return false;
        }
        return $this->isAssociative($asFieldset);
    }
    private function _getTargetSectionID($asTargetSectionID)
    {
        if (is_scalar($asTargetSectionID)) {
            return $asTargetSectionID;
        }
        return $asTargetSectionID;
    }
    public function removeField($sFieldID)
    {
        foreach ($this->aFieldsets as $_sSectionID => $_aSubSectionsOrFields) {
            if (array_key_exists($sFieldID, $_aSubSectionsOrFields)) {
                unset($this->aFieldsets[ $_sSectionID ][ $sFieldID ]);
            }
            foreach ($_aSubSectionsOrFields as $_sIndexOrFieldID => $_aSubSectionOrFields) {
                if ($this->isNumericInteger($_sIndexOrFieldID)) {
                    if (array_key_exists($sFieldID, $_aSubSectionOrFields)) {
                        unset($this->aFieldsets[ $_sSectionID ][ $_sIndexOrFieldID ]);
                    }
                    continue;
                }
            }
        }
    }
}
