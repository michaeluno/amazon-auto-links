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
 * Adds the `Help` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_HelpAdminPage_Help extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'help' ],
            'title'     => __( 'Help', 'amazon-auto-links' ),
            'order'     => 1000, // to be the last menu item
            'style'     => array(
                AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ),
                AmazonAutoLinks_Registry::getPluginURL( '/asset/css/code.css' ),
            ),
        );
    }

    /**
     * 
     * @callback        action      load_{page slug}
     */    
    public function replyToLoadPage( $oFactory ) {
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        
        // Tabs
        new AmazonAutoLinks_HelpAdminPage_Help_Support( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_HelpAdminPage_Help_FAQ( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_HelpAdminPage_Help_Tips( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_HelpAdminPage_Help_ChangeLog( $this->oFactory, $this->sPageSlug );

        if ( ! $_oOption->isAdvancedAllowed() ) {            
            new AmazonAutoLinks_HelpAdminPage_Help_GetPro( $this->oFactory, $this->sPageSlug );
        }

        new AmazonAutoLinks_HelpAdminPage_Help_About( $this->oFactory, $this->sPageSlug );
        
        $this->_doPageSettings( $oFactory );
        
    }
    
        /**
         * Page styling
         * @since       3
         * @return      void
         */
        private function _doPageSettings( $oFactory ) {
                        
            $oFactory->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $oFactory->setInPageTabTag( 'h2' );                
            $oFactory->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.
            // $oFactory->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
            // $oFactory->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( '/asset/css/code.css' ) );

        }    
            
}
