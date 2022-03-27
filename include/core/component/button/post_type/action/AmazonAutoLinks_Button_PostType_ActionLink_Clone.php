<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * A delegation class that adds the `Clone Button` action link to the button listing table.
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_PostType_ActionLink_Clone extends AmazonAutoLinks_PostType__ActionLink_Base {

    protected $_sActionSlug = 'clone_button';
    protected $_sNonceKey   = 'aal_nonce_button_actions';

    /**
     * @var AmazonAutoLinks_PostType_Button
     */
    protected $_oFactory;

    /**
     * Performs the action.
     * @param array|integer $aiPostID
     */
    protected function _doAction( $aiPostID ) {
        new AmazonAutoLinks_Button_ListTableAction_Clone( $this->getAsArray( $aiPostID ), $this->_oFactory );
    }

    /**
     * @param  WP_Post  $oPost
     * @return string
     */
    protected function _getActionLink( $oPost ) {
        $_sURL = add_query_arg(
            array(
                'post_type'     => $this->_oFactory->oProp->sPostType,
                'custom_action' => $this->_sActionSlug,
                'post'          => $oPost->ID,
                'nonce'         => $this->_sCustomNonce,
            ),
            admin_url( $this->_oFactory->oProp->sPageNow )
        );
        $_sLabel    = $this->_getActionLabel();
        $_sTitle    = __( 'Clone Button', 'amazon-auto-links' );
        return AmazonAutoLinks_Option::getInstance()->canCloneButtons()
            ? "<a href='" . esc_attr( $_sURL ) . "' title='" . esc_attr( $_sTitle ) . "'>"
                . $_sLabel
            . "</a> "
            : "<span class='disabled' title='" . esc_attr( AmazonAutoLinks_Message::getUpgradePromptMessage( false /* no link */ ) ) . "'>"
                . $_sLabel
            . "</span>";

    }

    /**
     * @return string
     * @since  5.2.0
     */
    protected function _getActionLabel() {
        return __( 'Clone', 'amazon-auto-links' );
    }

}