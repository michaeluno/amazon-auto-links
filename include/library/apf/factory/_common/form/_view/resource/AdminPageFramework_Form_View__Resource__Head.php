<?php
/**
 Admin Page Framework v3.7.8 by Michael Uno
 Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
 <http://en.michaeluno.jp/amazon-auto-links>
 Copyright (c) 2013-2015, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT>
 */
class AmazonAutoLinks_AdminPageFramework_Form_View__Resource__Head extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oForm;
    public function __construct($oForm, $sHeadActionHook = 'admin_head') {
        $this->oForm = $oForm;
        if (in_array($this->oForm->aArguments['structure_type'], array('widget'))) {
            return;
        }
        add_action($sHeadActionHook, array($this, '_replyToInsertRequiredInlineScripts'));
    }
    public function _replyToInsertRequiredInlineScripts() {
        if (!$this->oForm->isInThePage()) {
            return;
        }
        if ($this->hasBeenCalled(__METHOD__)) {
            return;
        }
        echo "<script type='text/javascript' class='amazon-auto-links-form-script-required-in-head'>" . '/* <![CDATA[ */ ' . $this->_getScripts_RequiredInHead() . ' /* ]]> */' . "</script>";
    }
    private function _getScripts_RequiredInHead() {
        return 'document.write( "<style class=\'amazon-auto-links-js-embedded-inline-style\'>' . str_replace('\\n', '', esc_js($this->_getInlineCSS())) . '</style>" );';
    }
    private function _getInlineCSS() {
        $_oLoadingCSS = new AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Loading;
        $_oLoadingCSS->add($this->_getScriptElementConcealerCSSRules());
        return $_oLoadingCSS->get();
    }
    private function _getScriptElementConcealerCSSRules() {
        return <<<CSSRULES
.amazon-auto-links-form-js-on {  
    visibility: hidden;
}
.widget .amazon-auto-links-form-js-on { 
    visibility: visible; 
}
CSSRULES;
        
    }
}