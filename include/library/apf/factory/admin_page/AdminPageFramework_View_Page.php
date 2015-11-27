<?php
abstract class AmazonAutoLinks_AdminPageFramework_View_Page extends AmazonAutoLinks_AdminPageFramework_Model_Page {
    public function __construct($sOptionKey = null, $sCallerPath = null, $sCapability = 'manage_options', $sTextDomain = 'amazon-auto-links') {
        parent::__construct($sOptionKey, $sCallerPath, $sCapability, $sTextDomain);
        if ($this->oProp->bIsAdminAjax) {
            return;
        }
        new AmazonAutoLinks_AdminPageFramework_View__PageMetaboxEnabler($this);
    }
    public function _replyToEnqueuePageAssets() {
        new AmazonAutoLinks_AdminPageFramework_View__Resource($this);
    }
    protected function _renderPage($sPageSlug, $sTabSlug = null) {
        $_oPageRenderer = new AmazonAutoLinks_AdminPageFramework_View__PageRenderer($this, $sPageSlug, $sTabSlug);
        $_oPageRenderer->render();
    }
}