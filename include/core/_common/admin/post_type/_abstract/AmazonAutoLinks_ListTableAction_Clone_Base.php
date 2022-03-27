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
 * A base class of the clone action of a post type.
 * 
 * @since 5.2.0
 */
abstract class AmazonAutoLinks_ListTableAction_Clone_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * Performs the action.
     * @since 5.2.0
     */
    public function __construct( array $aPostIDs, $oFactory ) {
        
        $_aFailedIDs  = array();
        $_aNewPostIDs = array();
        foreach( $aPostIDs as $_iPostID ) {
            if ( empty( $_iPostID ) ) {
                continue;
            }
            $_iNewPostID = $this->_clone( get_post( $_iPostID ) );
            if ( $_iNewPostID ) {
                $_aNewPostIDs[] = $_iNewPostID;
                continue;
            }
            $_aFailedIDs[] = $_iPostID;
        }
        
        if ( empty( $_aFailedIDs ) ) {
            $oFactory->setSettingNotice( $this->_getMessageSucceed( $_aNewPostIDs ), 'updated' );
            return;
        }
        $oFactory->setSettingNotice( $this->_getMessageFail( $_aFailedIDs ), 'error' );

    }

    /**
     * @since  5.2.0
     * @param  array  $aNewPostIDs
     * @return string
     */
    protected function _getMessageSucceed( array $aNewPostIDs ) {
        return __( 'The item has been cloned successfully.', 'amazon-auto-links' );
    }

    /**
     * @since  5.2.0
     * @param  array $aFailedPostIDs
     * @return string
     */
    protected function _getMessageFail( array $aFailedPostIDs ) {
        return sprintf(
            __( 'Failed to clone the followings: %1$s', 'amazon-auto-links' ),
            implode( ', ', $aFailedPostIDs )
        );
    }

    /**
     * @remark Override this method in an extended class.
     * @param  WP_Post $oPost
     * @since  5.2.0
     * @return integer The new post id. If failed, 0.
     */
    protected function _clone( WP_Post $oPost ) {
        return 0;
    }

    /* Utility Methods */

    /**
     * Give a unique post slug.
     * @return integer The ID of the post if the post is successfully updated in the database. Otherwise returns 0.
     */
    protected function _updatePostSlug( $_oNewPost ) {

        if ( ! in_array( $_oNewPost->post_status, array( 'publish', 'future' ) ) ) {
            return 0;
        }

        $_sNewPostName = wp_unique_post_slug(
            $_oNewPost->post_name,
            $_oNewPost->ID,
            $_oNewPost->post_status,
            $_oNewPost->post_type,
            $_oNewPost->post_parent
        );

        $_aNewPost = array();
        $_aNewPost[ 'ID' ]        = $_oNewPost->ID;
        $_aNewPost[ 'post_name' ] = $_sNewPostName;

        // Update the post into the database
        return wp_update_post( $_aNewPost );

    }
        
    /**
     * @return array
     */
    protected function _getTaxInputArgument( $oSourcePost ) {
        $_aTaxonomy = array();
        foreach( get_object_taxonomies( $oSourcePost->post_type ) as $_isIndex => $_sTaxonomySlug ) {
            $_aTaxonomy[ $_sTaxonomySlug ] = wp_get_object_terms(
                $oSourcePost->ID,
                $_sTaxonomySlug,
                array( 'fields' => 'slugs' )
            );
        }
        return $_aTaxonomy;
    }

    /**
     * @return array
     */
    protected function _getPostColumns( $_oPost ) {
        $_oCurrentUser = wp_get_current_user();
        return array(
            'comment_status' => $_oPost->comment_status,
            'ping_status'    => $_oPost->ping_status,
            'post_author'    => $_oCurrentUser->ID,
            'post_content'   => $_oPost->post_content,
            'post_excerpt'   => $_oPost->post_excerpt,
            'post_name'      => $_oPost->post_name,
            'post_parent'    => $_oPost->post_parent,
            'post_password'  => $_oPost->post_password,
            'post_status'    => $_oPost->post_status,
            'post_title'     => sprintf(
                __( 'Copy of %1$s', 'amazon-auto-links' ),
                $_oPost->post_title
            ),
            'post_type'      => $_oPost->post_type,
            'to_ping'        => $_oPost->to_ping,
            'menu_order'     => $_oPost->menu_order
        );
    }

}