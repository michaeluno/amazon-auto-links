<?php
/*
 * Admin Page Framework v3.9.2b01 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2023, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View__Resource__Head extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oForm;
    public function __construct($oForm, $sHeadActionHook='admin_head')
    {
        $this->oForm = $oForm;
        if (in_array($this->oForm->aArguments[ 'structure_type' ], array( 'widget' ))) {
            return;
        }
        add_action($sHeadActionHook, array( $this, '_replyToInsertRequiredInternalScripts' ));
    }
    public function _replyToInsertRequiredInternalScripts()
    {
        if (! $this->oForm->isInThePage()) {
            return;
        }
        if ($this->hasBeenCalled(__METHOD__)) {
            return;
        }
        echo "<script type='text/javascript' class='amazon-auto-links-form-script-required-in-head'>" . '/* <![CDATA[ */ ' . $this->___getScripts_RequiredInHead() . ' /* ]]> */' . "</script>";
    }
    private function ___getScripts_RequiredInHead()
    {
        return 'document.write( "<style class=\'amazon-auto-links-js-embedded-internal-style\'>' . str_replace('\\n', '', esc_js($this->___getInternalCSS())) . '</style>" );';
    }
    private function ___getInternalCSS()
    {
        $_oLoadingCSS = new AmazonAutoLinks_AdminPageFramework_Form_View___CSS_Loading;
        $_oLoadingCSS->add($this->___getScriptElementConcealerCSSRules());
        return $_oLoadingCSS->get();
    }
    private function ___getScriptElementConcealerCSSRules()
    {
        return <<<CSSRULES
.amazon-auto-links-form-js-on{visibility:hidden}.widget .amazon-auto-links-form-js-on{visibility:visible}
CSSRULES;
    }
}
