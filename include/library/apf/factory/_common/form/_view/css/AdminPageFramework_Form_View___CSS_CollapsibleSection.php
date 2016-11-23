<?php 
/**
	Admin Page Framework v3.8.11b04 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2016, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Base extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $aAdded = array();
    public function add($sCSSRules) {
        $this->aAdded[] = $sCSSRules;
    }
    public function get() {
        $_sCSSRules = $this->_get() . PHP_EOL;
        $_sCSSRules.= $this->_getVersionSpecific();
        $_sCSSRules.= implode(PHP_EOL, $this->aAdded);
        return $_sCSSRules;
    }
    protected function _get() {
        return '';
    }
    protected function _getVersionSpecific() {
        return '';
    }
}
class AmazonAutoLinks_AdminPageFramework_Form_View___CSS_CollapsibleSection extends AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Base {
    protected function _get() {
        return $this->_getCollapsibleSectionsRules();
    }
    private function _getCollapsibleSectionsRules() {
        $_sCSSRules = ".amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box, .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box{font-size:13px;background-color: #fff;padding: 15px 18px;margin-top: 1em; border-top: 1px solid #eee;border-bottom: 1px solid #eee;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.collapsed.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.collapsed {border-bottom: 1px solid #dfdfdf;margin-bottom: 1em; }.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box {margin-top: 0;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.collapsed {margin-bottom: 0;}#poststuff .amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.amazon-auto-links-section-title h3,#poststuff .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.amazon-auto-links-section-title h3{font-size: 1em;margin: 0;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after {top: 12px;right: 15px;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after {content: '\\f142';}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title.collapsed:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title.collapsed:after {content: '\\f140';} .amazon-auto-links-collapsible-sections-content.amazon-auto-links-collapsible-content.accordion-section-content,.amazon-auto-links-collapsible-section-content.amazon-auto-links-collapsible-content.accordion-section-content,.amazon-auto-links-collapsible-sections-content.amazon-auto-links-collapsible-content-type-box, .amazon-auto-links-collapsible-section-content.amazon-auto-links-collapsible-content-type-box{border: 1px solid #dfdfdf;border-top: 0;background-color: #fff;}tbody.amazon-auto-links-collapsible-content {display: table-caption; padding: 10px 20px 15px 20px;}tbody.amazon-auto-links-collapsible-content.table-caption {display: table-caption; }.amazon-auto-links-collapsible-toggle-all-button-container {margin-top: 1em;margin-bottom: 1em;width: 100%;display: table; }.amazon-auto-links-collapsible-toggle-all-button.button {height: 36px;line-height: 34px;padding: 0 16px 6px;font-size: 20px;width: auto;}.flipped > .amazon-auto-links-collapsible-toggle-all-button.button.dashicons {-moz-transform: scaleY(-1);-webkit-transform: scaleY(-1);transform: scaleY(-1);filter: flipv; }.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons {margin: 0;margin-right: 2em; }.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons.section_title_field_sibling {margin-top: 0;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .repeatable-section-button {background: none; }.accordion-section-content.amazon-auto-links-collapsible-content-type-button {background-color: transparent;}.amazon-auto-links-collapsible-button {color: #888;margin-right: 0.4em;font-size: 0.8em;}.amazon-auto-links-collapsible-button-collapse {display: inline;} .collapsed .amazon-auto-links-collapsible-button-collapse {display: none;}.amazon-auto-links-collapsible-button-expand {display: none;}.collapsed .amazon-auto-links-collapsible-button-expand {display: inline;}.amazon-auto-links-collapsible-section-title .amazon-auto-links-fields {display: inline;}.amazon-auto-links-collapsible-section-title .amazon-auto-links-field {float: none;}.amazon-auto-links-collapsible-section-title .amazon-auto-links-fieldset {display: inline;margin-right: 1em;vertical-align: middle; }#poststuff .amazon-auto-links-collapsible-title.amazon-auto-links-collapsible-section-title .section-title-container.has-fields .section-title{width: auto;display: inline-block;margin: 0 1em 0 0.4em;vertical-align: middle;}";
        if (version_compare($GLOBALS['wp_version'], '3.8', '<')) {
            $_sCSSRules.= ".amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after {content: '';top: 18px;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title.collapsed:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title.collapsed:after {content: '';} .amazon-auto-links-collapsible-toggle-all-button.button {font-size: 1em;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons {top: -8px;}";
        }
        return $_sCSSRules;
    }
}
