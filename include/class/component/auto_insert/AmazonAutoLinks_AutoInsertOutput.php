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
 * @since       2.0.0
*/
class AmazonAutoLinks_AutoInsertOutput extends AmazonAutoLinks_AutoInsertOutput_Base {
    
    protected $aAutoInsertIDs     = array();    // stores the post IDs of the auto-insert custom post type.
    protected $aAutoInsertOptions = array();    // multi-dimensional array storing all the options of the auto-insert definitions.
    protected $aActionHooks       = array();    // stores all the action hooks. 
    protected $aFilterHooks       = array();    // stores all the filter hooks.
    
    /**
     * Stores the current page type information.
     */
    protected $aDisplayedPageTypes = array(        
        'is_singular'   => null,
        'is_home'       => null,
        'is_archive'    => null,
        'is_404'        => null,
        'is_search'     => null,            
    );
    
    /**
     * Stores the current post type.
     */
    protected $sPostType ='';   
    
    /**
     * Stores the taxonomy terms' IDs of the current post.
     */
    protected $aTermIDs = array();   
    
    /**
     * Represents the array structure of subject page information.
     * 
     * This has a similar elements with $aDisplayedPageTypes but this is also used for the static insertion 
     * that does not mean that they use the currently displayed page information. They uses the passed post's ID and post type etc.
     */
    protected static $aStructure_SubjectPageInfo = array(
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
     * Takes care of the calls triggered by hooks.
     * 
     * Redirects the dynamic callbacks.
     */
    function __call( $sMethodName, $vArgs=null ) {    
        
        // callback_filter_
        $iLength = strlen( 'callback_filter_' );
        if ( substr( $sMethodName, 0, $iLength ) == 'callback_filter_' ) {
            
            $sFiletrName = substr( $sMethodName, $iLength );
            return $sFiletrName == 'wp_insert_post_data'
                ? $this->doFilterForStaticInsertion( $vArgs[0], isset( $vArgs[1] ) ? $vArgs[1] : array() )
                : $this->doFilter( $sFiletrName, $vArgs[0] );
            
        }
            
        // callback_action_
        $iLength = strlen( 'callback_action_' );
        if ( substr( $sMethodName, 0, strlen( 'callback_action_' ) ) == 'callback_action_' ) {
            return $this->doAction( substr( $sMethodName, $iLength ), $vArgs[0] );
        }
        
        // Unknown
        return $vArgs[ 0 ];
        
    }    
    
    protected function doFilter( $sFilterName, $sContent ) {
        
        if ( ! isset( $this->aFilterHooks[ $sFilterName ]  ) ) {
            return $sContent;
        }
        if ( ! is_string( $sContent ) ) {
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
            
            if ( ! $this->isAutoInsertEnabledPage( $iAutoInsertID, $aSubjectPageInfo ) ) {
                continue;
            }
    
            $aAutoInsertOptions = $this->aAutoInsertOptions[ $iAutoInsertID ];
            
            // position - above, below, or both,
            $sPosition = $aAutoInsertOptions['position'];
            
            if ( $sPosition == 'above' || $sPosition == 'both' ) {
                $oUnits  = new AmazonAutoLinks_Output( 
                    array( 
                        'id' => $aAutoInsertOptions['unit_ids'] 
                    ) 
                );
                $sOutput = $oUnits->get();            
                $sPre .= $sOutput;
            }
            if ( $sPosition == 'below' || $sPosition == 'both' ) {
                $oUnits = new AmazonAutoLinks_Output( 
                    array( 
                        'id' => $aAutoInsertOptions['unit_ids'] 
                    ) 
                );
                $sOutput = $oUnits->get();                    
                $sPost .= $sOutput;
            }
        
        }
        
        return $sPre . $sContent . $sPost;        
        
    }
    
    protected function doAction( $sActionName, $vArgs ) {
        
        if ( ! isset( $this->aActionHooks[ $sActionName ]  ) ) { 
            return;
        }
        
        $aSubjectPageInfo = array(
            'post_id' => $this->iPostID,
            'post_type' => $this->sPostType,
            'term_ids' => $this->aTermIDs,
        )  + $this->aDisplayedPageTypes;        

        foreach( $this->aActionHooks[ $sActionName ] as $iAutoInsertID ) {
                
            if ( ! $this->isAutoInsertEnabledPage( $iAutoInsertID, $aSubjectPageInfo ) ) {
                continue;
            }
            
            $aAutoInsertOptions = $this->aAutoInsertOptions[ $iAutoInsertID ];                
            $oUnits = new AmazonAutoLinks_Output( 
                array( 
                    'id' => $aAutoInsertOptions['unit_ids']
                ) 
            );
            $oUnits->render();            
            
        }
        
    }
    
    /**
     * Handles static insertion for posts.
     * 
     * @remark            Only category taxonomy allow/deny check is supported. Other types post_tags and custom taxonomies are not supported yet.
     */
    public function doFilterForStaticInsertion( $aPostContent, $aPostMeta=array() ) {

        // if the publish key exists, it means it is an update
        if ( isset( $aPostMeta['save'] ) && $aPostMeta['save'] == 'Update' ) {
            return $aPostContent;
        }
        
        // If it's auto-draft saving feature, do nothing.
        if ( isset( $aPostContent['post_status'] ) && $aPostContent['post_status'] != 'publish' ) {
            return $aPostContent;
        }
    
        // The default disabled post types.
        if ( in_array( $aPostContent['post_type'], array( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ], AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ], 'revision', 'attachment', 'nav_menu_item' ) )  ) {
            return $aPostContent;
        }
        
