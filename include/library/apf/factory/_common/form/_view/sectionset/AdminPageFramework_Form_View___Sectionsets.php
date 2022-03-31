<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___Sectionsets extends AmazonAutoLinks_AdminPageFramework_Form_View___Section_Base {
    public $aArguments = array( 'structure_type' => 'admin_page', 'capability' => '', 'nested_depth' => 0, );
    public $aStructure = array( 'field_type_definitions' => array(), 'sectionsets' => array(), 'fieldsets' => array(), );
    public $aSavedData = array();
    public $aFieldErrors = array();
    public $aCallbacks = array( 'section_head_output' => null, 'fieldset_output' => null, );
    public $oMsg;
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aArguments, $this->aStructure, $this->aSavedData, $this->aFieldErrors, $this->aCallbacks, $this->oMsg, );
        $this->aArguments = $this->getAsArray($_aParameters[ 0 ]) + $this->aArguments;
        $this->aStructure = $this->getAsArray($_aParameters[ 1 ]) + $this->aStructure;
        $this->aSavedData = $this->getAsArray($_aParameters[ 2 ]);
        $this->aFieldErrors = $this->getAsArray($_aParameters[ 3 ]);
        $this->aCallbacks = $this->getAsArray($_aParameters[ 4 ]) + $this->aCallbacks;
        $this->oMsg = $_aParameters[ 5 ];
    }
    public function get()
    {
        $_oFormatSectionsetsByTab = new AmazonAutoLinks_AdminPageFramework_Form_View___Format_SectionsetsByTab($this->aStructure[ 'sectionsets' ], $this->aStructure[ 'fieldsets' ], $this->aArguments[ 'nested_depth' ]);
        $_aOutput = array();
        foreach ($_oFormatSectionsetsByTab->getTabs() as $_sSectionTabSlug) {
            $_aOutput[] = $this->___getFormOutput($_oFormatSectionsetsByTab->getSectionsets($_sSectionTabSlug), $_oFormatSectionsetsByTab->getFieldsets($_sSectionTabSlug), $_sSectionTabSlug, $this->aCallbacks);
        }
        $_oDebugInfo = new AmazonAutoLinks_AdminPageFramework_Form_View___DebugInfo($this->aArguments[ 'structure_type' ], $this->aCallbacks, $this->oMsg);
        $_sOutput = implode(PHP_EOL, $_aOutput);
        $_sElementID = "amazon-auto-links-sectionsets-" . uniqid();
        return $this->___getSpinnerOutput($_sOutput) . "<div id='{$_sElementID}' class='amazon-auto-links-sectionsets amazon-auto-links-form-js-on'>" . $_sOutput . $_oDebugInfo->get() . "</div>" ;
    }
    private function ___getSpinnerOutput($_sOutput)
    {
        if (trim($_sOutput)) {
            return "<div class='amazon-auto-links-form-loading' style='display: none;'>" . $this->oMsg->get('loading') . "</div>";
        }
        return '';
    }
    private function ___getFormOutput(array $aSectionsets, array $aFieldsets, $sSectionTabSlug, $aCallbacks)
    {
        $_sSectionSet = $this->___getSectionsetsTables($aSectionsets, $aFieldsets, $aCallbacks);
        return $_sSectionSet ? "<div " . $this->getAttributes(array( 'class' => 'amazon-auto-links-sectionset', 'id' => "sectionset-{$sSectionTabSlug}_" . md5(serialize($aSectionsets)), )) . ">" . $_sSectionSet . "</div>" : '';
    }
    private function ___getSectionsetsTables(array $aSectionsets, array $aFieldsets, array $aCallbacks)
    {
        if (empty($aSectionsets)) {
            return '';
        }
        if (! count($aFieldsets)) {
            return '';
        }
        $_aFirstSectionset = $this->getFirstElement($aSectionsets);
        $_aOutputs = array( 'section_tab_list' => array(), 'section_contents' => array(), 'count_subsections' => 0, );
        $_sSectionTabSlug = $_aFirstSectionset[ 'section_tab_slug' ];
        $_sThisSectionID = $_aFirstSectionset[ 'section_id' ];
        $_sSectionsID = 'sections-' . $_sThisSectionID;
        $_aCollapsible = $this->___getCollapsibleArgumentForSections($_aFirstSectionset);
        foreach ($aSectionsets as $_aSectionset) {
            $_aOutputs = $this->___getSectionsetTable($_aOutputs, $_sSectionsID, $_aSectionset, $aFieldsets);
        }
        $_aOutputs[ 'section_contents' ] = array_filter($_aOutputs[ 'section_contents' ]);
        return $this->___getFormattedSectionsTablesOutput($_aOutputs, $_aFirstSectionset, $_sSectionsID, $this->getAsArray($_aCollapsible), $_sSectionTabSlug);
    }
    private function ___getCollapsibleArgumentForSections(array $aSectionset=array())
    {
        $_oArgumentFormater = new AmazonAutoLinks_AdminPageFramework_Form_Model___Format_CollapsibleSection($aSectionset[ 'collapsible' ], $aSectionset[ 'title' ], $aSectionset);
        $_aCollapsible = $this->getAsArray($_oArgumentFormater->get());
        return isset($_aCollapsible[ 'container' ]) && 'sections' === $_aCollapsible[ 'container' ] ? $_aCollapsible : array();
    }
    private function ___getSectionsetTable($_aOutputs, $_sSectionsID, array $_aSection, array $aFieldsInSections)
    {
        if (! $this->isSectionsetVisible($_aSection)) {
            return $_aOutputs;
        }
        $_aOutputs[ 'section_contents' ][] = $this->___getUnsetFlagSectionInputTag($_aSection);
        $_aSubSections = $this->getIntegerKeyElements($this->getElementAsArray($aFieldsInSections, $_aSection[ '_section_path' ], array()));
        $_aOutputs[ 'count_subsections' ] = count($_aSubSections);
        if ($_aOutputs[ 'count_subsections' ]) {
            return $this->___getSubSections($_aOutputs, $_sSectionsID, $_aSection, $_aSubSections);
        }
        $_oEachSectionArguments = new AmazonAutoLinks_AdminPageFramework_Form_Model___Format_EachSection($_aSection, null, array(), $_sSectionsID);
        return $this->___getSectionTableWithTabList($_aOutputs, $_oEachSectionArguments->get(), $this->getElementAsArray($aFieldsInSections, $_aSection[ '_section_path' ], array()));
    }
    private function ___getSubSections($_aOutputs, $_sSectionsID, $_aSection, $_aSubSections)
    {
        if (! empty($_aSection[ 'repeatable' ])) {
            $_aOutputs[ 'section_contents' ][] = AmazonAutoLinks_AdminPageFramework_Form_View___SectionRepeatableButtons::get($_sSectionsID, $_aOutputs[ 'count_subsections' ], $_aSection[ 'repeatable' ], $this->oMsg);
            $_aOutputs[ 'section_contents' ][] = $this->___getRepeatableSectionFlagTag($_aSection);
        }
        if (! empty($_aSection[ 'sortable' ])) {
            $_aOutputs[ 'section_contents' ][] = $this->___getSortableSectionFlagTag($_aSection);
        }
        $_aSubSections = $this->numerizeElements($_aSubSections);
        foreach ($_aSubSections as $_iIndex => $_aFields) {
            $_oEachSectionArguments = new AmazonAutoLinks_AdminPageFramework_Form_Model___Format_EachSection($_aSection, $_iIndex, $_aSubSections, $_sSectionsID);
            $_aOutputs = $this->___getSectionTableWithTabList($_aOutputs, $_oEachSectionArguments->get(), $_aFields);
        }
        return $_aOutputs;
    }
    private function ___getRepeatableSectionFlagTag(array $aSection)
    {
        return $this->getHTMLTag('input', array( 'class' => 'element-address', 'type' => 'hidden', 'name' => '__repeatable_elements_' . $aSection[ '_structure_type' ] . '[' . $aSection[ 'section_id' ] . ']', 'value' => $aSection[ 'section_id' ], ));
    }
    private function ___getSortableSectionFlagTag(array $aSection)
    {
        return $this->getHTMLTag('input', array( 'class' => 'element-address', 'type' => 'hidden', 'name' => '__sortable_elements_' . $aSection[ '_structure_type' ] . '[' . $aSection[ 'section_id' ] . ']', 'value' => $aSection[ 'section_id' ], ));
    }
    private function ___getUnsetFlagSectionInputTag(array $aSection)
    {
        if (false !== $aSection[ 'save' ]) {
            return '';
        }
        return $this->getHTMLTag('input', array( 'type' => 'hidden', 'name' => '__unset_' . $aSection[ '_structure_type' ] . '[' . $aSection[ 'section_id' ] . ']', 'value' => "__dummy_option_key|" . $aSection[ 'section_id' ], 'class' => 'unset-element-names element-address', ));
    }
    private function ___getSectionTableWithTabList(array $_aOutputs, array $aSectionset, $aFieldsetsPerSection)
    {
        $_aOutputs[ 'section_tab_list' ][] = $this->___getTabList($aSectionset, $aFieldsetsPerSection, $this->aCallbacks[ 'fieldset_output' ]);
        $_oSectionTable = new AmazonAutoLinks_AdminPageFramework_Form_View___Section($this->aArguments, $aSectionset, $this->aStructure, $aFieldsetsPerSection, $this->aSavedData, $this->aFieldErrors, $this->aStructure[ 'field_type_definitions' ], $this->aCallbacks, $this->oMsg);
        $_aOutputs[ 'section_contents' ][] = $_oSectionTable->get();
        return $_aOutputs;
    }
    private function ___getFormattedSectionsTablesOutput(array $aOutputs, $aSectionset, $sSectionsID, array $aCollapsible, $sSectionTabSlug)
    {
        if (empty($aOutputs[ 'section_contents' ])) {
            return '';
        }
        $_oCollapsibleSectionTitle = new AmazonAutoLinks_AdminPageFramework_Form_View___CollapsibleSectionTitle(array( 'title' => $this->getElement($aCollapsible, 'title', ''), 'tag' => 'h3', 'section_index' => null, 'collapsible' => $aCollapsible, 'container_type' => 'sections', 'sectionset' => $aSectionset, ), array(), $this->aSavedData, $this->aFieldErrors, $this->aStructure[ 'field_type_definitions' ], $this->oMsg, $this->aCallbacks);
        $_oSectionsTablesContainerAttributes = new AmazonAutoLinks_AdminPageFramework_Form_View___Attribute_SectionsTablesContainer($aSectionset, $sSectionsID, $sSectionTabSlug, $aCollapsible, $aOutputs[ 'count_subsections' ]);
        return $_oCollapsibleSectionTitle->get() . "<div " . $_oSectionsTablesContainerAttributes->get() . ">" . $this->___getSectionTabList($sSectionTabSlug, $aOutputs[ 'section_tab_list' ]) . implode(PHP_EOL, $aOutputs[ 'section_contents' ]) . "</div>";
    }
    private function ___getSectionTabList($sSectionTabSlug, array $aSectionTabList)
    {
        return $sSectionTabSlug ? "<ul class='amazon-auto-links-section-tabs nav-tab-wrapper'>" . implode(PHP_EOL, $aSectionTabList) . "</ul>" : '';
    }
    private function ___getTabList(array $aSection, array $aFields, $hfFieldCallback)
    {
        if (! $aSection[ 'section_tab_slug' ]) {
            return '';
        }
        $iSectionIndex = $aSection[ '_index' ];
        $_sSectionTagID = 'section-' . $aSection[ 'section_id' ] . '__' . $iSectionIndex;
        $_aTabAttributes = $aSection[ 'attributes' ][ 'tab' ] + array( 'class' => 'amazon-auto-links-section-tab nav-tab', 'id' => "section_tab-{$_sSectionTagID}", 'style' => null );
        $_aTabAttributes[ 'class' ] = $this->getClassAttribute($_aTabAttributes[ 'class' ], $aSection[ 'class' ][ 'tab' ]);
        $_aTabAttributes[ 'style' ] = $this->getStyleAttribute($_aTabAttributes[ 'style' ], $aSection[ 'hidden' ] ? 'display:none' : null);
        $_oSectionTitle = new AmazonAutoLinks_AdminPageFramework_Form_View___SectionTitle(array( 'title' => $aSection[ 'title' ], 'tag' => 'h4', 'section_index' => $iSectionIndex, 'sectionset' => $aSection, ), $aFields, $this->aSavedData, $this->aFieldErrors, $this->aStructure[ 'field_type_definitions' ], $this->oMsg, $this->aCallbacks);
        return "<li " . $this->getAttributes($_aTabAttributes) . ">" . "<a href='#{$_sSectionTagID}'>" . $_oSectionTitle->get() ."</a>" . "</li>";
    }
}
