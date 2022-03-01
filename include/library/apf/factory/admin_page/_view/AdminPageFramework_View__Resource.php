<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_View__Resource extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oFactory;
    public $sCurrentPageSlug;
    public $sCurrentTabSlug;
    public $aCSSRules = array();
    public $aScripts = array();
    public function __construct($oFactory)
    {
        $this->oFactory = $oFactory;
        $this->sCurrentPageSlug = $oFactory->oProp->getCurrentPageSlug();
        $this->sCurrentTabSlug = $oFactory->oProp->getCurrentTabSlug($this->sCurrentPageSlug);
        $this->_parseAssets($oFactory);
        $this->_setHooks();
    }
    private function _setHooks()
    {
        add_action("style_{$this->sCurrentPageSlug}", array( $this, '_replyToAddInternalCSSRules' ));
        if ($this->sCurrentTabSlug) {
            add_action("style_{$this->sCurrentPageSlug}_{$this->sCurrentTabSlug}", array( $this, '_replyToAddInternalCSSRules' ));
        }
        add_action("script_{$this->sCurrentPageSlug}", array( $this, '_replyToAddInternalScripts' ));
        if ($this->sCurrentTabSlug) {
            add_action("script_{$this->sCurrentPageSlug}_{$this->sCurrentTabSlug}", array( $this, '_replyToAddInternalScripts' ));
        }
    }
    public function _replyToAddInternalCSSRules($sCSS)
    {
        return $this->_appendInternalAssets($sCSS, $this->aCSSRules);
    }
    public function _replyToAddInternalScripts($sScript)
    {
        return $this->_appendInternalAssets($sScript, $this->aScripts);
    }
    public function _appendInternalAssets($sInternal, &$aContainer)
    {
        $_aInternals = array_unique($aContainer);
        $sInternal = PHP_EOL . $sInternal;
        foreach ($_aInternals as $_iIndex => $_sInternal) {
            $sInternal .= $_sInternal . PHP_EOL;
            unset($_aInternals[ $_iIndex ]);
        }
        $aContainer = $_aInternals;
        return $sInternal;
    }
    private function _parseAssets($oFactory)
    {
        $_aPageStyles = $this->getElementAsArray($oFactory->oProp->aPages, array( $this->sCurrentPageSlug, 'style' ));
        $this->_enqueuePageAssets($_aPageStyles, 'style');
        $_aPageScripts = $this->getElementAsArray($oFactory->oProp->aPages, array( $this->sCurrentPageSlug, 'script' ));
        $this->_enqueuePageAssets($_aPageScripts, 'script');
        if (! $this->sCurrentTabSlug) {
            return;
        }
        $_aInPageTabStyles = $this->getElementAsArray($oFactory->oProp->aInPageTabs, array( $this->sCurrentPageSlug, $this->sCurrentTabSlug, 'style' ));
        $this->_enqueuePageAssets($_aInPageTabStyles, 'style');
        $_aInPageTabScripts = $this->getElementAsArray($oFactory->oProp->aInPageTabs, array( $this->sCurrentPageSlug, $this->sCurrentTabSlug, 'script' ));
        $this->_enqueuePageAssets($_aInPageTabScripts, 'script');
    }
    private function _enqueuePageAssets(array $aPageAssets, $sType='style')
    {
        $_sMethodName = "_enqueueAsset_" . $sType;
        foreach ($aPageAssets as $_asPageAsset) {
            $this->{$_sMethodName}($_asPageAsset);
        }
    }
    private function _enqueueAsset_style($asPageStyle)
    {
        $_oFormatter = new AmazonAutoLinks_AdminPageFramework_Format_PageResource_Style($asPageStyle);
        $_aPageStyle = $_oFormatter->get();
        $_sSRC = $_aPageStyle[ 'src' ];
        if (file_exists($_sSRC) || filter_var($_sSRC, FILTER_VALIDATE_URL)) {
            return $this->oFactory->enqueueStyle($_sSRC, $this->sCurrentPageSlug, $this->sCurrentTabSlug, $_aPageStyle);
        }
        $this->aCSSRules[] = $_sSRC;
    }
    private function _enqueueAsset_script($asPageScript)
    {
        $_oFormatter = new AmazonAutoLinks_AdminPageFramework_Format_PageResource_Script($asPageScript);
        $_aPageScript = $_oFormatter->get();
        $_sSRC = $_aPageScript[ 'src' ];
        if ($this->isResourcePath($_sSRC)) {
            return $this->oFactory->enqueueScript($_sSRC, $this->sCurrentPageSlug, $this->sCurrentTabSlug, $_aPageScript);
        }
        $this->aScripts[] = $_sSRC;
    }
}
