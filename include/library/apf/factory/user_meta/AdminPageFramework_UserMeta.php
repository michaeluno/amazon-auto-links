<?php
abstract class AmazonAutoLinks_AdminPageFramework_UserMeta extends AmazonAutoLinks_AdminPageFramework_UserMeta_Controller {
    static protected $_sStructureType = 'user_meta';
    public function __construct($sCapability = 'edit_user', $sTextDomain = 'amazon-auto-links') {
        $this->oProp = new AmazonAutoLinks_AdminPageFramework_Property_UserMeta($this, get_class($this), $sCapability, $sTextDomain, self::$_sStructureType);
        parent::__construct($this->oProp);
        $this->oUtil->addAndDoAction($this, "start_{$this->oProp->sClassName}");
    }
}