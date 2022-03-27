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
 * Provides methods to clone units.
 * 
 * @since 3.3.0
 * @since 5.2.0 Extends the `AmazonAutoLinks_ListTableAction_Clone_Base`.
 */
class AmazonAutoLinks_ListTableAction_clone_unit extends AmazonAutoLinks_ListTableAction_Clone_Base {

    /**
     * Performs the action.
     * @since 3.3.0
     */
    public function __construct( array $aPostIDs, $oFactory ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->isUnitLimitReached() ) {
            $oFactory->setSettingNotice( AmazonAutoLinks_Message::getUpgradePromptMessageToAddMoreUnits() );
            return;
        }
        parent::__construct( $aPostIDs, $oFactory );
    }

    /**
     * @since  5.2.0
     * @param  array  $aNewPostIDs
     * @return string
     */
    protected function _getMessageSucceed( array $aNewPostIDs ) {
        return __( 'The unit has been cloned successfully.', 'amazon-auto-links' );
    }

    /**
     * @since  3.3.0
     * @param  WP_Post $oPost The source post to be cloned.
     * @return integer The new post id. If failed, 0.
     */
    protected function _clone( WP_Post $oPost ) {

        $_oOption      = AmazonAutoLinks_Option::getInstance();
        $_aPostMeta    = $this->getPostMeta( $oPost->ID, '', $_oOption->get( 'unit_default' ) );
        // @deprecated  3.7.8 It is okay to leave the status as the clone one uses the same cache.
        // unset( $_aPostMeta[ '_error' ] );    // stores the unit status
        $_iNewPostID   = $this->createPost(
            AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],    // post type slug
            $this->_getPostColumns( $oPost ),    // columns
            $_aPostMeta,   // meta
            $this->_getTaxInputArgument( $oPost )
        );
        if ( $_iNewPostID ) {
            $_iNewPostID = $this->_updatePostSlug( get_post( $_iNewPostID ) ); // Give a unique post slug
        }
        return $_iNewPostID;

    }

}