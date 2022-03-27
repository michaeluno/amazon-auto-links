<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Clones a button.
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_ListTableAction_Clone extends AmazonAutoLinks_ListTableAction_Clone_Base {

    /**
     * Sets up properties and hooks.
     * @param array $aPostIDs
     * @param AmazonAutoLinks_PostType_Button $oFactory
     * @since 5.2.0
     */
    public function __construct( array $aPostIDs, AmazonAutoLinks_PostType_Button $oFactory ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->canCloneButtons() ) {
            $oFactory->setSettingNotice( AmazonAutoLinks_Message::getUpgradePromptMessageToAddMoreUnits() );
            return;
        }
        parent::__construct( $aPostIDs, $oFactory );
    }

    /**
     * @since 5.2.0
     */
    protected function _clone( WP_Post $oPost ) {

        $_aPostMeta    = $this->getPostMeta( $oPost->ID, '', array() );

        // Replace the post ID part with `___button_id___` so that when generating the CSS cache, the placeholder (___button_id___) will be converted to the new post ID.
        $_aPostMeta[ 'button_css' ] = preg_replace(
            '/\.amazon-auto-links-button-\K(' . $oPost->ID . ')(?=\W)/',
            '___button_id___',
            $this->getElement( $_aPostMeta, array( 'button_css' ), '' )
        );
        $_sCustomCSS = $this->getElement( $_aPostMeta, array( 'custom_css' ), '' );
        if ( $_sCustomCSS ) {
            $_aPostMeta[ 'custom_css' ] = preg_replace(
                '/\.amazon-auto-links-button-\K(' . $oPost->ID . ')(?=\W)/',
                '___button_id___',
                $this->getElement( $_aPostMeta, array( 'custom_css' ), '' )
            );
        }

        $_iNewPostID   = $this->createPost(
            AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],    // post type slug
            $this->_getPostColumns( $oPost ),                     // columns
            $_aPostMeta,   // meta
            $this->_getTaxInputArgument( $oPost )
        );
        if ( $_iNewPostID ) {
            $_iNewPostID = $this->_updatePostSlug( get_post( $_iNewPostID ) ); // Give a unique post slug

            do_action( 'aal_action_update_active_buttons' );
            /**
             * This will schedule the button CSS update callback.
             * @see AmazonAutoLinks_PostType_Button::setUp()
             */
            do_action( 'aal_action_clone_button', $_iNewPostID );
        }
        return $_iNewPostID;

    }

}