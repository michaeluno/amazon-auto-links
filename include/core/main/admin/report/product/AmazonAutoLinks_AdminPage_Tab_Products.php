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

    private $___oListTable;

    protected function _construct( $oAdminPage ) {
        add_action(
            "do_form_{$this->sPageSlug}_{$this->sTabSlug}",
            array( $this, 'replyToDoBeforeForm' )
        );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _loadTab( $oAdminPage ) {

        $this->___oListTable = new AmazonAutoLinks_ListTable_Products;
        $this->___oListTable->process_bulk_action();

    }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    public function replyToDoBeforeForm( $oAdminPage ) {

        $this->___oListTable->prepare_items();
        ?>
        <div class="list-table-container">
            <form id="amazon-products" method="post">
                <?php $this->___oListTable->display() ?>
            </form>
        </div>
        <?php

    }

}
