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
 * Adds the 'General' tab to the 'Settings' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_General extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'general',
            'title'     => __( 'General', 'amazon-auto-links' ),
            'order'     => 20,
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_AdminPage_Setting_General_ProductFilters(
            $oAdminPage,
            $this->sPageSlug,
            array( 'tab_slug' => $this->sTabSlug, )
        );
        
        // 3.3.0+
        new AmazonAutoLinks_AdminPage_Setting_General_Feed( 
            $oAdminPage,
            $this->sPageSlug,
            array( 'tab_slug' => $this->sTabSlug, )
        );

        // 3.9.0+
        new AmazonAutoLinks_AdminPage_Setting_General_Widgets(
            $oAdminPage,
            $this->sPageSlug,
            array( 'tab_slug' => $this->sTabSlug, )
        );

        new AmazonAutoLinks_AdminPage_Setting_General_UnitPreview( 
            $oAdminPage,
            $this->sPageSlug,
            array( 'tab_slug' => $this->sTabSlug, )
        );

        new AmazonAutoLinks_AdminPage_Setting_General_CustomQueryKey( 
            $oAdminPage,
            $this->sPageSlug,
            array( 'tab_slug' => $this->sTabSlug, )
        );

        new AmazonAutoLinks_AdminPage_Setting_General_ExternalScript( 
            $oAdminPage,
            $this->sPageSlug, 
            array( 'tab_slug' => $this->sTabSlug, )
        );              
     
        new AmazonAutoLinks_AdminPage_Setting_General_MiunosoftAffiliate( 
            $oAdminPage,
            $this->sPageSlug, 
            array( 'tab_slug' => $this->sTabSlug, )
        );         
     
    }
    
    protected function _doTab( $oFactory ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()  
            . "</div>";
    }
            
}
