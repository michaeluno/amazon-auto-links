<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Outputs the theme-based button preview.
 *
 * @since 4.3.0
 * @since 5.2.0 Extends `AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base`. Renamed from `AmazonAutoLinks_Button_Event_Query_ButtonPreview`.
 */
class AmazonAutoLinks_Button_Event_Query_ButtonPreview_Theme extends AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base {

    /**
     * @var   array     Supported button types
     * @since 5.2.0
     */
    public $aButtonTypes = array( 'theme' );

    /**
     * @since  5.2.0
     * @return string
     */
    protected function _getHeadTagInnerHTML() {

        $_sMin               = $this->isDebugMode() ? '' : '.min';
        $_sStylesheetURL     = $this->getSRCFromPath( AmazonAutoLinks_Button_Loader::$sDirPath . "/asset/css/button-preview-framed-page{$_sMin}.css" );
        $_sScriptURL         = $this->getSRCFromPath( AmazonAutoLinks_Button_Loader::$sDirPath . "/asset/js/button-preview-framed-page{$_sMin}.js" );

        $_sHeadTagInnerHTML  = parent::_getHeadTagInnerHTML();
        $_sHeadTagInnerHTML .= "<script id='button-preview-framed-js' src='" . esc_url( $_sScriptURL ) . "' type='text/javascript'></script>";
        $_sHeadTagInnerHTML .= "<link rel='stylesheet' id='button-preview-framed-style' href='" . esc_url( $_sStylesheetURL ) . "' media='all'>";
        return $_sHeadTagInnerHTML;

    }

}