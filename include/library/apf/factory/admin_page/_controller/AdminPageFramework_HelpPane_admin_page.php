<?php
/*
 * Admin Page Framework v3.9.1b03 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_HelpPane_admin_page extends AmazonAutoLinks_AdminPageFramework_HelpPane_Base {
    protected static $_aStructure_HelpTabUserArray = array( 'page_slug' => null, 'page_tab_slug' => null, 'help_tab_title' => null, 'help_tab_id' => null, 'help_tab_content' => null, 'help_tab_sidebar_content' => null, );
    public function __construct($oProp)
    {
        parent::__construct($oProp);
        if ($oProp->bIsAdminAjax) {
            return;
        }
        add_action('admin_head', array( $this, '_replyToRegisterHelpTabs' ), 200);
    }
    public function _replyToRegisterHelpTabs()
    {
        if (! $this->oProp->oCaller->isInThePage()) {
            return;
        }
        $_sCurrentPageSlug = $this->oProp->getCurrentPageSlug();
        $_sCurrentTabSlug = $this->oProp->getCurrentTabSlug($_sCurrentPageSlug);
        if (! $this->oProp->isPageAdded($_sCurrentPageSlug)) {
            return;
        }
        foreach ($this->oProp->aHelpTabs as $aHelpTab) {
            $this->_registerHelpTab($aHelpTab, $_sCurrentPageSlug, $_sCurrentTabSlug);
        }
    }
    private function _registerHelpTab(array $aHelpTab, $sCurrentPageSlug, $sCurrentTabSlug)
    {
        if ($sCurrentPageSlug != $aHelpTab['sPageSlug']) {
            return;
        }
        if (isset($aHelpTab['sPageTabSlug']) && ! empty($aHelpTab['sPageTabSlug']) && $sCurrentTabSlug != $aHelpTab['sPageTabSlug']) {
            return;
        }
        $this->_setHelpTab($aHelpTab[ 'sID' ], $aHelpTab[ 'sTitle' ], $aHelpTab[ 'aContent' ], $aHelpTab[ 'aSidebar' ]);
    }
    public function _addHelpTab($aHelpTab)
    {
        $aHelpTab = ( array ) $aHelpTab + self::$_aStructure_HelpTabUserArray;
        if (! isset($this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ])) {
            $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ] = array( 'sID' => $aHelpTab['help_tab_id'], 'sTitle' => $aHelpTab['help_tab_title'], 'aContent' => ! empty($aHelpTab['help_tab_content']) ? array( $this->_formatHelpDescription($aHelpTab['help_tab_content']) ) : array(), 'aSidebar' => ! empty($aHelpTab['help_tab_sidebar_content']) ? array( $this->_formatHelpDescription($aHelpTab['help_tab_sidebar_content']) ) : array(), 'sPageSlug' => $aHelpTab['page_slug'], 'sPageTabSlug' => $aHelpTab['page_tab_slug'], );
            return;
        }
        if (! empty($aHelpTab['help_tab_content'])) {
            $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aContent'][] = $this->_formatHelpDescription($aHelpTab['help_tab_content']);
        }
        if (! empty($aHelpTab['help_tab_sidebar_content'])) {
            $this->oProp->aHelpTabs[ $aHelpTab['help_tab_id'] ]['aSidebar'][] = $this->_formatHelpDescription($aHelpTab['help_tab_sidebar_content']);
        }
    }
}
