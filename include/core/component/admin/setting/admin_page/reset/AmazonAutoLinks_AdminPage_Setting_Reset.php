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
 * Adds the 'Reset' tab to the 'Settings' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_Reset extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'reset',
            'title'     => __( 'Reset', 'amazon-auto-links' ),
            'order'     => 70,
        );
    }


    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_AdminPage_Setting_Reset_RestSettings( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'tab_slug'      => $this->sTabSlug,
            )
        );

        // 3.6.6+
        new AmazonAutoLinks_AdminPage_Setting_Reset_Data(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
            )
        );
     
    }
    
    public function replyToDoTab( $oFactory ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()  
            . "</div>";
    }    
            
}
