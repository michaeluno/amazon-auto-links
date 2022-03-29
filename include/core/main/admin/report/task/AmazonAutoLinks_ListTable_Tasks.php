<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers
 *  
 * @copyright   Copyright (c) 2013-2022, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
*/

/**
 * Handles the list table of Amazon products.
 * @since       4.4.4
 */
class AmazonAutoLinks_ListTable_Tasks extends AmazonAutoLinks_ListTableWrap_Base {

    /**
     * @return array
     * @since  4.4.4
     * @see    WP_List_Table::__construct()
     */
    protected function _getArguments() {
        return array(
            'singular'  => 'task',     // singular name of the listed items
            'plural'    => 'tasks',    // plural name of the listed items
            'ajax'      => false,         // does this table support ajax?
            'screen'    => null,          // not sure what this is for...
        );
    }

    /**
     * @return string[]
     * @since  4.4.4
     */
    protected function _getColumns() {
        return array(
            'cb'            => '<input type="checkbox" />', // Render a checkbox instead of text
            'name'          => __( 'Name', 'amazon-auto-links' ),
            'action'        => __( 'Action', 'amazon-auto-links' ),
            'arguments'     => __( 'Arguments', 'amazon-auto-links' ),
            'creation_time' => __( 'Created Time', 'amazon-auto-links' ),
            'next_run_time' => __( 'Next Run Time', 'amazon-auto-links' ),
            'now'           => __( 'Now', 'amazon-auto-links' ),
            'due'           => __( 'Due In', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @since  4.4.4
     */
    protected function _getSortableColumns() {
        return array(
            'creation_time'     => array( 'creation_time', false ), // true means it's already sorted
            'next_run_time'     => array( 'next_run_time', false ), // true means it's already sorted
        );
    }

    /**
     * @return array
     * @since  4.4.4
     */
    protected function _getBulkActions() {
        return array(
            'delete'    => __( 'Delete', 'amazon-auto-links' ),
        );
    }

    /**
     * @since  4.4.4
     */
    protected function _processBulkAction() {

        switch( strtolower( $this->current_action() ) ){
            case 'delete':
                $this->_deleteItems( $this->oUtil->getElementAsArray( $this->aREQUEST, array( 'name' ) ) );
                break;
            default:
                return;    // do nothing.
        }

        // Reload the page.
        exit(
            wp_safe_redirect(
                remove_query_arg( array( 'action', 'name' ), add_query_arg( $this->oUtil->getHTTPQueryGET(), admin_url( $GLOBALS[ 'pagenow' ] ) ) )
            )
        );

    }

        protected function _deleteItems( array $aObjectIDs ) {
            $_sInProducts = "('" . implode( "','", $aObjectIDs ) . "')";
            $_oTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
            $_oTable->getVariable(
                "DELETE FROM `{$_oTable->aArguments[ 'table_name' ]}` "
                . "WHERE name IN {$_sInProducts}"
            );
        }


    /**
     *
     * @param array $aItem
     * @return string
     * @since  4.4.4
     */
    public function column_cb( $aItem ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            'name',  /*$1%s*/
            $aItem[ 'name' ]  /*$2%s*/ // The value of the checkbox should be the record's id
        );
    }

    /**
     * @param  array  $aItem
     * @param  string $sColumnName
     * @return string
     * @since  4.4.4
     */
    public function column_default( $aItem, $sColumnName ) {
        if ( ! isset( $aItem[ $sColumnName ] ) ) {
            return 'Value not set';
        }
        return is_scalar( $aItem[ $sColumnName ] )
            ? "<p>{$aItem[ $sColumnName ]}</p>"
            : "<p>" . gettype( $aItem[ $sColumnName ] ) . "</p>";
    }

    /**
     * @param  array $aItem
     * @return string
     * @since  4.4.4
     */
    public function column_name( $aItem ){

        $_sDeleteActionURL = add_query_arg(
            array(
                'action' => 'delete',
                'name'   => $aItem[ 'name' ]
            ) + $this->aREQUEST,
            admin_url( $GLOBALS[ 'pagenow' ] )
        );
        $_aActionLinks = array(
            'delete' => sprintf(
                '<a href="%1$s">' . __( 'Delete', 'amazon-auto-links' ) . '</a>',
                esc_url( $_sDeleteActionURL )
            ),
        );
        return $aItem[ 'name' ]
            . ' '
            . $this->row_actions( $_aActionLinks );
    }

    /**
     * @param  array $aItem
     * @return string
     * @since  4.4.4
     */
    public function column_now( $aItem ) {
        return "<p>"
               . date( 'Y-m-d H:i:s', time() ) // no GMT offset
            . "</p>";
    }

    /**
     * @param  array $aItem
     * @return string
     * @since  4.4.4
     */
    public function column_due( $aItem ) {
        $_iNextRunTime = strtotime( $aItem[ 'next_run_time' ] );
        $_bFuture      = ( $_iNextRunTime - time() ) > 0;
        $_sLabelTime   = $_bFuture ? '' : __( 'before', 'amazon-auto-links' );
        return "<p>"
               . human_time_diff( $_iNextRunTime, time() ) . ' ' . $_sLabelTime
            . "</p>";
    }

    /**
     * @param  array $aItem
     * @return string
     * @since  4.4.4
     */
    public function column_arguments( $aItem ) {
        return is_array( $aItem[ 'arguments' ] )
            ? "<p>Array, " . __( 'length', 'amazon-auto-links' ) . ": " . count( $aItem[ 'arguments' ] ) . "</p>"
            : "<p class='error'>" . __( 'corrupt', 'amazon-auto-links' ) . "</p>";
    }

    /**
     * @param  integer $iPerPage
     * @param  integer $iPageNumber
     * @return array
     * @since  4.4.4
     */
    protected function _getData( $iPerPage, $iPageNumber ) {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_sQuery = "SELECT * "
            . "FROM `" . $_oTable->getTableName() . "`";
        if ( isset( $this->aREQUEST[ 's' ]) ) {
            $_sQuery .= " WHERE action ='" . $this->aREQUEST[ 's' ] . "'"
                . ' OR arguments LIKE "%' . $this->aREQUEST[ 's' ] . '%"';
        }
        $this->aREQUEST[ 'orderby' ] = empty( $this->aREQUEST[ 'orderby' ] )
            ? 'creation_time'   // default
            : $this->aREQUEST[ 'orderby' ];
        $this->aREQUEST[ 'orderby' ] = 'title' === $this->aREQUEST[ 'orderby' ] ? 'name' : $this->aREQUEST[ 'orderby' ];
        if ( ! empty( $this->aREQUEST[ 'orderby' ] ) ) {
            $_sQuery .= ' ORDER BY ' . esc_sql( $this->aREQUEST[ 'orderby' ] );
            $_sQuery .= ! empty( $this->aREQUEST[ 'order' ] ) ? ' ' . esc_sql( $this->aREQUEST[ 'order' ] ) : ' DESC';
        }
        $_sQuery    .= " LIMIT " . $iPerPage;
        $_sQuery    .= ' OFFSET ' . ( ( $iPageNumber - 1 ) * $iPerPage );
        return $_oTable->getRows( $_sQuery, 'ARRAY_A' );
    }

    /**
     * @return integer
     * @since  4.4.4
     */
    protected function _getTotalCount() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_sQuery = "SELECT COUNT(*) FROM `" . $_oTable->getTableName() . "`";
        return $_oTable->getVariable( $_sQuery );
    }

    /**
     * @since 4.4.4
     */
    public function display() {
        $this->search_box( __( 'Search by Action Name', 'amazon-auto-links' ),'search_task' );
        parent::display();
    }


}