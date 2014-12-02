<?php
/**
    Inserts product links into the pre-defined area of page contents. 
    
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.5
*/

abstract class AmazonAutoLinks_AutoInsert_SetUp {
    
    function __construct() {
                
        add_action( 'init', array( $this, '_replyToSetAutoInsertIDs' ), 10 );
                
        /**
         * Set up hooks - add hooks regardless whether the unit output is not for the displaying page or not
         * in order to let custom hooks being added which are loaded earlier than the $wp_query object is established.
         * This must be done after setting up the auto-insert ID property array.
         */
        add_action( 'init', array( $this, '_replyToSetUpHooks' ), 11 );

        /**
         * Set up the properties for currently displaying page.
         * The `init` hook is too early to perform the functions including is_singular(), is_page() etc. 
         * as $wp_query is not established yet.
         */
        add_action( 'wp', array( $this, '_replyToSetUpPageTypeProperties' ) );

        
    }    

    /**
     * Stores auto-insert IDs into the property.
     * 
     * Find auto-insert definitions and if no auto-insert items are set, do nothing.
     */
    public function _replyToSetAutoInsertIDs() {
            
        $this->arrAutoInsertIDs = $this->getAutoInsertIDs();
        
    }    
        /**
         * Returns the auto-insert ids.
         * 
         * @todo        The database query is slow so implement a mechanism that stores the enabled items in the options table. 
         * When the auto-insert items are removed or added, the option should will be updated and this class only uses the value stored there.
         */
        protected function getAutoInsertIDs() {
            
            $_oQuery = new WP_Query(
                array(
                    'post_status'    => 'publish',     // optional
                    'post_type'      => AmazonAutoLinks_Commons::PostTypeSlugAutoInsert, 
                    'posts_per_page' => -1, // ALL posts
                    'fields'         => 'ids',  // return an array of post IDs
                )
            );       
            return $_oQuery->posts;
            
        }

    /**
     * Sets up registered hooks and store hooks in the property array.
     */
    public function _replyToSetUpHooks() {
        
        if ( 0 == count( $this->arrAutoInsertIDs ) ) { return; }
        
        // Retrieve all the options.
        foreach( $this->arrAutoInsertIDs as $intID ) {
        
            $this->arrAutoInsertOptions[ $intID ] = AmazonAutoLinks_Option::getUnitOptionsByPostID( $intID )
                + AmazonAutoLinks_Form_AutoInsert::$arrStructure_AutoInsertOptions;
            
            // convert comma delimited stings to array
            $this->arrAutoInsertOptions[ $intID ]['diable_post_ids'] = AmazonAutoLinks_Utilities::convertStringToArray( $this->arrAutoInsertOptions[ $intID ]['diable_post_ids'], ',' );
            $this->arrAutoInsertOptions[ $intID ]['enable_post_ids'] = AmazonAutoLinks_Utilities::convertStringToArray( $this->arrAutoInsertOptions[ $intID ]['enable_post_ids'], ',' );
            
        }
        
        // Find out used filters - user-defined and built-in(plugin's predefined) filters.
        $this->arrFilterHooks = $this->getFilters( $this->arrAutoInsertOptions );
        
        // Find out used actions - get user-defined custom filters
        $this->arrActionHooks = $this->getHooks( $this->arrAutoInsertOptions, 'action_hooks' );
    
        // Add hooks!
        foreach ( $this->arrFilterHooks as $strFilter => $arrAutoInsertIDs ) {
            
            if ( $strFilter == 'wp_insert_post_data' ) {
                add_filter( $strFilter, array( $this, "callback_filter_{$strFilter}" ), '99', 2 );
            } else {
                add_filter( $strFilter, array( $this, "callback_filter_{$strFilter}" ) );
            }
            
        }
                            
        foreach ( $this->arrActionHooks as $strAction => $arrAutoInsertIDs ) {
            add_action( $strAction, array( $this, "callback_action_{$strAction}" ) );        
        }

    }
    
