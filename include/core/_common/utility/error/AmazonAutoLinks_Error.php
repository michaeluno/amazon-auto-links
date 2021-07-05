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
 * A wrapper class of WP_Error.
 *
 * @since       4.2.0
 */
class AmazonAutoLinks_Error extends WP_Error {

    /**
     * Triggers an action hook so that the error can be logged.
     *
	 * @param string|integer $isCode Error code
	 * @param string $sMessage Error message
	 * @param mixed $mData Optional. Error data.
     * @param boolean whether to generate a stack trace.
     */
    public function __construct( $isCode='', $sMessage='', $mData='', $bStackTrace=false ) {

        parent::__construct( $isCode, $sMessage, $mData );

        $_sStackTrace = $bStackTrace
            ? AmazonAutoLinks_Debug::getStackTrace( 2 )
            : '';
        do_action( 'aal_action_error', $isCode, $sMessage, AmazonAutoLinks_PluginUtility::getAsArray( $mData ), current_filter(), $_sStackTrace );

    }

}