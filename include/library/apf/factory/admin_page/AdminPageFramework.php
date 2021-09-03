<?php 
/**
	Admin Page Framework v3.9.0b09 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AmazonAutoLinks_AdminPageFramework extends AmazonAutoLinks_AdminPageFramework_Controller {
    protected $_sStructureType = 'admin_page';
    public function __construct($isOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'amazon-auto-links') {
        if (!$this->_isInstantiatable()) {
            return;
        }
        parent::__construct($isOptionKey, $this->_getCallerPath($sCallerPath), $sCapability, $sTextDomain);
    }
    private function _getCallerPath($sCallerPath) {
        if ($sCallerPath) {
            return trim($sCallerPath);
        }
        if (!is_admin()) {
            return null;
        }
        if (!isset($GLOBALS['pagenow'])) {
            return null;
        }
        return 'plugins.php' === $GLOBALS['pagenow'] || isset($_GET['page']) ? AmazonAutoLinks_AdminPageFramework_Utility::getCallerScriptPath(__FILE__) : null;
    }
    }
    