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
 * Checks HTTP response errors.
 *
 * When an HTTP response error is detected, it adds to the $aNotes property of the unit output object
 * so that the note will be added to the unit output.
 *
 * @since 4.6.17
 */
class AmazonAutoLinks_UnitOutput__HTTPErrorChecks extends AmazonAutoLinks_UnitOutput__DelegationBase {

    /**
     * @return array
     */
    protected function _getActionArguments() {
        return array(
            array(
                'aal_action_detected_http_error',
                array( $this, 'replyToCaptureHTTPErrors' ),   // callback
                10,  // priority - set low as it should be inserted last
                5    // number of parameters
            ),
        );
    }

    /**
     * @param  WP_Error $oWPError
     * @param  string   $sURL
     * @param  string   $sCacheName
     * @since  4.6.17
     */
    public function replyToCaptureHTTPErrors( WP_Error $oWPError, $sURL, $sCacheName ) {
        $_sNote = $oWPError->get_error_code() . ': ' . $oWPError->get_error_message()
            . ' URL: ' . $sURL
            . ' Cache: ' . $sCacheName;
        $this->_oUnitOutput->aNotes[] = $_sNote;
    }

}