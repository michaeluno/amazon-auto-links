<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Modifies the HTTP Client arguments based on the options set via UI by the user.
 *
 * @since        4.0.0
 */
class AmazonAutoLinks_Main_Event_Filter_HTTPClientArguments extends AmazonAutoLinks_PluginUtility {


    public function __construct() {

        add_filter( 'aal_filter_http_request_arguments', array( $this, 'replyToGetHTTPClientArguments' ), 10, 1 );

    }

    /**
     * @param array  $aArguments
     * @return array
     * @since   4.0.0
     */
    public function replyToGetHTTPClientArguments( array $aArguments ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        $aArguments[ 'compress_cache' ] = ( boolean ) $_oOption->get( 'cache', 'compress' );
        return $aArguments;

    }

}