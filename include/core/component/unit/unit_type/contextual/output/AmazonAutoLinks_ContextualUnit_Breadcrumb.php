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
 * 
 * 
 * @since       3
 * @since       3.5.0       Renamed from `AmazonAutoLinks_ContextualProductWidget_Breadcrumb `.
 * @filter      aal_filter_current_page_type
 * @filter      aal_filter_current_queried_term_object
 * @filter      aal_filter_current_queried_author
 */
class AmazonAutoLinks_ContextualUnit_Breadcrumb extends AmazonAutoLinks_PluginUtility {

    private $___aGET = array();
    private $___oPost;

    /**
     * AmazonAutoLinks_ContextualUnit_Breadcrumb constructor.
     *
     * @param $aGET
     * @param $oPost
     * @since 3.6.0
     */
    public function __construct( $aGET, $oPost ) {
        $this->___aGET  = $aGET;
        $this->___oPost = $oPost;
    }

    /**
     * 
     * @return  array
     */
    public function get() {

        $_sCurrentPageType = apply_filters(
            'aal_filter_current_page_type',
            $this->getCurrentPageType()
        );  // 3.6.0+ For ajax unit loading
        $_aBreadcrumbs     = ( array ) call_user_func(
            array( 
                $this, 
                '___getItemsByType_' . $_sCurrentPageType
            ) 
        );
        return array_filter( $_aBreadcrumbs );
    
    }

    /**
     * For undetected page types.
     * @remark      the `getCurrentPageType()` method returns an empty string when undetected.
     * @since       3.6.0
     */
    private function ___getItemsByType_() {
        return array();
    }

    /**
     * 
     * @return  array
     */
    private function ___getItemsByType_home() {
        return array( get_bloginfo( 'name' ) );
    }
    /**
     * 
     * @return  array
     */
    private function ___getItemsByType_front() {
        return array( get_bloginfo( 'name' ) );
    }
    
    /**
     * 
     * @return  array
     */
    private function ___getItemsByType_singular() {
        
        $_aKeywords = array();
        if ( 
            ! isset( $this->___oPost->post_parent ) 
            || ! $this->___oPost->post_parent 
        ) {
            return $_aKeywords;
        }
        
        // Get ancestor post titles.
        $_iParentPostID = $this->___oPost->post_parent;
        while( $_iParentPostID ) {
            $_oPost         = get_post( $_iParentPostID );
            $_aKeywords[]   = $_oPost->post_title;
            $_iParentPostID = $_oPost->post_parent;
        }                     
        return $_aKeywords;
        
    }
    private function ___getItemsByType_post_type_archive() {
        return array();
    }
    private function ___getItemsByType_taxonomy() {
        $_oTerm = apply_filters(
            'aal_filter_current_queried_term_object',
            $this->getCurrentQueriedObject()
        ); // for ajax unit loading

        return array_filter( array( $_oTerm->name ) )
            + $this->___getParentTerms( $_oTerm );
    }
        /**
         * Get parent terms
         * @return  array       Holds term parent 'names'.
         */
        private function ___getParentTerms( $oTerm ) {
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
    
    private function ___getItemsByType_date() {
        return array();
    }
    private function ___getItemsByType_author() {
        $_oAuthor = $this->getCurrentQueriedObject();
        $_sAuthor = isset( $_oAuthor->display_name ) ? $_oAuthor->display_name : '';
        $_sAuthor = apply_filters( 'aal_filter_current_queried_author', $_sAuthor );  // for ajax unit loading
        return array_filter( array( $_sAuthor ) );   // drop non true items
    }
    private function ___getItemsByType_search() {
        $_aKeywords = array();
        if ( isset( $this->___aGET[ 's' ] ) ) {
            $_aKeywords[] = $this->___aGET[ 's' ];
        }
        return $_aKeywords;                
    }                    
    private function ___getItemsByType_404() {
        return array();
    }
        /**
         * 
         * @return      array
         * @deprecated  not used at the moment
         */
        private function ___getSiteNameAndURL() {
            return array(
                get_bloginfo( 'name' ),
                get_bloginfo( 'url' ),
            );                                                
        }    
         
}