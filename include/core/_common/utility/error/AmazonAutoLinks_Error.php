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
 * A wrapper class of WP_Error.
 *
 * @since       4.2.0
 */
class AmazonAutoLinks_Error extends WP_Error {

    /**
     * Triggers an action hook so that the error can be logged.
     *
	 * @param string|int $isCode Error code
	 * @param string $sMessage Error message
	 * @param mixed $mData Optional. Error data.
     */
    public function __construct( $isCode='', $sMessage='', $mData='' ) {
        parent::__construct( $isCode, $sMessage, $mData );
        do_action( 'aal_action_error', $isCode, $sMessage, $mData );
    }

}