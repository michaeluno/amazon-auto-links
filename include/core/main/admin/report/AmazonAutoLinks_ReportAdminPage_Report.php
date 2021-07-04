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
 * Adds the `Report` admin page.
 * 
 * @since 4.4.0
 */
class AmazonAutoLinks_ReportAdminPage_Report extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        return array(
            'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'report' ],
            'title'     => __( 'Reports', 'amazon-auto-links' ),
            'order'     => 80, // to be the last menu item
            'style'     => array(
                AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
                AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/report.css',
            ),
        );
    }

    /**
     * @param    AmazonAutoLinks_AdminPageFramework
     * @callback add_action() load_{page slug}
     */    
    public function replyToLoadPage( $oFactory ) {

        // Tabs
        new AmazonAutoLinks_AdminPage_Tab_Products( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Tab_Product( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Tab_Tasks( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Tab_SiteDebugLog( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Tab_HTTPRequests( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Tab_HTTPRequest( $this->oFactory, $this->sPageSlug );

        $this->_doPageSettings( $oFactory );
        
    }

}