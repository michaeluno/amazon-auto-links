<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_Widget_View extends AmazonAutoLinks_AdminPageFramework_Widget_Model {
    public function content($sContent, $aArguments, $aFormData)
    {
        return $sContent;
    }
    public function _printWidgetForm()
    {
        echo $this->oForm->get();
    }
}
