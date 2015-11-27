<?php
abstract class AmazonAutoLinks_AdminPageFramework_Format_Base extends AmazonAutoLinks_AdminPageFramework_WPUtility {
    static public $aStructure = array();
    public $aSubject = array();
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSubject,);
        $this->aSubject = $_aParameters[0];
    }
    public function get() {
        return $this->aSubject;
    }
}