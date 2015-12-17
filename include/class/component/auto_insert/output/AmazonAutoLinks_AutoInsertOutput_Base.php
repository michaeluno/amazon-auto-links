<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Inserts product links into the pre-defined area of page contents. 
 *  
 * @package     Amazon Auto Links
 * @since       2.0.5
*/
abstract class AmazonAutoLinks_AutoInsertOutput_Base extends AmazonAutoLinks_PluginUtility {
    
    /**
     * Sets up hooks.
     */
    public function __construct() {
                
        if ( ! $this->_shouldProceed() ) {
            return;
        }
                                
        /**
         * Set up hooks - add hooks regardless whether the unit output is not for the displaying page or not
         * in order to let custom hooks being added which are loaded earlier than the $wp_query object is established.
         * This must be done after setting up the auto-insert ID property array.
         */
        add_action( 'init', array( $this, '_replyToSetUpHooks' ) );

        /**
         * Set up the properties for currently displaying page.
         * The `init` hook is too early to perform the functions including is_singular(), is_page() etc. 
         * as $wp_query is not established yet.
         */
        add_action( 'wp', array( $this, '_replyToSetUpPageTypeProperty' ) );

        
    }    
        /**
         * @since       3.3.0
         * @return      boolean
         */
        private function _shouldProceed() {
            
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                return false;
            }
            
            if ( defined( 'DOING_CRON' ) ) {
                return false;
            }

            return ! is_admin();
            
        }

    /**
     * Sets up registered hooks and store hooks in the property array.
     * 
     * @callback        action      init
     */
    public function _replyToSetUpHooks() {
        
        $this->aAutoInsertIDs = $this->_getActiveAutoInsertIDs();
        if ( 0 == count( $this->aAutoInsertIDs ) ) { 
            return; 
        }
        
        // Retrieve all the options.
        foreach( $this->aAutoInsertIDs as $iID ) {
        
            $this->aAutoInsertOptions[ $iID ] = $this->getPostMeta( $iID )
                + AmazonAutoLinks_AutoInsertAdminPage::$aStructure_AutoInsertDefaultOptions;
            
            // convert comma delimited stings to array
            $this->aAutoInsertOptions[ $iID ]['diable_post_ids'] = $this->convertStringToArray( $this->aAutoInsertOptions[ $iID ]['diable_post_ids'], ',' );
            $this->aAutoInsertOptions[ $iID ]['enable_post_ids'] = $this->convertStringToArray( $this->aAutoInsertOptions[ $iID ]['enable_post_ids'], ',' );
            
        }
        
        // Find out used filters - user-defined and built-in(plugin's predefined) filters.
        $this->aFilterHooks = $this->getFilters( $this->aAutoInsertOptions );
        
        // Find out used actions - get user-defined custom filters
        $this->aActionHooks = $this->getHooks( $this->aAutoInsertOptions, 'action_hooks' );
    
        // Add hooks!
        foreach ( $this->aFilterHooks as $sFilter => $aAutoInsertIDs ) {
            
            if ( $sFilter == 'wp_insert_post_data' ) {
                add_filter( $sFilter, array( $this, "callback_filter_{$sFilter}" ), '99', 2 );
            } else {
                add_filter( $sFilter, array( $this, "callback_filter_{$sFilter}" ) );
            }
            
        }
                            
        foreach ( $this->aActionHooks as $sAction => $aAutoInsertIDs ) {
            add_action( $sAction, array( $this, "callback_action_{$sAction}" ) );        
        }

    }
    
        /**
         * Returns the auto-insert ids.
         * 
         * @return  array
         */
        protected function _getActiveAutoInsertIDs() {
            
            // 3.3.0+
            $_abActiveAutoInsertIDs = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'auto_insert' ] );
            if ( false !== $_abActiveAutoInsertIDs ) {
                return $this->getAsArray( $_abActiveAutoInsertIDs );
            }
            
            // Backward compatibility - if the option is not set, query the database.
            return $this->getActiveAutoInsertIDs();

        }    
    
        protected function getFilters( $aAutoInsertOptions ) {

            $aFilterHooks = array();    
                
            // Get built-in & static filters if enabled.
            foreach( $aAutoInsertOptions as $iAutoInsertID => $aOptions ) {
                $aOptionFilters = $aOptions['built_in_areas'] + $aOptions['static_areas'];
                foreach( $aOptionFilters as $sFilter => $fEnabled ) {
                    if ( $fEnabled ) {
                        $aFilterHooks[ $sFilter ] = isset( $aFilterHooks[ $sFilter ] ) && is_array( $aFilterHooks[ $sFilter ] )
                            ? array_merge( $aFilterHooks[ $sFilter ], array( $iAutoInsertID ) )
                            : array( $iAutoInsertID );
                    }
                }
                
            }
            
            // Get user-defined custom filters
            $aFilterHooks = $this->getHooks( $aAutoInsertOptions, 'filter_hooks', $aFilterHooks );
            
            return $aFilterHooks;
            
        }
        
        /**
         * Creates an array storing the auto-insert definition(post) ids with the keys of hooks.
         * 
         * @param            string            $sOptionKey            either 'filter_hooks' or 'action_hooks' which are defined in the AmazonAutoLinks_Form_AutoInsert class.
         */
        protected function getHooks( $aAutoInsertOptions, $sOptionKey, $aHooks=array() ) {
            
            foreach( $aAutoInsertOptions as $iAutoInsertID => $aOptions ) {
                
                $aParsedHooks = $this->convertStringToArray( 
                    $aOptions[ $sOptionKey ], 
                    ',' 
                );        
                $aParsedHooks = array_filter( $aParsedHooks ); // drop non-values.
                foreach( $aParsedHooks as $sHook ) {
                    $aHooks[ $sHook ] = isset( $aHooks[ $sHook ] ) && is_array( $aHooks[ $sHook ] ) 
                        ? array_merge( $aHooks[ $sHook ], array( $iAutoInsertID ) )
                        : array( $iAutoInsertID );
                }
                
            }
            foreach( $aHooks as &$aIDs ) {
                $aIDs = array_unique( array_filter( $aIDs ) );
                if ( empty( $aIDs ) ) {
                    unset( $aIDs );
                }
            }
            
            return $aHooks;
            
        }        
        
    /**
     * Sets up the properties for the criteria of page types, taxonomies etc.
     * 
     * @remark          The $wp_query object has to be set prior to calling this method.
     * @callback        action      wp
     */
    public function _replyToSetUpPageTypeProperty() {
        
        if ( count( $this->aAutoInsertIDs ) == 0 ) { 
            return; 
        }
        
        $this->iPostID = $this->getPostID();
        
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
        
        $this->aTermIDs = array();
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
        protected function getPostID() {
            if ( isset( $GLOBALS['wp_query']->post ) && is_object( $GLOBALS['wp_query']->post ) ) {
                return $GLOBALS['wp_query']->post->ID;    
            }
        }
        
}