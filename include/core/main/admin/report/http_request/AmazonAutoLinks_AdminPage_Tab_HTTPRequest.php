<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the 'HTTP Request' hidden admin page tab.
 * 
 * @since 4.7.0
 */
class AmazonAutoLinks_AdminPage_Tab_HTTPRequest extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'          => 'http_request',
            'title'             => __( 'HTTP Requests', 'amazon-auto-links' ),
            'parent_tab_slug'   => 'http_requests',
            'show_in_page_tab'  => false,
            'order'             => 65,
            'style'             => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/http-request.css',
        );
    }

    protected function _construct( $oAdminPage ) {}

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @since 4.7.0
     */
    protected function _doTab( $oAdminPage ) {

        $_sNonce        = wp_create_nonce( 'aal-nonce-http-request-cache-preview' );
        $_sName         = isset( $_GET[ 'name' ] ) ? $_GET[ 'name' ] : '';
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_aCache        = $_oTable->getCache( $_sName );
        echo $this->___getGoBackLink();
        echo "<h3>" . __( 'Details', 'amazon-auto-links' ) . "</h3>";
        echo $this->___getCacheDetails( $_aCache );
        echo "<h3>" . __( 'Cache', 'amazon-auto-links' ) . "</h3>";
        echo $this->___getCacheDataTable( $_aCache, $_sNonce );
    }
        private function ___getCacheDataTable( array $aCache, $sNonce ) {

            $_aData         = $aCache[ 'data' ];

            if ( is_wp_error( $_aData ) ) {
                return $this->getTableOfArray(
                    array( '<span class="icon-warning dashicons dashicons-warning"></span> WP_Error' => $_aData->get_error_code() . ': ' . $_aData->get_error_message() ),
                    array(
                        'table' => array(
                            'class' => 'widefat striped fixed http-request-cache cache-data',
                        ),
                    ),
                    array(),
                    array(),
                    false
                );
            }

            // Move the body element to last
            $_sBody         = $_aData[ 'body' ];
            unset( $_aData[ 'body' ] );
            $_aData[ 'body' ] = $_sBody;

            // Insert the Preview link
            $_sPreviewURL   = site_url( '?aal-http-request-cache=1&name=' . $aCache[ 'name' ] . '&nonce=' . $sNonce );
            $_sPreviewLink  = "<a href='" . esc_url( $_sPreviewURL ) . "' target='_blank'>"
                    . __( 'View', 'amazon-auto-links' )
                    . "<span class='icon-view dashicons dashicons-external'></span>"
                . "</a>";
            // $this->___setValueBeforeKey( $_aData, 'headers', __( 'Preview', 'amazon-auto-links' ), $_sPreviewLink );

            /**
             * @see WP_Error
             */
            $_aData[ 'body' ]    = esc_html( $_aData[ 'body' ] );
            $_aData[ 'headers' ] = $this->getHeaderFromResponse( $_aData );
            $_aData[ 'cookies' ] = $this->getCookiesToParse( $_aData[ 'cookies' ] );

            return $this->getTableOfArray(
                $_aData,
                array(
                    'table' => array(
                        'class' => 'widefat striped fixed http-request-cache cache-data',
                    ),
                    'td'    => array(
                        array( 'class' => 'width-one-fourth', ),  // first td
                    ),
                    'th'    => array(
                        array( 'class' => 'width-one-fourth column-key', ),  // first td
                        array( 'style' => 'width: 88%;', ),  // 2nd td
                    ),
                ),
                array( __( 'Preview', 'amazon-auto-links' ) => $_sPreviewLink ),
                array(),
                false
            );
        }

        /**
         * @param  array $aCache
         * @return string
         * @since  4.7.0
         */
        private function ___getCacheDetails( array $aCache ) {
            unset( $aCache[ 'data' ] );
            return $this->getTableOfArray(
                $aCache,
                array(
                    'table' => array(
                        'class' => 'widefat striped fixed http-request-cache cache-details',
                    ),
                    'td'    => array(
                        array( 'class' => 'width-one-fourth', ),  // first td
                    )
                )
            );
        }

        /**
         * @return string
         * @since  4.7.0
         */
        private function ___getGoBackLink() {
            $_sProductsPageURL = add_query_arg(
                array(
                    'tab' => 'http_requests',
                )
            );
            $_sProductsPageURL = remove_query_arg(
                array(
                    'name'
                ),
                $_sProductsPageURL
            );
            return "<div class='go-back'>"
                    . "<span class='dashicons dashicons-arrow-left-alt small-icon'></span>"
                    . "<a href='{$_sProductsPageURL}'>"
                        . __( 'Go Back', 'amazon-auto-links' )
                    . "</a>"
                . "</div>";
        }

}