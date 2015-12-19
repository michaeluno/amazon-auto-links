<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */


/**
 * 
 * 
 * @since           3  
 */
class AmazonAutoLinks_ContextualProductWidget_Breadcrumb extends AmazonAutoLinks_PluginUtility {
         
    /**
     * 
     * @return  array
     */
    public function get() {

        $_aBreadcrumbs = ( array ) call_user_func( 
            array( 
                $this, 
                '_getItemsByType_' . $this->getCurrentPageType()
            ) 
        );
        return array_filter( $_aBreadcrumbs );
    
    }       

    /**
     * 
     * @return  array
     */
    private function _getItemsByType_home() {
        return array( get_bloginfo( 'name' ) );
    }
    /**
     * 
     * @return  array
     */
    private function _getItemsByType_front() {
        return array( get_bloginfo( 'name' ) );
    }
        
    
    /**
     * 
     * @return  array
     */
    private function _getItemsByType_singular() {
        
        $_aKeywords = array();
        if ( 
            ! isset( $GLOBALS['post']->post_parent ) 
            || ! $GLOBALS['post']->post_parent 
        ) {
            return $_aKeywords;
        }
        
        // Get ancestor post titles.
        $_iParentPostID = $GLOBALS['post']->post_parent;
        while( $_iParentPostID ) {
            $_oPost         = get_post( $_iParentPostID );
            $_aKeywords[]   = $_oPost->post_title;
            $_iParentPostID = $_oPost->post_parent;
        }                     
        return $_aKeywords;
        
    }
    private function _getItemsByType_post_type_archive() {
        return array();
    }
    private function _getItemsByType_taxonomy() {
        
        if ( ! method_exists( $GLOBALS[ 'wp_query' ], 'get_queried_object' ) ) {
            return array();
        }                        
        $_oTerm  = $GLOBALS['wp_query']->get_queried_object();
        return array(
                $_oTerm->name,
            ) 
            + $this->getParentTerms( $_oTerm )
        ;

    }
        /**
         * Get parent terms
         * @return  array       Holds term parent 'names'.
         */
        private function getParentTerms( $oTerm ) {
            if ( ! is_taxonomy_hierarchical( $oTerm->taxonomy ) ) {
                return array();
            }
            if ( ! $oTerm->parent ) {
                return array();
            }
            
            $_oTaxonomy = $oTerm->taxonomy;
            $_aParents  = array();
            while ( 0 != $oTerm->parent ) {
                $oTerm       = get_term( $oTerm->parent, $_oTaxonomy );
                $_aParents[] = $oTerm;
            }
            return array_reverse( $_aParents );
            
        }                    
    
    private function _getItemsByType_date() {
        return array();
    }
    private function _getItemsByType_author() {
        
        if ( ! method_exists( $GLOBALS[ 'wp_query' ], 'get_queried_object' ) ) {
            return array();
        }
        $_oUser = $GLOBALS[ 'wp_query' ]->get_queried_object();
        return array( $_oUser->display_name );
        
    }
    private function _getItemsByType_search() {
        $_aKeywords = array();
        if ( isset( $_GET[ 's' ] ) ) {
            $_aKeywords[] = $_GET[ 's' ];
        }
        return $_aKeywords;                
    }                    
    private function _getItemsByType_404() {
        return array();
    }
        /**
         * 
         * @return      array
         */
        private function _getSiteNameAndURL() {
            return array(
                get_bloginfo( 'name' ),
                get_bloginfo( 'url' ),
            );                                                
        }    
         
}