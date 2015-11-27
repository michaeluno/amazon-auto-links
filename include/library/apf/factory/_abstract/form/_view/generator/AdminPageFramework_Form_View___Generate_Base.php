<?php
abstract class AmazonAutoLinks_AdminPageFramework_Form_View___Generate_Base extends AmazonAutoLinks_AdminPageFramework_WPUtility {
    public $aArguments = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aArguments,);
        $this->aArguments = $_aParameters[0];
    }
    public function get() {
        return '';
    }
}