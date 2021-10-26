<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Creates Auto Amazon Links custom preview post type.
 * 
 * @since 2.2.0
 */
class AmazonAutoLinks_PostType_UnitPreview {
    
    /**
     * @var   string
     * @since 2.2.0
     */ 
    protected $sDefaultPreviewSlug = '';
    
    /**
     * @var   string
     * @since 2.2.0
     */
    protected $sPreviewPostTypeSlug = '';

    /**
     * @var   boolean
     * @since 5.0.0
     */
    public $bIsPreviewVisible = true;

    /**
     * Sets up hooks and properties.
     *
     * @since 2.2.0
     */
    public function __construct( $sPreviewPostTypeSlug='' ) {
        
        // If a custom preview post type is not set, do nothing.
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $this->___isCustomPreviewPostTypeSet( $_oOption ) ) {
            return;
        }

        // Properties
        $this->sDefaultPreviewSlug  = AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ];
        $this->sPreviewPostTypeSlug = $sPreviewPostTypeSlug ? $sPreviewPostTypeSlug : $_oOption->get( 'unit_preview', 'preview_post_type_slug' );
        $this->bIsPreviewVisible    = $_oOption->isPreviewVisible();
        
        if ( ! $this->sPreviewPostTypeSlug ) {
            return;
        }
        
        // Hooks
        /// Post Type
        add_action( 'init', array( $this, 'replyToRegisterCustomPreviewPostType' ) );
        add_filter( 'register_post_type_args', array( $this, 'replyToGetDefaultUnitPostTypeArguments' ), 10, 2 );

        /// Modify the links
        add_filter( 'post_row_actions', array( $this, 'replyToAddViewActionLink' ), 10, 2 );
        add_filter( 'post_type_link', array( $this, 'replyToGetModifiedPermalinks' ), 10, 3 );
        add_filter( 'post_link', array( $this, 'replyToGetModifiedPermalinks' ), 10, 3 );
        add_filter( "previous_post_link", array( $this, 'replyToModifyPostLink' ), 10, 4 );
        add_filter( "next_post_link", array( $this, 'replyToModifyPostLink' ), 10, 4 );    

        /// Modify database queries
        add_filter( 'request', array( $this, 'replyToModifyDatabaseQuery' ) );

    }
        /**
         * @param  AmazonAutoLinks_Option $oOption
         * @since  2.2.0
         * @since  5.0.0   Moved from `AmazonAutoLinks_Option`. Changed the visibility scope to private from public. Added the `$oOption` parameter.
         * @return boolean
         */
        private function ___isCustomPreviewPostTypeSet( $oOption )  {
            $_sPreviewPostTypeSlug = $oOption->get( 'unit_preview', 'preview_post_type_slug' );
            if ( ! $_sPreviewPostTypeSlug ) {
                return false;
            }
            return AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] !== $_sPreviewPostTypeSlug;
        }

    /**
     * @param  array  $aArguments
     * @param  string $sPostTypeSlug
     * @return array
     * @since  5.0.0
     */
    public function replyToGetDefaultUnitPostTypeArguments( $aArguments, $sPostTypeSlug ) {
        if ( $sPostTypeSlug !== $this->sDefaultPreviewSlug ) {
            return $aArguments;
        }
        $aArguments[ 'public' ] = false;
        return $aArguments;
    }

    /**
     * Modifies the action link of the post listing table.
     * 
     * @callback add_filter() post_row_actions
     * @return   array        The action link definition array.
     */
    public function replyToAddViewActionLink( $aActions, $oPost ) {

        if ( $this->sDefaultPreviewSlug !== $oPost->post_type ) {
            return $aActions;
        }    
        
        $_sLink = $this->___getDefaultPostTypeSlugReplaced( get_permalink( $oPost->ID ) );
        $aActions[ 'view' ] = '<a target="_blank" href="'
            . $_sLink . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $oPost->post_title ) ) . '" rel="permalink">' 
                . __( 'View' )
            . '</a>';
        return $aActions;
        
    }
      
    /**
     * @param    string       $sOutput    The adjacent post link.
     * @param    string       $format     Link anchor format.
     * @param    string       $link       Link permalink format.
     * @param    WP_Post      $oPost      The adjacent post.
     * @callback add_filter() previous_post_link
     * @callback add_filter() next_post_link
     */
    public function replyToModifyPostLink( $sOutput, $format, $link, $oPost ) {
        if ( ! isset( $oPost->post_type ) ) {
            return $sOutput;
        }
        if ( $this->sDefaultPreviewSlug !== $oPost->post_type ) {
            return $sOutput;
        }
        return $this->___getDefaultPostTypeSlugReplaced( $sOutput );
    }

    /**
     * Creates a custom post type for preview pages.
     * 
     * If this is not created, the default plugin unit post type will be used.
     * And if this is created, the default one will be publicly disabled except some admin ui functionality.
     * 
     * @callback add_action() init
     * @since    2.2.0
     */
    public function replyToRegisterCustomPreviewPostType() {

        if ( ! $this->sPreviewPostTypeSlug ) {
            return;
        }
        register_post_type(
            $this->sPreviewPostTypeSlug,
            array(            // argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels'             => AmazonAutoLinks_Unit_Utility::getUnitPostTypeLabels(),
                'public'             => true,
                'show_ui'            => false,
                'publicly_queryable' => $this->bIsPreviewVisible,
                'show_in_nav_menus'  => false,  // even though this is set to `true`, in the Menus UI screen, the posts do not appear for some reasons.
            )        
        );
            
    }    
        /**
         * 
         * @since    2.2.0
         * @callback add_filter() post_link
         * @param    string       $sPermalink     The post's permalink.
         * @param    WP_Post      $oPost          The post in question.
         * @param    boolean      $bLeaveName     Whether to keep the post name.
         * @return   string
         */
        public function replyToGetModifiedPermalinks( $sPermalink, $oPost, $bLeaveName ) {
            return $this->sDefaultPreviewSlug !== $oPost->post_type
                ? $sPermalink
                : $this->___getDefaultPostTypeSlugReplaced( $sPermalink );
        }    
           
        /**
         * @return string
         * @since  2.2.0
         * @since  5.0.0  Renamed from `_replaceWithUserSetPostTypeSlug()`.
         */
        private function ___getDefaultPostTypeSlugReplaced( $sSubject ) {
            return str_replace(
                array( 
                    '/' . $this->sDefaultPreviewSlug . '/',
                    'post_type=' . $this->sDefaultPreviewSlug,
                    $this->sDefaultPreviewSlug . '='
                ), // search
                array(
                    '/' . $this->sPreviewPostTypeSlug . '/',
                    'post_type=' . $this->sPreviewPostTypeSlug,
                    $this->sPreviewPostTypeSlug . '='
                ), // replace
                $sSubject     // subject
            );            
        }
         
    
    /**
     * 
     * @since    2.2.0
     * @callback add_filter() request
     * @return   array        The database query request array.
     */
    public function replyToModifyDatabaseQuery( $aRequest ) {

        if ( ! isset( $aRequest[ 'post_type' ], $aRequest[ 'name' ] ) ) {
            return $aRequest;
        }
        if ( $this->sPreviewPostTypeSlug !== $aRequest[ 'post_type' ] ) {
            return $aRequest;
        }
        if ( ! $this->bIsPreviewVisible ) {
            return $aRequest;
        }
        
        $aRequest[ 'post_type' ] = $this->sDefaultPreviewSlug;
        $aRequest[ $this->sDefaultPreviewSlug ] = $aRequest[ 'name' ];
        return $aRequest;
    
    }

}