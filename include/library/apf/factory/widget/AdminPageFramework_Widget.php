<?php
abstract class AmazonAutoLinks_AdminPageFramework_Widget extends AmazonAutoLinks_AdminPageFramework_Widget_Controller {
    static protected $_sStructureType = 'widget';
    public function __construct($sWidgetTitle, $aWidgetArguments = array(), $sCapability = 'edit_theme_options', $sTextDomain = 'amazon-auto-links') {
        if (empty($sWidgetTitle)) {
            return;
        }
        $this->oProp = new AmazonAutoLinks_AdminPageFramework_Property_Widget($this, null, get_class($this), $sCapability, $sTextDomain, self::$_sStructureType);
        $this->oProp->sWidgetTitle = $sWidgetTitle;
        $this->oProp->aWidgetArguments = $aWidgetArguments;
        parent::__construct($this->oProp);
        $this->oUtil->addAndDoAction($this, "start_{$this->oProp->sClassName}", $this);
    }
}