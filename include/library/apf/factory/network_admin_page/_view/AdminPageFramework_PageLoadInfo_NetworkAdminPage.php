<?php
class AmazonAutoLinks_AdminPageFramework_PageLoadInfo_NetworkAdminPage extends AmazonAutoLinks_AdminPageFramework_PageLoadInfo_Base {
    private static $_oInstance;
    private static $aClassNames = array();
    function __construct($oProp, $oMsg) {
        if (is_network_admin() && defined('WP_DEBUG') && WP_DEBUG) {
            add_action('in_admin_footer', array($this, '_replyToSetPageLoadInfoInFooter'), 999);
        }
        parent::__construct($oProp, $oMsg);
    }
    public static function instantiate($oProp, $oMsg) {
        if (!is_network_admin()) {
            return;
        }
        if (in_array($oProp->sClassName, self::$aClassNames)) return self::$_oInstance;
        self::$aClassNames[] = $oProp->sClassName;
        self::$_oInstance = new AmazonAutoLinks_AdminPageFramework_PageLoadInfo_NetworkAdminPage($oProp, $oMsg);
        return self::$_oInstance;
    }
    public function _replyToSetPageLoadInfoInFooter() {
        if ($this->oProp->isPageAdded()) {
            add_filter('update_footer', array($this, '_replyToGetPageLoadInfo'), 999);
        }
    }
}