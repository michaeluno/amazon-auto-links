<?php
class AmazonAutoLinks_AdminPageFramework_Form_widget extends AmazonAutoLinks_AdminPageFramework_Form {
    public $sStructureType = 'widget';
    public function construct() {
        $this->_addDefaultResources();
    }
    private function _addDefaultResources() {
        $_oCSS = new AmazonAutoLinks_AdminPageFramework_Form_View___CSS_widget;
        $this->addResource('inline_styles', $_oCSS->get());
    }
}