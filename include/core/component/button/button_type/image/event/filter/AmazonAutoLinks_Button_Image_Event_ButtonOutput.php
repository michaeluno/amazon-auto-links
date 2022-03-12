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
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_Event_ButtonOutput {

    public $sButtonType = 'image';

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_button_by_type_' . $this->sButtonType, array( $this, 'replyToGetButtonOutput' ), 10, 3 );
    }

    /**
     * @param  string         $sButtonOutput
     * @param  integer|string $isButtonID
     * @param  string         $sButtonLabel
     * @return string
     */
    public function replyToGetButtonOutput( $sButtonOutput, $isButtonID, $sButtonLabel ) {
        $_iButtonID = ( integer ) $isButtonID;
        $_sButtonIDSelector = $_iButtonID >= 0   // preview sets it to -1
            ? "amazon-auto-links-button-$isButtonID"
            : "amazon-auto-links-button-___button_id___";
        $_sImageURL = filter_var( $sButtonLabel, FILTER_VALIDATE_URL )
            ? $sButtonLabel
            : ( $_iButtonID > 0
                ? get_post_meta( $_iButtonID, '_image_url', true )
                : ''
            );
        $_aAttributes = array(
            'src' => esc_url( $_sImageURL ),
            'alt' => $sButtonLabel,
        );
        return "<div class='amazon-auto-links-button {$_sButtonIDSelector}'>"
                . "<img " . AmazonAutoLinks_Utility::getAttributes( $_aAttributes ) . " />"
            . "</div>";
    }
    
}