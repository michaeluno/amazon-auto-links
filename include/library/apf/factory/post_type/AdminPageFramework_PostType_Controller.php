<?php
abstract class AmazonAutoLinks_AdminPageFramework_PostType_Controller extends AmazonAutoLinks_AdminPageFramework_PostType_View {
    public function __construct($oProp) {
        parent::__construct($oProp);
        $this->oUtil->registerAction('init', array($this, 'setup_pre'));
    }
    public function setUp() {
    }
    public function enqueueStyles($aSRCs, $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueStyles')) {
            return $this->oResource->_enqueueStyles($aSRCs, array($this->oProp->sPostType), $aCustomArgs);
        }
    }
    public function enqueueStyle($sSRC, $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueStyle')) {
            return $this->oResource->_enqueueStyle($sSRC, array($this->oProp->sPostType), $aCustomArgs);
        }
    }
    public function enqueueScripts($aSRCs, $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueScripts')) {
            return $this->oResource->_enqueueScripts($aSRCs, array($this->oProp->sPostType), $aCustomArgs);
        }
    }
    public function enqueueScript($sSRC, $aCustomArgs = array()) {
        if (method_exists($this->oResource, '_enqueueScript')) {
            return $this->oResource->_enqueueScript($sSRC, array($this->oProp->sPostType), $aCustomArgs);
        }
    }
    protected function setAutoSave($bEnableAutoSave = True) {
        $this->oProp->bEnableAutoSave = $bEnableAutoSave;
    }
    protected function addTaxonomy($sTaxonomySlug, array $aArgs, array $aAdditionalObjectTypes = array()) {
        $sTaxonomySlug = $this->oUtil->sanitizeSlug($sTaxonomySlug);
        $aArgs = $aArgs + array('show_table_filter' => null, 'show_in_sidebar_menus' => null,);
        $this->oProp->aTaxonomies[$sTaxonomySlug] = $aArgs;
        if ($aArgs['show_table_filter']) {
            $this->oProp->aTaxonomyTableFilters[] = $sTaxonomySlug;
        }
        if (!$aArgs['show_in_sidebar_menus']) {
            $this->oProp->aTaxonomyRemoveSubmenuPages["edit-tags.php?taxonomy={$sTaxonomySlug}&amp;post_type={$this->oProp->sPostType}"] = "edit.php?post_type={$this->oProp->sPostType}";
        }
        $_aExistingObjectTypes = $this->oUtil->getElementAsArray($this->oProp->aTaxonomyObjectTypes, $sTaxonomySlug, array());
        $aAdditionalObjectTypes = array_merge($_aExistingObjectTypes, $aAdditionalObjectTypes);
        $this->oProp->aTaxonomyObjectTypes[$sTaxonomySlug] = array_unique($aAdditionalObjectTypes);
        $this->_addTaxonomy_setUpHooks($sTaxonomySlug, $aArgs, $aAdditionalObjectTypes);
    }
    private function _addTaxonomy_setUpHooks($sTaxonomySlug, array $aArgs, array $aAdditionalObjectTypes) {
        if (did_action('init')) {
            $this->_registerTaxonomy($sTaxonomySlug, $aAdditionalObjectTypes, $aArgs);
        } else {
            if (1 == count($this->oProp->aTaxonomies)) {
                add_action('init', array($this, '_replyToRegisterTaxonomies'));
            }
        }
        if (did_action('admin_menu')) {
            $this->_replyToRemoveTexonomySubmenuPages();
        } else {
            if (1 == count($this->oProp->aTaxonomyRemoveSubmenuPages)) {
                add_action('admin_menu', array($this, '_replyToRemoveTexonomySubmenuPages'), 999);
            }
        }
    }
    protected function setAuthorTableFilter($bEnableAuthorTableFileter = false) {
        $this->oProp->bEnableAuthorTableFileter = $bEnableAuthorTableFileter;
    }
    protected function setPostTypeArgs($aArgs) {
        $this->setArguments(( array )$aArgs);
    }
    protected function setArguments(array $aArguments = array()) {
        $this->oProp->aPostTypeArgs = $aArguments;
    }
}