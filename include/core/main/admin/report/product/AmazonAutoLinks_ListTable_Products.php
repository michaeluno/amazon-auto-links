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
 * Handles the list table of Amazon products.
 * @since       4.4.3
 */
class AmazonAutoLinks_ListTable_Products extends AmazonAutoLinks_ListTableWrap_Base {

    /**
     * @return array
     * @since  4.4.3
     * @see    WP_List_Table::__construct()
     */
    protected function _getArguments() {
        return array(
            'singular'  => 'product',     // singular name of the listed items
            'plural'    => 'products',    // plural name of the listed items
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
            'cb'            => '<input type="checkbox" />', // Render a checkbox instead of text
            'name'          => __( 'Title', 'amazon-auto-links' ),
            'image'         => __( 'Image', 'amazon-auto-links' ),
            'product'       => __( 'Product', 'amazon-auto-links' ),
            'categories'    => __( 'Category', 'amazon-auto-links' ),
            'prices'        => __( 'Price', 'amazon-auto-links' ),
            'details'       => __( 'Details', 'amazon-auto-links' ),
            'modified_time' => __( 'Date', 'amazon-auto-links' ),
        );
    }
        public function replyToDropColumns( $sColumnName ) {
            if ( false !== strpos( $sColumnName, 'price'  ) ) {
                return false;
            }
            return true;
        }

    /**
     * @return array
     * @since  4.4.3
     */
    protected function _getSortableColumns() {
        return array(
            'title'             => array( 'title', false ),    //true means it's already sorted
            'modified_time'     => array( 'modified_time', false ),     //true means it's already sorted
            // 'thumbnail'  => array( 'thumbnail', false ),
            // 'description'   => array( 'description', false ),
        );
    }

    protected function _getBulkActions() {
        return array(
            'delete'    => __( 'Delete', 'amazon-auto-links' ),
            // 'activate'    => __( 'Activate', 'amazon-auto-links' ),
            // 'deactivate'  => __( 'Deactivate', 'amazon-auto-links' ),
        );
    }

    protected function _processBulkAction() {

        switch( strtolower( $this->current_action() ) ){
            case 'delete':
                $this->_deleteItems( ( array ) $_REQUEST[ 'object_id' ] );
                break;
            default:
                return;    // do nothing.
        }

        // Reload the page.
        exit(
            wp_safe_redirect(
                remove_query_arg( array( 'action', 'object_id' ), add_query_arg( $_GET, admin_url( $GLOBALS[ 'pagenow' ] ) ) )
            )
        );

    }

        protected function _deleteItems( array $aObjectIDs ) {
            $_sInProducts = '(' . implode( ',', $aObjectIDs ) . ')';
            $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
            $_oTable->getVariable(
                "DELETE FROM `{$_oTable->aArguments[ 'table_name' ]}` "
                . "WHERE object_id IN {$_sInProducts}"
            );
        }


