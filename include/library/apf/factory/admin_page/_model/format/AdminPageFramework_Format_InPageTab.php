<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Format_InPageTab extends AmazonAutoLinks_AdminPageFramework_Format_Base {
    public static $aStructure = array( 'page_slug' => null, 'tab_slug' => null, 'title' => null, 'order' => 10, 'show_in_page_tab' => true, 'parent_tab_slug' => null, 'url' => null, 'disabled' => null, 'attributes' => null, 'capability' => null, 'if' => true, 'show_debug_info' => null, );
    public $aInPageTab = array();
    public $sPageSlug = '';
    public $oFactory;
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aInPageTab, $this->sPageSlug, $this->oFactory, );
        $this->aInPageTab = $_aParameters[ 0 ];
        $this->sPageSlug = $_aParameters[ 1 ];
        $this->oFactory = $_aParameters[ 2 ];
    }
    public function get()
    {
        return array( 'page_slug' => $this->sPageSlug, ) + $this->aInPageTab + array( 'capability' => $this->_getPageCapability(), 'show_debug_info' => $this->_getPageShowDebugInfo(), ) + self::$aStructure;
    }
    private function _getPageShowDebugInfo()
    {
        return $this->getElement($this->oFactory->oProp->aPages, array( $this->sPageSlug, 'show_debug_info' ), $this->oFactory->oProp->bShowDebugInfo);
    }
    private function _getPageCapability()
    {
        return $this->getElement($this->oFactory->oProp->aPages, array( $this->sPageSlug, 'capability' ), $this->oFactory->oProp->sCapability);
    }
}
