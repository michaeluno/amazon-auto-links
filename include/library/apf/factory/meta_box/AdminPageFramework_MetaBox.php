<?php
abstract class AmazonAutoLinks_AdminPageFramework_MetaBox extends AmazonAutoLinks_AdminPageFramework_MetaBox_Controller {
    static protected $_sStructureType = 'post_meta_box';
    function __construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID = array('post'), $sContext = 'normal', $sPriority = 'default', $sCapability = 'edit_posts', $sTextDomain = 'amazon-auto-links') {
        if (!$this->_isInstantiatable()) {
            return;
        }
        $this->oProp = new AmazonAutoLinks_AdminPageFramework_Property_MetaBox($this, get_class($this), $sCapability, $sTextDomain, self::$_sStructureType);
        $this->oProp->aPostTypes = is_string($asPostTypeOrScreenID) ? array($asPostTypeOrScreenID) : $asPostTypeOrScreenID;
        parent::__construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID, $sContext, $sPriority, $sCapability, $sTextDomain);
        $this->oUtil->addAndDoAction($this, "start_{$this->oProp->sClassName}", $this);
    }
}