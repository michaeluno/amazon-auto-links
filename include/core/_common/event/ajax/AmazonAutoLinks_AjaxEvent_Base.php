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
 * Class AmazonAutoLinks_AjaxEvent_Base
 * @sinec       3.7.6
 */
abstract class AmazonAutoLinks_AjaxEvent_Base extends AmazonAutoLinks_Event___Action_Base {

    /**
     * Override this value to specify a nonce key for this event.
     * @var string
     */
    protected $_sNonceKey = '';

    protected function _doAction() {

        check_ajax_referer(
            $this->_sNonceKey,   // the nonce key passed to the `wp_create_nonce()` - `add-post` is done by WordPress
            'aal_nonce' // the $_REQUEST key storing the nonce.
        );

        $_bSuccess  = true;
        $_asMessage = '';
        try {

            $_iUserID = get_current_user_id();
            if ( ! $_iUserID ) {
                throw new Exception( __( 'Could not get a user ID.', 'feed-zapper' ) );
            }
            $_asMessage = $this->_getResponse( $_POST );

        } catch ( Exception $_oException ) {

            $_bSuccess = false;
            $_asMessage = $_oException->getMessage();

        }
        exit(
            json_encode(
                array(
                    'success' => $_bSuccess,
                    // the front-end js script parse these and remove from the session array from the key one by one
                    'result'  => $_asMessage,
                )
            )
        );

    }

    /**
     * Override this method to return a response.
     * @return array|string
     */
    protected function _getResponse( array $aPost ) {
        return array();
    }

}
