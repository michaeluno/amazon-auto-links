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
 * Provides shared utility methods among the button component.
 *  
 * @since 4.0.1
 * @since 5.2.0 Renamed from `AmazonAutoLinks_ButtonUtility`.
 */
class AmazonAutoLinks_Button_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * @param  integer|string $isButtonID
     * @param  integer|string $isButtonType         The button type. Accepts `_by_id`, `classic`, `image`, `theme`, `flat`, `0`. `0` is an alias for `theme`.
     * @param  null|string    $nsButtonLabel        If `null` is passed, the label will not be set. This accepts an empty string as a label.
     * @param  array          $aFrameAttributes
     * @param  array          $aContainerAttributes
     * @param  string         $sNonce               A nonce value
     * @since  5.2.0
     * @return string
     */
    static public function getIframeButtonPreview( $isButtonID, $isButtonType, $nsButtonLabel=null, array $aFrameAttributes=array(), array $aContainerAttributes=array(), $sNonce='' ) {
        $_sFrameSRC        = self::getButtonPreviewURL( $isButtonID, $isButtonType, $nsButtonLabel, $sNonce );
        $_aFrameAttributes = $aFrameAttributes + array(
            'title'          => 'Button Preview of ' . $isButtonID,    // this is a sort of internal (not apparent) attribute to avoid a warning from a browser so no need to translate
            'class'          => 'frame-button-preview',
            'data-button-id' => $isButtonID,
            'frameborder'    => '0',
            'border'         => '0',
            // 'width'          => 200,
            // 'height'         => 60,
            // 'style'          => 'height:60px;border:none;overflow:hidden;',
            'style'          => 'height:60px; border:none; overflow:hidden; margin: 0 auto; display: block; max-width: 100%',   // max-width is needed for the classic button editing screen
            'scrolling'      => 'no',
            'src'            => $_sFrameSRC,
            'data-src'       => $_sFrameSRC,    // this is for screens that have multiple iframes which need to load one by one to reduce the load.
        );
        $_aContainerAttributes = $aContainerAttributes + array(
            'class'       => 'iframe-button-preview-container',
        );
        return "<div " . self::getAttributes( $_aContainerAttributes ) . ">"
                . "<iframe " . self::getAttributes( $_aFrameAttributes ) . "></iframe>"
            . "</div>";
    }
    
    /**
     * @since  5.2.0
     * @param  integer|string $isButtonID
     * @param  integer|string $isButtonType  The button type. Accepts `classic`, `image`, `theme`, `flat`, `0`. `0` is an alias for `theme`.
     * @param  null|string    $nsButtonLabel
     * @param  string         $sNonce
     * @return string
     */
    static public function getButtonPreviewURL( $isButtonID, $isButtonType=0, $nsButtonLabel=null, $sNonce='' ) {
        $_aQuery = array(
            'aal-button-preview' => $isButtonType, // @todo confirm the behavior when 0 is passed
            'button-id'          => $isButtonID,
            'button-label'       => $nsButtonLabel,
            'nonce'              => $sNonce ? $sNonce : wp_create_nonce( 'aal_button_preview_nonce' ),
        );
        return add_query_arg( array_filter( $_aQuery, array( __CLASS__, 'isNotNull' ) ), get_site_url() );
    }

    /**
     * Returns all CSS rules of active buttons.
     *
     * @return string
     * @since  3
     * @since  4.0.1  Moved from `AmazonAutoLinks_PluginUtility`.
     */
    static public function getCSSRulesOfActiveButtons() {
        $_aCSSRules = array();
        foreach( self::getActiveButtonIDs() as $_iID ) {
            $_aCSSRules[]  = str_replace(
                '___button_id___',
                $_iID,
                trim( get_post_meta( $_iID, 'button_css', true ) )
            );
            $_aCSSRules[] = trim( get_post_meta( $_iID, 'custom_css', true ) );
        }
        return trim( implode( PHP_EOL, array_filter( $_aCSSRules ) ) );
    }

}