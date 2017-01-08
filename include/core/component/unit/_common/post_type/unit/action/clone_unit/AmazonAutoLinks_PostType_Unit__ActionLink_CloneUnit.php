<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 *
 */

/**
 * A delegation class that adds the `Clone Unit` action link to the unit listing table.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_PostType_Unit__ActionLink_CloneUnit extends AmazonAutoLinks_PostType_Unit__ActionLink_Base {

    protected $_sActionSlug = 'clone_unit';

    /**
     * Performs the action.
     * @param   array|integer $aiPostID
     * @return  void
     */
    protected function _doAction( $aiPostID ) {
        new AmazonAutoLinks_ListTableAction_clone_unit(
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
        $_sLabel    = __( 'Clone', 'amazon-auto-links' );
        $_sTitle    = __( 'Clone Unit', 'amazon-auto-links' );
        return AmazonAutoLinks_Option::getInstance()->canCloneUnits()
            ? "<a href='" . esc_attr( $_sURL ) . "' title='" . esc_attr( $_sTitle ) . "'>"
                . $_sLabel
            . "</a> "
            : "<span class='disabled' title='" . esc_attr( AmazonAutoLinks_PluginUtility::getUpgradePromptMessage( false /* no link */ ) ) . "'>"
                . $_sLabel
            . "</span>";

    }

}