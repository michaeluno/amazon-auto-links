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
 * Adds the 'Generator' tab to the 'Tools' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_Authentication extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'authentication',
            'title'     => __( 'Authentication', 'amazon-auto-links' ),
            'order'     => 10,
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oAdminPage ) {

        // Sections
        new AmazonAutoLinks_AdminPage_Setting_Authentication_AuthenticationKeys(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
            )
        );
        
    }
            
}