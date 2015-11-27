<?php
class AmazonAutoLinks_AdminPageFramework_Form_View__Resource__Head extends AmazonAutoLinks_AdminPageFramework_WPUtility {
    public $oForm;
    public function __construct($oForm, $sHeadActionHook) {
        $this->oForm = $oForm;
        add_action($sHeadActionHook, array($this, '_replyToInsertRequiredInlineScripts'));
    }
    public function _replyToInsertRequiredInlineScripts() {
        if ($this->hasBeenCalled(__METHOD__)) {
            return;
        }
        if (!$this->oForm->isInThePage()) {
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