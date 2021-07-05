<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the 'Tasks' admin page tab.
 * 
 * @since 4.4.4
 */
class AmazonAutoLinks_AdminPage_Tab_Tasks extends AmazonAutoLinks_AdminPage_Tab_Products {

    /**
     * @return array
     * @since  4.4.4
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'tasks',
            'title'     => __( 'Tasks', 'amazon-auto-links' ),
            'order'     => 60,
            'style'     => array(
                AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/table-products.css',
                AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/table-tasks.css',
            ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _loadTab( $oAdminPage ) {


        $this->_oListTable = new AmazonAutoLinks_ListTable_Tasks;
        $this->_oListTable->process_bulk_action();

    }

}
