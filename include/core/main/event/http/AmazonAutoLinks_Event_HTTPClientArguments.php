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
class AmazonAutoLinks_Event_HTTPClientArguments extends AmazonAutoLinks_PluginUtility {


    public function __construct() {

        add_filter( 'aal_filter_http_request_arguments', array( $this, 'replyToGetHTTPClientArguments' ), 10, 2 );

    }

    /**
     * @param array  $aArguments
     * @param string $sRequestType
     * @return array
     * @since   4.0.0
     * @since   4.0.1   Added the `$sRequestType` parameter.
     */
    public function replyToGetHTTPClientArguments( array $aArguments, $sRequestType ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        $aArguments[ 'compress_cache' ] = ( boolean ) $_oOption->get( 'cache', 'compress' );
        return $aArguments;

    }

}