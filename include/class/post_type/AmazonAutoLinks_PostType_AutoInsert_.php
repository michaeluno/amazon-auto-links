<?php
/**
 * Creates Auto-insert Amazon Auto Links custom post type.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 * 
 */
abstract class AmazonAutoLinks_PostType_AutoInsert_ extends AmazonAutoLinks_AdminPageFramework_PostType {

    public function start_AmazonAutoLinks_PostType_AutoInsert() {

        $this->setPostTypeArgs(
            array(            // argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels'                => array(
                    'name'                  => __( 'Auto Insert', 'amazon-auto-links' ),
                    'singular_name'         => __( 'Auto Insert', 'amazon-auto-links' ),
                    'menu_name'             => __( 'Manage Auto Insert', 'amazon-auto-links' ),    // this changes the root menu name 
                    'add_new'               => __( 'Add New Auto Insert', 'amazon-auto-links' ),
                    'add_new_item'          => __( 'Add New Auto Insert', 'amazon-auto-links' ),
                    'edit'                  => __( 'Edit', 'amazon-auto-links' ),
                    'edit_item'             => __( 'Edit Auto Insert', 'amazon-auto-links' ),
                    'new_item'              => __( 'New Auto Insert', 'amazon-auto-links' ),
                    'view'                  => __( 'View', 'amazon-auto-links' ),
                    'view_item'             => __( 'View Auto Insert', 'amazon-auto-links' ),
                    'search_items'          => __( 'Search Auto Insert Definitions', 'amazon-auto-links' ),
                    'not_found'             => __( 'No definitions found for Auto Insert', 'amazon-auto-links' ),
                    'not_found_in_trash'    => __( 'No definitions Found for Auto Insert in Trash', 'amazon-auto-links' ),
                    'parent'                => __( 'Parent Auto Insert', 'amazon-auto-links' ),
                ),
                'public'                => false,
                'menu_position'         => 120,
                'supports'              => array( 'title' ),    // 'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),    // 'custom-fields'
                'taxonomies'            => array( '' ),
                'menu_icon'             => AmazonAutoLinks_Commons::getPluginURL( 'asset/image/menu_icon_16x16.png' ),
                'has_archive'           => false,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'exclude_from_search'   => true,    // Whether to exclude posts with this post type from front end search results.
                'publicly_queryable'    => false,    // Whether queries can be performed on the front end as part of parse_request(). 
                'show_ui'               => false,
                'show_in_nav_menus'     => false,
                'show_in_menu'          => false,
            )        
        );
            
        // Check custom actions
        if ( is_admin() && $GLOBALS['pagenow'] == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == AmazonAutoLinks_Commons::PostTypeSlugAutoInsert ) {

            // add_filter( 'post_row_actions', array( $this, 'modifyRowActions' ), 10, 2 );
            add_filter( 'bulk_actions-edit-' . $this->oProps->strPostType, array( $this, 'modifyBulkActionsDropDownList' ) );
        
        
            // $this->setAutoSave( false );
            $this->setAuthorTableFilter( false );
        
            $this->strCustomNonce = uniqid();
            AmazonAutoLinks_WPUtilities::setTransient( 'AAL_Nonce_' . $this->strCustomNonce, $this->strCustomNonce, 60*10 );
            
            $this->handleCustomActions();
                                  
        }
        
        // if not in admin or the post type slug is not set, the oLink object won't be set.
        if ( isset( $this->oLink ) ) {       
            $this->oLink->strSettingPageLinkTitle = __( 'Auto-Insert', 'amazon-auto-links' );
        }   
        