        protected function getFilters( $arrAutoInsertOptions ) {

            $arrFilterHooks = array();    
                
            // Get built-in & static filters if enabled.
            foreach( $arrAutoInsertOptions as $intAutoInsertID => $arrOptions ) {
                $arrOptionFilters = $arrOptions['built_in_areas'] + $arrOptions['static_areas'];
                foreach( $arrOptionFilters as $strFilter => $fEnabled ) {
                    if ( $fEnabled ) {
                        $arrFilterHooks[ $strFilter ] = isset( $arrFilterHooks[ $strFilter ] ) && is_array( $arrFilterHooks[ $strFilter ] )
                            ? array_merge( $arrFilterHooks[ $strFilter ], array( $intAutoInsertID ) )
                            : array( $intAutoInsertID );
                    }
                }
                
            }
            
            // Get user-defined custom filters
            $arrFilterHooks = $this->getHooks( $arrAutoInsertOptions, 'filter_hooks', $arrFilterHooks );
            
            return $arrFilterHooks;
            
        }
        
        /**
         * Creates an array storing the auto-insert definition(post) ids with the keys of hooks.
         * 
         * @param            string            $strOptionKey            either 'filter_hooks' or 'action_hooks' which are defined in the AmazonAutoLinks_Form_AutoInsert class.
         */
        protected function getHooks( $arrAutoInsertOptions, $strOptionKey, $arrHooks=array() ) {
            
            foreach( $arrAutoInsertOptions as $intAutoInsertID => $arrOptions ) {
                
                $arrParsedHooks = AmazonAutoLinks_Utilities::convertStringToArray( $arrOptions[ $strOptionKey ], ',' );        
                $arrParsedHooks = array_filter( $arrParsedHooks ); // drop non-values.
                foreach( $arrParsedHooks as $strHook ) {
                    $arrHooks[ $strHook ] = isset( $arrHooks[ $strHook ] ) && is_array( $arrHooks[ $strHook ] ) 
                        ? array_merge( $arrHooks[ $strHook ], array( $intAutoInsertID ) )
                        : array( $intAutoInsertID );
                }
                
            }
            foreach( $arrHooks as &$arrIDs ) {
                $arrIDs = array_unique( array_filter( $arrIDs ) );
                if ( empty( $arrIDs ) ) {
                    unset( $arrIDs );
                }
            }
            
            return $arrHooks;
            
        }        
        
    /**
     * Sets up the properties for the criteria of page types, taxonomies etc.
     * 
     * @remark            The $wp_query object has to be set priort to calling this method.
     */
    public function _replyToSetUpPageTypeProperties() {
        
        if ( count( $this->arrAutoInsertIDs ) == 0 ) { return; }
        
        $this->intPostID = $this->getPostID();
        
        $this->arrDisplayedPageTypes = array(
            'is_single'     => is_single(),    // deprecated
            'is_singular'   => is_singular(),
            'is_home'       => ( is_home() || is_front_page() ),
            'is_archive'    => is_archive(),
            'is_404'        => is_404(),
            'is_search'     => is_search(),            
        );
        
        // The below are nothing to do with pages that don't have a post ID.
        if ( ! $this->intPostID ) { return; }
                
        $this->strPostType = get_post_type( $this->intPostID );    
        
        $this->arrTermIDs = array();
        $arrTaxonomies = AmazonAutoLinks_WPUtilities::getPostTaxonomies( $this->intPostID );
        foreach( $arrTaxonomies as $strTaxonomySlug => $oTaxonomy ) {
            
            $arrTaxonomyTerms = wp_get_post_terms( $this->intPostID, $strTaxonomySlug );
            foreach( $arrTaxonomyTerms as $oTerm ) {
                $this->arrTermIDs[] = $oTerm->term_id;
            }
            
        }
        $this->arrTermIDs = array_unique( $this->arrTermIDs );     
        
    }
        protected function getPostID() {
            if ( isset( $GLOBALS['wp_query']->post ) && is_object( $GLOBALS['wp_query']->post ) ) {
                return $GLOBALS['wp_query']->post->ID;    
            }
        }
        
}