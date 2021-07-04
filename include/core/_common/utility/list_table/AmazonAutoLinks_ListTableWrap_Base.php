<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers
 *  
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2021, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
*/

if ( ! class_exists( 'WP_List_Table', false ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Handles the list table of Amazon products.
 * @since       4.4.3
 */
abstract class AmazonAutoLinks_ListTableWrap_Base extends WP_List_Table {

    /**
     * @var         array
     * @since       4.4.3
     */
    public $aData       = array();

    /**
     * @var         array
     * @since       4.4.3
     */
    public $aArguments  = array();

    /**
     * @var   AmazonAutoLinks_PluginUtility
     * @since 4.4.3
     */
    public $oUtil;

    /**
     * Sets up properties and hooks.
     * @since 4.4.3
     */
    public function __construct(){

        // Set parent defaults
        $this->aArguments = $this->_getArguments();
        if ( headers_sent() ) {
            parent::__construct( $this->aArguments );
        } else {
            add_action( 'admin_notices', array( $this, '_replyToDelayConstructor' ) );
        }

        $this->oUtil = new AmazonAutoLinks_PluginUtility;

    }

    /**
     * * @since 4.4.3
     */
    public function _replyToDelayConstructor() {
        parent::__construct( $this->aArguments );
    }

    /**
     * Defines columns.
     * @since 4.4.3
     */
    public function get_columns() {
        return $this->_getColumns();
    }

    /**
     * @since 4.4.3
     * @return array
     */
    public function get_hidden_columns() {
        return $this->_getHiddenColumns();
    }

    /**
     * Defines sortable columns.
     * @since 4.4.3
     */
    public function get_sortable_columns() {
        return $this->_getSortableColumns();
    }

    /**
     * Undefined column items will be passed.
     *
     * Show the whole array contents for troubleshooting.
     * @param  array  $aItem        A row item
     * @param  string $sColumnName  The column name.
     * @return string
     * @since 4.4.3
     */
    public function column_default( $aItem, $sColumnName ) {
        if ( ! isset( $aItem[ $sColumnName ] ) ) {
            return 'Value not set';
        }
        return is_scalar( $aItem[ $sColumnName ] )
            ? "<p>{$aItem[ $sColumnName ]}</p>"
            : gettype( $aItem[ $sColumnName ] );
    }

    /**
     *
     * @param array $aItem
     * @return string
     * @since 4.4.3
     */
    public function column_cb( $aItem ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args[ 'singular' ],  /*$1%s*/
            '' // $aItem[ 'id' ]  /*$2%s*/ // The value of the checkbox should be the record's id
        );
    }

    /**
     * @return array
     * @since  4.4.3
     */
    public function get_bulk_actions() {
        return $this->_getBulkActions();
    }

    /**
     * Processes bulk actions of the table form.
     *
     * @remark This method uses redirect so it must be called before the header gets sent.
     * @since  4.4.3
     */
    public function process_bulk_action() {
        $this->_processBulkAction();
    }

    /**
     * Sets up items for table rows.
     * @since 4.4.3
     */
    public function prepare_items() {

        $this->_column_headers = array(
            $this->get_columns(),
            $this->get_hidden_columns(),
            $this->get_sortable_columns()
        );

        $this->process_bulk_action();

        $_iPerPage     = $this->get_items_per_page('records_per_page', 20 );
        $_iCurrentPage = $this->get_pagenum();
        $_aItems       = $this->_getData( $_iPerPage, $_iCurrentPage );
        $_iTotalItems  = $this->_getTotalCount();

        $this->set_pagination_args(
            array(
                'total_items' => $_iTotalItems,
                'per_page'    => $_iPerPage,
                'total_pages' => round( $_iTotalItems / $_iPerPage ),
            )
        );
        $this->items  = $_aItems;
    }

    /**
     * @return array
     * @since  4.4.3
     * @see    WP_List_Table::__construct()
     */
    protected function _getArguments() {
        return array(
            'singular'  => '',     // singular name of the listed items
            'plural'    => '',    // plural name of the listed items
            'ajax'      => false,         // does this table support ajax?
            'screen'    => null,          // not sure what this is for...
        );
    }

    /**
     * @return string[]
     * @since  4.4.3
     */
    protected function _getColumns() {
        return array(
            'cb'   => '<input type="checkbox" />', // Render a checkbox instead of text
            // 'name' => 'Name',
        );
    }

    /**
     * @return array
     * @since  4.4.3
     */
    protected function _getHiddenColumns() {
        return array();
    }

    /**
     * @return array
     * @since  4.4.3
     */
    protected function _getSortableColumns() {
        return array(
            // 'name'          => array( 'name', false ),     //true means it's already sorted
            // 'thumbnail'  => array( 'thumbnail', false ),
            // 'description'   => array( 'description', false ),
        );
    }

    /**
     * @return array
     * @since  4.4.3
     */
    protected function _getBulkActions() {
        return array(
            'delete'    => __( 'Delete', 'amazon-auto-links' ),
        );
    }

    /**
     * @since 4.4.3
     */
    protected function _processBulkAction() {
        exit;
    }

    /**
     * @param  integer $iPerPage
     * @param  integer $iCurrentPage
     * @return array
     * @sicne  4.4.3
     */
    protected function _getData( $iPerPage, $iCurrentPage ) {
        return array();
    }

    /**
     * @return integer
     * @since  4.4.3
     */
    protected function _getTotalCount() {
        return 0;
    }

    /* Utility Methods */

    /**
     * @param  mixed  $mValue
     * @return string
     * @since  4.4.3
     */
    protected function _getYesOrNo( $mValue ) {
        if ( ! isset( $mValue ) ) {
            return 'n/a';
        }
        return ! empty( $mValue )
            ? __( 'Yes', 'amazon-auto-links' )
            : __( 'No', 'amazon-auto-links' );
    }

}