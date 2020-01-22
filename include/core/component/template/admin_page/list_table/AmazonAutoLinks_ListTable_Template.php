<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers
 *  
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2020, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
*/

if ( ! class_exists( 'WP_List_Table', false ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Handles the list table of Amazon Auto Links templates. 
 * @filter      aal_filter_template_listing_table_action_links
 * @since       2.0.0
 */
class AmazonAutoLinks_ListTable_Template extends WP_List_Table {
    
    /**
     * 
     * @since       2.0.0
     * @since       2.2.0       Declared for compatibility with WordPress 4.2.
     */
    public $aData       = array();
    public $aArguments  = array();

    /**
     * Sets up properties and hooks.
     */
    public function __construct( $aData ){
              
        $this->aData = $aData;
        
        // Set parent defaults
        $this->aArguments = array(
            'singular'  => 'template',     // singular name of the listed items
            'plural'    => 'templates',    // plural name of the listed items
            'ajax'      => false,          // does this table support ajax?
            'screen'    => null,           // not sure what this is for... 
        );
        if ( headers_sent() ) {
            parent::__construct( $this->aArguments );
        } else {
            add_action( 'admin_notices', array( $this, '_replyToDelayConstructor' ) );
        }
        
    }
        /**
         * @callback        action      admin_notices
         */
        public function _replyToDelayConstructor() {
            parent::__construct( $this->aArguments );
        }    
    
    /**
     * Defines columns.
     */
    public function get_columns() {
        return array(
            'cb'            => '<input type="checkbox" />', // Render a checkbox instead of text
            'name'          => __( 'Template Name', 'amazon-auto-links' ),
            'thumbnail'     => __( 'Thumbnail', 'amazon-auto-links' ),
            'description'   => __( 'Description', 'amazon-auto-links' ),
        );
    }
    
    /**
     * Defines sortable columns.
     */
    public function get_sortable_columns() {
        return array(
            'name'          => array( 'name', false ),     //true means it's already sorted
            // 'thumbnail'  => array( 'thumbnail', false ),
            // 'description'   => array( 'description', false ),
        );
    }    
    
    /**
     * Undefined column items will be passed.
     * 
     * Show the whole array contents for troubleshooting.
     * 
     * @callback        filter      'column_' + 'default'
     */
    public function column_default( $aItem, $sColumnName ) {    
        return '<pre>' 
                . print_r( $aItem, true ) 
            . '</pre>';  
    }
    
    /**
     * 
     * @callback        filter      column_ + cb
     */
    public function column_cb( $aItem ){    
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],  /*$1%s*/
            $aItem[ 'id' ]  /*$2%s*/ // The value of the checkbox should be the record's id
        );
    }    
    
    /**
     * 
     * @callback        filter      column_{$column_title}
     */ 
    public function column_name( $aItem ){    
        
        // Build row actions
        $aActions = array();
        if ( $aItem[ 'is_active' ] ) {
            $aActions[ 'deactivate' ] = sprintf( 
                '<a href="?post_type=%s&page=%s&action=%s&template=%s">' . __( 'Deactivate', 'amazon-auto-links' ) . '</a>', 
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ], 
                $_REQUEST[ 'page' ], 
                'deactivate', 
                $aItem[ 'id' ]
            );
        } else  {
            $aActions[ 'activate' ]   = sprintf( 
                '<a href="?post_type=%s&page=%s&action=%s&template=%s">' . __( 'Activate', 'amazon-auto-links' ) . '</a>', 
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ], 
                $_REQUEST[ 'page' ], 
                'activate', 
                $aItem[ 'id' ]
            );
        }
        $aActions = apply_filters( 
            'aal_filter_template_listing_table_action_links', 
            $aActions, 
            $aItem[ 'id' ]
        );    

        // Return the title contents
        return sprintf(
            '%1$s %2$s',    // <span style="color:silver">(id:%2$s)</span>
            $aItem[ 'is_active' ]   /*$1%s*/ 
                ? "<strong>{$aItem[ 'name' ]}</strong>" 
                : $aItem[ 'name' ],
            $this->row_actions( $aActions ) /*$2%s*/ 
        );
        
    }
        
    /**
     * 
     * @callback        filter      column_{$column_title}
     */         
    public function column_thumbnail( $aItem ) {
        
        $_sThumbnailPath = $this->_getThumbnailPath( $aItem );
        if ( ! file_exists( $_sThumbnailPath ) ) {
            return '';
        }
        $_sImageURL = esc_url(
            AmazonAutoLinks_WPUtility::getSRCFromPath( $_sThumbnailPath )
        );
        $_sID = esc_attr( md5( $aItem[ 'id' ] ) );
        return "<a href='{$_sImageURL}' data-lightbox='{$_sID}' data-title='" . esc_attr( $aItem[ 'name' ] ) . "'>"
                . "<img src='{$_sImageURL}' style='max-width:80px; max-height:80px;' />"
            . "</a>";
        return "<a class='template-thumbnail' href='#thumb'>"
                . "<img src='{$_sImageURL}' style='max-width:80px; max-height:80px;' />"
                . "<span>"
                    . "<div>"
                        . "<img src='{$_sImageURL}' /><br />"
                        . $aItem[ 'name' ]
                    . "</div>"
                . "</span>"
            . "</a>";       
    }
        /**
         * @since       3.0.3
         * @return      string
         */
        private function _getThumbnailPath( $aItem ) {
            
            if ( isset( $aItem[ 'thumbnail_path' ] ) ) {
                return $aItem[ 'thumbnail_path' ];
            }
            
            // backward compatibility for v2
            if ( isset( $aItem[ 'strThumbnailPath' ] ) ) {
                return $aItem[ 'strThumbnailPath' ];
            }
            
            return '';
            
        }
        
    /**
     * 
     * @callback        filter      column_ + description
     */
    public function column_description( $aItem ) {
        
        // Action Links
        $aActions = array(
            'version'   => sprintf( 
                __( 'Version', 'amazon-auto-links' ) . '&nbsp;' . $aItem[ 'version' ] 
            ),
            'author'    => sprintf( 
                '<a href="%s">' . $aItem[ 'author' ] . '</a>', 
                $aItem[ 'author_uri' ] 
            ),
            'css'       => sprintf( 
                '<a href="%s">' . __( 'CSS', 'amazon-auto-links' ) . '</a>', 
                esc_url( AmazonAutoLinks_WPUtility::getSRCFromPath( $aItem[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'style.css' ) )
            ),
        );
        return sprintf(
            '%1$s <div class="active second">%2$s</div>',
            $aItem[ 'description' ],    /*$1%s*/ 
            $this->row_actions( $aActions ) /*$2%s*/
        );

    }
    
    public function get_bulk_actions() {
        return array(
            // 'delete'    => 'Delete',
            'activate'    => __( 'Activate', 'amazon-auto-links' ),
            'deactivate'  => __( 'Deactivate', 'amazon-auto-links' ),
        );
    }
    
    /**
     * Processes bulk actions of the table form.
     * 
     * @remark      This method uses redirect so it must be called before the header gets sent.
     */
    public function process_bulk_action() {

        if ( ! isset( $_REQUEST[ 'template' ] ) ) {
            return;
        }
        
        switch( strtolower( $this->current_action() ) ){
            case 'activate':
                do_action( 'aal_action_activate_templates', ( array ) $_REQUEST[ 'template' ], true );
                break;
            case 'deactivate':
                do_action( 'aal_action_deactivate_templates', ( array ) $_REQUEST[ 'template' ], true );
                break;    
            default:
                return;    // do nothing.
        }

        // Reload the page.
        exit( 
            wp_safe_redirect( 
                add_query_arg( 
                    array(
                        'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                        'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'template' ],
                        'tab'       => 'table',
                    ), 
                    admin_url( $GLOBALS[ 'pagenow' ] ) 
                )
            )
        );
       
    }

    /**
     * Sets up items for table rows.
     */
    public function prepare_items() {
        
        /**
         * Set how many records per page to show
         */
        $_iItemsPerPage = 20;
    
        /**
         * Define our column headers. 
         */
        $this->_column_headers = array( 
            $this->get_columns(), // $aColumns
            array(), // $aHidden
            $this->get_sortable_columns() // $aSortable
        );
        
     
        /**
         * Process bulk actions.
         */
        // $this->process_bulk_action(); // in our case, it is dealt before the header is sent. ( with the Admin page class )
              
        /**
         * Variables
         */
        $_aData = $this->aData;
                
        
        /**
         * Sort the array.
         */
        usort( $_aData, array( $this, 'usort_reorder' ) );
           
                
        /**
         * For pagination.
         */
        $_iCurrentPageNumber = $this->get_pagenum();
        $_iTotalItems        = count( $_aData );
        $this->set_pagination_args( 
            array(
                'total_items' => $_iTotalItems, // calculate the total number of items
                'per_page'    => $_iItemsPerPage, // determine how many items to show on a page
                'total_pages' => ceil( $_iTotalItems / $_iItemsPerPage ) // calculate the total number of pages
            )
        );
        $_aData = array_slice( 
            $_aData, 
            ( ( $_iCurrentPageNumber -1 ) * $_iItemsPerPage ), 
            $_iItemsPerPage 
        );
        
        /*
         * Set data
         * */
        $this->items = $_aData;
        
    }
        public function usort_reorder( $a, $b ) {
            
            // If no sort, default to title
            $sOrderBy = ( ! empty( $_REQUEST['orderby'] ) ) 
                ? $_REQUEST['orderby'] 
                : 'name'; 
            if ( 'description' === $sOrderBy ) {
                $sOrderBy = 'description';
            } else if ( 'name' === $sOrderBy ) {
                $sOrderBy = 'name';
            }
                        
            // If no order, default to asc
            $sOrder = empty( $_REQUEST['order'] )
                ? 'asc'
                : $_REQUEST['order'];
                
            // Determine sort order
            $result = strcmp( $a[ $sOrderBy ], $b[ $sOrderBy ] ); 

            // Send final sort direction to usort
            return ( 'asc' === $sOrder ) 
                ? $result 
                : -$result; 
            
        }
    
}
