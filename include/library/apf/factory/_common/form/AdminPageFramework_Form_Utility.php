<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_Form_Utility extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public static function getInputsUnset(array $aInputs, $sFieldsType, $iSkipDepth=0)
    {
        $_sUnsetKey = '__unset_' . $sFieldsType;
        if (! isset($_POST[ $_sUnsetKey ])) {
            return $aInputs;
        }
        $_aUnsetElements = self::getHTTPRequestSanitized(self::getElementAsArray($_POST, array( $_sUnsetKey )));
        $_aUnsetElements = array_unique($_aUnsetElements);
        foreach ($_aUnsetElements as $_sFlatInputName) {
            $_aDimensionalKeys = explode('|', $_sFlatInputName);
            if ('__dummy_option_key' === $_aDimensionalKeys[ 0 ]) {
                array_shift($_aDimensionalKeys);
            }
            for ($_i = 0; $_i < $iSkipDepth; $_i++) {
                unset($_aDimensionalKeys[ $_i ]);
            }
            self::unsetDimensionalArrayElement($aInputs, $_aDimensionalKeys);
        }
        return $aInputs;
    }
    public static function getElementPathAsArray($asPath)
    {
        if (is_array($asPath)) {
            return;
        }
        return explode('|', $asPath);
    }
    public static function getFormElementPath($asID)
    {
        return implode('|', self::getAsArray($asID));
    }
    public static function getIDSanitized($asID)
    {
        return is_scalar($asID) ? self::sanitizeSlug($asID) : self::getAsArray($asID);
    }
    public static function hasNestedFields($aFieldset)
    {
        if (! self::hasFieldDefinitionsInContent($aFieldset)) {
            return false;
        }
        if ('inline_mixed' === self::getElement($aFieldset, 'type')) {
            return false;
        }
        return is_array(self::getElement($aFieldset[ 'content' ], 0));
    }
    public static function hasFieldDefinitionsInContent($aFieldset)
    {
        if (! isset($aFieldset[ 'content' ])) {
            return false;
        }
        if (empty($aFieldset[ 'content' ])) {
            return false;
        }
        return is_array($aFieldset[ 'content' ]);
    }
    public static function hasSubFields(array $aFields, $aField)
    {
        if (count($aFields) > 1) {
            return true;
        }
        if (self::isDynamicField($aField)) {
            return true;
        }
        return false;
    }
    public static function isDynamicField($aField)
    {
        if (! empty($aField[ 'repeatable' ])) {
            return true;
        }
        if (! empty($aField[ 'sortable' ])) {
            return true;
        }
        return false;
    }
    public static function getTrailingPipeCharacterAppended($sString)
    {
        if (empty($sString)) {
            return $sString;
        }
        return $sString . '|';
    }
    public static function getFieldsetReformattedBySubFieldIndex($aFieldset, $iSubFieldIndex, $bHasSubFields, $aParentFieldset)
    {
        $_oCallerForm = $aFieldset[ '_caller_object' ];
        $aFieldset[ '_parent_field_path' ] = self::getAOrB($bHasSubFields, $aFieldset[ '_parent_field_path' ] . '|' . $iSubFieldIndex, $aFieldset[ '_parent_field_path' ]);
        $aFieldset[ '_parent_tag_id' ] = self::getAOrB($bHasSubFields, $aParentFieldset[ 'tag_id' ] . '__' . $iSubFieldIndex, $aParentFieldset[ 'tag_id' ]);
        $_oFieldsetFormatter = new AmazonAutoLinks_AdminPageFramework_Form_Model___Format_Fieldset($aFieldset, $aFieldset[ '_structure_type' ], $aFieldset[ 'capability' ], ( integer ) $iSubFieldIndex + 1, $aFieldset[ '_subsection_index' ], $aFieldset[ '_is_section_repeatable' ], $aFieldset[ '_caller_object' ]);
        $aFieldset = $_oFieldsetFormatter->get();
        $_oFieldsetOutputFormatter = new AmazonAutoLinks_AdminPageFramework_Form_Model___Format_FieldsetOutput($aFieldset, $aFieldset[ '_section_index' ], $_oCallerForm->aFieldTypeDefinitions);
        return $_oFieldsetOutputFormatter->get();
    }
    public static function isNormalPlacement(array $aFieldset)
    {
        if ('section_title' === $aFieldset[ 'type' ]) {
            return false;
        }
        return 'normal' === $aFieldset[ 'placement' ];
    }
    public static function getModalForDisabledRepeatableElement($sBoxElementID, $aArguments)
    {
        if (empty($aArguments)) {
            return '';
        }
        if (self::hasBeenCalled('disabled_repeatable_elements_modal_' . $sBoxElementID)) {
            return '';
        }
        add_thickbox();
        return "<div id='{$sBoxElementID}' style='display:none'>" . "<p>" . $aArguments[ 'message' ] . "</p>" . "</div>";
    }
}
