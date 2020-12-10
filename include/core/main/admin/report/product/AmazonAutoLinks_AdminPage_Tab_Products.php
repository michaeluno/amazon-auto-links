<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Adds the 'Products' admin page tab.
 * 
 * @since 4.4.3
 */
class AmazonAutoLinks_AdminPage_Tab_Products extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'products',
            'title'     => __( 'Products', 'amazon-auto-links' ),
            'order'     => 50,
            'style'     => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/products-table.css',
        );
    }

    /**
     * @var AmazonAutoLinks_ListTableWrap_Base
     */
    protected $_oListTable;

    protected function _construct( $oAdminPage ) {

        // Disable these query keys embedded in navigation tab links
        $oAdminPage->oProp->aDisallowedQueryKeys[] = 'orderby';
        $oAdminPage->oProp->aDisallowedQueryKeys[] = 'order';
        $oAdminPage->oProp->aDisallowedQueryKeys[] = 'product_id';

        // For the table output.
        add_action( "do_form_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToDoBeforeForm' ) );
     }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _loadTab( $oAdminPage ) {

        $this->_oListTable = new AmazonAutoLinks_ListTable_Products;
        $this->_oListTable->process_bulk_action();

    }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    public function replyToDoBeforeForm( $oAdminPage ) {

        $this->_oListTable->prepare_items();
        ?>
        <div class="list-table-container">
            <form id="amazon-products" method="post">
                <?php $this->_oListTable->display() ?>
            </form>
        </div>
        <?php

    }

}