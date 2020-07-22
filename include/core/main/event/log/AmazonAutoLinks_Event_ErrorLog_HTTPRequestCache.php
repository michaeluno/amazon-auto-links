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
 * Logs errors of HTTP requests.
 *
 * @since
 */
class AmazonAutoLinks_Event_ErrorLog_HTTPRequestCache extends AmazonAutoLinks_Event_ErrorLog {

    /**
     * Stores error logs;
     * @var array
     */
    static public $aErrorLog = array();

    public function __construct() {

        add_action( 'aal_action_error_http_request_cache_data', array( $this, 'replyToLogErrorHTTPRequestCache' ), 10, 2 );

    }

    /**
     * Called when HTTP request cache is not an array.
     *
     * @param string $sErrorMessage
     * @param array $aCache
     *
     * @return  void
     * @since   4.0.0
     */
    public function replyToLogErrorHTTPRequestCache( $sErrorMessage, array $aCache=array() ) {
        $_aExtra = array(
            'cache_name' => $aCache[ 'name' ],
            'url'        => $aCache[ 'request_uri' ],
            'data_type'  => gettype( $aCache[ 'data' ] ) . ( is_object( $aCache[ 'data' ] ) ? ':' . get_class( $aCache[ 'data' ] ) : '' ),
            'length'     => is_scalar( $aCache[ 'data' ] ) ? strlen( $aCache[ 'data' ] ) : 'n/a',
        );
        $this->setErrorLogItem( $sErrorMessage, $_aExtra );
    }

}