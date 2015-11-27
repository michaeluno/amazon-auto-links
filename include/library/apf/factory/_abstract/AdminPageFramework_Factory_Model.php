<?php
abstract class AmazonAutoLinks_AdminPageFramework_Factory_Model extends AmazonAutoLinks_AdminPageFramework_Factory_Router {
    public function __construct($oProp) {
        parent::__construct($oProp);
        add_filter('field_types_admin_page_framework', array($this, '_replyToFilterFieldTypeDefinitions'));
    }
    protected function _setUp() {
        $this->setUp();
    }
    public function _replyToFieldsetReourceRegistration($aFieldset) {
        $aFieldset = $aFieldset + array('help' => null, 'title' => null, 'help_aside' => null,);
        if (!$aFieldset['help']) {
            return;
        }
        $this->oHelpPane->_addHelpTextForFormFields($aFieldset['title'], $aFieldset['help'], $aFieldset['help_aside']);
    }
    public function _replyToFilterFieldTypeDefinitions($aFieldTypeDefinitions) {
        return $this->oUtil->addAndApplyFilters($this, "field_types_{$this->oProp->sClassName}", $aFieldTypeDefinitions);
    }
    public function _replyToModifySectionsets($aSectionsets) {
        return $this->oUtil->addAndApplyFilter($this, "sections_{$this->oProp->sClassName}", $aSectionsets);
    }
    public function _replyToModifyFieldsets($aFieldsets, $aSectionsets) {
        foreach ($aFieldsets as $_sSectionPath => $_aFields) {
            $_aSectionPath = explode('|', $_sSectionPath);
            $_sFilterSuffix = implode('_', $_aSectionPath);
            $aFieldsets[$_sSectionPath] = $this->oUtil->addAndApplyFilter($this, "fields_{$this->oProp->sClassName}_{$_sFilterSuffix}", $_aFields);
        }
        $aFieldsets = $this->oUtil->addAndApplyFilter($this, "fields_{$this->oProp->sClassName}", $aFieldsets);
        if (count($aFieldsets)) {
            $this->oProp->bEnableForm = true;
        }
        return $aFieldsets;
    }
    public function _replyToModifyFieldsetsDefinitions($aFieldsets) {
        return $this->oUtil->addAndApplyFilter($this, "field_definition_{$this->oProp->sClassName}", $aFieldsets);
    }
    public function _replyToModifyFieldsetDefinition($aFieldset) {
        $_sFieldPart = '_' . implode('_', $aFieldset['_field_path_array']);
        $_sSectionPart = implode('_', $aFieldset['_section_path_array']);
        $_sSectionPart = $this->oUtil->getAOrB('_default' === $_sSectionPart, '', '_' . $_sSectionPart);
        return $this->oUtil->addAndApplyFilter($this, "field_definition_{$this->oProp->sClassName}{$_sSectionPart}{$_sFieldPart}", $aFieldset, $aFieldset['_subsection_index']);
    }
    public function _replyToHandleSubmittedFormData($aSavedData, $aArguments, $aSectionsets, $aFieldsets) {
    }
    public function _replyToFormatFieldsetDefinition($aFieldset, $aSectionsets) {
        if (empty($aFieldset)) {
            return $aFieldset;
        }
        return $aFieldset;
    }
    public function _replyToFormatSectionsetDefinition($aSectionset) {
        if (empty($aSectionset)) {
            return $aSectionset;
        }
        $aSectionset = $aSectionset + array('_fields_type' => $this->oProp->_sPropertyType, '_structure_type' => $this->oProp->_sPropertyType,);
        return $aSectionset;
    }
    public function _replyToDetermineWhetherToProcessFormRegistration($bAllowed) {
        return $this->_isInThePage();
    }
    public function _replyToGetCapabilityForForm($sCapability) {
        return $this->oProp->sCapability;
    }
    public function _replyToGetSavedFormData() {
        return $this->oUtil->addAndApplyFilter($this, 'options_' . $this->oProp->sClassName, $this->oProp->aOptions);
    }
    public function getSavedOptions() {
        return $this->oForm->aSavedData;
    }
    public function getFieldErrors() {
        return $this->oForm->getFieldErrors();
    }
    protected function _getFieldErrors() {
        return $this->oForm->getFieldErrors();
    }
    public function setLastInputs(array $aLastInputs) {
        return $this->oForm->setLastInputs($aLastInputs);
    }
    public function _setLastInput($aLastInputs) {
        return $this->setLastInputs($aLastInputs);
    }
}