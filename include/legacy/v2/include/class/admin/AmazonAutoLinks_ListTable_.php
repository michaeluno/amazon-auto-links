<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers
 *  
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2015, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
*/

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Handles the list table of Amazon Auto Links templates. 
 * @filter      aal_filter_template_listing_table_action_links
 * @since       2.0.0
 */
class AmazonAutoLinks_ListTable_ extends WP_List_Table {
    
    /**
     * 
     * @since       2.0.0
     * @since       2.2.0       Declared for compatibility with WordPress 4.2.
     */
    public $arrData = array();
    public $arrArgs = array();
    
    public function __construct( $arrData ){
              
        $this->arrData = $arrData;
        
        // Set parent defaults
        $this->arrArgs = array(
            'singular'  => 'template',        // singular name of the listed items
            'plural'    => 'templates',        // plural name of the listed items
            'ajax'      => false,            // does this table support ajax?
            'screen'    => null,            // not sure what this is for... 
        );
        if ( headers_sent() ) {
            parent::__construct( $this->arrArgs );
        } else {
            add_action( 'admin_notices', array( $this, 'delayConstructor' ) );
        }
        
    }
    public function delayConstructor() {
        parent::__construct( $this->arrArgs );
    }    

    public function column_default( $arrItem, $strColumnName ) {    // 'column_' + 'default'
    
        switch( $strColumnName ){

            case 'description':
                //Build row actions
                $arrActions = array(
                    'version'    => sprintf( __( 'Version', 'amazon-auto-links' ) . '&nbsp;' . $arrItem['strVersion'] ),
                    'author'    => sprintf( '<a href="%s">' . $arrItem['strAuthor'] . '</a>', $arrItem['strAuthorURI'] ),
                    'css'        => sprintf( '<a href="%s">' . __( 'CSS', 'amazon-auto-links' ) . '</a>', AmazonAutoLinks_WPUtilities::getSRCFromPath( $arrItem['strCSSPath'] ) ),
                    // 'css'        => sprintf( '<a href="%s">' . __( 'CSS', 'amazon-auto-links' ) . '</a>', site_url() . "?amazon_auto_links_style={$arrItem['strID']}" ),
                );
                
                //Return the title contents
                return sprintf('%1$s <div class="active second">%2$s</div>',
                    /*$1%s*/ $arrItem['strDescription'],
                    /*$2%s*/ $this->row_actions( $arrActions )
                );
            case 'thumbnail':
                if ( ! file_exists( $arrItem['strThumbnailPath'] ) ) return;
                $strImageURL = AmazonAutoLinks_WPUtilities::getSRCFromPath( $arrItem['strThumbnailPath'] );
                // $strImageURL = site_url() . "?amazon_auto_links_image=" . base64_encode( $arrItem['strThumbnailPath'] );
                return "<a class='template-thumbnail' href='#thumb'>"
                        . "<img src='{$strImageURL}' style='max-width:80px; max-height:80px;' />"
                        . "<span>"
                            . "<div>"
                                . "<img src='{$strImageURL}' /><br />"
                                . $arrItem['strName']
                            . "</div>"
                        . "</span>"
                    . "</a>";                
            default:
                return print_r( $arrItem, true ); //Show the whole array for troubleshooting purposes
        }
        
    }
        
    public function column_name( $arrItem ){    // column_{$column_title}
        
        //Build row actions
        $arrActions = array();
        if ( $arrItem['fIsActive']  )                        
            $arrActions[ 'deactivate' ] = sprintf( '<a href="?post_type=%s&page=%s&action=%s&template=%s">' . __( 'Deactivate', 'amazon-auto-links' ) . '</a>', AmazonAutoLinks_Commons::PostTypeSlug, $_REQUEST['page'], 'deactivate', $arrItem['strID'] );
        else  
            $arrActions[ 'activate' ] = sprintf( '<a href="?post_type=%s&page=%s&action=%s&template=%s">' . __( 'Activate', 'amazon-auto-links' ) . '</a>', AmazonAutoLinks_Commons::PostTypeSlug, $_REQUEST['page'], 'activate', $arrItem['strID'] );
        $arrActions = apply_filters( 'aal_filter_template_listing_table_action_links', $arrActions, $arrItem['strID'] );    

        //Return the title contents
        return sprintf('%1$s %2$s',    // <span style="color:silver">(id:%2$s)</span>
            /*$1%s*/ $arrItem['fIsActive'] ? "<strong>{$arrItem['strName']}</strong>" : $arrItem['strName'],
            /*$2%s*/ $this->row_actions( $arrActions )
        );
        
    }
    
