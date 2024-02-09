<?php
/*
 * Admin Page Framework v3.9.2b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2023, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_PostType extends AmazonAutoLinks_AdminPageFramework_PostType_Controller {
    protected $_sStructureType = 'post_type';
    public function __construct($sPostType, $aArguments=array(), $sCallerPath=null, $sTextDomain='amazon-auto-links')
    {
        if (empty($sPostType)) {
            return;
        }
        $_sPropertyClassName = isset($this->aSubClassNames[ 'oProp' ]) ? $this->aSubClassNames[ 'oProp' ] : 'AmazonAutoLinks_AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName($this, $this->_getCallerScriptPath($sCallerPath), get_class($this), 'publish_posts', $sTextDomain, $this->_sStructureType);
        $this->oProp->sPostType = AmazonAutoLinks_AdminPageFramework_WPUtility::sanitizeSlug($sPostType);
        $this->oProp->aPostTypeArgs = $aArguments;
        parent::__construct($this->oProp);
    }
    private function _getCallerScriptPath($sCallerPath)
    {
        $sCallerPath = trim($sCallerPath);
        if ($sCallerPath) {
            return $sCallerPath;
        }
        if (! is_admin()) {
            return null;
        }
        $_sPageNow = AmazonAutoLinks_AdminPageFramework_Utility::getElement($GLOBALS, 'pagenow');
        if (in_array($_sPageNow, array( 'edit.php', 'post.php', 'post-new.php', 'plugins.php', 'tags.php', 'edit-tags.php', 'term.php' ))) {
            return AmazonAutoLinks_AdminPageFramework_Utility::getCallerScriptPath(__FILE__);
        }
        return null;
    }
}
