<?php
abstract class AmazonAutoLinks_AdminPageFramework_PostType extends AmazonAutoLinks_AdminPageFramework_PostType_Controller {
    public function __construct($sPostType, $aArguments = array(), $sCallerPath = null, $sTextDomain = 'amazon-auto-links') {
        if (empty($sPostType)) {
            return;
        }
        $this->oProp = new AmazonAutoLinks_AdminPageFramework_Property_PostType($this, $this->_getCallerScriptPath($sCallerPath), get_class($this), 'publish_posts', $sTextDomain, 'post_type');
        $this->oProp->sPostType = AmazonAutoLinks_AdminPageFramework_WPUtility::sanitizeSlug($sPostType);
        $this->oProp->aPostTypeArgs = $aArguments;
        parent::__construct($this->oProp);
        $this->oUtil->addAndDoAction($this, "start_{$this->oProp->sClassName}", $this);
    }
    private function _getCallerScriptPath($sCallerPath) {
        $sCallerPath = trim($sCallerPath);
        if ($sCallerPath) {
            return $sCallerPath;
        }
        if (!is_admin()) {
            return null;
        }
        $_sPageNow = AmazonAutoLinks_AdminPageFramework_Utility::getElement($GLOBALS, 'pagenow');
        if (in_array($_sPageNow, array('edit.php', 'post.php', 'post-new.php', 'plugins.php', 'tags.php', 'edit-tags.php',))) {
            return AmazonAutoLinks_AdminPageFramework_Utility::getCallerScriptPath(__FILE__);
        }
        return null;
    }
}