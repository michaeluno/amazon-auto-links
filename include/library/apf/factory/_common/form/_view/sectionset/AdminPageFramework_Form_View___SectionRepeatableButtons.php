<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___SectionRepeatableButtons extends AmazonAutoLinks_AdminPageFramework_Form_Utility {
    public static function get($sContainerTagID, $iSectionCount, $asArguments, $oMsg)
    {
        if (empty($asArguments)) {
            return '';
        }
        if (self::hasBeenCalled('repeatable_section_' . $sContainerTagID)) {
            return '';
        }
        $_oFormatter = new AmazonAutoLinks_AdminPageFramework_Form_Model___Format_RepeatableSection($asArguments, $oMsg);
        $_aArguments = $_oFormatter->get();
        $_aArguments[ 'id' ] = $sContainerTagID;
        $_sButtons = self::___getRepeatableSectionButtons($_aArguments, $oMsg, $sContainerTagID, $iSectionCount);
        return "<div class='hidden repeatable-section-buttons-model' " . self::getDataAttributes($_aArguments) . ">" . $_sButtons . "</div>";
    }
    private static function ___getRepeatableSectionButtons($_aArguments, $oMsg, $sContainerTagID, $iSectionCount)
    {
        $_sIconRemove = '-';
        $_sIconAdd = '+';
        if (version_compare($GLOBALS[ 'wp_version' ], '5.3', '>=')) {
            $_sIconRemove = "<span class='dashicons dashicons-minus'></span>";
            $_sIconAdd = "<span class='dashicons dashicons-plus-alt2'></span>";
        }
        return "<div class='amazon-auto-links-repeatable-section-buttons-outer-container'>" . "<div " . self::___getContainerAttributes($_aArguments, $oMsg) . ' >' . "<a " . self::___getRemoveButtonAttributes($sContainerTagID, $oMsg, $iSectionCount) . ">" . $_sIconRemove . "</a>" . "<a " . self::___getAddButtonAttributes($sContainerTagID, $oMsg, $_aArguments) . ">" . $_sIconAdd . "</a>" . "</div>" . "</div>" . AmazonAutoLinks_AdminPageFramework_Form_Utility::getModalForDisabledRepeatableElement('repeatable_section_disabled_' . $sContainerTagID, $_aArguments[ 'disabled' ]);
    }
    private static function ___getContainerAttributes(array $aArguments, $oMsg)
    {
        $_aAttributes = array( 'class' => self::getClassAttribute('amazon-auto-links-repeatable-section-buttons', empty($aArguments[ 'disabled' ]) ? '' : 'disabled'), );
        unset($aArguments[ 'disabled' ][ 'message' ]);
        if (empty($aArguments[ 'disabled' ])) {
            unset($aArguments[ 'disabled' ]);
        }
        return self::getAttributes($_aAttributes) . ' ' . self::getDataAttributes($aArguments);
    }
    private static function ___getRemoveButtonAttributes($sContainerTagID, $oMsg, $iSectionCount)
    {
        return self::getAttributes(array( 'class' => 'repeatable-section-remove-button button-secondary ' . 'repeatable-section-button button button-large', 'title' => $oMsg->get('remove_section'), 'style' => $iSectionCount <= 1 ? 'display:none' : null, 'data-id' => $sContainerTagID, ));
    }
    private static function ___getAddButtonAttributes($sContainerTagID, $oMsg, $aArguments)
    {
        return self::getAttributes(array( 'class' => 'repeatable-section-add-button button-secondary ' . 'repeatable-section-button button button-large', 'title' => $oMsg->get('add_section'), 'data-id' => $sContainerTagID, 'href' => ! empty($aArguments[ 'disabled' ]) ? '#TB_inline?width=' . $aArguments[ 'disabled' ][ 'box_width' ] . '&height=' . $aArguments[ 'disabled' ][ 'box_height' ] . '&inlineId=' . 'repeatable_section_disabled_' . $sContainerTagID : null, ));
    }
}
