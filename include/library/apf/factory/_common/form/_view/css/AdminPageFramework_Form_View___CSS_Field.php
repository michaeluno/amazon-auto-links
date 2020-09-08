<?php 
/**
	Admin Page Framework v3.8.22b04 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2020, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Field extends AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Base {
    protected function _get() {
        return $this->___getFormFieldRules();
    }
    static private function ___getFormFieldRules() {
        return "td.amazon-auto-links-field-td-no-title {padding-left: 0;padding-right: 0;}.amazon-auto-links-fields {display: table; width: 100%;table-layout: fixed;}.amazon-auto-links-field input[type='number'] {text-align: right;} .amazon-auto-links-fields .disabled,.amazon-auto-links-fields .disabled input,.amazon-auto-links-fields .disabled textarea,.amazon-auto-links-fields .disabled select,.amazon-auto-links-fields .disabled option {color: #BBB;}.amazon-auto-links-fields hr {border: 0; height: 0;border-top: 1px solid #dfdfdf; }.amazon-auto-links-fields .delimiter {display: inline;}.amazon-auto-links-fields-description {margin-bottom: 0;}.amazon-auto-links-field {float: left;clear: both;display: inline-block;margin: 1px 0;}.amazon-auto-links-field label {display: inline-block; width: 100%;}@media screen and (max-width: 782px) {.form-table fieldset > label {display: inline-block;}}.amazon-auto-links-field .amazon-auto-links-input-label-container {margin-bottom: 0.25em;}@media only screen and ( max-width: 780px ) { .amazon-auto-links-field .amazon-auto-links-input-label-container {margin-top: 0.5em; margin-bottom: 0.5em;}} .amazon-auto-links-field .amazon-auto-links-input-label-string {padding-right: 1em; vertical-align: middle; display: inline-block; }.amazon-auto-links-field .amazon-auto-links-input-button-container {padding-right: 1em; }.amazon-auto-links-field .amazon-auto-links-input-container {display: inline-block;vertical-align: middle;}.amazon-auto-links-field-image .amazon-auto-links-input-label-container { vertical-align: middle;}.amazon-auto-links-field .amazon-auto-links-input-label-container {display: inline-block; vertical-align: middle; } .repeatable .amazon-auto-links-field {clear: both;display: block;}.amazon-auto-links-repeatable-field-buttons {float: right; margin: 0.1em 0 0.5em 0.3em;vertical-align: middle;}.amazon-auto-links-repeatable-field-buttons .repeatable-field-button {margin: 0 0.1em;font-weight: normal;vertical-align: middle;text-align: center;}@media only screen and (max-width: 960px) {.amazon-auto-links-repeatable-field-buttons {margin-top: 0;}}.amazon-auto-links-sections.sortable-section > .amazon-auto-links-section,.sortable > .amazon-auto-links-field {clear: both;float: left;display: inline-block;padding: 1em 1.32em 1em;margin: 1px 0 0 0;border-top-width: 1px;border-bottom-width: 1px;border-bottom-style: solid;-webkit-user-select: none;-moz-user-select: none;user-select: none; text-shadow: #fff 0 1px 0;-webkit-box-shadow: 0 1px 0 #fff;box-shadow: 0 1px 0 #fff;-webkit-box-shadow: inset 0 1px 0 #fff;box-shadow: inset 0 1px 0 #fff;-webkit-border-radius: 3px;border-radius: 3px;background: #f1f1f1;background-image: -webkit-gradient(linear, left bottom, left top, from(#ececec), to(#f9f9f9));background-image: -webkit-linear-gradient(bottom, #ececec, #f9f9f9);background-image: -moz-linear-gradient(bottom, #ececec, #f9f9f9);background-image: -o-linear-gradient(bottom, #ececec, #f9f9f9);background-image: linear-gradient(to top, #ececec, #f9f9f9);border: 1px solid #CCC;background: #F6F6F6;} .amazon-auto-links-fields.sortable {margin-bottom: 1.2em; } .amazon-auto-links-field .button.button-small {width: auto;} .font-lighter {font-weight: lighter;} .amazon-auto-links-field .button.button-small.dashicons {font-size: 1.2em;padding-left: 0.2em;padding-right: 0.22em;min-width: 1em; }@media screen and (max-width: 782px) {.amazon-auto-links-field .button.button-small.dashicons {min-width: 1.8em; }}.amazon-auto-links-field .button.button-small.dashicons:before {position: relative;top: 7.2%;}@media screen and (max-width: 782px) {.amazon-auto-links-field .button.button-small.dashicons:before {top: 8.2%;}}.amazon-auto-links-field-title {font-weight: 600;min-width: 80px;margin-right: 1em;}.amazon-auto-links-fieldset {font-weight: normal;}.amazon-auto-links-input-label-container,.amazon-auto-links-input-label-string{min-width: 140px;}";
    }
    protected function _getVersionSpecific() {
        $_sCSSRules = '';
        if (version_compare($GLOBALS['wp_version'], '3.8', '<')) {
            $_sCSSRules.= ".amazon-auto-links-field .remove_value.button.button-small {line-height: 1.5em; }";
        }
        $_sCSSRules.= $this->___getForWP38OrAbove();
        $_sCSSRules.= $this->___getForWP53OrAbove();
        return $_sCSSRules;
    }
    private function ___getForWP38OrAbove() {
        if (version_compare($GLOBALS['wp_version'], '3.8', '<')) {
            return '';
        }
        return ".amazon-auto-links-repeatable-field-buttons {margin: 2px 0 0 0.3em;}.amazon-auto-links-repeatable-field-buttons.disabled > .repeatable-field-button {color: #edd;border-color: #edd;} @media screen and ( max-width: 782px ) {.amazon-auto-links-fieldset {overflow-x: hidden;overflow-y: hidden;}}";
    }
    private function ___getForWP53OrAbove() {
        if (version_compare($GLOBALS['wp_version'], '5.3', '<')) {
            return '';
        }
        return ".amazon-auto-links-field .button.button-small.dashicons:before {position: relative;top: -5.4px;}@media screen and (max-width: 782px) {.amazon-auto-links-field .button.button-small.dashicons:before {top: -6.2%;}.amazon-auto-links-field .button.button-small.dashicons {min-width: 2.4em;}}.amazon-auto-links-repeatable-field-buttons .repeatable-field-button.button.button-small {min-width: 2.4em;padding: 0;}.repeatable-field-button .dashicons {position: relative;top: 4px;font-size: 16px;}@media screen and (max-width: 782px) {.amazon-auto-links-repeatable-field-buttons {margin: 0.5em 0 0 0.28em;}.repeatable-field-button .dashicons {position: relative;top: 10px;font-size: 18px;}.amazon-auto-links-repeatable-field-buttons .repeatable-field-button.button.button-small {margin-top: 0;margin-bottom: 0;min-width: 2.6em;min-height: 2.4em;}.amazon-auto-links-fields.sortable .amazon-auto-links-repeatable-field-buttons {margin: 0;}}";
    }
    }
    