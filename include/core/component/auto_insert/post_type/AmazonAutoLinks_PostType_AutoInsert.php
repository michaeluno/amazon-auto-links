<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2020, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Creates Auto-insert Amazon Auto Links custom post type. 
 * @since       2.0.0
 */
class AmazonAutoLinks_PostType_AutoInsert extends AmazonAutoLinks_PostType_AutoInsert_Action {



    /**
     * Sets up properties and hooks.
     */
    public function setUp() {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        $this->setArguments(
            array(            // @see http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels'                => array(
                    'name'                  => __( 'Auto-insert', 'amazon-auto-links' ),
                    'singular_name'         => __( 'Auto-insert', 'amazon-auto-links' ),
                    'menu_name'             => __( 'Manage Auto-insert', 'amazon-auto-links' ),    // this changes the root menu name 
                    'add_new'               => __( 'Add New Auto-insert', 'amazon-auto-links' ),
                    'add_new_item'          => __( 'Add New Auto-insert', 'amazon-auto-links' ),
                    'edit'                  => __( 'Edit', 'amazon-auto-links' ),
                    'edit_item'             => __( 'Edit Auto-insert', 'amazon-auto-links' ),
                    'new_item'              => __( 'New Auto-insert', 'amazon-auto-links' ),
                    'view'                  => __( 'View', 'amazon-auto-links' ),
                    'view_item'             => __( 'View Auto-insert', 'amazon-auto-links' ),
                    'search_items'          => __( 'Search Auto-insert Definitions', 'amazon-auto-links' ),
                    'not_found'             => __( 'No definitions found for Auto-insert', 'amazon-auto-links' ),
                    'not_found_in_trash'    => __( 'No definitions Found for Auto-insert in Trash', 'amazon-auto-links' ),
                    'parent'                => __( 'Parent Auto-insert', 'amazon-auto-links' ),
                    
                    // framework specific keys
                    'plugin_listing_table_title_cell_link' => __( 'Auto-insert', 'amazon-auto-links' ),
                ),
                'public'                => false,
                'menu_position'         => 120,
                'supports'              => array( 'title' ),    // 'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),    // 'custom-fields'
                'taxonomies'            => array( '' ),
                'menu_icon'             => AmazonAutoLinks_Registry::getPluginURL( 'asset/image/menu_icon_16x16.png' ),
                'has_archive'           => false,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'exclude_from_search'   => true,   // Whether to exclude posts with this post type from front end search results.
                'publicly_queryable'    => false,  // Whether queries can be performed on the front end as part of parse_request(). 
                'show_in_nav_menus'     => false,
                'show_ui'               => true,
                'show_in_menu'          => 'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'can_export'            => $_oOption->canExport(),
                
                'submenu_order_manage'  => 7,
            )        
        );
            
        // Check custom actions
        if (  $this->_isInThePage() ) {
        
            $this->setAuthorTableFilter( false );
            add_filter( 'months_dropdown_results', '__return_empty_array' );
        
        }

        // If a unit is deleted, check auto-insert items if there are empty items. If so, delete them as well.
        if ( is_admin() ) {
            add_action( 
                'before_delete_post', 
                array( 
                    $this, 
                    'replyToCheckEmptyAutoInsert' 
                ) 
            );
        }
        
        parent::setUp();
        
    }        

    
    /**
     * Indicates whether the callback is added at the shutdown event to delete empty auto-insert items.
     * @since   2.1.1
     */
    static private $_bCallbackAdded_DeleteEmptyAutoInsert = false;
    
    /**
     * Checks if there are empty auto insert items when a unit is deleted.
     * @since           2.1.1
     * @callback        action      before_delete_post
     */
    public function replyToCheckEmptyAutoInsert( $iPostID ) {

        if ( self::$_bCallbackAdded_DeleteEmptyAutoInsert ) {
            return;
        }
        $_oPost = get_post( $iPostID );
        if ( AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] === $_oPost->post_type ) {
            add_action( 'shutdown', array( $this, '_replyToDeleteEmptyAutoInsert' ) );
            self::$_bCallbackAdded_DeleteEmptyAutoInsert = true;
        }    
        
    }
        /**
         * Deletes empty auto-insert items.
         * @since   2.1.1
         */
        public function _replyToDeleteEmptyAutoInsert() {
            $_oQuery = new WP_Query(
                array(
                    'post_status'    => array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ),
                    'post_type'      => AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ], 
                    'posts_per_page' => -1, // ALL posts
                    'fields'         => 'ids',  // return an array of post IDs
                )
            );       
            foreach( $_oQuery->posts as $_iAutoInsertPostID ) {
                $_aUnitIDs = get_post_meta( $_iAutoInsertPostID, 'unit_ids', true );
                if ( empty( $_aUnitIDs ) || ! is_array( $_aUnitIDs ) ) {
                    wp_delete_post( $_iAutoInsertPostID );
                    continue;
                }
                if ( ! $this->_doPostsExist( $_aUnitIDs ) ) {
                    wp_delete_post( $_iAutoInsertPostID );
                    continue;
                }

            }
            
        }
            /**
             * Checks if the posts of the given post IDs exits or not.
             * 
             * If there is at least one exists, return s true. Otherwise, returns false.
             * @since       2.1.1
             */
            private function _doPostsExist( $asPostIDs ) {
                $_aPostIDs = is_array( $asPostIDs ) ? $asPostIDs : array( $asPostIDs );
                foreach( $_aPostIDs as $_iID ) {                    
                    // If exists,
                    if ( is_string( get_post_status( $_iID ) ) ) {
                        return true;
                    }
                }
                return false;
            }
        



    

    // Style for this custom post type pages
    public function style_AmazonAutoLinks_PostType_AutoInsert() {
        $_sNone = 'none';
        return "#post-body-content {
                margin-bottom: 10px;
            }
            #edit-slug-box {
                display: {$_sNone};
            }
            #icon-edit.icon32.icon32-posts-" . AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ] . " {
                background:url('" . AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ) . "') no-repeat;
            } 
            /* Hide the submit button for the post type drop-down filter */
            #post-query-submit {
                display: {$_sNone};
            }
        ";
    }
}