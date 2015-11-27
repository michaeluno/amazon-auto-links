<?php
abstract class AmazonAutoLinks_AdminPageFramework_PageMetaBox extends AmazonAutoLinks_AdminPageFramework_PageMetaBox_Controller {
    function __construct($sMetaBoxID, $sTitle, $asPageSlugs = array(), $sContext = 'normal', $sPriority = 'default', $sCapability = 'manage_options', $sTextDomain = 'amazon-auto-links') {
        if (empty($asPageSlugs)) {
            return;
        }
        if (!$this->_isInstantiatable()) {
            return;
        }
        parent::__construct($sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain);
    }
}