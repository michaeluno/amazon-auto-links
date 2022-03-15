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
 * Outputs the image button preview.
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_Event_Query_ButtonPreview extends AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base {

    /**
     * @var   array     Supported button types
     * @since 5.2.0
     */
    public $aButtonTypes = array( 'image' );

    /**
     * @since  5.2.0
     * @param  integer $iButtonID
     * @param  string  $sButtonLabel
     * @return string
     */
    protected function _getBodyTagInnerHTML( $iButtonID, $sButtonLabel ) {
        return "<div id='preview-button'>"
                . $this->getButton( $iButtonID, $sButtonLabel, true, false, 'image' )
            . "</div>";
    }

    /**
     * @since  5.2.0
     * @param  string $sHeadTagInnerHTML
     * @return string
     */
    protected function _getExtraResourcesAddedToHead( $sHeadTagInnerHTML ) {
        $_sMin              = $this->isDebugMode() ? '' : '.min';
        $_sStylesheetURL1   = $this->getSRCFromPath( AmazonAutoLinks_Button_Loader::$sDirPath . "/asset/css/button-preview-framed-page" . $_sMin . ".css" );
        $_sStylesheetURL2   = $this->getSRCFromPath( AmazonAutoLinks_Button_Image_Loader::$sDirPath . "/asset/css/button-image-preview-framed" . $_sMin . ".css" );
        $_sScriptURL        = $this->getSRCFromPath( AmazonAutoLinks_Button_Image_Loader::$sDirPath . "/asset/js/button-image-preview-framed" . $_sMin . ".js" );
        $sHeadTagInnerHTML .= "<script id='button-preview-framed-image-js' src='" . esc_url( $_sScriptURL ) . "' type='text/javascript'></script>";
        $sHeadTagInnerHTML .= "<link rel='stylesheet' id='button-preview-framed-style' href='" . esc_url( $_sStylesheetURL1 ) . "' media='all'>";
        $sHeadTagInnerHTML .= "<link rel='stylesheet' id='button-image-preview-framed-style' href='" . esc_url( $_sStylesheetURL2 ) . "' media='all'>";
        return $sHeadTagInnerHTML;
    }

}