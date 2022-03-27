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
 * Creates 'button' custom post type.
 *
 * @since 3
 */
class AmazonAutoLinks_PostType_Button extends AmazonAutoLinks_PostType_Button_ListTable {

    public function start() {
        // add_filter( 'upload_mimes', array( $this, 'replyToGetAllowedFileTypesToUpload' ) );
    }

    public function setUp() {
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        $this->setArguments(
            array(            // @see http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels'                => array(
                    'name'                  => __( 'Buttons', 'amazon-auto-links' ),
                    'singular_name'         => __( 'Button', 'amazon-auto-links' ),
                    'menu_name'             => __( 'Manage Buttons', 'amazon-auto-links' ),    // this changes the root menu name 
                    'add_new'               => __( 'Add New Button', 'amazon-auto-links' ),
                    'add_new_item'          => __( 'Add New Button', 'amazon-auto-links' ),
                    'edit'                  => __( 'Edit', 'amazon-auto-links' ),
                    'edit_item'             => __( 'Edit Button', 'amazon-auto-links' ),
                    'new_item'              => __( 'New Button', 'amazon-auto-links' ),
                    'view'                  => __( 'View', 'amazon-auto-links' ),
                    'view_item'             => __( 'View Button', 'amazon-auto-links' ),
                    'search_items'          => __( 'Search Buttons', 'amazon-auto-links' ),
                    'not_found'             => __( 'No definitions found for Button', 'amazon-auto-links' ),
                    'not_found_in_trash'    => __( 'No definitions Found for Button in Trash', 'amazon-auto-links' ),
                    'parent'                => __( 'Parent Button', 'amazon-auto-links' ),
                    
                    // framework specific keys
                    'plugin_action_link' => __( 'Buttons', 'amazon-auto-links' ),
                ),
                
                'menu_position'         => 120,
                'supports'              => array( 
                    'title', 
                ),    // e.g. array( 'title', 'editor', 'comments', 'thumbnail' ),    // 'custom-fields'
                'taxonomies'            => array( '' ),
                'menu_icon'             => $this->oProp->bIsAdmin
                    ? AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/icon/menu_icon_16x16.png', true )
                    : null,
                'has_archive'           => false,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'exclude_from_search'   => true,   // Whether to exclude posts with this post type from front end search results.
                'publicly_queryable'    => false,  // Whether queries can be performed on the front end as part of parse_request(). 
                'show_in_nav_menus'     => false,
                'show_ui'               => true,
                'public'                => false,
                'show_in_menu'          => 'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'can_export'            => $_oOption->canExport(),
                
                'submenu_order_manage'  => 8,
            )        
        );

        if (  $this->_isInThePage() ) {
            
            $this->setAutoSave( false );
            $this->setAuthorTableFilter( false );            
            add_filter( 'months_dropdown_results', '__return_empty_array' );
            
            // add_filter( 'enter_title_here', array( $this, 'replyToModifyTitleMetaBoxFieldLabel' ) );
            // add_action( 'edit_form_after_title', array( $this, 'replyToAddTextAfterTitle' ) );    
            add_filter( 'post_updated_messages', array( $this, 'replyToModifyPostUpdatedMessages' ) );        

            // Unit listing table columns
            add_filter( 'columns_' . AmazonAutoLinks_Registry::$aPostTypes[ 'button' ], array( $this, 'replyToModifyColumnHeader' ) );

            new AmazonAutoLinks_PostType__Button___ActionLink_Status( $this );  // [4.3.0]
                     
        }

        if ( is_admin() ) {
            add_action( 'save_post', array( $this, 'replyToUpdateButtonCSSOnSavingPost' ) );
            add_action( 'transition_post_status', array( $this, 'replyToUpdateButtonCSSOnPostStatusChange' ), 10, 3 );
            add_filter( 'admin_url', array( $this, 'replyToGetCustomAddNewLink' ), 10, 2 );
            add_action( 'admin_enqueue_scripts', array( $this, 'replyToLoadResources' ) );
        }

