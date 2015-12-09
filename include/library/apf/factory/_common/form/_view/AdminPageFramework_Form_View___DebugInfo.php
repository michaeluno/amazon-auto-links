<?php
class AmazonAutoLinks_AdminPageFramework_Form_View___DebugInfo extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $sStructureType = '';
    public $oMsg;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->sStructureType, $this->oMsg,);
        $this->sStructureType = $_aParameters[0];
        $this->oMsg = $_aParameters[1];
    }
    public function get() {
        if (!$this->isDebugModeEnabled()) {
            return '';
        }
        if (!in_array($this->sStructureType, array('widget', 'post_meta_box', 'page_meta_box', 'user_meta'))) {
            return '';
        }
        return "<div class='amazon-auto-links-info'>" . $this->oMsg->get('debug_info') . ': ' . AmazonAutoLinks_AdminPageFramework_Registry::NAME . ' ' . AmazonAutoLinks_AdminPageFramework_Registry::getVersion() . "</div>";
    }
}