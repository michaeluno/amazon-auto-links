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
 * Inserts product links into the pre-defined area of page contents. 
 * 
 * @package     Amazon Auto Links
 * @since       2.0.0
*/
class AmazonAutoLinks_AutoInsertOutput_Frontend extends AmazonAutoLinks_AutoInsertOutput_Base {

    private $iPostID;
    
    /**
     * Stores the current page type information.
     */
    private $aDisplayedPageTypes = array(        
        'is_singular'   => null,
        'is_home'       => null,
        'is_archive'    => null,
        'is_404'        => null,
        'is_search'     => null,            
    );
    
    /**
     * Stores the current post type.
     */
    private $sPostType ='';   
    
    /**
     * Stores the taxonomy terms' IDs of the current post.
     */
    private $aTermIDs = array();     
        
    /**
     * Sets up hooks.
     */
    public function __construct() {
        
        parent::__construct();

        /**
         * Set up the properties for currently displaying page.
         * The `init` hook is too early to perform the functions including is_singular(), is_page() etc. 
         * as $wp_query is not established yet.
         */
        add_action( 'wp', array( $this, '_replyToSetUpPageTypeProperty' ) );        
        
    }        
        /**
         * Sets up the properties for the criteria of page types, taxonomies etc.
         * 
         * @remark          The $wp_query object has to be set prior to calling this method.
         * @callback        action      wp
         */
        public function _replyToSetUpPageTypeProperty() {
            
            if ( 0 === count( $this->aAutoInsertIDs ) ) { 
                return; 
            }
            
            $this->iPostID = $this->___getPostID();
            
            $this->aDisplayedPageTypes = array(
                'is_single'     => is_single(),    // deprecated
                'is_singular'   => is_singular(),
                'is_home'       => ( is_home() || is_front_page() ),
                'is_archive'    => is_archive(),
                'is_404'        => is_404(),
                'is_search'     => is_search(),            
            );
            
            // The below are nothing to do with pages that don't have a post ID.
            if ( ! $this->iPostID ) { 
                return; 
            }
                    
            $this->sPostType = get_post_type( $this->iPostID );            
            $this->aTermIDs  = array();
            
            $aTaxonomies = $this->getPostTaxonomies( $this->iPostID );
            foreach( $aTaxonomies as $sTaxonomySlug => $oTaxonomy ) {
                
                $aTaxonomyTerms = wp_get_post_terms( 
                    $this->iPostID, 
                    $sTaxonomySlug 
                );
                foreach( $aTaxonomyTerms as $oTerm ) {
                    $this->aTermIDs[] = $oTerm->term_id;
                }
                
            }
            $this->aTermIDs = array_unique( $this->aTermIDs );     
            
        }
            /**
             * @return      integer
             */
            private function ___getPostID() {
                if ( ! isset( $GLOBALS[ 'wp_query' ]->post ) ) {
                    return 0;
                }
                if ( ! is_object( $GLOBALS[ 'wp_query' ]->post ) ) {
                    return 0;
                }
                return $GLOBALS[ 'wp_query' ]->post->ID;    
            }    
    
    /**
     * @since       3.4.10
     * @return      boolean
     */
    protected function _shouldProceed() {
        if ( is_admin() ) {
            return false;
        }
        return parent::_shouldProceed();
    }
    
    protected function _getFiltersApplied( $sFilterName, $aArguments ) {
        
        $sContent = $this->getElement( $aArguments, 0 );
        if ( ! isset( $this->aFilterHooks[ $sFilterName ]  ) ) {
            return $sContent;
        }
        if ( ! is_string( $sContent ) ) {
            return $sContent;        
        }

        // 4.0.4+ The `the_content` filter should be applied only in the main loop. Otherwise, Prevent Duplicates option takes effect in unknown calls.
        if ( 'the_content' === $sFilterName && ! in_the_loop() ) {
            return $sContent;
        }

        $aSubjectPageInfo = array(
            'post_id'   => $this->iPostID,
            'post_type' => $this->sPostType,
            'term_ids'  => $this->aTermIDs,
        )  + $this->aDisplayedPageTypes;
        
        $sPre  = '';
        $sPost = '';
        foreach( $this->aFilterHooks[ $sFilterName ] as $iAutoInsertID ) {
            
            if ( ! $this->_isAutoInsertEnabledPage( $iAutoInsertID, $aSubjectPageInfo ) ) {
                continue;
            }
    
            $aAutoInsertOptions = $this->aAutoInsertOptions[ $iAutoInsertID ];
            
            // position - above, below, or both,
            $sPosition   = $aAutoInsertOptions[ 'position' ];
            $_aArguments = array( 
                'id' => $aAutoInsertOptions[ 'unit_ids' ]
            );
            if ( $sPosition == 'above' || $sPosition == 'both' ) {
                $sPre  .= AmazonAutoLinks( $_aArguments, false );
            }
            if ( $sPosition == 'below' || $sPosition == 'both' ) {
                $sPost .= AmazonAutoLinks( $_aArguments, false );
            }
        
        }
        
        return $sPre . $sContent . $sPost;        
        
    }
    
    /**
     * Performs actions.
     */
    protected function _doActions( $sActionName, $aArguments ) {
        
        if ( ! isset( $this->aActionHooks[ $sActionName ]  ) ) { 
            return;
        }
        
        $aSubjectPageInfo = array(
            'post_id'   => $this->iPostID,
            'post_type' => $this->sPostType,
            'term_ids'  => $this->aTermIDs,
        )  + $this->aDisplayedPageTypes;        

        foreach( $this->aActionHooks[ $sActionName ] as $iAutoInsertID ) {
                
            if ( ! $this->_isAutoInsertEnabledPage( $iAutoInsertID, $aSubjectPageInfo ) ) {
                continue;
            }
            
            $aAutoInsertOptions = $this->aAutoInsertOptions[ $iAutoInsertID ];                  
            $_aArguments = array( 
                'id' => $aAutoInsertOptions[ 'unit_ids' ]
            );            
            AmazonAutoLinks( $_aArguments );

        }

    }

}
