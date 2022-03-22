<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Format_SubMenuItem extends AmazonAutoLinks_AdminPageFramework_Format_Base {
    public static $aStructure = array( );
    public $aSubMenuItem = array();
    public $oFactory;
    public $iParsedIndex = 1;
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aSubMenuItem, $this->oFactory, $this->iParsedIndex, );
        $this->aSubMenuItem = $_aParameters[ 0 ];
        $this->oFactory = $_aParameters[ 1 ];
        $this->iParsedIndex = $_aParameters[ 2 ];
    }
    public function get()
    {
        $_aSubMenuItem = $this->getAsArray($this->aSubMenuItem);
        if (isset($_aSubMenuItem[ 'page_slug' ])) {
            $_oFormatter = new AmazonAutoLinks_AdminPageFramework_Format_SubMenuPage($_aSubMenuItem, $this->oFactory, $this->iParsedIndex);
            return $_oFormatter->get();
        }
        if (isset($_aSubMenuItem[ 'href' ])) {
            $_oFormatter = new AmazonAutoLinks_AdminPageFramework_Format_SubMenuLink($_aSubMenuItem, $this->oFactory, $this->iParsedIndex);
            return $_oFormatter->get();
        }
        return array();
    }
}
