<?php
/*
 * Admin Page Framework v3.9.1b03 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Format_SubMenuLink extends AmazonAutoLinks_AdminPageFramework_Format_SubMenuPage {
    public static $aStructure = array( 'type' => 'link', 'title' => null, 'href' => null, 'capability' => null, 'order' => null, 'show_page_heading_tab' => true, 'show_in_menu' => true, );
    public $aSubMenuLink = array();
    public $oFactory;
    public $iParsedIndex = 1;
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aSubMenuLink, $this->oFactory, $this->iParsedIndex );
        $this->aSubMenuLink = $_aParameters[ 0 ];
        $this->oFactory = $_aParameters[ 1 ];
        $this->iParsedIndex = $_aParameters[ 2 ];
    }
    public function get()
    {
        return $this->_getFormattedSubMenuLinkArray($this->aSubMenuLink);
    }
    protected function _getFormattedSubMenuLinkArray(array $aSubMenuLink)
    {
        if (! filter_var($aSubMenuLink[ 'href' ], FILTER_VALIDATE_URL)) {
            return array();
        }
        return array( 'capability' => $this->getElement($aSubMenuLink, 'capability', $this->oFactory->oProp->sCapability), 'order' => isset($aSubMenuLink[ 'order' ]) && is_numeric($aSubMenuLink[ 'order' ]) ? $aSubMenuLink[ 'order' ] : $this->iParsedIndex * 10, ) + $aSubMenuLink + self::$aStructure;
    }
}
