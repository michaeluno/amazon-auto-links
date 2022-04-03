<?php
/*
 * Admin Page Framework v3.9.1b05 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_PostType_Model__FlushRewriteRules extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oFactory;
    public function __construct($oFactory)
    {
        if (! $this->_shouldProceed($oFactory)) {
            return;
        }
        $this->oFactory = $oFactory;
        register_activation_hook($this->oFactory->oProp->sCallerPath, array( $this, '_replyToSetUpPostType' ));
        add_action('registered_post_type', array( $this, '_replyToScheduleToFlushRewriteRules' ), 10, 2);
    }
    private function _shouldProceed($oFactory)
    {
        if (! $oFactory->oProp->bIsAdmin) {
            return false;
        }
        if (! $oFactory->oProp->sCallerPath) {
            return false;
        }
        return 'plugin' === $oFactory->oProp->sScriptType;
    }
    public function _replyToSetUpPostType()
    {
        do_action("set_up_{$this->oFactory->oProp->sClassName}", $this);
    }
    public function _replyToScheduleToFlushRewriteRules($sPostType, $aArguments)
    {
        if ($this->oFactory->oProp->sPostType !== $sPostType) {
            return;
        }
        if (did_action('activate_' . plugin_basename($this->oFactory->oProp->sCallerPath))) {
            add_action('shutdown', array( $this, '_replyToFlushRewriteRules' ));
        }
    }
    public function _replyToFlushRewriteRules()
    {
        if ($this->hasBeenCalled('flush_rewrite_rules')) {
            return;
        }
        $this->flushRewriteRules();
    }
}
