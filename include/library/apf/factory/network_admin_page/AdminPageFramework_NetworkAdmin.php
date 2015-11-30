<?php
abstract class AmazonAutoLinks_AdminPageFramework_NetworkAdmin extends AmazonAutoLinks_AdminPageFramework {
    protected $_aBuiltInRootMenuSlugs = array('dashboard' => 'index.php', 'sites' => 'sites.php', 'themes' => 'themes.php', 'plugins' => 'plugins.php', 'users' => 'users.php', 'settings' => 'settings.php', 'updates' => 'update-core.php',);
    public function __construct($sOptionKey = null, $sCallerPath = null, $sCapability = 'manage_network', $sTextDomain = 'amazon-auto-links') {
        if (!$this->_isInstantiatable()) {
            return;
        }
        add_action('network_admin_menu', array($this, '_replyToBuildMenu'), 98);
        $sCallerPath = $sCallerPath ? $sCallerPath : AmazonAutoLinks_AdminPageFramework_Utility::getCallerScriptPath(__FILE__);
        $this->oProp = new AmazonAutoLinks_AdminPageFramework_Property_NetworkAdmin($this, $sCallerPath, get_class($this), $sOptionKey, $sCapability, $sTextDomain);
        parent::__construct($sOptionKey, $sCallerPath, $sCapability, $sTextDomain);
    }
    protected function _isInstantiatable() {
        if (isset($GLOBALS['pagenow']) && 'admin-ajax.php' === $GLOBALS['pagenow']) {
            return false;
        }
        if (is_network_admin()) {
            return true;
        }
        return false;
    }
    static public function getOption($sOptionKey, $asKey = null, $vDefault = null) {
        return AmazonAutoLinks_AdminPageFramework_WPUtility::getSiteOption($sOptionKey, $asKey, $vDefault);
    }
}