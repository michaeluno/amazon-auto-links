<?php 
/**
	Admin Page Framework v3.8.23b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2020, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Form_widget extends AmazonAutoLinks_AdminPageFramework_Form {
    public $sStructureType = 'widget';
    public function construct() {
        $this->_addDefaultResources();
    }
    private function _addDefaultResources() {
        $_oCSS = new AmazonAutoLinks_AdminPageFramework_Form_View___CSS_widget;
        $this->addResource('internal_styles', $_oCSS->get());
    }
    }
    