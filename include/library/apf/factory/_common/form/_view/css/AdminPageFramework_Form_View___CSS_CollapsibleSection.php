<?php 
/**
	Admin Page Framework v3.8.22b04 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2020, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
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
            return $this->___getCollapsibleSectionsRules();
        }
        private function ___getCollapsibleSectionsRules() {
            $_sCSSRules = ".amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box, .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box{font-size:13px;background-color: #fff;padding: 1em 2.6em 1em 2em;border-top: 1px solid #eee;border-bottom: 1px solid #eee;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.collapsed.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.collapsed {border-bottom: 1px solid #dfdfdf;margin-bottom: 1em; }.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box {margin-top: 0;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.collapsed {margin-bottom: 0;}#poststuff .amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.amazon-auto-links-section-title > .section-title-outer-container > .section-title-container > .section-title,#poststuff .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.amazon-auto-links-section-title > .section-title-outer-container > .section-title-container > .section-title{font-size: 1em;margin: 0 1em 0 0; }#poststuff .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.amazon-auto-links-section-title > .section-title-outer-container > .section-title-container > fieldset {line-height: 0; }#poststuff .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.amazon-auto-links-section-title > .section-title-outer-container > .section-title-container > fieldset .amazon-auto-links-field {margin: 0;padding: 0;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after {top: 0.88em;top: 34%;right: 15px;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after {content: '\\f142';}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title.collapsed:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title.collapsed:after {content: '\\f140';} .amazon-auto-links-collapsible-sections-content.amazon-auto-links-collapsible-content.accordion-section-content,.amazon-auto-links-collapsible-section-content.amazon-auto-links-collapsible-content.accordion-section-content,.amazon-auto-links-collapsible-sections-content.amazon-auto-links-collapsible-content-type-box, .amazon-auto-links-collapsible-section-content.amazon-auto-links-collapsible-content-type-box{border: 1px solid #dfdfdf;border-top: 0;background-color: #fff;}tbody.amazon-auto-links-collapsible-content {display: table-caption; padding: 10px 20px 15px 20px;}tbody.amazon-auto-links-collapsible-content.table-caption {display: table-caption; }.amazon-auto-links-collapsible-toggle-all-button-container {margin-top: 1em;margin-bottom: 1em;width: 100%;display: table; }.amazon-auto-links-collapsible-toggle-all-button.button {height: 36px;line-height: 34px;padding: 0 16px 6px;font-size: 20px;width: auto;}.flipped > .amazon-auto-links-collapsible-toggle-all-button.button.dashicons {-moz-transform: scaleY(-1);-webkit-transform: scaleY(-1);transform: scaleY(-1);filter: flipv; }.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons {margin: 0; }.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons.section_title_field_sibling {margin-top: 0;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .repeatable-section-button {background: none; line-height: 1.8em; margin: 0;padding: 0;width: 2em;height: 2em;text-align: center;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box .section-title-height-fixer, .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .section-title-height-fixer {height: 100%;width: 0;display: inline-block;vertical-align: middle;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box .section-title-outer-container, .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .section-title-outer-container {width: 88%;display: inline-block;text-align: left;vertical-align: middle;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons-outer-container,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons-outer-container {width: 10.88%;min-width: 60px; display: inline-block;text-align: right;vertical-align: middle;}@media only screen and ( max-width: 782px ) {.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box .section-title-outer-container, .amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .section-title-outer-container {width: 87.8%;}}.accordion-section-content.amazon-auto-links-collapsible-content-type-button {background-color: transparent;}.amazon-auto-links-collapsible-button {color: #888;margin-right: 0.4em;font-size: 0.8em;}.amazon-auto-links-collapsible-button-collapse {display: inline;} .collapsed .amazon-auto-links-collapsible-button-collapse {display: none;}.amazon-auto-links-collapsible-button-expand {display: none;}.collapsed .amazon-auto-links-collapsible-button-expand {display: inline;}.amazon-auto-links-collapsible-section-title .amazon-auto-links-fields {display: inline;vertical-align: middle; line-height: 1em; }.amazon-auto-links-collapsible-section-title .amazon-auto-links-field {float: none;}.amazon-auto-links-collapsible-section-title .amazon-auto-links-fieldset {display: inline;margin-right: 1em;vertical-align: middle; }#poststuff .amazon-auto-links-collapsible-title.amazon-auto-links-collapsible-section-title .section-title-container.has-fields .section-title{width: auto;display: inline-block;margin: 0 1em 0 0.4em;vertical-align: middle;}";
            $_sCSSRules.= $this->___getForWP38OrBelow();
            $_sCSSRules.= $this->___getForWP53OrAbove();
            return $_sCSSRules;
        }
        private function ___getForWP53OrAbove() {
            if (version_compare($GLOBALS['wp_version'], '5.3', '<')) {
                return '';
            }
            return ".amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .repeatable-section-button {min-width: 2.4em;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .repeatable-section-button .dashicons {font-size: 1.2em;height: 100%;vertical-align: text-top;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .repeatable-section-button .dashicons:before {vertical-align: middle;}@media screen and (max-width: 782px) {.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons {white-space: nowrap;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .repeatable-section-button {font-size: 1.4em;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .repeatable-section-button .dashicons {height: unset;vertical-align: unset;}}";
        }
        private function ___getForWP38OrBelow() {
            if (version_compare($GLOBALS['wp_version'], '3.8', '>=')) {
                return '';
            }
            return ".amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title:after {content: '';top: 18px;}.amazon-auto-links-collapsible-sections-title.amazon-auto-links-collapsible-type-box.accordion-section-title.collapsed:after,.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box.accordion-section-title.collapsed:after {content: '';} .amazon-auto-links-collapsible-toggle-all-button.button {font-size: 1em;}.amazon-auto-links-collapsible-section-title.amazon-auto-links-collapsible-type-box .amazon-auto-links-repeatable-section-buttons {top: -8px;}";
        }
    }
    