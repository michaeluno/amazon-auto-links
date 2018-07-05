<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Adds the 'Cache' tab to the 'Settings' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_Cache extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_AdminPage_Setting_Cache_Cache( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'section_id'    => 'cache',
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'Caches', 'amazon-auto-links' ),
                // 'description'   => array(
                    // __( 'Set the criteria to filter fetched items.', 'amazon-auto-links' ),
                // ),
            )
        );
        
    }

    public function replyToDoTab( $oFactory ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()  
            . "</div>";
    }    
        
}
