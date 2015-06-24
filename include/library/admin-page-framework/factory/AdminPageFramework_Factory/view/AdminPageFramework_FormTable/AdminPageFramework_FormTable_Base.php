<?php
/**
 Admin Page Framework v3.5.9b09 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/admin-page-framework>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AmazonAutoLinks_AdminPageFramework_FormTable_Base extends AmazonAutoLinks_AdminPageFramework_FormOutput {
    public function __construct($aFieldTypeDefinitions, array $aFieldErrors, $oMsg = null) {
        $this->aFieldTypeDefinitions = $aFieldTypeDefinitions;
        $this->aFieldErrors = $aFieldErrors;
        $this->oMsg = $oMsg ? $oMsg : AmazonAutoLinks_AdminPageFramework_Message::getInstance();
        $this->_loadScripts();
    }
    static private $_bIsLoadedTabPlugin;
    private function _loadScripts() {
        if (self::$_bIsLoadedTabPlugin) {
            return;
        }
        self::$_bIsLoadedTabPlugin = true;
        new AmazonAutoLinks_AdminPageFramework_Script_Tab;
    }
    protected function _getSectionTitle($sTitle, $sTag, $aFields, $hfFieldCallback) {
        $_aSectionTitleField = $this->_getSectionTitleField($aFields);
        return $_aSectionTitleField ? call_user_func_array($hfFieldCallback, array($_aSectionTitleField)) : "<{$sTag}>" . $sTitle . "</{$sTag}>";
    }
    private function _getSectionTitleField(array $aFields) {
        foreach ($aFields as $_aField) {
            if ('section_title' === $_aField['type']) {
                return $_aField;
            }
        }
    }
    protected function _getCollapsibleArgument(array $aSections = array()) {
        foreach ($aSections as $_aSection) {
            if (!isset($_aSection['collapsible'])) {
                continue;
            }
            if (empty($_aSection['collapsible'])) {
                return array();
            }
            $_aSection['collapsible']['toggle_all_button'] = $this->_sanitizeToggleAllButtonArgument($_aSection['collapsible']['toggle_all_button'], $_aSection);
            return $_aSection['collapsible'];
        }
        return array();
    }
    private function _sanitizeToggleAllButtonArgument($sToggleAll, array $aSection) {
        if (!$aSection['repeatable']) {
            return $sToggleAll;
        }
        if ($aSection['_is_first_index'] && $aSection['_is_last_index']) {
            return $sToggleAll;
        }
        if (!$aSection['_is_first_index'] && !$aSection['_is_last_index']) {
            return 0;
        }
        $_aToggleAll = $this->getAOrB(true === $sToggleAll || 1 === $sToggleAll, array('top-right', 'bottom-right'), explode(',', $sToggleAll));
        $_aToggleAll = $this->getAOrB($aSection['_is_first_index'], $this->dropElementByValue($_aToggleAll, array(1, true, 0, false, 'bottom-right', 'bottom-left')), $_aToggleAll);
        $_aToggleAll = $this->getAOrB($aSection['_is_last_index'], $this->dropElementByValue($_aToggleAll, array(1, true, 0, false, 'top-right', 'top-left')), $_aToggleAll);
        $_aToggleAll = $this->getAOrB(empty($_aToggleAll), array(0), $_aToggleAll);
        return implode(',', $_aToggleAll);
    }
    protected function _getCollapsibleSectionTitleBlock(array $aCollapsible, $sContainer = 'sections', array $aFields = array(), $hfFieldCallback = null) {
        if (empty($aCollapsible)) {
            return '';
        }
        if ($sContainer !== $aCollapsible['container']) {
            return '';
        }
        return $this->_getCollapsibleSectionsEnablerScript() . "<div " . $this->generateAttributes(array('class' => $this->generateClassAttribute('admin-page-framework-section-title', 'accordion-section-title', 'admin-page-framework-collapsible-title', 'sections' === $aCollapsible['container'] ? 'admin-page-framework-collapsible-sections-title' : 'admin-page-framework-collapsible-section-title', $aCollapsible['is_collapsed'] ? 'collapsed' : ''),) + $this->getDataAttributeArray($aCollapsible)) . ">" . $this->_getSectionTitle($aCollapsible['title'], 'h3', $aFields, $hfFieldCallback) . "</div>";
    }
    static private $_bLoadedTabEnablerScript = false;
    protected function _getSectionTabsEnablerScript() {
        if (self::$_bLoadedTabEnablerScript) {
            return '';
        }
        self::$_bLoadedTabEnablerScript = true;
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
// the parent element of the ul tag; The ul element holds li tags of titles.
jQuery( '.admin-page-framework-section-tabs-contents' ).createTabs(); 
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-section-tabs-script'>" . $_sScript . "</script>";
    }
    static private $_bLoadedCollapsibleSectionsEnablerScript = false;
    protected function _getCollapsibleSectionsEnablerScript() {
        if (self::$_bLoadedCollapsibleSectionsEnablerScript) {
            return;
        }
        self::$_bLoadedCollapsibleSectionsEnablerScript = true;
        new AmazonAutoLinks_AdminPageFramework_Script_CollapsibleSection($this->oMsg);
    }
    static private $_aSetContainerIDsForRepeatableSections = array();
    protected function _getRepeatableSectionsEnablerScript($sContainerTagID, $iSectionCount, $aSettings) {
        if (empty($aSettings)) {
            return '';
        }
        if (in_array($sContainerTagID, self::$_aSetContainerIDsForRepeatableSections)) {
            return '';
        }
        self::$_aSetContainerIDsForRepeatableSections[$sContainerTagID] = $sContainerTagID;
        new AmazonAutoLinks_AdminPageFramework_Script_RepeatableSection($this->oMsg);
        $aSettings = $this->getAsArray($aSettings) + array('min' => 0, 'max' => 0);
        $_sAdd = $this->oMsg->get('add_section');
        $_sRemove = $this->oMsg->get('remove_section');
        $_sVisibility = $iSectionCount <= 1 ? " style='display:none;'" : "";
        $_sSettingsAttributes = $this->generateDataAttributes($aSettings);
        $_sButtons = "<div class='admin-page-framework-repeatable-section-buttons' {$_sSettingsAttributes} >" . "<a class='repeatable-section-remove button-secondary repeatable-section-button button button-large' href='#' title='{$_sRemove}' {$_sVisibility} data-id='{$sContainerTagID}'>-</a>" . "<a class='repeatable-section-add button-secondary repeatable-section-button button button-large' href='#' title='{$_sAdd}' data-id='{$sContainerTagID}'>+</a>" . "</div>";
        $_sButtonsHTML = '"' . $_sButtons . '"';
        $_aJSArray = json_encode($aSettings);
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function() {
    // Adds the buttons
    jQuery( '#{$sContainerTagID} .admin-page-framework-section-caption' ).each( function(){
        
        jQuery( this ).show();
        
        var _oButtons = jQuery( $_sButtonsHTML );
        if ( jQuery( this ).children( '.admin-page-framework-collapsible-section-title' ).children( 'fieldset' ).length > 0 ) {
            _oButtons.addClass( 'section_title_field_sibling' );
        }
        var _oCollapsibleSectionTitle = jQuery( this ).find( '.admin-page-framework-collapsible-section-title' );
        if ( _oCollapsibleSectionTitle.length ) {
            _oButtons.find( '.repeatable-section-button' ).removeClass( 'button-large' );
            _oCollapsibleSectionTitle.prepend( _oButtons );
        } else {
            jQuery( this ).prepend( _oButtons );
        }
        
    } );
    // Update the fields     
    jQuery( '#{$sContainerTagID}' ).updateAPFRepeatableSections( $_aJSArray ); 
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-seciton-repeatable-script'>" . $_sScript . "</script>";
    }
    protected function _getDebugInfo($sFieldsType) {
        if (!$this->isDebugModeEnabled()) {
            return '';
        }
        if (!in_array($sFieldsType, array('widget', 'post_meta_box', 'page_meta_box', 'user_meta'))) {
            return '';
        }
        return "<div class='admin-page-framework-info'>" . 'Debug Info: ' . AmazonAutoLinks_AdminPageFramework_Registry::NAME . ' ' . AmazonAutoLinks_AdminPageFramework_Registry::getVersion() . "</div>";
    }
}