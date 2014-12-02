<?php
/**
    Inserts product links into the pre-defined area of page contents. 
    
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
*/

abstract class AmazonAutoLinks_AutoInsert_ extends AmazonAutoLinks_AutoInsert_SetUp {
    
    protected $arrAutoInsertIDs     = array();    // stores the post IDs of the auto-insert custom post type.
    protected $arrAutoInsertOptions = array();    // multi-dimensional array storing all the options of the auto-insert definitions.
    protected $arrActionHooks       = array();    // stores all the action hooks. 
    protected $arrFilterHooks       = array();    // stores all the filter hooks.
    
    protected $arrDisplayedPageTypes = array(        // stores the current page type information.
        'is_single'     => null,    // deprecated
        'is_singular'   => null,
        'is_home'       => null,
        'is_archive'    => null,
        'is_404'        => null,
        'is_search'     => null,            
    );
    protected $strPostType ='';    // stores the current post type.
    protected $arrTermIDs = array();    // stores the taxonomy terms' IDs of the current post.
    
    /**
     * Represents the array structure of subject page information.
     * 
     * This has a similar elements with $arrDisplayedPageTypes but this is also used for the static insertion 
     * that does not mean that they use the currently displayed page information. They uses the passed post's ID and post type etc.
     * 
     */
    protected static $arrStructure_SubjectPageInfo = array(
        'post_id'       => null,
        'post_type'     => null, 
        'is_single'     => null,    // deprecated
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
    function __call( $strMethodName, $vArgs=null ) {    
        
        // callback_filter_
        $intLength = strlen( 'callback_filter_' );
        if ( substr( $strMethodName, 0, $intLength ) == 'callback_filter_' ) {
            
            $strFiletrName = substr( $strMethodName, $intLength );
            return $strFiletrName == 'wp_insert_post_data'
                ? $this->doFilterForStaticInsertion( $vArgs[0], isset( $vArgs[1] ) ? $vArgs[1] : array() )
                : $this->doFilter( $strFiletrName, $vArgs[0] );
            
        }
            
        // callback_action_
        $intLength = strlen( 'callback_action_' );
        if ( substr( $strMethodName, 0, strlen( 'callback_action_' ) ) == 'callback_action_' ) 
            return $this->doAction( substr( $strMethodName, $intLength ), $vArgs[0] );
        
        // Unknown
        return $vArgs[0];
        
    }    
    
    protected function doFilter( $strFilterName, $strContent ) {
        
        if ( ! isset( $this->arrFilterHooks[ $strFilterName ]  ) ) return $strContent;
        if ( ! is_string( $strContent ) ) return $strContent;        
        
        $arrSubjectPageInfo = array(
            'post_id' => $this->intPostID,
            'post_type' => $this->strPostType,
            'term_ids' => $this->arrTermIDs,
        )  + $this->arrDisplayedPageTypes;
        
        $strPre = '';
        $strPost = '';
        foreach( $this->arrFilterHooks[ $strFilterName ] as $intAutoInsertID ) {
            
            if ( ! $this->isAutoInsertEnabledPage( $intAutoInsertID, $arrSubjectPageInfo ) ) continue;
    
            $arrAutoInsertOptions = $this->arrAutoInsertOptions[ $intAutoInsertID ];        
            
            // position - above, below, or both,
            $strPosition = $arrAutoInsertOptions['position'];
            
            if ( $strPosition == 'above' || $strPosition == 'both' ) {
                $oUnits = new AmazonAutoLinks_Units( array( 'id' => $arrAutoInsertOptions['unit_ids'] ) );
                $strOutput = $oUnits->getOutput();            
                $strPre .= $strOutput;
            }
            if ( $strPosition == 'below' || $strPosition == 'both' ) {
                $oUnits = new AmazonAutoLinks_Units( array( 'id' => $arrAutoInsertOptions['unit_ids'] ) );
                $strOutput = $oUnits->getOutput();                    
                $strPost .= $strOutput;
            }
        
        }
        
        return $strPre . $strContent . $strPost;        
        
    }
    
    protected function doAction( $strActionName, $vArgs ) {
        
        if ( ! isset( $this->arrActionHooks[ $strActionName ]  ) ) return;
        
        $arrSubjectPageInfo = array(
            'post_id' => $this->intPostID,
            'post_type' => $this->strPostType,
            'term_ids' => $this->arrTermIDs,
        )  + $this->arrDisplayedPageTypes;        

        foreach( $this->arrActionHooks[ $strActionName ] as $intAutoInsertID ) {
                
            if ( ! $this->isAutoInsertEnabledPage( $intAutoInsertID, $arrSubjectPageInfo ) ) continue;
            
            $arrAutoInsertOptions = $this->arrAutoInsertOptions[ $intAutoInsertID ];                
            $oUnits = new AmazonAutoLinks_Units( array( 'id' => $arrAutoInsertOptions['unit_ids'] ) );
            $oUnits->render();            
            
        }
        
    }
    
    /**
     * Handles static insertion for posts.
     * 
     * @remark            Only category taxonomy allow/deny check is supported. Other types post_tags and custom taxonomies are not supported yet.
     */
    public function doFilterForStaticInsertion( $arrPostContent, $arrPostMeta=array() ) {

        // if the publish key exists, it means it is an update
        if ( isset( $arrPostMeta['save'] ) && $arrPostMeta['save'] == 'Update' ) return $arrPostContent;
        
        // If it's auto-draft saving feature, do nothing.
        if ( isset( $arrPostContent['post_status'] ) && $arrPostContent['post_status'] != 'publish' ) return $arrPostContent;
    
        // The default disabled post types.
        if ( in_array( $arrPostContent['post_type'], array( AmazonAutoLinks_Commons::PostTypeSlug, AmazonAutoLinks_Commons::PostTypeSlugAutoInsert, 'revision', 'attachment', 'nav_menu_item' ) )  ) 
            return $arrPostContent;
        
        /*    $arrPostMeta structure
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

        $arrSubjectPostInfo = array(
            'post_id' => $arrPostMeta['ID'],
            'post_type' => $arrPostContent['post_type'],
            'term_ids' => $arrPostMeta['post_category'],
        ) + self::$arrStructure_SubjectPageInfo;

        $strPre = '';
        $strPost = '';
        foreach( $this->arrFilterHooks[ 'wp_insert_post_data' ] as $intAutoInsertID ) {
            
            if ( ! $this->isAutoInsertEnabledPage( $intAutoInsertID, $arrSubjectPostInfo ) ) continue;

            $arrAutoInsertOptions = $this->arrAutoInsertOptions[ $intAutoInsertID ];        
            
            // position - above, below, or both,
            $strPosition = $arrAutoInsertOptions['static_position'];
            
            if ( $strPosition == 'above' || $strPosition == 'both' ) {
                $oUnits = new AmazonAutoLinks_Units( array( 'id' => $arrAutoInsertOptions['unit_ids'] ) );
                $strPre .= $oUnits->getOutput();            
            }
            if ( $strPosition == 'below' || $strPosition == 'both' ) {
                $oUnits = new AmazonAutoLinks_Units( array( 'id' => $arrAutoInsertOptions['unit_ids'] ) );
                $strPost .= $oUnits->getOutput();                    
            }
        
        }
        
        $arrPostContent['post_content'] = $strPre . $arrPostContent['post_content'] . $strPost;
            
        return $arrPostContent;
        
    }
                    
    protected function isAutoInsertEnabledPage( $intAutoInsertID, $arrSubjectPostInfo ) {
        
        $arrSubjectPostInfo = $arrSubjectPostInfo + self::$arrStructure_SubjectPageInfo;

        if ( $arrSubjectPostInfo['post_type'] == AmazonAutoLinks_Commons::PostTypeSlug ) return false;
        
        $arrAutoInsertOptions = $this->arrAutoInsertOptions[ $intAutoInsertID ];

        if ( ! $arrAutoInsertOptions['status'] ) return;
        
        // Check the Disable (Deny) criteria.
        if ( $arrAutoInsertOptions['enable_denied_area'] && $this->isDenied( $arrAutoInsertOptions, $arrSubjectPostInfo ) )
            return false;
    
        // Check if the Enable (Allow) criteria.
        if ( $arrAutoInsertOptions['enable_allowed_area'] && ! $this->isAllowed( $arrAutoInsertOptions, $arrSubjectPostInfo ) )
            return false;
        
        return true;
        
    }
    protected function isDenied( $arrAutoInsertOptions, $arrSubjectPostInfo ) {
        
        /* Post IDs - the option field is converted to array at earlier point in this class */
        if ( in_array( $arrSubjectPostInfo['post_id'], $arrAutoInsertOptions['diable_post_ids'] ) )
            return true;
        
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
        foreach( ( array ) $arrAutoInsertOptions['disable_page_types'] as $strKey => $fDisable ) {
            
            if ( ! $fDisable || ! isset( $arrSubjectPostInfo[ $strKey ] ) ) continue;
            
            if ( $arrSubjectPostInfo[ $strKey ] )    // if the current page type is checked,
                return true;    // it means it is denied.
            
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
            isset( $arrAutoInsertOptions['disable_post_types'][ $arrSubjectPostInfo['post_type'] ] ) 
            && $arrAutoInsertOptions['disable_post_types'][ $arrSubjectPostInfo['post_type'] ] 
        )
            return true;
            
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
        $arrTerms = array();
        foreach( ( array ) $arrAutoInsertOptions['disable_taxonomy'] as $strTaxonomySlug => $arrTheseTerms ) 
            $arrTerms = $arrTerms + $arrTheseTerms;    // array_merge() loses numeric index.
    
        // Drop unchecked items
        $arrTerms = array_filter( $arrTerms );
        $arrTermIDs = array_keys( $arrTerms ); // get the keys as the values.        
        foreach( $arrTermIDs as $intDisabledTermID ) 
            if ( in_array( $intDisabledTermID, $arrSubjectPostInfo['term_ids'] ) )
                return true;
                
        // Otherwise, it's nor denied.
        return false;
        
    }
    
    protected function isAllowed( $arrAutoInsertOptions, $arrSubjectPostInfo ) {

        /* Post IDs - the option field is converted to array at earlier point in this class */
        $_aEnabledPostIDs = array_filter( $arrAutoInsertOptions['enable_post_ids'] );
        if ( ! empty( $_aEnabledPostIDs ) && ! in_array( $arrSubjectPostInfo['post_id'], $_aEnabledPostIDs ) ) {    // at least one id is set
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
        $_aEnabledPageTypes = array_filter( ( array ) $arrAutoInsertOptions['enable_page_types'] );
        if ( ! empty( $_aEnabledPageTypes ) ) {            // means at least one item is selected    
            $_fIsEnabled = false;
            foreach( $_aEnabledPageTypes as $strKey => $_fEnable ) {
                if ( isset( $arrSubjectPostInfo[ $strKey ] ) && $arrSubjectPostInfo[ $strKey ] && $_fEnable ) {
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
        $_aEnabledPostTypes = array_filter( ( array ) $arrAutoInsertOptions['enable_post_types'] );    // drop unchecked items
        $_sCurrentPostType = $arrSubjectPostInfo['post_type'];
        if ( ! empty( $_aEnabledPostTypes ) ) {    // if one more post types are enabled
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
        $arrTerms = array();
        foreach( ( array ) get_object_taxonomies( $arrSubjectPostInfo['post_type'], 'names' ) as $strTaxonomySlug  ) 
            $arrTerms = isset( $arrAutoInsertOptions['enable_taxonomy'][ $strTaxonomySlug ] )
                ? $arrTerms + $arrAutoInsertOptions['enable_taxonomy'][ $strTaxonomySlug ]
                : $arrTerms;
            
        // Drop unchecked items
        $arrTerms = array_filter( $arrTerms );
        if ( ! empty( $arrTerms ) ) {        // at least one item is checked for the taxonomies of the current post
        
            $arrTermIDs = array_keys( $arrTerms ); // get the keys as the values.
            $fIsEnabled = false;
            foreach( $arrTermIDs as $intAllowedTermID ) 
                if ( in_array( $intAllowedTermID, $arrSubjectPostInfo['term_ids'] ) )
                    $fIsEnabled = true;
                    
            if ( ! $fIsEnabled )
                return false;
                
        }
    
        // Otherwise, it's enabled
        return true;
        
    }
    
    
}