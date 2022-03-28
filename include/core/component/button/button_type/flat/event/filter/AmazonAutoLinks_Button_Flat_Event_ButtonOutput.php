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
class AmazonAutoLinks_Button_Flat_Event_ButtonOutput extends AmazonAutoLinks_PluginUtility {

    public $sButtonType = 'flat';

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
        $_iButtonID         = ( integer ) $isButtonID;
        // Whether the button is stored as a custom post type post
        $_bIsPost           = is_numeric( $isButtonID )
            && $_iButtonID >= 0;      // preview sets it to -1
        $_sButtonIDSelector = $_bIsPost
            ? esc_attr( "amazon-auto-links-button-$isButtonID" )
            : "amazon-auto-links-button-___button_id___";
        return "<div class='amazon-auto-links-button {$_sButtonIDSelector}' data-type='" . esc_attr( $this->sButtonType ) . "'>"
                . "<span class='button-icon button-icon-left'><i></i></span>"
                . "<span class='button-label'>" . $sButtonLabel. "</span>"
                . "<span class='button-icon button-icon-right'><i></i></span>"
            . "</div>";
    }
    
}