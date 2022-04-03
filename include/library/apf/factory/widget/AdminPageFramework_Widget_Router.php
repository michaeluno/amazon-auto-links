<?php
/*
 * Admin Page Framework v3.9.1b05 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_Widget_Router extends AmazonAutoLinks_AdminPageFramework_Factory {
    public function __construct($oProp)
    {
        parent::__construct($oProp);
        $this->oUtil->registerAction('widgets_init', array( $this, '_replyToDetermineToLoad' ));
    }
    public function _replyToLoadComponents()
    {
        return;
    }
    public function __call($sMethodName, $aArguments=null)
    {
        if ($this->oProp->bAssumedAsWPWidget) {
            if (in_array($sMethodName, $this->oProp->aWPWidgetMethods)) {
                return call_user_func_array(array( $this->oProp->oWidget, $sMethodName ), $aArguments);
            }
        }
        return parent::__call($sMethodName, $aArguments);
    }
    public function __get($sPropertyName)
    {
        if ($this->oProp->bAssumedAsWPWidget) {
            if (isset($this->oProp->aWPWidgetProperties[ $sPropertyName ])) {
                return $this->oProp->oWidget->$sPropertyName;
            }
        }
        return parent::__get($sPropertyName);
    }
}
