<?php
/**
 * Amazon Auto Links
 * 
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds an in-page tab to an admin page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_HelpAdminPage_Help_ChangeLog extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        
        // $oFactory->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
    }
    
    /**
     * 
     * @callback        action      do_{page slug}_{tab slug}
     */
    public function replyToDoTab( $oFactory ) {

        $_oWPReadmeParser = new AmazonAutoLinks_AdminPageFramework_WPReadmeParser( 
            AmazonAutoLinks_Registry::$sDirPath . '/readme.txt'
        );    
        echo "<h3>" . __( 'Change Log', 'amazon-auto-links' ) . "</h3>"
            . $_oWPReadmeParser->getSection( 'Changelog' );    
            
       
    
    }    
            
}