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
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'search_unit' ],
            'title'         => __( 'Add Unit by Search', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
        );
    }

    /**
     * A user constructor.
     *
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @since       3
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     *
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback        action      load_{page slug}
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

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _doAfterPage( $oFactory ) {
        if ( ! $oFactory->oUtil->isDebugMode() ) {
            return;
        }
        echo "<h4>Debug</h4>"
            . $oFactory->oDebug->get( 
                $oFactory->oProp->aOptions 
            );
        echo "<h4>Last Inputs</h4>"
            . $oFactory->oDebug->get(
                get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ] )
            );
    }
        
}
