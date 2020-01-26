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
 * Adds an in-page tab to an admin page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_HelpAdminPage_Help_ChangeLog extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'change_log',
            'title'     => __( 'Change Log', 'amazon-auto-links' ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    protected function _loadTab( $oFactory ) {}
    
    /**
     * 
     * @callback        action      do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {

        $_oWPReadmeParser = new AmazonAutoLinks_AdminPageFramework_WPReadmeParser( 
            AmazonAutoLinks_Registry::$sDirPath . '/readme.txt'
        );    
        echo "<h3>" . __( 'Change Log', 'amazon-auto-links' ) . "</h3>"
            . $_oWPReadmeParser->getSection( 'Changelog' );    

    }
            
}