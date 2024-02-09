<?php
/*
 * Admin Page Framework v3.9.2b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2023, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_Widget_Controller extends AmazonAutoLinks_AdminPageFramework_Widget_View {
    public function setUp()
    {}
    public function load()
    {}
    protected function setArguments(array $aArguments=array())
    {
        $this->oProp->aWidgetArguments = $aArguments;
    }
}