        add_action( 'wp_before_admin_bar_render', array( $this, 'replyToModifyAdminBar' ) );    // [4.1.0]

    }

    /**
     * @since  5.2.0
     * @param  array $aMimeTypes
     * @return array
     * @deprecated 5.2.0 Doesn't work as WordPress disallows SVG files to be uploaded using the media uploader.
     */
    public function replyToGetAllowedFileTypesToUpload( $aMimeTypes ) {
      $aMimeTypes[ 'svg' ]  = 'image/svg+xml';     // Adding .svg extension
      $aMimeTypes[ 'svgz' ] = 'application/x-gzip';
      return $aMimeTypes;
    }

    /**
     * @since    5.2.0
     * @callback admin_enqueue_scripts
     */
    public function replyToLoadResources() {
        $this->enqueueStyles(
            array(
                AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
                AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/css/edit.aal-button.css',
            )
        );
        $_sURLAddClassicButton = add_query_arg(
            array(
                'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
            ),
            admin_url( 'post-new.php' )
        );
        $this->enqueueScript(
            AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/js/button-add-new-buttons.js',
            array(
                'handle_id'    => 'aalButtonAddNew',
                'dependencies' => array( 'jquery' ),
                'translation'  => array(
                    'labels' => array(
                        'generateDefaults' => __( 'Generate Default Buttons', 'amazon-auto-links' ),
                        'addImage'         => __( 'Add Image Button', 'amazon-auto-links' ),
                        'addClassic'       => __( 'Add Classic Button', 'amazon-auto-links' ),
                    ),
                    'URLs'   => array(
                        'generateDefaults' => add_query_arg(
                            array(
                                'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                                'aal_action'  => 'generate_default_buttons',
                            ),
                            admin_url( $GLOBALS[ 'pagenow' ] )
                        ),
                        'addImage' => add_query_arg(
                            array(
                                'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                                'button_type' => 'image'
                            ),
                            $_sURLAddClassicButton
                        ),
                        'addClassic' => $_sURLAddClassicButton,
                    ),
                ),
                'in_footer'    => true,
            )
        );

        // [5.2.0] Only in the Add New or Edit screen, (not the button listing screen)
        if ( in_array( $GLOBALS[ 'pagenow' ], array( 'post.php', 'post-new.php' ), true ) ) {
            $this->enqueueScript(
                AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/js/button-preview-meta-box.js',
                array(
                    'dependencies' => array( 'jquery' ),
                    'in_footer'    => true,
                )
            );
        }

        // For the button listing table screen,
        if ( 'edit.php' === $GLOBALS[ 'pagenow' ] ) {
            // [5.2.0]
            $this->enqueueScript(
                apply_filters( 'aal_filter_admin_button_js_preview_src', '' ),
                apply_filters( 'aal_filter_admin_button_js_preview_enqueue_arguments', array() )
            );
            $this->enqueueScript(
                AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/js/button-preview-frame-delay-loader.js',
                array(
                    'dependencies' => array( 'jquery' ),
                    'in_footer'    => true,
                )
            );
        }

    }

    /**
     * @since    5.2.0
     * @callback add_filter()   admin_url
     * @param    string         $sURL
     * @param    string         $sPath
     * @return   string         Adds the `button_type` query parameter for new button types.
     */
    public function replyToGetCustomAddNewLink( $sURL, $sPath ){
        if( $sPath === 'post-new.php?post_type=' . $this->oProp->sPostType ) {
            $sURL = add_query_arg( array(
                'button_type' => 'button2',
            ), $sURL );
        }
        return $sURL;
    }

    /**
     * @since 4.1.0
     */
    public function replyToModifyAdminBar() {
        $this->___removeNewLinkInAdminBar( $GLOBALS[ 'wp_admin_bar' ] );
    }
        /**
         * @param WP_Admin_Bar $oWPAdminBar
         */
        private function ___removeNewLinkInAdminBar( WP_Admin_Bar $oWPAdminBar ) {
            $oWPAdminBar->remove_node( 'new-' . $this->oProp->sPostType );
        }

        /**
         * @callback add_action() transition_post_status
         */    
        public function replyToUpdateButtonCSSOnPostStatusChange( $sNewStatus, $sOldStatus, $oPost ) {

            if ( $this->oProp->sPostType !== $oPost->post_type ) {
                return;
            }
            if ( $sNewStatus === $sOldStatus ) {
                return;
            }
            // This can be called when the user performs bulk actions.
            // In that case, it is called many times in a single page load.
            // To avoid updating the option over and over again in a single page load,
            // do it only once at shutdown.
            add_action( 'shutdown', array( $this, 'replyToUpdateButtonCSSOnShutdown' ) );

        }
        /**
         * @callback add_action() save_post
         */
        public function replyToUpdateButtonCSSOnSavingPost( $iPostID ) {

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }
            // This can be called when the user performs bulk actions.
            // In that case, it is called many times in a single page load.
            // To avoid updating the option over and over again in a single page load,
            // do it only once at shutdown.
            add_action( 'shutdown', array( $this, 'replyToUpdateButtonCSSOnShutdown' ) );

        }
        /**
         * Updates the active button CSS rules.
         * @callback add_action() shutdown
         */
        public function replyToUpdateButtonCSSOnShutdown() {
            update_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ], AmazonAutoLinks_Button_Utility::getCSSRulesOfActiveButtons() );
        }

    /**
     * @callback add_filter() post_updated_messages
     * @return   array
     */ 
    public function replyToModifyPostUpdatedMessages( $aMessages ) {
        
        global $post, $post_ID;
    
        $_sPostTypeSlug = get_post_type( $post_ID );
        if ( ! in_array( $_sPostTypeSlug, array( $this->oProp->sPostType ) ) ) {
            return $aMessages;
        }
        // Disable messages.
        return array();

    }
        
    /**
     * Style for this custom post type pages
     * @callback add_filter() style_{class name}
     */
    public function style_AmazonAutoLinks_PostType_Button() {
        $_sCSS = <<<CSS
td .shortcode {
    font-size: 1em;
}
td .description {
    font-style: italic;
    font-size: smaller;
}
CSS;
        return $this->oUtil->isDebugMode()
            ? trim( $_sCSS )
            : $this->oUtil->getCSSMinified( $_sCSS );

    }
}