    /**
     *
     * @param array $aItem
     * @return string
     */
    public function column_cb( $aItem ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            'object_id',  /*$1%s*/
            $aItem[ 'object_id' ]  /*$2%s*/ // The value of the checkbox should be the record's id
        );
    }

    public function column_default( $aItem, $sColumnName ) {
        if ( ! isset( $aItem[ $sColumnName ] ) ) {
            return 'Value not set';
        }
        return is_scalar( $aItem[ $sColumnName ] )
            ? "<p>{$aItem[ $sColumnName ]}</p>"
            : gettype( $aItem[ $sColumnName ] );
    }

    /**
     * @param  array $aItem
     * @return string
     */
    public function column_name( $aItem ){
        $_sDetailURL        = add_query_arg(
            array(
                'tab'        => 'product',
                'product_id' => $aItem[ 'product_id' ],
            )
        );
        $_sDeleteActionURL = add_query_arg(
            array(
                'action' => 'delete',
                'object_id' => $aItem[ 'object_id' ]
            ) + $_GET,
            admin_url( $GLOBALS[ 'pagenow' ] )
        );
        $_aActionLinks = array(
            'view'  => sprintf(
                '<a href="%1$s">' . __( 'View', 'amazon-auto-links' ) . '</a>',
                esc_url( $_sDetailURL )
            ),
            'delete' => sprintf(
                '<a href="%1$s">' . __( 'Delete', 'amazon-auto-links' ) . '</a>',
                esc_url( $_sDeleteActionURL )
            ),
        );
        return "<a href='" . esc_url( $_sDetailURL ) . "'>" . $aItem[ 'title' ] . "</a>"
            . ' '
            . $this->row_actions( $_aActionLinks );
    }

    public function column_image( $aItem ) {
        $_sThumbnailURL = $this->oUtil->getElement( $aItem, array( 'images', 'main', 'MediumImage' ) );
        $_sImage        = $_sThumbnailURL
            ? "<img src='" . esc_url( $_sThumbnailURL ) . "' alt='thumbnail-{$aItem[ 'product_id' ]}' />"
            : 'n/a';
        return "<div class='thumbnail'>"
                . "<a href='" . esc_url( $aItem[ 'links' ] ) . "' target='_blank'>"
                    . $_sImage
                . "</a>"
            . "</div>";

    }

    /**
     * Base information of the product.
     * @param  array $aItem
     * @return string
     */
    public function column_product( $aItem ){
        $_aDetails = array(
            __( 'ASIN', 'amazon-auto-links' ) => $aItem[ 'asin' ],
            __( 'Locale', 'amazon-auto-links' ) => $aItem[ 'locale' ],
            __( 'Currency', 'amazon-auto-links' ) => $aItem[ 'currency' ],
            __( 'Language', 'amazon-auto-links' ) => $aItem[ 'language' ],
        );
        return $this->___getDetailList( $_aDetails );
    }

    public function column_categories( $aItem ) {
        return $aItem[ 'categories' ];
    }

    /**
     * Additional data of the product.
     * @param  array $aItem
     * @return string
     */
    public function column_details( $aItem ){
        $_aDetails = array(
            __( 'Prime', 'amazon-auto-links' )          => $this->___getYesOrNo( $aItem[ 'is_prime' ] ),
            __( 'Adult', 'amazon-auto-links' )          => $this->___getYesOrNo( $aItem[ 'is_adult' ] ),
            __( 'Free Shipping', 'amazon-auto-links' )  => $this->___getYesOrNo( $aItem[ 'delivery_free_shipping' ] ),
            __( 'Delivery FBA', 'amazon-auto-links' )   => $this->___getYesOrNo( $aItem[ 'delivery_fba' ] ),
        );
        return $this->___getDetailList( $_aDetails );
    }

    public function column_prices( $aItem ) {
        $_aDetails = array(
            __( 'Proper', 'amazon-auto-links' )     => $aItem[ 'price_formatted' ],
            __( 'Discounted', 'amazon-auto-links' ) => $aItem[ 'discounted_price_formatted' ],
        );
        return $this->___getDetailList( $_aDetails );
    }

    public function column_modified_time( $aItem ) {
        $_aDetails = array(
            __( 'Modified', 'amazon-auto-links' ) => $aItem[ 'modified_time' ],
            __( 'Expires', 'amazon-auto-links' )  => $aItem[ 'expiration_time' ],
        );
        $_sOutput = "<ul>";
        foreach( $_aDetails as $_sKey => $_sValue ) {
            $_sOutput .= "<li><span class='detail-title'>{$_sKey}:</span></li>"
                . "<li><span class='detail-value'>{$_sValue}</span></li>";
        }
        $_sOutput .= "</ul>";
        return $_sOutput;
        // return $this->___getDetailList( $_aDetails );
    }


    /**
     * @param  integer $iPerPage
     * @param  integer $iPageNumber
     * @return array
     * @sicne  4.4.3
     */
    protected function _getData( $iPerPage, $iPageNumber ) {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_sQuery = "SELECT * "
            . "FROM `" . $_oTable->getTableName() . "`";
        if ( isset( $_REQUEST[ 's' ]) ) {
            $_sQuery .= " WHERE asin ='" . $_REQUEST[ 's' ] . "'"
                . ' OR title LIKE "%' . $_REQUEST[ 's' ] . '%"';
        }
        $_REQUEST[ 'orderby' ] = empty( $_REQUEST[ 'orderby' ] )
            ? 'modified_time'   // default
            : $_REQUEST[ 'orderby' ];
        if ( ! empty( $_REQUEST[ 'orderby' ] ) ) {
            $_sQuery .= ' ORDER BY ' . esc_sql( $_REQUEST[ 'orderby' ] );
            $_sQuery .= ! empty( $_REQUEST[ 'order' ] ) ? ' ' . esc_sql( $_REQUEST[ 'order' ] ) : ' DESC';
        }
        $_sQuery    .= " LIMIT " . $iPerPage;
        $_sQuery    .= ' OFFSET ' . ( ( $iPageNumber - 1 ) * $iPerPage );
        return $_oTable->getRows( $_sQuery, 'ARRAY_A' );

    }

    /**
     * @return integer
     * @sicne  4.4.3
     */
    protected function _getTotalCount() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_sQuery = "SELECT COUNT(*) FROM `" . $_oTable->getTableName() . "`";
        return $_oTable->getVariable( $_sQuery );
    }

    public function display() {
        $this->search_box( __( 'Search by ASIN or Title', 'amazon-auto-links' ),'search_product' );
        parent::display();
    }

    // Utilities

    private function ___getDetailList( array $aDetails ) {
        $_sList = '';
        foreach( $aDetails as $_sKey => $_sValue ) {
            $_sList .= $_sKey
                ? "<li><span class='detail-title'>" . $_sKey . ":</span><span class='detail-value'>" . $_sValue . "</span></li>"
                : "<li><span class='detail-value'>" . $_sValue . "</span></li>";
        }
        return "<ul>" . $_sList . "</ul>";
    }
    private function ___getYesOrNo( $mValue ) {
        if ( ! isset( $mValue ) ) {
            return 'n/a';
        }
        return ! empty( $mValue )
            ? __( 'Yes', 'amazon-auto-links' )
            : __( 'No', 'amazon-auto-links' );
    }

}