    public function column_cb( $arrItem ){    // column_ + cb
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  
            /*$2%s*/ $arrItem['strID']                //The value of the checkbox should be the record's id
        );
    }
    
    public function get_columns() {
        return array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            'name'            => __( 'Template Name', 'amazon-auto-links' ),
            'thumbnail'        => __( 'Thumbnail', 'amazon-auto-links' ),
            'description'    => __( 'Description', 'amazon-auto-links' ),
        );
    }
    
    public function get_sortable_columns() {
        return array(
            'name'                => array( 'name', false ),     //true means it's already sorted
            // 'thumbnail'        => array( 'thumbnail', false ),
            'description'        => array( 'description', false ),
        );
    }
    
    public function get_bulk_actions() {
        return array(
            // 'delete'    => 'Delete',
            'activate'        => __( 'Activate', 'amazon-auto-links' ),
            'deactivate'    => __( 'Deactivate', 'amazon-auto-links' ),
        );
    }
    
    /**
     * This method uses redirct so it must be called before the header gets sent.
     */
    public function process_bulk_action() {
        
// echo '<h3>Template Options before Updating</h3>';
// echo '<pre>'. print_r( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['arrTemplates'], true ) . '</pre>';    

// echo '<h3>Set Data</h3>';
// echo '<pre>'. print_r( $this->arrData, true ) . '</pre>';    

        if ( ! isset( $_REQUEST['template'] ) ) return;
        
        switch( strtolower( $this->current_action() ) ){

            case 'activate':
                foreach( ( array ) $_REQUEST['template'] as $strID ) {
                    $this->arrData[ $strID ]['fIsActive'] = true;
                    $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['arrTemplates'][ $strID ] = $this->arrData[ $strID ];
                }
                break;
            case 'deactivate':
                foreach( ( array ) $_REQUEST['template'] as $strID ) {
                    $this->arrData[ $strID ]['fIsActive'] = false;
                    unset( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['arrTemplates'][ $strID ] );    // the option array only stores active templates.
                }
                break;    
            default:
                return;    // do nothing.
                
        }   

        // Save the options.
        // unset( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['arrTemplates'] );    // just in case.
        $GLOBALS['oAmazonAutoLinks_Option']->save();
        
        // Clear the url that contains lots of arguments.
        wp_redirect( admin_url( 'edit.php?post_type=amazon_auto_links&page=aal_templates&tab=table' ) );
        
        
// echo '<h3>List Data Array</h3>';
// echo '<pre>'. print_r( $this->arrData, true ) . '</pre>';    
// echo '<h3>Template Options</h3>';
// echo '<pre>'. print_r( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['arrTemplates'], true ) . '</pre>';    
    
                
    }

    function prepare_items() {
        
// echo '<h3>List Data Array</h3>';
// echo '<pre>'. print_r( $this->arrData, true ) . '</pre>';    
// echo '<h3>Template Options</h3>';
// echo '<pre>'. print_r( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['arrTemplates'], true ) . '</pre>';    
    
        /**
         * Set how many records per page to show
         */
        $intItemsPerPage = 20;
        
    
        /**
         * Define our column headers. 
         */
        $this->_column_headers = array( 
            $this->get_columns(),     // $arrColumns
            array(),    // $arrHidden
            $this->get_sortable_columns()    // $arrSortable
        );
        
     
        /**
         * Process bulk actions.
         */
        // $this->process_bulk_action(); // in our case, it is dealt before the header is sent. ( with the Admin page class )
              
        /**
         * Variables
         */
        $arrData = $this->arrData;
                
        
        /**
         * Sort the array.
         */
        usort( $arrData, array( $this, 'usort_reorder' ) );
           
                
        /**
         * For pagination.
         */
        $intCurrentPageNumber = $this->get_pagenum();
        $intTotalItems = count( $arrData );
        $this->set_pagination_args( 
            array(
                'total_items' => $intTotalItems,                      // calculate the total number of items
                'per_page'    => $intItemsPerPage,                     // determine how many items to show on a page
                'total_pages' => ceil( $intTotalItems / $intItemsPerPage )   // calculate the total number of pages
            )
        );
        $arrData = array_slice( $arrData, ( ( $intCurrentPageNumber -1 ) * $intItemsPerPage ), $intItemsPerPage );
        
        /*
         * Set data
         * */
        $this->items = $arrData;
        
    }
        public function usort_reorder( $a, $b ) {
            
            $strOrderBy = ( !empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'strName'; //If no sort, default to title
            if ( $strOrderBy == 'description' )
                $strOrderBy = 'strDescription';
            else if ( $strOrderBy == 'name' )
                $strOrderBy = 'strName';
                        
            $strOrder = ( !empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp( $a[ $strOrderBy ], $b[ $strOrderBy ] ); //Determine sort order
            return ( $strOrder === 'asc' ) ? $result : -$result; //Send final sort direction to usort
            
        }
    
}
