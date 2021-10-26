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
 * Handles JSON feed output.
 *
 * @package     Auto Amazon Links
 * @since       3.5.0
 * @since       4.6.0   Extends `AmazonAutoLinks_Event___Feed_Base`.
 */
class AmazonAutoLinks_Event___Feed_JSON extends AmazonAutoLinks_Event___Feed_Base {

    /**
     * @var string The feed type such as rss2 or json.
     * @since 4.6.4
     */
    public $sType = 'json';

    /**
     * @since       4.6.0
     */
    protected function _load() {

        // the priority of the credit comment insertion callback is `100` so it must be larger than that.
        add_filter( 'aal_filter_unit_output', array( $this, 'replyToRemoveCredit' ), PHP_INT_MAX  - 100 );
        add_filter( 'aal_filter_unit_show_error_mode', '__return_zero' );

        $_aArguments = array(
            'id' => absint( $this->getHTTPQueryGET( 'id' ) ),
        );
        $_aArguments[ 'template_path' ]        = AmazonAutoLinks_Registry::$sDirPath . '/template/json/template.php';
        $_aArguments[ 'credit_link' ]          = false;
        $_aArguments[ '_no_outer_container' ]  = true;
        $_aArguments[ 'show_errors' ]          = 0;
        $_aArguments[ 'load_with_javascript' ] = false; // 3.6.0+

        header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ), true );
        AmazonAutoLinks( $_aArguments, true );

    }

    /**
     * @param    string $sUnitOutput
     * @return   string
     * @since    3.1.0
     * @callback add_action aal_filter_unit_output
     */
    public function replyToRemoveCredit( $sUnitOutput ) {
        return str_replace( $this->getCommentCredit(), '', $sUnitOutput );
    }

}