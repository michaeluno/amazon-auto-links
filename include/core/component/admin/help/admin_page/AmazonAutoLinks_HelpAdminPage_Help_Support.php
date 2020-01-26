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
class AmazonAutoLinks_HelpAdminPage_Help_Support extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'support',
            'title'     => __( 'Support', 'amazon-auto-links' ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     * 
     * @callback        action      load_{page slug}_{tab slug}
     */
    protected function _loadTab( $oAdminPage ) {}
    
    /**
     * 
     * @callback        action      do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {
        
        echo "<h3>" 
                . __( 'Support Forum', 'amazon-auto-links' )
            . "</h3>";
        echo "<p>"
            . sprintf( 
                __( 'To get free support, visit the <a href="%1$s" target="_blank">support forum</a>.', 'amazon-auto-links' ),
                'https://wordpress.org/support/plugin/amazon-auto-links'
            )
            . "</p>";

        echo "<h3>" 
                . __( 'Priority Support', 'amazon-auto-links' )
            . "</h3>";
        echo "<p>"
                . sprintf(
                    __( 'You can get priority email support by purchasing <a href="%1$s" target="_blank">Pro</a>.', 'amazon-auto-links' ),
                    'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/'
                )
            . "</p>";
            
    }    
            
}