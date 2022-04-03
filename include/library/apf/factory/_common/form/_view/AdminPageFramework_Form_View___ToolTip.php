<?php
/*
 * Admin Page Framework v3.9.1b05 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_View___ToolTip extends AmazonAutoLinks_AdminPageFramework_Form_View___Section_Base {
    public $aArguments = array( 'attributes' => array( 'container' => array(), 'title' => array(), 'content' => array(), 'description' => array(), 'icon' => array(), ), 'icon' => null, 'dash-icon' => 'dashicons-editor-help', 'icon_alt_text' => '[?]', 'title' => null, 'content' => null, 'width' => null, 'height' => null, );
    public $sTitleElementID;
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aArguments, $this->sTitleElementID, );
        $this->aArguments = $this->___getArgumentsFormatted($_aParameters[ 0 ], $this->aArguments);
        $this->sTitleElementID = $_aParameters[ 1 ];
    }
    private function ___getArgumentsFormatted($asArguments, $aDefaults)
    {
        $_aArguments = array();
        if ($this->___isContent($asArguments)) {
            $_aArguments[ 'content' ] = $asArguments;
            return $_aArguments + $aDefaults;
        }
        $_aArguments = $this->getAsArray($asArguments);
        $_aArguments[ 'attributes' ] = $this->uniteArrays($this->getElementAsArray($_aArguments, 'attributes'), $aDefaults[ 'attributes' ]);
        return $_aArguments + $aDefaults;
    }
    private function ___isContent($asContent)
    {
        if (is_string($asContent)) {
            return true;
        }
        if (is_array($asContent) && ! $this->isAssociative($asContent)) {
            return true;
        }
        return false;
    }
    public function get()
    {
        if (! $this->aArguments[ 'content' ]) {
            return '';
        }
        $_aAttributes = array( 'data-width' => $this->getElement($this->aArguments, array( 'width' )), 'data-height' => $this->getElement($this->aArguments, array( 'height' )), );
        return "<span " . $this->___getElementAttributes('container', array( 'amazon-auto-links-form-tooltip', 'no-js' ), $_aAttributes) . ">" . $this->___getTipIcon() . "<span " . $this->___getElementAttributes('content', 'amazon-auto-links-form-tooltip-content') . ">" . $this->___getTipTitle() . $this->___getDescriptions() . "</span>" . "</span>" ;
    }
    private function ___getTipIcon()
    {
        if (isset($this->aArguments[ 'icon' ])) {
            return $this->aArguments[ 'icon' ];
        }
        if (version_compare($GLOBALS[ 'wp_version' ], '3.8', '>=')) {
            return "<span " . $this->___getElementAttributes('icon', array( 'dashicons', $this->aArguments[ 'dash-icon' ] )) . "></span>";
        }
        return $this->aArguments[ 'icon_alt_text' ];
    }
    private function ___getTipTitle()
    {
        if (isset($this->aArguments[ 'title' ])) {
            return "<span " . $this->___getElementAttributes('title', 'amazon-auto-links-form-tooltip-title') . ">" . $this->aArguments[ 'title' ] . "</span>";
        }
        return '';
    }
    private function ___getDescriptions()
    {
        if (isset($this->aArguments[ 'content' ])) {
            return "<span " . $this->___getElementAttributes('description', 'amazon-auto-links-form-tooltip-description') . ">" . implode("</span><span " . $this->___getElementAttributes('description', 'amazon-auto-links-form-tooltip-description') . ">", $this->getAsArray($this->aArguments[ 'content' ])) . "</span>" ;
        }
        return '';
    }
    private function ___getElementAttributes($sElementKey, $asClassSelectors, $aAdditional=array())
    {
        $_aContainerAttributes = $this->getElementAsArray($this->aArguments, array( 'attributes', $sElementKey )) + array( 'class' => '' ) ;
        $_aContainerAttributes[ 'class' ] = $this->getClassAttribute($_aContainerAttributes[ 'class' ], $asClassSelectors);
        $_aContainerAttributes = $_aContainerAttributes + $aAdditional;
        return $this->getAttributes($_aContainerAttributes);
    }
}
