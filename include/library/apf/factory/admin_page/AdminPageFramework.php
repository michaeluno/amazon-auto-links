<?php
abstract class AmazonAutoLinks_AdminPageFramework extends AmazonAutoLinks_AdminPageFramework_Controller {
    public function __construct($isOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'amazon-auto-links') {
        if (!$this->_isInstantiatable()) {
            return;
        }
        parent::__construct($isOptionKey, $sCallerPath ? trim($sCallerPath) : $sCallerPath = (is_admin() && (isset($GLOBALS['pagenow']) && in_array($GLOBALS['pagenow'], array('plugins.php',)) || isset($_GET['page'])) ? AmazonAutoLinks_AdminPageFramework_Utility::getCallerScriptPath(__FILE__) : null), $sCapability, $sTextDomain);
        $this->oUtil->addAndDoAction($this, 'start_' . $this->oProp->sClassName, $this);
    }
}