<?php
class AmazonAutoLinks_AdminPageFramework_Format_SubMenuLink extends AmazonAutoLinks_AdminPageFramework_Format_SubMenuPage {
    static public $aStructure = array('type' => 'link', 'title' => null, 'href' => null, 'capability' => null, 'order' => null, 'show_page_heading_tab' => true, 'show_in_menu' => true,);
    public $aSubMenuLink = array();
    public $oFactory;
    public function __construct() {
        $_aParameters = func_get_args() + array($this->aSubMenuLink, $this->oFactory,);
        $this->aSubMenuLink = $_aParameters[0];
        $this->oFactory = $_aParameters[1];
    }
    public function get() {
        return $this->_getFormattedSubMenuLinkArray($this->aSubMenuLink);
    }
    protected function _getFormattedSubMenuLinkArray(array $aSubMenuLink) {
        if (!filter_var($aSubMenuLink['href'], FILTER_VALIDATE_URL)) {
            return array();
        }
        return array('capability' => $this->getElement($aSubMenuLink, 'capability', $this->oFactory->oProp->sCapability), 'order' => isset($aSubMenuLink['order']) && is_numeric($aSubMenuLink['order']) ? $aSubMenuLink['order'] : count($this->oFactory->oProp->aPages) + 10,) + $aSubMenuLink + self::$aStructure;
    }
}