<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Sanitizes `$_POST` values passed to the category selection Ajax action.
 *
 * @since        4.6.23
 */
class AmazonAutoLinks_Proxy_WebPageDumper_Event_Must_Filter_CategorySelectionPostSanitization extends AmazonAutoLinks_Utility {

    /**
     * Sets up hooks.
     * @since 4.6.23
     */
    public function __construct() {
        add_filter( 'aal_filter_ajax_post_sanitization_category_selection', array( $this, 'replyToSanitizePost' ) );
    }

    /**
     * @param  array
     * @return array
     * @since  4.6.23
     */
    public function replyToSanitizePost( $aPost ) {
AmazonAutoLinks_Debug::log( '$_POST' );
AmazonAutoLinks_Debug::log( $_POST );
        $_aPost = array(
            'enableWebPageDumper' => ( boolean ) $this->getElement( $_POST, 'enableWebPageDumper' ),
        ) + $aPost;
AmazonAutoLinks_Debug::log( '$_aPost' );
AmazonAutoLinks_Debug::log( $_aPost );
return $_aPost;
    }

}