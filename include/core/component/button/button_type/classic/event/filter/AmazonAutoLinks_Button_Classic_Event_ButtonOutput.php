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
class AmazonAutoLinks_Button_Classic_Event_ButtonOutput {

    public $sButtonType = 'classic';

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
        return "<div class='amazon-auto-links-button {$_sButtonIDSelector}' data-type='classic'>"
                    . $sButtonLabel
                . "</div>";
    }

}