<?php
/*
 * Admin Page Framework v3.9.1b02 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_PageLoadInfo_Base extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oProp;
    public $oMsg;
    protected $_nInitialMemoryUsage;
    public function __construct($oProp, $oMsg)
    {
        if (! $this->_shouldProceed($oProp)) {
            return;
        }
        $this->oProp = $oProp;
        $this->oMsg = $oMsg;
        $this->_nInitialMemoryUsage = memory_get_usage();
        add_action('in_admin_footer', array( $this, '_replyToSetPageLoadInfoInFooter' ), 999);
    }
    private function _shouldProceed($oProp)
    {
        if ($oProp->bIsAdminAjax || ! $oProp->bIsAdmin) {
            return false;
        }
        return ( boolean ) $oProp->bShowDebugInfo;
    }
    public function _replyToSetPageLoadInfoInFooter()
    {}
    private static $_bLoadedPageLoadInfo = false;
    public function _replyToGetPageLoadInfo($sFooterHTML)
    {
        if (! $this->oProp->bShowDebugInfo) {
            return $sFooterHTML;
        }
        if (self::$_bLoadedPageLoadInfo) {
            return $sFooterHTML;
        }
        self::$_bLoadedPageLoadInfo = true;
        return $sFooterHTML . $this->___getPageLoadStats();
    }
    private function ___getPageLoadStats()
    {
        $_nSeconds = timer_stop(0);
        $_nQueryCount = get_num_queries();
        $_iMemoryUsage = memory_get_usage();
        $_nMemoryUsage = round($_iMemoryUsage, 2);
        $_sMemoryUsage = $this->getReadableBytes($_iMemoryUsage);
        $_nMemoryPeakUsage = round(memory_get_peak_usage(), 2);
        $_sMemoryPeakUsage = $this->getReadableBytes($_nMemoryPeakUsage);
        $_iMemoryLimit = $this->getNumberOfReadableSize(WP_MEMORY_LIMIT);
        $_sMemoryLimit = $this->getReadableBytes($_iMemoryLimit);
        $_nMemoryLimit = round($_iMemoryLimit, 2);
        $_nInitialMemoryUsage = round($this->_nInitialMemoryUsage, 2);
        $_sInitialMemoryUsage = $this->getReadableBytes($_nInitialMemoryUsage);
        return "<div id='amazon-auto-links-page-load-stats'>" . "<ul>" . "<li>" . sprintf($this->oMsg->get('queries_in_seconds'), $_nQueryCount, $_nSeconds) . "</li>" . "<li>" . sprintf($this->oMsg->get('out_of_x_memory_used'), $_sMemoryUsage, $_sMemoryLimit, round(($_nMemoryUsage / $_nMemoryLimit), 2) * 100 . '%') . "</li>" . "<li>" . sprintf($this->oMsg->get('peak_memory_usage'), $_sMemoryPeakUsage) . "</li>" . "<li>" . sprintf($this->oMsg->get('initial_memory_usage'), $_sInitialMemoryUsage) . "</li>" . "</ul>" . "</div>";
    }
}
