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
 * @since       2.0.5
*/
abstract class AmazonAutoLinks_AutoInsertOutput_Base extends AmazonAutoLinks_PluginUtility {
        
    /**
     * Stores the post IDs of the auto-insert custom post type.
     */
    protected $aAutoInsertIDs     = array();    
    
    /**
     * Multi-dimensional array storing all the options of the auto-insert definitions.
     */
    protected $aAutoInsertOptions = array();    
    
    /**
     * Stores all the action hooks. 
     */
    protected $aActionHooks       = array();    
    
    /**
     * Stores all the filter hooks.
     */
    protected $aFilterHooks       = array();        
    
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
        
    }    
    
    /**
     * @since       3.3.0
     * @remark      Override this method in an extended class.
     * @return      boolean
     */
    protected function _shouldProceed() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return false;
        }
        if ( defined( 'DOING_CRON' ) ) {
            return false;
        }
        return true;
    }

    /**
     * Sets up registered hooks and store hooks in the property array.
     * 
     * @callback        action      init
     * @return          void
     */
    public function _replyToSetUpHooks() {
        
        $this->aAutoInsertIDs = $this->getActiveAutoInsertIDs();
        if ( 0 === count( $this->aAutoInsertIDs ) ) { 
            return; 
        }
        
        // Retrieve all the options.
        foreach( $this->aAutoInsertIDs as $iID ) {
        
            $this->aAutoInsertOptions[ $iID ] = $this->getPostMeta( $iID )
                + AmazonAutoLinks_AutoInsertAdminPage::$aStructure_AutoInsertDefaultOptions;
            
            // convert comma delimited stings to array
            $this->aAutoInsertOptions[ $iID ][ 'diable_post_ids' ] = $this->getStringIntoArray( $this->aAutoInsertOptions[ $iID ]['diable_post_ids'], ',' );
            $this->aAutoInsertOptions[ $iID ][ 'enable_post_ids' ] = $this->getStringIntoArray( $this->aAutoInsertOptions[ $iID ]['enable_post_ids'], ',' );
            
        }
        
        // Find out used filters - user-defined and built-in(plugin's predefined) filters.
        $this->aFilterHooks = $this->___getFilterHooks( $this->aAutoInsertOptions );
        
        // Find out used actions - get user-defined custom filters
        $this->aActionHooks = $this->___getHooks( $this->aAutoInsertOptions, 'action_hooks' );
    
        // Add hooks!
        foreach ( $this->aFilterHooks as $sFilter => $aAutoInsertIDs ) {
            
            if ( 'wp_insert_post_data' === $sFilter ) {
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
         * @return      array
         */
        private function ___getFilterHooks( $aAutoInsertOptions ) {

            $aFilterHooks = array();    
                
            // Get built-in & static filters if enabled.
            foreach( $aAutoInsertOptions as $iAutoInsertID => $aOptions ) {
                $aOptionFilters = $aOptions[ 'built_in_areas' ] + $aOptions[ 'static_areas' ];
                foreach( $aOptionFilters as $sFilter => $_bEnabled ) {
                    if ( ! $_bEnabled ) {
                        continue;
                    }                    
                    $aFilterHooks[ $sFilter ] = isset( $aFilterHooks[ $sFilter ] ) && is_array( $aFilterHooks[ $sFilter ] )
                        ? array_merge( $aFilterHooks[ $sFilter ], array( $iAutoInsertID ) )
                        : array( $iAutoInsertID );
                }                
            }
            
            // Get user-defined custom filters
            $aFilterHooks = $this->___getHooks( $aAutoInsertOptions, 'filter_hooks', $aFilterHooks );
            
            return $aFilterHooks;
            
        }
        
        /**
         * Creates an array storing the auto-insert definition(post) ids with the keys of hooks.
         * 
         * @param            string            $sOptionKey            either 'filter_hooks' or 'action_hooks' which are defined in the AmazonAutoLinks_Form_AutoInsert class.
         */
        private function ___getHooks( $aAutoInsertOptions, $sOptionKey, $aHooks=array() ) {
            
            foreach( $aAutoInsertOptions as $iAutoInsertID => $aOptions ) {
                
                $aParsedHooks = $this->getStringIntoArray( $aOptions[ $sOptionKey ], ',' );        
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
     * Takes care of the calls triggered by hooks.
     * 
     * Redirects the dynamic callbacks.
     */
    public function __call( $sMethodName, $aArguments=null ) {    

        // Apply filters.
        if ( $this->hasPrefix( 'callback_filter_', $sMethodName ) ) {            
            $_iLength     = strlen( 'callback_filter_' );
            $_sFilterName = substr( $sMethodName, $_iLength );
            return $this->_getFiltersApplied( $_sFilterName, $aArguments );
        }
            
        // Do actions.
        if ( $this->hasPrefix( 'callback_action_', $sMethodName ) ) {
            $_iLength     = strlen( 'callback_action_' );
            $_sActionName = substr( $sMethodName, $_iLength );
            return $this->_doActions( $_sActionName, $aArguments );
        }
        
        // Unknown
        return $this->getElement( $aArguments, 0 );
        
    }   
    
    /**
     * @since       3.4.10
     */
    protected function _doActions( $sActionName, $aArguments ) {}

    /**
     * @since       3.4.10
     */
    protected function _getFiltersApplied( $sFilterName, $aArguments ) {
        return $this->getElement( $aArguments, 0 );
    }

    /**
     * @return      boolean
     */ 
    protected function _isAutoInsertEnabledPage( $iAutoInsertID, $aSubjectPostInfo ) {
        
        $aSubjectPostInfo = $aSubjectPostInfo + self::$___aStructure_SubjectPageInfo;

        if ( $aSubjectPostInfo[ 'post_type' ] == AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] ) {
            return false;
        }
        
        $aAutoInsertOptions = $this->aAutoInsertOptions[ $iAutoInsertID ];

        if ( ! $aAutoInsertOptions[ 'status' ] ) {
            return false;
        }
        
        // Check the Disable (Deny) criteria.
        if ( $aAutoInsertOptions['enable_denied_area'] && $this->___isDisallowed( $aAutoInsertOptions, $aSubjectPostInfo ) ) {
            return false;
        }
    
        // Check if the Enable (Allow) criteria.
        if ( $aAutoInsertOptions['enable_allowed_area'] && ! $this->___isAllowed( $aAutoInsertOptions, $aSubjectPostInfo ) ) {
            return false;
        }
        
        return true;
        
    }  
        /**
         * Represents the array structure of subject page information.
         * 
         * This has a similar elements with $aDisplayedPageTypes but this is also used for the static insertion 
         * that does not mean that they use the currently displayed page information. They uses the passed post's ID and post type etc.
         */
        private static $___aStructure_SubjectPageInfo = array(
            'post_id'       => null,
            'post_type'     => null, 
            'is_singular'   => null,
            'is_home'       => null,
            'is_archive'    => null,
            'is_404'        => null,
            'is_search'     => null,
            'term_ids'      => array(),
        );
        
        /**
         * @return      boolean
         */
        private function ___isDisallowed( $aAutoInsertOptions, $aSubjectPostInfo ) {
            
            /* Post IDs - the option field is converted to array at earlier point in this class */
            if ( in_array( $aSubjectPostInfo['post_id'], $aAutoInsertOptions['diable_post_ids'] ) ) {
                return true;
            }
            
            /* 
             * Page Types - structure example
                [disable_page_types] => Array (
                    [is_singular] => 0
                    [is_home] => 1
                    [is_archive] => 0
                    [is_404] => 1
                    [is_search] => 0
                )
             */
            foreach( ( array ) $aAutoInsertOptions['disable_page_types'] as $sKey => $fDisable ) {
                
                if ( ! $fDisable || ! isset( $aSubjectPostInfo[ $sKey ] ) ) {
                    continue;
                }
                
                // if the current page type is checked,
                if ( $aSubjectPostInfo[ $sKey ] ) {
                    // it means it is denied.
                    return true;    
                }
                
            }

            /*    
             *     Post Types    - structure example
                [disable_post_types] => Array (
                    [post] => 0
                    [page] => 1
                    [apf_posts] => 0
                )
             */        
            if ( 
                isset( $aAutoInsertOptions['disable_post_types'][ $aSubjectPostInfo['post_type'] ] ) 
                && $aAutoInsertOptions['disable_post_types'][ $aSubjectPostInfo['post_type'] ] 
            ) {
                return true;
            }
                
            /* 
             * Taxonomies - structure example
                [disable_taxonomy] => Array (
                    [category] => Array (
                        [10] => 0
                        [1] => 0
                        [2] => 0
                    )
                    [post_tag] => Array (
                        [7] => 0
                    )
                    [amazon_auto_links_tag] => Array (
                        [8] => 0
                        [16] => 0
                    )
                )
            */
            // Since each term id is unique throughout the WordPress site settings, drop the taxonomy slugs.
            $aTerms = array();
            foreach( ( array ) $aAutoInsertOptions['disable_taxonomy'] as $sTaxonomySlug => $aTheseTerms ) {
                // array_merge() loses numeric index.
                $aTerms = $aTerms + $aTheseTerms;    
            }
        
            // Drop unchecked items
            $aTerms   = array_filter( $aTerms );
            $aTermIDs = array_keys( $aTerms ); // get the keys as the values.        
            foreach( $aTermIDs as $iDisabledTermID ) {
                if ( in_array( $iDisabledTermID, $aSubjectPostInfo['term_ids'] ) ) {
                    return true;
                }
            }
                    
            // Otherwise, it's nor denied.
            return false;
            
        }
    
        private function ___isAllowed( $aAutoInsertOptions, $aSubjectPostInfo ) {

            /* Post IDs - the option field is converted to array at earlier point in this class */
            $_aEnabledPostIDs = array_filter( $aAutoInsertOptions['enable_post_ids'] );
            if ( ! empty( $_aEnabledPostIDs ) && ! in_array( $aSubjectPostInfo['post_id'], $_aEnabledPostIDs ) ) {    // at least one id is set
                return false;            
            }
            
            /* 
             * Page Types - structure example
             *     [enable_page_types] => Array (
                        [is_singular] => 1
                        [is_home] => 1
                        [is_archive] => 0
                        [is_404] => 1
                        [is_search] => 0
                    )
             */
            $_aEnabledPageTypes = array_filter( ( array ) $aAutoInsertOptions['enable_page_types'] );
            if ( ! empty( $_aEnabledPageTypes ) ) {            // means at least one item is selected    
                $_fIsEnabled = false;
                foreach( $_aEnabledPageTypes as $sKey => $_fEnable ) {
                    if ( isset( $aSubjectPostInfo[ $sKey ] ) && $aSubjectPostInfo[ $sKey ] && $_fEnable ) {
                        $_fIsEnabled = true;    // it means it is denied.
                    }                
                }    
                if ( ! $_fIsEnabled ) {
                    return false;
                }
            }

            /*    
             *     Post Types    - this should be performed after evaluation the taxonomies.
             *     structure example
                [enable_post_types] => Array (
                    [post] => 0
                    [page] => 1
                    [apf_posts] => 0
                )
             */        
            $_aEnabledPostTypes = array_filter( ( array ) $aAutoInsertOptions['enable_post_types'] );    // drop unchecked items
            $_sCurrentPostType  = $aSubjectPostInfo['post_type'];
            
            // if one more post types are enabled
            if ( ! empty( $_aEnabledPostTypes ) ) {    
                if ( ! ( isset( $_aEnabledPostTypes[ $_sCurrentPostType ] ) && $_aEnabledPostTypes[ $_sCurrentPostType ] ) ) {
                    return false;
                }
            }
                
            /* 
             * Taxonomies - structure example
                [enable_taxonomy] => Array (
                    [category] => Array (
                        [10] => 0
                        [1] => 0
                        [2] => 0
                    )
                    [post_tag] => Array (
                        [7] => 0
                    )
                    [amazon_auto_links_tag] => Array (
                        [8] => 0
                        [16] => 0
                    )
                )
            */
            // Retrieve the taxonomy names associated with the current page's post type
            $aTerms = array();
            foreach( ( array ) get_object_taxonomies( $aSubjectPostInfo['post_type'], 'names' ) as $sTaxonomySlug  ) {
                $aTerms = isset( $aAutoInsertOptions[ 'enable_taxonomy' ][ $sTaxonomySlug ] )
                    ? $aTerms + ( array ) $aAutoInsertOptions[ 'enable_taxonomy' ][ $sTaxonomySlug ]
                    : $aTerms;
            }
                
            // Drop unchecked items
            $aTerms = array_filter( $aTerms );
            
            // at least one item is checked for the taxonomies of the current post
            if ( ! empty( $aTerms ) ) {        
            
                // get the keys as the values.
                $aTermIDs   = array_keys( $aTerms ); 
                $fIsEnabled = false;
                foreach( $aTermIDs as $iAllowedTermID ) {
                    if ( in_array( $iAllowedTermID, $aSubjectPostInfo['term_ids'] ) ) {
                        $fIsEnabled = true;
                    }
                }
                        
                if ( ! $fIsEnabled ) {
                    return false;
                }
                    
            }
        
            // Otherwise, it's enabled
            return true;
            
        }
        
}
