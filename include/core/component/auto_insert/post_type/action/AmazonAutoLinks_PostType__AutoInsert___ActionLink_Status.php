<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Handles the on/off action link for Auto Insert.
 *
 * @since       3.7.8
 * @since       4.3.0       Change the base class from `AmazonAutoLinks_PostType__ActionLink_Base` to `AmazonAutoLinks_PostType__Common___ActionLink_Status_Base`.
 */
class AmazonAutoLinks_PostType__AutoInsert___ActionLink_Status extends AmazonAutoLinks_PostType__Common___ActionLink_Status_Base {

    protected $_sMetaKey = 'status';
    protected $_sNonceKey = 'aal_nonce_auto_insert';

    protected function _doAction( $aiPostID ) {

        foreach( $this->getAsArray( $aiPostID ) as $_iPostID ) {

            $_aUnitIDs = get_post_meta( $_iPostID, 'unit_ids', true );    
            // if this field is empty, the post must be the wrong post type.
            if ( empty( $_aUnitIDs ) ) { 
                return; 
            }  
            
            $_bIsEnabled = get_post_meta( $_iPostID, 'status', true );
            update_post_meta( $_iPostID, 'status', ! $_bIsEnabled );

        }
        do_action( 'aal_action_update_active_auto_inserts' );

    }
    
}