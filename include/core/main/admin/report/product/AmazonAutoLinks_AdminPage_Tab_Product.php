<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the 'Product' hidden admin page tab.
 * 
 * @since 4.4.3
 */
class AmazonAutoLinks_AdminPage_Tab_Product extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'product',
            'title'     => __( 'Products', 'amazon-auto-links' ),
            'parent_tab_slug' => 'products',
            'show_in_page_tab' => false,
            'order'     => 55,
            'style'     => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/table-product.css',
        );
    }

    protected function _construct( $oAdminPage ) {
        // Disable these query keys embedded in navigation tab links
        $oAdminPage->oProp->aDisallowedQueryKeys[] = 'orderby';
        $oAdminPage->oProp->aDisallowedQueryKeys[] = 'order';
        $oAdminPage->oProp->aDisallowedQueryKeys[] = 'product_id';
    }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _doTab( $oAdminPage ) {

        $_sProductID    = $this->getHTTPQueryGET( 'product_id', '' );
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_aProducts     = $_sProductID ? $_oTable->getRowsByProductID( array( $_sProductID ) ) : array();
        $_aProduct      = reset( $_aProducts );
        $_sThumbnailURL = $this->getElement( $_aProduct, array( 'images', 'main', 'MediumImage' ) );
        $_sImage        = $_sThumbnailURL
            ? "<img src='" . esc_url( $_sThumbnailURL ) . "' alt='thumbnail-{$_aProduct[ 'product_id' ]}' title='" . esc_attr( $_aProduct[ 'title' ] ) . "' />"
            : "<div class='centered'><span>" . __( 'No thumbnail', 'amazon-auto-links' ) . "</span></div>";
        echo "<div class='product-thumbnail float-right'>" . wp_kses( $_sImage, 'post' ) . "</div>";
        echo wp_kses( $this->___getGoBackLink(), 'post' );
        echo "<h3>" . __( 'Product Details', 'amazon-auto-links' ) . "</h3>";
        $_sTable = $this->getTableOfArray(
            $_aProduct,
            array(
                'table' => array(
                    'class' => 'widefat striped fixed product-details',
                ),
                'td'    => array(
                    array( 'class' => 'width-one-fourth', ),  // first td
                )
            )
        );
        echo wp_kses( $_sTable, 'post' );
    }
        private function ___getGoBackLink() {
            $_sProductsPageURL = add_query_arg(
                array(
                    'tab' => 'products',
                )
            );
            $_sProductsPageURL = remove_query_arg(
                array(
                    'product_id'
                ),
                $_sProductsPageURL
            );
            return "<div class='go-back'>"
                    . "<span class='dashicons dashicons-arrow-left-alt small-icon'></span>"
                    . "<a href='" . esc_url( $_sProductsPageURL ) . "'>"
                        . __( 'Go Back', 'amazon-auto-links' )
                    . "</a>"
                . "</div>";
        }

}
