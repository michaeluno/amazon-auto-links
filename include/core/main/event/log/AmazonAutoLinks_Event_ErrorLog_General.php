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
 * Logs general errors.
 *
 * @since        4.2.0
 */
class AmazonAutoLinks_Event_ErrorLog_General extends AmazonAutoLinks_Event_ErrorLog {

    public function __construct() {
        add_action( 'aal_action_error', array( $this, 'replyToLogErrors' ), 10, 3 );
    }

    /**
     * Called when an error is detected and AmazonAutoLinks_Error is instantiated.
     *
     * @return  void
     * @since   4.2.0
     */
    public function replyToLogErrors( $isCode, $sErrorMessage, $aData ) {

        $sErrorMessage = $sErrorMessage
            ? $sErrorMessage
            : '(no error message)';
        $_sCode        = trim( $isCode );
        $sErrorMessage = $_sCode
            ? $_sCode . ': ' . $sErrorMessage
            : $sErrorMessage;
        $this->setErrorLogItem( $sErrorMessage, $aData );

    }

}