        /*    $aPostMeta structure
            [ID] => 278
            [post_category] => Array (
                [0] => 0
                [1] => 10
                [2] => 9
                [3] => 1
            )
            [tax_input] => Array(
                [post_tag] => test
            )
        */  

        $aSubjectPostInfo = array(
            'post_id'   => $aPostMeta['ID'],
            'post_type' => $aPostContent['post_type'],
            'term_ids'  => $aPostMeta['post_category'],
        ) + self::$aStructure_SubjectPageInfo;

        $sPre  = '';
        $sPost = '';
        foreach( $this->aFilterHooks[ 'wp_insert_post_data' ] as $iAutoInsertID ) {
            
            if ( ! $this->isAutoInsertEnabledPage( $iAutoInsertID, $aSubjectPostInfo ) ) {
                continue;
            }

            $aAutoInsertOptions = $this->aAutoInsertOptions[ $iAutoInsertID ];        
            
            // position - above, below, or both,
            $sPosition = $aAutoInsertOptions['static_position'];
            
            if ( $sPosition == 'above' || $sPosition == 'both' ) {
                $oUnits = new AmazonAutoLinks_Output( 
                    array( 
                        'id' => $aAutoInsertOptions['unit_ids'] 
                    ) 
                );
                $sPre  .= $oUnits->get();            
            }
            if ( $sPosition == 'below' || $sPosition == 'both' ) {
                $oUnits = new AmazonAutoLinks_Output( 
                    array( 
                        'id' => $aAutoInsertOptions['unit_ids'] 
                    ) 
                );
                $sPost .= $oUnits->get();                    
            }
        
        }
        
        $aPostContent['post_content'] = $sPre . $aPostContent['post_content'] . $sPost;
            
        return $aPostContent;
        
    }
                    
    protected function isAutoInsertEnabledPage( $iAutoInsertID, $aSubjectPostInfo ) {
        
        $aSubjectPostInfo = $aSubjectPostInfo + self::$aStructure_SubjectPageInfo;

        if ( $aSubjectPostInfo['post_type'] == AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] ) {
            return false;
        }
        
        $aAutoInsertOptions = $this->aAutoInsertOptions[ $iAutoInsertID ];

        if ( ! $aAutoInsertOptions['status'] ) {
            return;
        }
        
        // Check the Disable (Deny) criteria.
        if ( $aAutoInsertOptions['enable_denied_area'] && $this->isDenied( $aAutoInsertOptions, $aSubjectPostInfo ) ) {
            return false;
        }
    
        // Check if the Enable (Allow) criteria.
        if ( $aAutoInsertOptions['enable_allowed_area'] && ! $this->isAllowed( $aAutoInsertOptions, $aSubjectPostInfo ) ) {
            return false;
        }
        
        return true;
        
    }
    protected function isDenied( $aAutoInsertOptions, $aSubjectPostInfo ) {
        
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
    
    protected function isAllowed( $aAutoInsertOptions, $aSubjectPostInfo ) {

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
            $aTerms = isset( $aAutoInsertOptions['enable_taxonomy'][ $sTaxonomySlug ] )
                ? $aTerms + $aAutoInsertOptions['enable_taxonomy'][ $sTaxonomySlug ]
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