        // If a unit is deleted, check auto-insert items if there are empty items. If so, delete them as well.
        if ( is_admin() ) {
            add_action( 'before_delete_post', array( $this, '_replyToCheckEmptyAutoInsert' ) );
        }
        
    }        
        protected function handleCustomActions() {
            
            if ( ! isset( $_GET['custom_action'], $_GET['nonce'], $_GET['post'] ) ) { return; }
            
            $strNonce = AmazonAutoLinks_WPUtilities::getTransient( 'AAL_Nonce_' . $_GET['nonce'] );
            if ( $strNonce === false ) { 
                add_action( 'admin_notices', array( $this, 'notifyNonceFailed' ) );
                return;
            }
            AmazonAutoLinks_WPUtilities::deleteTransient( 'AAL_Nonce_' . $_GET['nonce'] );
            
            // Currently only the status toggle is supported.
            If ( $_GET['custom_action'] == 'toggle_status' && $_GET['post'] ) {
                
                $arrUnitIDs = get_post_meta( $_GET['post'], 'unit_ids', true );    
                if ( empty( $arrUnitIDs ) ) { return; }   // if this field is empty, the post must be the wrong post type.
                
                $fIsEnabled = get_post_meta( $_GET['post'], 'status', true );    
                update_post_meta( $_GET['post'], 'status', ! $fIsEnabled );
                
            }
        
        }
            public function notifyNonceFailed() {
                echo '<div class="error">'
                        . '<p>The action could not be processed due to the inactivity.</p>'
                    . '</div>';
            }
    
    /**
     * Indicates whether the callback is added at the shutdown event to delete empty auto-insert items.
     * @since   2.1.1
     */
    static private $_bCallbackAdded_DeleteEmptyAutoInsert = false;
    
    /**
     * Checks if thre are empty auto insert itemn when a unit is deleted.
     * @since   2.1.1
     */
    public function _replyToCheckEmptyAutoInsert( $iPostID ) {

        if ( self::$_bCallbackAdded_DeleteEmptyAutoInsert ) {
            return;
        }
        $_oPost = get_post( $iPostID );
        if ( AmazonAutoLinks_Commons:: PostTypeSlug === $_oPost->post_type ) {
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
                    'post_type'      => AmazonAutoLinks_Commons::PostTypeSlugAutoInsert, 
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
             * If there is at leaset one exists, return s true. Otherwise, returns false.
             * @sicne       2.1.1
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
        
    public function modifyBulkActionsDropDownList( $aBulkActions ) {
        unset( $aBulkActions['edit'] );
        return $aBulkActions;
    }

    public function modifyRowActions( $aActions, WP_Post $oPost ) {
        
        if ( $oPost->post_type != $this->oProps->strPostType ) {
            return $aActions;
        }
        
        unset( $aActions['inline'] );
        unset( $aActions['inline hide-if-no-js'] );
        // http://.../wp-admin/edit.php?post_type=amazon_auto_links&page=aal_define_auto_insert
        $_sSettingPageURL = add_query_arg( 
            array( 
                'post_type' => AmazonAutoLinks_Commons::PostTypeSlug,    // the main post type slug, not auto-insert's.
                'page'      => 'aal_define_auto_insert',
                'mode'      => 'edit',
                'post'      => $oPost->ID,
            ), 
            admin_url( 'edit.php' ) 
        );    
        $aActions['edit'] = "<a href='{$_sSettingPageURL}'>" 
                . __( 'Edit', 'amazon-auto-links' ) 
            . "</a>";
        
        return $aActions;
    }

    
    
    /*
     * Extensible methods
     */
    public function setColumnHeader( $aColumnHeader ) {
        // Set the table header.
        return array(
            'cb'                => '<input type="checkbox" />',    // Checkbox for bulk actions. 
            // 'title'          => __( 'Auto Insert Name', 'amazon-auto-links' ),        // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
            'auto_inserts'      => __( 'Auto Insert Definitions', 'amazon-auto-links' ),
            'status'            => __( 'Status', 'amazon-auto-links' ),
            'area'              => __( 'Areas', 'amazon-auto-links' ),        // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
            // 'unit_type'            => __( 'Unit Type', 'amazon-auto-links' ),
            // 'author'            => __( 'Author', 'amazon-auto-links' ),        // Post author.
            // 'amazon_auto_links_tag'    => __( 'Labels', 'amazon-auto-links' ),    // Tags for the post. 
            // 'code'                => __( 'Shortcode / PHP Code', 'amazon-auto-links' ),
            // 'date'            => __( 'Date', 'amazon-auto-links' ),     // The date and publish status of the post. 
        );        
        // return array_merge( $aColumnHeader, $this->arrColumnHeaders );
    }
    public function setSortableColumns( $aColumns ) {
        return array_merge( $aColumns, $this->oProps->arrColumnSortable );        
    }    
        
    /*
     *     Modify cells
     */
    public function cell_aal_auto_insert_auto_inserts( $strCell, $intPostID ) {
        
        $arrUnitIDs = ( array ) get_post_meta( $intPostID, 'unit_ids', true );
        $arrTitles  = array();
        
        foreach( $arrUnitIDs as $intUnitID ) {
            
            if ( empty( $intUnitID ) ) { continue; }       // casted array with an empty value causes an index key of zero.
            
            $strTitle = get_the_title( $intUnitID );
            if ( empty( $strTitle ) ) { continue; }
            
            $arrTitles[] = "<strong>" . $strTitle . "</strong>";
            if ( count( $arrTitles ) >= 3 ) {
                $arrTitles[] = __( 'etc.', 'amazon-auto-links' );
                break;
            }
            
        }
        $arrTitles = array_filter( $arrTitles );    // drop empty values.
        if ( empty( $arrTitles ) ) {
            $arrTitles[] = __( '(No unit is selected)', 'amazon-auto-links' );    // this happens if an associated unit is deleted.        
        }

        $strSettingPageURL = add_query_arg( 
            array( 
                'post_type' => AmazonAutoLinks_Commons::PostTypeSlug,    // the main post type slug, not auto-insert's.
                'page'      => 'aal_define_auto_insert',
                'mode'      => 'edit',
                'post'      => $intPostID,
            ), 
            admin_url( 'edit.php' ) 
        );    
        $strActions = "<div class='row-actions'>"
                . "<span class='edit'>"
                    . "<a href='{$strSettingPageURL}' title='" . esc_attr( __( 'Edit this item', 'amazon-auto-links' ) ) . "'>" . __( 'Edit', 'amazon-auto-links' ) . "</a>"
                . "</span>"
            . "</div>";
        return implode( ', ', $arrTitles ) 
            . $strActions;
        
    }
    public function cell_aal_auto_insert_status( $strCell, $intPostID ) {
        
        $strToggleStatusURL = add_query_arg( 
            array( 
                'post_type'     => AmazonAutoLinks_Commons::PostTypeSlugAutoInsert,    
                'custom_action' => 'toggle_status',
                'post'          => $intPostID,
                'nonce'         => $this->strCustomNonce,
            ), 
            admin_url( 'edit.php' ) 
        );    
        
        $fIsEnabled         = get_post_meta( $intPostID, 'status', true );
        $strStatus          = $fIsEnabled ? "<strong>" . __( 'On', 'amazon-auto-links' ) . "</strong>" : __( 'Off', 'amazon-auto-links' );
        $strOppositeStatus  = $fIsEnabled ? __( 'Off', 'amazon-auto-links' ) : __( 'On', 'amazon-auto-links' );
        $strActions         = "<div class='row-actions'>"
                . "<span class='toggle-status'>"
                    . "<a href='{$strToggleStatusURL}' title='" . __( 'Toggle the status', 'amazon-auto-links' ) . "'>" . sprintf( __( 'Set it %1$s', 'amazon-auto-links' ), $strOppositeStatus ) . "</a>"
                . "</span>"
            . "</div>";
        return $strStatus . $strActions;
        
    }    
    public function cell_aal_auto_insert_area( $strCell, $intPostID ) {
        
        $arrList = array();
        $arrSelectedAreas = ( ( array ) get_post_meta( $intPostID, 'built_in_areas', true ) )
            + ( ( array ) get_post_meta( $intPostID, 'static_areas', true ) );
        $arrSelectedAreas = array_filter( $arrSelectedAreas );

        $arrAreasLabel = AmazonAutoLinks_Form_AutoInsert::getPredefinedFilters() + AmazonAutoLinks_Form_AutoInsert::getPredefinedFiltersForStatic( false );
        foreach( $arrSelectedAreas as $strArea => $fEnable ) {            
            if ( isset( $arrAreasLabel[ $strArea ] ) ) {
                $arrList[] = $arrAreasLabel[ $strArea ];
            }
        }
        $arrFilters = AmazonAutoLinks_Utilities::convertStringToArray( get_post_meta( $intPostID, 'filter_hooks', true ), ',' );
        $arrActions = AmazonAutoLinks_Utilities::convertStringToArray( get_post_meta( $intPostID, 'action_hooks', true ), ',' );
        $arrList    = array_merge( $arrFilters, $arrActions, $arrList );
        
        return '<p>' . implode( ', ', $arrList ) . '</p>';
        
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
            #icon-edit.icon32.icon32-posts-" . AmazonAutoLinks_Commons::PostTypeSlugAutoInsert . " {
                background:url('" . AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ) . "') no-repeat;
            }            
        ";
    }
}