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
 * Adds the `Test` page.
 * 
 * @since       4.3.0
 */
class AmazonAutoLinks_Test_AdminPage_Test extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return  array
     * @since   4.3.0
     */
    protected function _getArguments() {
        return array(
            'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'test' ],
            'title'     => 'Test',
            'order'     => 99999, // to be the last menu item
//            'style'     => array(
//                AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ),
//                AmazonAutoLinks_Registry::getPluginURL( '/asset/css/code.css' ),
//            ),
        );
    }

    /**
     * @param           AmazonAutoLinks_AdminPage $oFactory
     * @callback        action      load_{page slug}
     */    
    public function replyToLoadPage( $oFactory ) {

        // Tabs
        new AmazonAutoLinks_Test_AdminPage_Test_Scratch( $this->oFactory, $this->sPageSlug );

        $this->_doPageSettings( $oFactory );
        
    }
    
        /**
         * Page styling
         *
         * @param AmazonAutoLinks_AdminPageFramework $oFactory
         * @since       4.3.0
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
