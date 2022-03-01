<?php
/*
 * Admin Page Framework v3.9.0 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_FieldType_radio extends AmazonAutoLinks_AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array( 'radio' );
    protected $aDefaultKeys = array( 'label' => array(), 'attributes' => array(), );
    protected function getEnqueuingScripts()
    {
        return array( array( 'handle_id' => 'amazon-auto-links-field-type-radio', 'src' => dirname(__FILE__) . '/js/radio.bundle.js', 'in_footer' => true, 'dependencies' => array( 'jquery', 'amazon-auto-links-script-form-main' ), 'translation_var' => 'AmazonAutoLinks_AdminPageFrameworkFieldTypeRadio', 'translation' => array( 'fieldTypeSlugs' => $this->aFieldTypeSlugs, 'messages' => array(), ), ), );
    }
    protected function getField($aField)
    {
        $_aOutput = array();
        foreach ($this->getAsArray($aField[ 'label' ]) as $_sKey => $_sLabel) {
            $_aOutput[] = $this->___getEachRadioButtonOutput($aField, $_sKey, $_sLabel);
        }
        return implode(PHP_EOL, $_aOutput);
    }
    private function ___getEachRadioButtonOutput(array $aField, $sKey, $sLabel)
    {
        $_aAttributes = $aField[ 'attributes' ] + $this->getElementAsArray($aField, array( 'attributes', $sKey ));
        $_oRadio = new AmazonAutoLinks_AdminPageFramework_Input_radio($_aAttributes);
        $_oRadio->setAttributesByKey($sKey);
        $_oRadio->setAttribute('data-default', $aField[ 'default' ]);
        $_oRadio->addClass('amazon-auto-links-input-radio');
        return $this->getElementByLabel($aField[ 'before_label' ], $sKey, $aField[ 'label' ]) . "<div " . $this->getLabelContainerAttributes($aField, 'amazon-auto-links-input-label-container amazon-auto-links-radio-label') . ">" . "<label " . $this->getAttributes(array( 'for' => $_oRadio->getAttribute('id'), 'class' => $_oRadio->getAttribute('disabled') ? 'disabled' : null, )) . ">" . $this->getElementByLabel($aField[ 'before_input' ], $sKey, $aField[ 'label' ]) . $_oRadio->get($sLabel) . $this->getElementByLabel($aField[ 'after_input' ], $sKey, $aField[ 'label' ]) . "</label>" . "</div>" . $this->getElementByLabel($aField[ 'after_label' ], $sKey, $aField[ 'label' ]) ;
    }
}
