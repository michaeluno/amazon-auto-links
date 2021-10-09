<?php
/**
 * Amazon Auto Links
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
                    'search_items'          => __( 'Search Button Definitions', 'amazon-auto-links' ),
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
            
            $this->enqueueStyles(
                array(
                    AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
                    AmazonAutoLinks_ButtonLoader::$sDirPath . '/asset/css/edit.aal-button.css',
                )
            );

            // unit listing table columns
            add_filter(    
                'columns_' . AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                array( $this, 'replyToModifyColumnHeader' )
            );

            // 4.3.0
            new AmazonAutoLinks_PostType__Button___ActionLink_Status( $this );
                     
        }
        
        if ( is_admin() ) {
            add_action( 
                'save_post', 
                array( $this, 'replyToUpdateButtonCSSOnSavingPost', ) 
            );
            add_action(
                'transition_post_status',
                array( $this, 'replyToUpdateButtonCSSOnPostStatusChange' ),
                10,  // priority
                3   // number of parameter
            );

        }

        // 4.1.0+
        add_action( 'wp_before_admin_bar_render', array( $this, 'replyToModifyAdminBar' ) );

    }

    /**
     * @since   4.1.0
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
         * @callback        action      transition_post_status
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
            add_action(
                'shutdown',
                array( $this, 'replyToUpdateButtonCSSOnShutdown' )
            );

        }
        /**
         * @callback        action      save_post
         */
        public function replyToUpdateButtonCSSOnSavingPost( $iPostID ) {
            
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }
            // This can be called when the user performs bulk actions.
            // In that case, it is called many times in a single page load.
            // To avoid updating the option over and over again in a single page load,
            // do it only once at shutdown.
            add_action(
                'shutdown',
                array( $this, 'replyToUpdateButtonCSSOnShutdown' )
            );            

        }
        /**
         * Updates the active button CSS rules.
         * @callback    action  shutdown
         */
        public function replyToUpdateButtonCSSOnShutdown() {
            update_option(
                AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ],
                AmazonAutoLinks_ButtonUtility::getCSSRulesOfActiveButtons()    // data
            );
        }

    /**
     * 
     * @callback    filter      post_updated_messages
     * @return      array
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
     * @callback        filter      style_{class name}
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