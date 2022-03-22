<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_Model extends AmazonAutoLinks_AdminPageFramework_Form_Base {
    public $aArguments;
    public $aFieldTypeDefinitions;
    public $aSavedData;
    public $sCapability;
    public function __construct()
    {
        if ($this->aArguments[ 'register_if_action_already_done' ]) {
            $this->registerAction($this->aArguments[ 'action_hook_form_registration' ], array( $this, '_replyToRegisterFormItems' ), 100);
        } else {
            add_action($this->aArguments[ 'action_hook_form_registration' ], array( $this, '_replyToRegisterFormItems' ));
        }
    }
    public function getSubmittedData(array $aDataToParse, $bExtractFromFieldStructure=true, $bStripSlashes=true)
    {
        $_aSubmittedFormData = $bExtractFromFieldStructure ? $this->castArrayContents($this->getDataStructureFromAddedFieldsets(), $aDataToParse) : $aDataToParse;
        return $this->getSortedInputs($_aSubmittedFormData);
    }
    public function getSortedInputs(array $aFormInputs)
    {
        $_aDynamicFieldAddressKeys = $this->getHTTPRequestSanitized(array_unique(array_merge($this->getElementAsArray($_POST, '__repeatable_elements_' . $this->aArguments[ 'structure_type' ], array()), $this->getElementAsArray($_POST, '__sortable_elements_' . $this->aArguments[ 'structure_type' ], array()))));
        if (empty($_aDynamicFieldAddressKeys)) {
            return $aFormInputs;
        }
        $_oInputSorter = new AmazonAutoLinks_AdminPageFramework_Form_Model___Modifier_SortInput($aFormInputs, $_aDynamicFieldAddressKeys);
        return $_oInputSorter->get();
    }
    public function getDataStructureFromAddedFieldsets()
    {
        $_aFormDataStructure = array();
        foreach ($this->getAsArray($this->aFieldsets) as $_sSectionID => $_aFieldsets) {
            if ($_sSectionID !== '_default') {
                $_aFormDataStructure[ $_sSectionID ] = $_aFieldsets;
                continue;
            }
            foreach ($_aFieldsets as $_sFieldID => $_aFieldset) {
                $_aFormDataStructure[ $_aFieldset[ 'field_id' ] ] = $_aFieldset;
            }
        }
        return $_aFormDataStructure;
    }
    public function dropRepeatableElements(array $aSubject)
    {
        $_oFilterRepeatableElements = new AmazonAutoLinks_AdminPageFramework_Form_Model___Modifier_FilterRepeatableElements($aSubject, $this->getHTTPRequestSanitized($this->getElementAsArray($_POST, '__repeatable_elements_' . $this->aArguments[ 'structure_type' ])));
        return $_oFilterRepeatableElements->get();
    }
    public function _replyToRegisterFormItems()
    {
        if (! $this->isInThePage()) {
            return;
        }
        $this->_setFieldTypeDefinitions('admin_page_framework');
        $this->_setFieldTypeDefinitions($this->aArguments[ 'caller_id' ]);
        $this->aSavedData = $this->_getSavedData($this->aSavedData + $this->getDefaultFormValues());
        $this->_handleCallbacks();
        $_oFieldResources = new AmazonAutoLinks_AdminPageFramework_Form_Model___SetFieldResources($this->aArguments, $this->aFieldsets, self::$_aResources, $this->aFieldTypeDefinitions, $this->aCallbacks, $this->oMsg);
        self::$_aResources = $_oFieldResources->get();
        $this->callBack($this->aCallbacks[ 'handle_form_data' ], array( $this->aSavedData, $this->aArguments, $this->aSectionsets, $this->aFieldsets, ));
    }
    private function _handleCallbacks()
    {
        $this->aSectionsets = $this->callBack($this->aCallbacks[ 'sectionsets_before_registration' ], array( $this->aSectionsets, ));
        $this->aFieldsets = $this->callBack($this->aCallbacks[ 'fieldsets_before_registration' ], array( $this->aFieldsets, $this->aSectionsets, ));
    }
    private static $_aFieldTypeDefinitions = array( 'admin_page_framework' => array(), );
    private function _setFieldTypeDefinitions($_sCallerID)
    {
        if ('admin_page_framework' === $_sCallerID) {
            $this->_setSiteWideFieldTypeDefinitions();
        }
        $this->aFieldTypeDefinitions = apply_filters("field_types_{$_sCallerID}", self::$_aFieldTypeDefinitions[ 'admin_page_framework' ]);
    }
    private function _setSiteWideFieldTypeDefinitions()
    {
        if ($this->hasBeenCalled('__filed_types_admin_page_framework')) {
            return;
        }
        $_oBuiltInFieldTypeDefinitions = new AmazonAutoLinks_AdminPageFramework_Form_Model___BuiltInFieldTypeDefinitions('admin_page_framework', $this->oMsg);
        self::$_aFieldTypeDefinitions[ 'admin_page_framework' ] = apply_filters('field_types_admin_page_framework', $_oBuiltInFieldTypeDefinitions->get());
    }
    private function _getSavedData($aDefaultValues)
    {
        $_aSavedData = $this->getAsArray($this->callBack($this->aCallbacks[ 'saved_data' ], array( $aDefaultValues, ))) + $aDefaultValues;
        $_aLastInputs = $this->getHTTPQueryGET('field_errors') || isset($_GET[ 'confirmation' ]) ? $this->oLastInputs->get() : array();
        return $_aLastInputs + $_aSavedData;
    }
    public function getDefaultFormValues()
    {
        $_oDefaultValues = new AmazonAutoLinks_AdminPageFramework_Form_Model___DefaultValues($this->aFieldsets);
        return $_oDefaultValues->get();
    }
    protected function _formatElementDefinitions(array $aSavedData)
    {
        $_oSectionsetsFormatter = new AmazonAutoLinks_AdminPageFramework_Form_Model___FormatSectionsets($this->aSectionsets, $this->aArguments[ 'structure_type' ], $this->sCapability, $this->aCallbacks, $this);
        $this->aSectionsets = $_oSectionsetsFormatter->get();
        $_oFieldsetsFormatter = new AmazonAutoLinks_AdminPageFramework_Form_Model___FormatFieldsets($this->aFieldsets, $this->aSectionsets, $this->aArguments[ 'structure_type' ], $this->aSavedData, $this->sCapability, $this->aCallbacks, $this);
        $this->aFieldsets = $_oFieldsetsFormatter->get();
    }
    public function getFieldErrors()
    {
        $_aErrors = $this->oFieldError->get();
        $this->oFieldError->delete();
        return $_aErrors;
    }
    public function setLastInputs(array $aLastInputs)
    {
        $this->oLastInputs->set($aLastInputs);
    }
}
