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
 * Modifies the reload message that appears when an HTTP request fails.
 *
 * @since        4.6.23
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Filter_CategorySelectionReloadMessage extends AmazonAutoLinks_Utility {

    /**
     * Sets up hooks.
     * @since 4.6.23
     */
    public function __construct() {
        $_oToolOption = AmazonAutoLinks_ToolOption::getInstance();
        if ( $_oToolOption->get( array( 'web_page_dumper', 'enable' ), false ) ) {
            return;
        }
        add_filter( 'aal_filter_output_category_selection_reload_message', array( $this, 'replyToGetReloadMessage' ) );
    }

    /**
     * @param  array
     * @return string
     * @since  4.6.23
     */
    public function replyToGetReloadMessage( $sOutput ) {
        return "<p class='display-inline-block'>"
                . "<label class='label-checkbox' for='enable-web-page-dumper'><input type='checkbox' id='enable-web-page-dumper' name='enable-web-page-dumper' />"
                    . sprintf( __( 'Enable %1$s', 'amazon auto links' ), 'Web Page Dumper' )
                . "</label>"
            . $sOutput
            . "</p>";
    }

}