<?php
/*
 * Admin Page Framework v3.9.2b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2023, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_Format_Base extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public static $aStructure = array();
    public $aSubject = array();
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aSubject, );
        $this->aSubject = $_aParameters[ 0 ];
    }
    public function get()
    {
        return $this->aSubject;
    }
}
