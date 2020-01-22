<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * Handles the on/off action link for Auto Insert.
 *
 * @since       3.7.8
 */
class AmazonAutoLinks_PostType__AutoInsert___ActionLink_Status extends AmazonAutoLinks_PostType__ActionLink_Base {

    protected $_sActionSlug  = 'toggle_status';

    protected function _construct() {

        $_sPostTypeSlug = $this->_oFactory->oProp->sPostType;
        add_filter(
            "cell_{$_sPostTypeSlug}_status",
            array( $this, 'replyToSetActionLink' ),
            10,
            2
        );
        
    }

        /**
         * @param $sCell
         * @param $iPostID
         *
         * @return      string
         * @callback    filter      cell_{post type slug}_status
         */
        public function replyToSetActionLink( $sCell, $iPostID ) {

            $sToggleStatusURL = add_query_arg( 
                array( 
                    'post_type'     => $this->_oFactory->oProp->sPostType,
                    'custom_action' => $this->_sActionSlug,
                    'post'          => $iPostID,
                    'nonce'         => $this->_sCustomNonce,
                ), 
                admin_url( 'edit.php' ) 
            );
            $sToggleStatusURL = esc_url( $sToggleStatusURL );
            $_bEnabled        = get_post_meta( $iPostID, 'status', true );
            $sStatus          = $_bEnabled ? "<strong>" . __( 'On', 'amazon-auto-links' ) . "</strong>" : __( 'Off', 'amazon-auto-links' );
            $sOppositeStatus  = $_bEnabled ? __( 'Off', 'amazon-auto-links' ) : __( 'On', 'amazon-auto-links' );
            $_sTitleAttribute = esc_attr( __( 'Toggle the status', 'amazon-auto-links' ) );
            $sActions         = "<div class='row-actions'>"
                    . "<span class='toggle-status'>"
                        . "<a href='{$sToggleStatusURL}' title='" . $_sTitleAttribute . "'>" . sprintf( __( 'Set it %1$s', 'amazon-auto-links' ), $sOppositeStatus ) . "</a>"
                    . "</span>"
                . "</div>";
            return $sStatus . $sActions;
        }


    protected function _getActionLabel() {
        return __( 'Toggle On/Off', 'amazon-auto-links' );
    }


    protected function _doAction( $aiPostID ) {
        
        $_aPostIDs = $this->getAsArray( $aiPostID );
        foreach( $_aPostIDs as $_iPostID ) {

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