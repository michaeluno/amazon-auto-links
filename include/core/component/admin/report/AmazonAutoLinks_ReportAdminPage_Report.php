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
 * Adds the `Report` page.
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
                AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ),
                // AmazonAutoLinks_Registry::getPluginURL( 'asset/css/code.css' ),
            ),
        );
    }

    /**
     * @param    AmazonAutoLinks_AdminPageFramework
     * @callback add_action() load_{page slug}
     */    
    public function replyToLoadPage( $oFactory ) {

        // Tabs
        // new AmazonAutoLinks_HelpAdminPage_Help_Support( $this->oFactory, $this->sPageSlug );

        $this->_doPageSettings( $oFactory );
        
    }

}