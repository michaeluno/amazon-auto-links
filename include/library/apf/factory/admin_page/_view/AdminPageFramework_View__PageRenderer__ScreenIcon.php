<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_View__PageRenderer__ScreenIcon extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oFactory;
    public $sPageSlug;
    public $sTabSlug;
    public function __construct($oFactory, $sPageSlug, $sTabSlug)
    {
        $this->oFactory = $oFactory;
        $this->sPageSlug = $sPageSlug;
        $this->sTabSlug = $sTabSlug;
    }
    public function get()
    {
        if (! $this->_isScreenIconVisible()) {
            return '';
        }
        return $this->_getScreenIcon($this->sPageSlug);
    }
    private function _isScreenIconVisible()
    {
        $_bShowPageTitle = $this->getElement($this->oFactory->oProp->aPages, array( $this->sPageSlug, 'show_page_title' ));
        if ($_bShowPageTitle) {
            return true;
        }
        $_bShowPageHeadingTabs = $this->getElement($this->oFactory->oProp->aPages, array( $this->sPageSlug, 'show_page_heading_tabs' ));
        if ($_bShowPageHeadingTabs) {
            return true;
        }
        $_bShowInPageTabs = $this->getElement($this->oFactory->oProp->aPages, array( $this->sPageSlug, 'show_in_page_tabs' ));
        if ($_bShowInPageTabs) {
            return true;
        }
        $_bShowInPageTab = $this->getElementAsArray($this->oFactory->oProp->aInPageTabs, array( $this->sPageSlug, $this->sTabSlug, 'show_in_page_tab' ), false);
        $_sInPageTabTitle = $this->getElement($this->oFactory->oProp->aInPageTabs, array( $this->sPageSlug, $this->sTabSlug, 'title' ));
        if ($_bShowInPageTab && $_sInPageTabTitle) {
            return true;
        }
    }
    private function _getScreenIcon($sPageSlug)
    {
        try {
            $this->_throwScreenIconByURLOrPath($sPageSlug);
            $this->_throwScreenIconByID($sPageSlug);
        } catch (Exception $_oException) {
            return $_oException->getMessage();
        }
        return $this->_getDefaultScreenIcon();
    }
    private function _throwScreenIconByURLOrPath($sPageSlug)
    {
        $_sScreenIconPath = $this->getElement($this->oFactory->oProp->aPages, array( $sPageSlug, 'href_icon_32x32' ), '');
        if (! $_sScreenIconPath) {
            return;
        }
        $_sScreenIconPath = $this->getResolvedSRC($_sScreenIconPath, true);
        $_aAttributes = array( 'style' => $this->getInlineCSS(array( 'background-image' => "url('" . esc_url($_sScreenIconPath) . "')" )) );
        throw new Exception($this->_getScreenIconByAttributes($_aAttributes));
    }
    private function _throwScreenIconByID($sPageSlug)
    {
        $_sScreenIconID = $this->getElement($this->oFactory->oProp->aPages, array( $sPageSlug, 'screen_icon_id' ), '');
        if (! $_sScreenIconID) {
            return;
        }
        $_aAttributes = array( 'id' => "icon-" . $_sScreenIconID, );
        throw new Exception($this->_getScreenIconByAttributes($_aAttributes));
    }
    private function _getDefaultScreenIcon()
    {
        $_oScreen = get_current_screen();
        $_sIconIDAttribute = $this->_getScreenIDAttribute($_oScreen);
        $_aAttributes = array( 'class' => $this->getClassAttribute($this->getAOrB(empty($_sIconIDAttribute) && $_oScreen->post_type, sanitize_html_class('icon32-posts-' . $_oScreen->post_type), ''), $this->getAOrB(empty($_sIconIDAttribute) || $_sIconIDAttribute == $this->oFactory->oProp->sClassName, 'generic', '')), 'id' => "icon-" . $_sIconIDAttribute, );
        return $this->_getScreenIconByAttributes($_aAttributes);
    }
    private function _getScreenIDAttribute($oScreen)
    {
        if (! empty($oScreen->parent_base)) {
            return $oScreen->parent_base;
        }
        if ('page' === $oScreen->post_type) {
            return 'edit-pages';
        }
        return esc_attr($oScreen->base);
    }
    private function _getScreenIconByAttributes(array $aAttributes)
    {
        $aAttributes[ 'class' ] = $this->getClassAttribute('icon32', $this->getElement($aAttributes, 'class'));
        return "<div " . $this->getAttributes($aAttributes) . ">" . "<br />" . "</div>";
    }
}
