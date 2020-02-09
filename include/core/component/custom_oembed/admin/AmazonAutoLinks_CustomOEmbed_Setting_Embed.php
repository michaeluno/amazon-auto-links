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
 * Adds the 'Embed' tab to the 'Settings' page of the loader plugin.
 * 
 * @since       4.0.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_CustomOEmbed_Setting_Embed extends AmazonAutoLinks_AdminPage_Tab_Base {
    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'embed',
            'title'     => __( 'Embed', 'amazon-auto-links' ),
            'order'     => 20,
            'style'     => AmazonAutoLinks_CustomOEmbed_Loader::$sDirPath . '/asset/css/settings.css',
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oAdminPage ) {
        new AmazonAutoLinks_CustomOEmbed_Setting_Embed_Section( $oAdminPage, $this->sPageSlug );
    }
            
    protected function _doTab( $oAdminPage ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()  
            . "</div>";
    }
    
}
