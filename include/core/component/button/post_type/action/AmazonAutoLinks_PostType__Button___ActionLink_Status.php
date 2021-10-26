<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Handles the on/off action link for buttons.
 *
 * @since       4.3.0
 */
class AmazonAutoLinks_PostType__Button___ActionLink_Status extends AmazonAutoLinks_PostType__Common___ActionLink_Status_Base {

    protected $_sMetaKey = '_status';
    protected $_sNonceKey = 'aal_nonce_button_actions';

    /**
     * @param $iPostID
     *
     * @return bool
     */
    protected function _getToggleStatus( $iPostID ) {
        $_isStatus = get_post_meta( $iPostID, $this->_sMetaKey, true );
        if ( '' === $_isStatus ) {
            $_isStatus = 1;
        }
        return ( boolean ) $_isStatus;
    }

    protected function _doAction( $aiButtonID ) {
        foreach( $this->getAsArray( $aiButtonID ) as $_iButtonID ) {
            $_isEnabled = get_post_meta( $_iButtonID, $this->_sMetaKey, true );

            // Storing a boolean of `false` results in an empty string when retrieved with get_post_meta()
            // so to work around it, store an integer value.
            // For backward compatibility as get_post_meta() returns an empty string for non existent value
            if ( '' === $_isEnabled ) {
                $_isEnabled = 1;  // (true)
            }
            $_bEnabled = ! ( ( boolean ) $_isEnabled ); // flip the value
            update_post_meta( $_iButtonID, $this->_sMetaKey, ( integer ) $_bEnabled );
        }
        do_action( 'aal_action_update_active_buttons' );
    }

}