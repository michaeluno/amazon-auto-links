<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Creates Auto Amazon Links custom post type.
 *
 * @since 2.0.0
 */
class AmazonAutoLinks_PostType_Unit extends AmazonAutoLinks_PostType_Unit_PostContent {

    /**
     * Used in the Manage Units page for action links and ajax calls.
     * @var   string
     * @since 3.7.6
     */
    protected $_sNonceKey = 'aal_unit_listing_table';
    /**
     * @var   string
     * @since 3.7.6
     */
    protected $_sNonce    = '';

    public function setUp() {
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        $this->setArguments(
            array(            // argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels'                => AmazonAutoLinks_Unit_Utility::getUnitPostTypeLabels(),
                
                // If a custom preview post type is set, make it not public. 
                // However, other ui arguments should be enabled.
                'public'                => true,
                'publicly_queryable'    => $_oOption->isPreviewVisible(),
                'has_archive'           => true,
                'show_ui'               => true,
                'show_in_nav_menus'     => true,
                'show_in_menu'          => true,
                'show_in_rest'          => true,    // 5.1.0 to show title list in the Gutenberg editor
                'menu_position'         => 110,
                'supports'              => array( 'title' ),
                'taxonomies'            => array( '' ),
                'menu_icon'             => $this->oProp->bIsAdmin
                    ? AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/icon/menu_icon_16x16.png', true )
                    : null,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'can_export'            => $_oOption->canExport(),
                'exclude_from_search'   => ! $_oOption->get( 'unit_preview', 'searchable' ),
                
                'show_submenu_add_new'  => false,   // an admin page will be placed instead
                'submenu_order_manage'  => 5,
            )        
        );
        
        $this->addTaxonomy( 
            AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ], 
            array(
                'labels'                => array(
                    'name'          => __( 'Label', 'amazon-auto-links' ),
                    'add_new_item'  => __( 'Add New Label', 'amazon-auto-links' ),
                    'new_item_name' => __( 'New Label', 'amazon-auto-links' ),
                ),
                'show_ui'               => true,
                'show_tagcloud'         => false,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'show_in_nav_menus'     => false,
                'show_table_filter'     => true,  // framework specific key
                'show_in_sidebar_menus' => true,  // framework specific key
                'submenu_order'         => 40,  // the Setting page is 50
            )
        );

        if (  $this->_isInThePage() ) {
            $this->_sNonce = wp_create_nonce( $this->_sNonceKey );
            add_filter( 'get_user_option_meta-box-order_' . $this->oProp->sPostType, array( $this, 'replyToSetMetaBoxOrder' ) );    // 4.7.0+

        }

        // 4.1.0+
        add_action( 'wp_before_admin_bar_render', array( $this, 'replyToModifyAdminBar' ) );

        parent::setUp();
           
    }

        /**
         * @param  array $aOrder
         * @return array
         * @since  4.7.0
         */
        public function replyToSetMetaBoxOrder( $aOrder ) {
            if ( ! empty( $aOrder ) ) {
                return $aOrder;
            }
            // Set the default order
            $aOrder = $this->oUtil->getAsArray( $aOrder );
            $aOrder[ 'side' ] = 'amazonautolinks_unitpostmetabox_viewlink,amazonautolinks_unitpostmetabox_submit_category,submitdiv';
            return $aOrder;
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
     * Called when the edit.php of the post type starts loading.
     * @since 3.3.5
     */
    public function load() {

        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }

        $this->setAutoSave( false );
        $this->setAuthorTableFilter( false );            
        add_filter( 'months_dropdown_results', '__return_empty_array' );
        
        add_filter( 'enter_title_here', array( $this, 'replyToModifyTitleMetaBoxFieldLabel' ) );
        add_action( 'edit_form_after_title', array( $this, 'replyToAddTextAfterTitle' ) );

        $_bDebugMode = $this->oUtil->isDebugMode();
        $this->enqueueStyles(
            array(
                $_bDebugMode
                    ? AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css'
                    : AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.min.css',
                $_bDebugMode
                    ? AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/css/aal-unit-post-type.css'
                    : AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/css/aal-unit-post-type.min.css',
            )
        );

        // For the post listing table
        $_sScreenID = get_current_screen()->id;
        if ( "edit-{$this->oProp->sPostType}" !== $_sScreenID ) {
            return;
        }
        add_thickbox();
        wp_enqueue_script( 'jquery' );

        // for warnings
        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'wp-pointer' );

        $this->enqueueScripts(
            array(
                $_bDebugMode
                    ? AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/js/manage-units.js'
                    : AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/js/manage-units.min.js',
            ),
            array(
                'handle_id'     => 'aalManageUnits',
                'in_footer'     => true,
                'translation'   => array(
                    'labels' => array(
                        'copied'    => __( 'Copied the text.', 'amazon-auto-links' ),
                    ),
                    'debugMode' => $_bDebugMode,
                ),
            )
        );
        $this->enqueueScripts(
            array(
                $_bDebugMode
                    ? AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/utility.js'
                    : AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/utility.min.js',
            ),
            array(
                'handle_id'     => 'aalUtility',
                'dependencies'  => array( 'jquery', ),
                'in_footer'     => true,
            )
        );
        $this->enqueueScripts(
            array(
                $_bDebugMode
                    ? AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/js/manage-units-unit-status-updater.js'
                    : AmazonAutoLinks_Unit_Loader::$sDirPath . '/asset/js/manage-units-unit-status-updater.min.js',
            ),
            array(
                'handle_id'     => 'aalUnitStatusUpdater',
                'dependencies'  => array(
                    'jquery', 'aalUtility'
                ),
                'translation'   => array(
                    'ajaxURL' => admin_url( 'admin-ajax.php' ),
                ),
                'in_footer'     => true,
            )
        );

        // [3.7.6+] Set nonce
        add_action( 'admin_footer', array( $this, 'replyToEmbedNonce' ) );

    }
        /**
         * @since 3.7.6
         */
        public function replyToEmbedNonce() {
            echo "<input type='hidden' id='amazon-auto-links-nonce' value='" . esc_attr( $this->_sNonce ) . "' />";
        }


    /**
     * @callback add_filter() enter_title_here
     */
    public function replyToModifyTitleMetaBoxFieldLabel( $strText ) {
        return __( 'Set the unit name here.', 'amazon-auto-links' );        
    }
    /**
     * @callback add_action() edit_form_after_title
     */
    public function replyToAddTextAfterTitle() {
        //@todo insert plugin news text headline.
    }
        
    /**
     * Style for this custom post type pages
     * @callback add_filter() style_{class name}
     * @return   string
     */
    public function style_AmazonAutoLinks_PostType_Unit() {
        $_sSpinnerURL    = esc_url( admin_url( 'images/loading.gif' ) );
        $_sUnitPostType  = AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ];
        $_sScreenIconURL = esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ) );
        return <<<CSS
#icon-edit.icon32.icon32-posts-{$_sUnitPostType} {
    background:url('{$_sScreenIconURL}') no-repeat;
}            
.circle.loading {
    background-image: url({$_sSpinnerURL});
    background-repeat: no-repeat;            
    min-height: 16px;
    min-width: 16px;            
}
CSS;
    }

}