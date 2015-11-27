<?php
class AmazonAutoLinks_AdminPageFramework_Property_Widget extends AmazonAutoLinks_AdminPageFramework_Property_Base {
    public $_sPropertyType = 'widget';
    public $sStructureType = 'widget';
    public $sClassName = '';
    public $sCallerPath = '';
    public $sWidgetTitle = '';
    public $aWidgetArguments = array();
    public $bShowWidgetTitle = true;
    public $oWidget;
    public function __construct($oCaller, $sCallerPath, $sClassName, $sCapability = 'manage_options', $sTextDomain = 'amazon-auto-links', $sStructureType) {
        $this->_sFormRegistrationHook = 'load_' . $sClassName;
        parent::__construct($oCaller, $sCallerPath, $sClassName, $sCapability, $sTextDomain, $sStructureType);
    }
}