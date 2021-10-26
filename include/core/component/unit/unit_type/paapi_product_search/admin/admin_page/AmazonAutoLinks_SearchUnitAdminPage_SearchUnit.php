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
 * Adds a setting page.
 * 
 * @since       3
 */
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'paapi_search_unit' ],
            'title'         => __( 'Add Unit by PA-API', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ),
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
            'order'         => 45,
        );
    }

    /**
     *
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback    add_action()      load_{page slug}
     */
    protected function _loadPage( $oFactory ) {

        AmazonAutoLinks_Unit_Admin_Utility::checkAPIKeys( $oFactory );
        
        // Tabs
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_First( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_search_products( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_item_lookup( $this->oFactory, $this->sPageSlug );
 
        $this->___doPageSettings();
        
    }

        private function ___doPageSettings() {
            $this->oFactory->setPageHeadingTabsVisibility( false );
            $this->oFactory->setPageTitleVisibility( false ); 
            $this->oFactory->setInPageTabsVisibility( false );
        }

}
