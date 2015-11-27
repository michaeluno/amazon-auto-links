<?php
class AmazonAutoLinks_AdminPageFramework_Form_post_meta_box extends AmazonAutoLinks_AdminPageFramework_Form_Meta {
    public $sStructureType = 'post_meta_box';
    public function construct() {
        $this->_addDefaultResources();
    }
    private function _addDefaultResources() {
        $_oCSS = new AmazonAutoLinks_AdminPageFramework_Form_View___CSS_meta_box;
        $this->addResource('inline_styles', $_oCSS->get());
    }
}