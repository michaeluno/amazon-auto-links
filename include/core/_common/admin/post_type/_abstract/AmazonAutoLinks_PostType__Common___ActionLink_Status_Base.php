<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * A base class for a status action link and its column.
 *
 * @since       4.3.0
 */
abstract class AmazonAutoLinks_PostType__Common___ActionLink_Status_Base extends AmazonAutoLinks_PostType__ActionLink_Base {


    protected $_sActionSlug  = 'toggle_status';

    /**
     * Should be overridden in an extended class.
     * @var string
     */
    protected $_sMetaKey = '';

    protected function _construct() {
        add_filter( "cell_{$this->_oFactory->oProp->sPostType}_status", array( $this, 'replyToSetActionLink' ), 10, 2 );
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
            $_bEnabled        = $this->_getToggleStatus( $iPostID );
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

    /**
     * @param $iPostID
     *
     * @return bool
     */
    protected function _getToggleStatus( $iPostID ) {
        return ( boolean ) get_post_meta( $iPostID, $this->_sMetaKey, true );
    }

    protected function _getActionLabel() {
        return __( 'Toggle On/Off', 'amazon-auto-links' );
    }

    protected function _doAction( $aiPostID ) {
        parent::_doAction( $aiPostID );
    }
    
    
}