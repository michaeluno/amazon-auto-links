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
 * Class AmazonAutoLinks_AjaxEvent_Base
 * @since       3.7.6
 */
abstract class AmazonAutoLinks_AjaxEvent_Base extends AmazonAutoLinks_Event___Action_Base {

    /**
     * Override this value to specify a nonce key for this event.
     * @var string
     */
    protected $_sNonceKey = '';

    /**
     * The action hook name suffix.
     *
     * The action hook names will be:
     *  - for guests:  `wp_ajax_nopriv_{...}`
     *  - for logged-in users: `wp_ajax_{...}`
     *
     * The part `{...}` is where the suffix resides.
     *
     * @var string
     */
    protected $_sActionHookSuffix = '';

    /**
     * Whether to be accessible for logged-in users.
     * @var bool
     */
    protected $_bLoggedIn = true;
    /**
     * Whether to be accessible for non-logged-in users (guests).
     * @var bool
     */
    protected $_bGuest    = true;

    public function __construct() {

        // Set up action hook names
        if ( $this->_sActionHookSuffix ) {
            if ( $this->_bLoggedIn ) {
                $this->_aActionHookNames[] = 'wp_ajax_' . $this->_sActionHookSuffix;
            }
            if ( $this->_bGuest ) {
                $this->_aActionHookNames[] = 'wp_ajax_nopriv_' . $this->_sActionHookSuffix;
            }
            $this->_sNonceKey = $this->_sNonceKey
                ? $this->_sNonceKey
                : $this->_sActionHookSuffix;
        }


        parent::__construct();

    }


    protected function _doAction() {

        check_ajax_referer(
            $this->_sNonceKey,   // the nonce key passed to the `wp_create_nonce()` - `add-post` is done by WordPress
            'aal_nonce' // the $_REQUEST key storing the nonce.
        );

        $_bSuccess  = true;
        try {

            if ( ! $this->_bGuest && ! get_current_user_id() ) {
                throw new Exception( __( 'Could not get a user ID.', 'amazon-auto-links' ) );
            }

            $_asMessage = $this->_getResponse( $this->_getPostSanitized( $_POST ) );

        } catch ( Exception $_oException ) {

            $_bSuccess  = false;
            $_asMessage = maybe_unserialize( $_oException->getMessage() );

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
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        return $this->getArrayMappedRecursive( 'sanitize_text_field', $aPost );
    }

    /**
     * Override this method to return a response.
     * @return array|string
     * @param  array $aPost     Sanitized POST data.
     */
    protected function _getResponse( array $aPost ) {
        return array();
    }

}