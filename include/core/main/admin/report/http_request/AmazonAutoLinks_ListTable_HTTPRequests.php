<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers
 *  
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2021, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License 
*/

/**
 * Handles the list table of HTTP request caches.
 * @since       4.7.0
 */
class AmazonAutoLinks_ListTable_HTTPRequests extends AmazonAutoLinks_ListTableWrap_Base {

    /**
     * @return array
     * @since  4.6.0
     * @see    WP_List_Table::__construct()
     */
    protected function _getArguments() {
        return array(
            'singular'  => 'http_request',     // singular name of the listed items
            'plural'    => 'http_requests',    // plural name of the listed items
            'ajax'      => false,         // does this table support ajax?
            'screen'    => null,          // not sure what this is for...
        );
    }

    /**
     * @return string[]
     * @since  4.7.0
     */
    protected function _getColumns() {
        return array(
            'cb'               => '<input type="checkbox" />', // Render a checkbox instead of text
            'request_uri'      => __( 'URL', 'amazon-auto-links' ),
            'type'             => __( 'Type', 'amazon-auto-links' ),
            'modified_time'    => __( 'Last Modified', 'amazon-auto-links' ),
            'expiration_time'  => __( 'Expiry', 'amazon-auto-links' ),
            'expired'          => __( 'Expired', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getSortableColumns() {
        // true means it's already sorted
        return array(
            'modified_time'     => array( 'modified_time', false ),
            'expiration_time'     => array( 'modified_time', false ),
        );
    }

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getBulkActions() {
        return array(
            'delete'    => __( 'Delete', 'amazon-auto-links' ),
            // 'renew'     => __( 'Renew', 'amazon-auto-links' ), // @todo add this action
        );
    }

    /**
     * @since  4.7.0
     */    
    protected function _processBulkAction() {

        switch( strtolower( $this->current_action() ) ){
            case 'delete':
                $this->___deleteItems( ( array ) $_REQUEST[ 'name' ] );
                break;
            default:
                return;    // do nothing.
        }

        // Reload the page.
        exit(
            wp_safe_redirect(
                remove_query_arg( array( 'action', 'name' ), add_query_arg( $_GET, admin_url( $GLOBALS[ 'pagenow' ] ) ) )
            )
        );

    }
        /**
         * @param array $aNames
         * @since 4.7.0
         */
        private function ___deleteItems( array $aNames ) {
            $_oTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
            $_oTable->deleteCache( $aNames );
        }
        
    /**
     * @param  array $aItem
     * @return string
     * @since  4.7.0
     */
    public function column_cb( $aItem ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            'name',  /*$1%s*/
            $aItem[ 'name' ]  /*$2%s*/ // The value of the checkbox should be the record's id
        );
    }

    public function column_request_uri( $aItem ) {
        $_sPreviewURL = site_url( '?aal-http-request-cache=1&name=' . $aItem[ 'name' ] . '&nonce=' . $this->sNonce );
        return "<a target='_blank' href='" . esc_url( $_sPreviewURL ) . "'>" . $aItem[ 'request_uri' ] . "</a>" . ' '
            . $this->row_actions( $this->___getActionLinks( $aItem, $_sPreviewURL ) );
    }

        /**
         * @param  array  $aItem
         * @param  string $sPreviewURL
         * @return array
         * @since  4.7.0
         */
        private function ___getActionLinks( $aItem, $sPreviewURL ) {
            $_sDetailURL        = add_query_arg(
                array(
                    'tab'        => 'http_request',
                    'name'       => $aItem[ 'name' ],
                )
            );
            return array(
                'view'  => sprintf(
                    '<a target="_blank" href="%1$s">' . __( 'View Cache', 'amazon-auto-links' ) . '</a>',
                    esc_url( $sPreviewURL )
                ),
                'detail'  => sprintf(
                    '<a href="%1$s">' . __( 'Details', 'amazon-auto-links' ) . '</a>',
                    esc_url( $_sDetailURL )
                ),
                'original'  => sprintf(
                    '<a target="_blank" href="%1$s">' . __( 'Visit Original', 'amazon-auto-links' ) . '</a>',
                    esc_url( $aItem[ 'request_uri' ] )
                ),
                'delete' => sprintf(
                    '<a href="%1$s">' . __( 'Delete', 'amazon-auto-links' ) . '</a>',
                    esc_url(
                        add_query_arg(
                            array(
                                'action' => 'delete',
                                'name'   => $aItem[ 'name' ],
                            ) + $_GET,
                            admin_url( $GLOBALS[ 'pagenow' ] )
                        )
                    )
                ),
            );
        }
        // @deprecated
        // private function ___getViewModalElement( $sName ) {
        //     $_sModalID = 'http-request-cache-' . $sName;
        //     $_sTitle   = __( 'View', 'amazon-auto-links' );
        //     $_sModalIDEscaped = esc_attr( $_sModalID );
        //     $_sTitleEscaped   = esc_attr( $_sTitle );
        //     return "<span class='' title='" . $_sTitle . "'></span>"
        //         . "<div id='{$_sModalIDEscaped}' style='display:none;'>"
        //             . "<p>This is a test</p>"
        //         . "</div>";
        //         // . "<div class='row-actions'>"
        //         //     . "<span class='show'>"
        //         //         . "<a href='#TB_inline?width=800&height=1200&inlineId={$_sModalIDEscaped}' class='inline hide-if-no-js thickbox' title='{$_sTitleEscaped}'>"
        //         //             . __( 'View', 'amazon-auto-links' )
        //         //         . "</a>"
        //         //     . "</span>"
        //         // . "</div>";
        //
        // }


    public function column_modified_time( $aItem ) {
        return "<p>"
                . AmazonAutoLinks_PluginUtility::getSiteReadableDate( strtotime( $aItem[ 'modified_time' ] ), 'Y-m-d H:i:s', true )
            . "</p>";  // @todo need to caclcurate the offset
    }

    public function column_expiration_time( $aItem ) {
        return "<p>"
                . AmazonAutoLinks_PluginUtility::getSiteReadableDate( strtotime( $aItem[ 'expiration_time' ] ), 'Y-m-d H:i:s', true )
            . "</p>";
    }

    public function column_expired( $aItem ) {
        return "<p>"
                . ( strtotime( $aItem[ 'expiration_time' ] ) < time() ? __( 'Yes', 'amazon-auto-links' ) : __( 'No', 'amazon-auto-links' ) )
            . "</p>";
    }

    /**
     * @param  integer $iPerPage
     * @param  integer $iPageNumber
     * @return array
     * @sicne  4.7.0
     */
    protected function _getData( $iPerPage, $iPageNumber ) {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_sQuery = "SELECT * "
            . "FROM `" . $_oTable->getTableName() . "`";
        if ( isset( $_REQUEST[ 's' ]) ) {
            $_sQuery .= ' WHERE request_uri LIKE "%' . $_REQUEST[ 's' ] . '%"';
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
     * @sicne  4.7.0
     */
    protected function _getTotalCount() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_sQuery = "SELECT COUNT(*) FROM `" . $_oTable->getTableName() . "`";
        return $_oTable->getVariable( $_sQuery );
    }

    /**
     * @sicne  4.7.0
     */
    public function display() {
        $this->search_box( __( 'Search by URL', 'amazon-auto-links' ),'search_http_request_cache' );
        parent::display();
    }

}