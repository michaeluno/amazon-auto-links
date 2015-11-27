<?php
abstract class AmazonAutoLinks_AdminPageFramework_MetaBox_Page extends AmazonAutoLinks_AdminPageFramework_PageMetaBox {
    function __construct($sMetaBoxID, $sTitle, $asPageSlugs = array(), $sContext = 'normal', $sPriority = 'default', $sCapability = 'manage_options', $sTextDomain = 'amazon-auto-links') {
        trigger_error(sprintf(__('The class <code>%1$s</code> is deprecated. Use <code>%2$s</code> instead.', 'amazon-auto-links'), __CLASS__, 'AmazonAutoLinks_AdminPageFramework_PageMetaBox'), E_USER_WARNING);
        parent::__construct($sMetaBoxID, $sTitle, $asPageSlugs, $sContext, $sPriority, $sCapability, $sTextDomain);
    }
}