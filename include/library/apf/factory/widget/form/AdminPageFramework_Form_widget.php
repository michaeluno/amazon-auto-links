<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_widget extends AmazonAutoLinks_AdminPageFramework_Form {
    public $sStructureType = 'widget';
    public function construct()
    {
        $this->_addDefaultResources();
    }
    private function _addDefaultResources()
    {
        $_oCSS = new AmazonAutoLinks_AdminPageFramework_Form_View___CSS_widget;
        $this->addResource('internal_styles', $_oCSS->get());
    }
}
