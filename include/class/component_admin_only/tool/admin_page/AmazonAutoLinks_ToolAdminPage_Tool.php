<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the `Tools` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_ToolAdminPage_Tool extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * 
     * @callback        action      load_{page slug}
     */    
    public function replyToLoadPage( $oFactory ) {
        
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
            $oFactory->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
  
        }    
        
}
