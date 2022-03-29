<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Adds the 'HTTP Requests' admin page tab.
 * 
 * @since 4.7.0
 */
class AmazonAutoLinks_AdminPage_Tab_HTTPRequests extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'http_requests',
            'title'     => __( 'HTTP Requests', 'amazon-auto-links' ),
            'order'     => 60,
            'style'     => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/http-requests.css',
            'if'        => AmazonAutoLinks_Option::getInstance()->isDebug( 'back_end' ),
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

        // For the table output.
        add_action( "do_form_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToDoBeforeForm' ) );
     }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _loadTab( $oAdminPage ) {

        $this->_oListTable = new AmazonAutoLinks_ListTable_HTTPRequests();
        $this->_oListTable->setNonce( 'aal-nonce-http-request-cache-preview' );
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