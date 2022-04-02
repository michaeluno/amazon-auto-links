<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___CSS_widget extends AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Base {
    protected function _get()
    {
        return $this->_getWidgetRules();
    }
    private function _getWidgetRules()
    {
        return <<<CSSRULES
.widget .amazon-auto-links-section .form-table>tbody>tr>td,.widget .amazon-auto-links-section .form-table>tbody>tr>th{display:inline-block;width:100%;padding:0;float:right;clear:right}.widget .amazon-auto-links-field,.widget .amazon-auto-links-input-label-container{width:100%}.widget .sortable .amazon-auto-links-field{padding:4% 4.4% 3.2% 4.4%;width:91.2%}.widget .amazon-auto-links-field input{margin-bottom:.1em;margin-top:.1em}.widget .amazon-auto-links-field input[type=text],.widget .amazon-auto-links-field textarea{width:100%}@media screen and (max-width:782px){.widget .amazon-auto-links-fields{width:99.2%}.widget .amazon-auto-links-field input[type='checkbox'],.widget .amazon-auto-links-field input[type='radio']{margin-top:0}}
CSSRULES;
    }
    protected function _getVersionSpecific()
    {
        $_sCSSRules = '';
        if (version_compare($GLOBALS[ 'wp_version' ], '3.8', '<')) {
            $_sCSSRules .= <<<CSSRULES
.widget .amazon-auto-links-section table.mceLayout{table-layout:fixed}
CSSRULES;
        }
        if (version_compare($GLOBALS[ 'wp_version' ], '3.8', '>=')) {
            $_sCSSRules .= <<<CSSRULES
.widget .amazon-auto-links-section .form-table th{font-size:13px;font-weight:400;margin-bottom:.2em}.widget .amazon-auto-links-section .form-table{margin-top:1em}
CSSRULES;
        }
        return $_sCSSRules;
    }
}
