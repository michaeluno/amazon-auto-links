<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * A delegation class that adds the `Renew Cache` action link to the unit listing table.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_PostType_Unit__ActionLink_RenewCache extends AmazonAutoLinks_PostType_Unit__ActionLink_Base {

    protected $_sActionSlug = 'renew_cache';

    /**
     * Performs the action.
     * @param   array|integer $aiPostID
     * @return  void
     */
    protected function _doAction( $aiPostID ) {
        new AmazonAutoLinks_ListTableAction_renew_cache(
            $this->getAsArray( $aiPostID ),
            $this->_oFactory
        );
    }

    /**
     * @param       object  $oPost
     * @return      string
     */
    protected function _getActionLink( $oPost ) {
        $_sURL = add_query_arg(
            array(
                'post_type'     => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'custom_action' => $this->_sActionSlug,
                'post'          => $oPost->ID,
                'nonce'         => $this->_sCustomNonce,
            ),
            admin_url( $this->_oFactory->oProp->sPageNow )
        );
        $_sLabel = $this->_getActionLabel();
        return "<a href='" . esc_url( $_sURL ) . "' title='" . esc_attr( $_sLabel ) . "'>"
                . $_sLabel
            . "</a> ";
    }

    /**
     * @return string
     * @since   3.7.6
     */
    protected function _getActionLabel() {
        return __( 'Renew Cache', 'amazon-auto-links' );
    }

}