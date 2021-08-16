<?php 
/**
	Admin Page Framework v3.9.0b04 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Form_View___Format_SectionsetsByTab extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $aSectionsets = array();
    public $aFieldsets = array();
    public $iNestedDepth = 0;
    public $aSectionTabs = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSectionsets, $this->aFieldsets, $this->iNestedDepth,);
        $this->aSectionsets = $_aParameters[0];
        $this->aFieldsets = $_aParameters[1];
        $this->iNestedDepth = $_aParameters[2];
        $this->_divideElementsBySectionTabs($this->aSectionsets, $this->aFieldsets, $this->aSectionTabs);
    }
    public function getSectionsets($sTabSlug) {
        return $this->getElementAsArray($this->aSectionsets, $sTabSlug);
    }
    public function getFieldsets($sTabSlug) {
        return $this->getElementAsArray($this->aFieldsets, $sTabSlug);
    }
    public function getTabs() {
        return $this->aSectionTabs;
    }
    private function _divideElementsBySectionTabs(array & $aSectionsets, array & $aFieldsets, array & $aSectionTabs) {
        $_aSectionsBySectionTab = array();
        $_aFieldsBySectionTab = array();
        $_iIndex = 0;
        foreach ($aSectionsets as $_sSectionPath => $_aSectionset) {
            if (!isset($aFieldsets[$_sSectionPath]) && !$this->_isCustomContentSet($_aSectionset)) {
                continue;
            }
            if ($this->iNestedDepth <> $_aSectionset['_nested_depth']) {
                continue;
            }
            $_sSectionTaqbSlug = $this->getAOrB($_aSectionset['section_tab_slug'], $_aSectionset['section_tab_slug'], '_default_' . $this->iNestedDepth . '_' . (++$_iIndex));
            $_aSectionsBySectionTab[$_sSectionTaqbSlug][$_sSectionPath] = $_aSectionset;
            $_aFieldsBySectionTab[$_sSectionTaqbSlug][$_sSectionPath] = $this->getElement($aFieldsets, $_sSectionPath);
            $aSectionTabs[$_sSectionTaqbSlug] = $_sSectionTaqbSlug;
        }
        $aSectionsets = $_aSectionsBySectionTab;
        $aFieldsets = $_aFieldsBySectionTab;
    }
    private function _isCustomContentSet(array $aSectionset, array $aKeys = array('content')) {
        return isset($aSectionset['content']);
    }
    }
    