<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Outputs button previews for classic buttons.
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Classic_Event_Query_ButtonPreview extends AmazonAutoLinks_Button_Event_Query_ButtonPreview_Base {

    /**
     * @var   array Supported button types
     * @since 5.2.0
     */
    public $aButtonTypes = array( 'classic' );

    /**
     * @since  5.2.0
     * @param  string $sHeadTagInnerHTML
     * @return string
     */
    protected function _getExtraResourcesAddedToHead( $sHeadTagInnerHTML ) {
        $sHeadTagInnerHTML  = parent::_getExtraResourcesAddedToHead( $sHeadTagInnerHTML );
        $_sMin              = $this->isDebugMode() ? '' : '.min';
        $_sStylesheetURL    = $this->getSRCFromPath( AmazonAutoLinks_Button_Classic_Loader::$sDirPath . "/asset/css/button-preview-framed-classic{$_sMin}.css" );
        $sHeadTagInnerHTML .= "<link rel='stylesheet' id='button-preview-framed-classic-style' href='" . esc_url( $_sStylesheetURL ) . "' media='all'>";
        return $sHeadTagInnerHTML;
    }

}