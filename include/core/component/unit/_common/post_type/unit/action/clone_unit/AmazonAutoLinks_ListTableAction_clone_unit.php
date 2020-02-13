<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Provides methods to clone units.
 * 
 * @package     Amazon Auto Links
 * @since       3.3.0
 */
class AmazonAutoLinks_ListTableAction_clone_unit extends AmazonAutoLinks_PluginUtility {

    /**
     * Performs the action.
     * @since       3.3.0
     */
    public function __construct( array $aPostIDs, $oFactory ) {
        
        // Check the limitation.
        $_oOption         = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->isUnitLimitReached() ) {
            $oFactory->setSettingNotice( $this->getUpgradePromptMessageToAddMoreUnits() );
            return ;
        }            
        
        $_iCount      = count( $aPostIDs );
        $_aFailed     = array();
        $_aNewPostIDs = array();
        foreach( $aPostIDs as $_iPostID ) {
            $_iNewPostID    = $this->___cloneUnit( $_iPostID );
            if ( $_iNewPostID ) {
                $_aNewPostIDs[] = $_iNewPostID;
                continue;
            }
            $_aFailed[] = $_iPostID; 
        }
        
        if ( ! empty( $_aFailed ) ) {          
            $oFactory->setSettingNotice(
                sprintf(
                    __( 'The following unit(s) failed to be cloned.', 'amazon-auto-links' ),
                    explode( ', ', $_aFailed )
                ),
                'error'                
            );
        } else {      
            $oFactory->setSettingNotice(
                __( 'The unit has been cloned successfully.', 'amazon-auto-links' ),
                'updated'                
            ); 
        }
        
    }
        /**
         * @since        3.3.0
         * 
         * @return      integer     The new post id. If failed, 0.
         */
        private function ___cloneUnit( $iPostID ) {

            $_oSourcePost  = get_post( $iPostID );
            $_aPostMeta    = $this->getPostMeta( $iPostID );
            // @deprecated  3.7.8 It is okay to leave the status as the clone one uses the same cache.
            // unset( $_aPostMeta[ '_error' ] );    // stores the unit status
            $_iNewPostID   = $this->createPost( 
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],    // post type slug
                $this->_getPostColumns( $_oSourcePost ),    // columns
                $_aPostMeta,   // meta
                $this->_getTaxInputArgument( $_oSourcePost )
            );
            if ( $_iNewPostID ) {
                               
                // Give a unique post slug
                $_iNewPostID = $this->_updatePostSlug( get_post( $_iNewPostID ) );
                
            }
            return $_iNewPostID;
            
        }
            /**
             * Give a unique post slug.
             * @return      integer     The ID of the post if the post is successfully updated in the database. Otherwise returns 0.
             */
            private function _updatePostSlug( $_oNewPost ) {
                
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
             * @return      array
             */
            private function _getTaxInputArgument( $oSourcePost ) {
                
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
             * @return     array
             */
            private function _getPostColumns( $_oPost ) {

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