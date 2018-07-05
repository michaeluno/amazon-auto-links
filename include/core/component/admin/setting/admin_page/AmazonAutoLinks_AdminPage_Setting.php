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
 * Adds the `Settings` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting extends AmazonAutoLinks_AdminPage_Page_Base {
    
    /**
     * Gets load when the page starts loading.
     * @callback    load_{page slug}
     * @return      void
     */
    public function replyToLoadPage( $oFactory ) {
        
        // Tabs
        new AmazonAutoLinks_AdminPage_Setting_Authentication( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'authentication',
                'title'     => __( 'Authentication', 'amazon-auto-links' ),
            )
        );
        new AmazonAutoLinks_AdminPage_Setting_General( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'general',
                'title'     => __( 'General', 'amazon-auto-links' ),
            )
        );
        new AmazonAutoLinks_AdminPage_Setting_Default(
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'default',
                'title'     => __( 'Default', 'amazon-auto-links' ),
            )        
        );
        new AmazonAutoLinks_AdminPage_Setting_Cache( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'cache',
                'title'     => __( 'Cache', 'amazon-auto-links' ),
            )
        );        
        new AmazonAutoLinks_AdminPage_Setting_Misc( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'misc',
                'title'     => __( 'Misc', 'amazon-auto-links' ),
            )
        );        
        new AmazonAutoLinks_AdminPage_Setting_Reset( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'reset',
                'title'     => __( 'Reset', 'amazon-auto-links' ),
            )
        );    
      
    }
    
    /**
     * Prints debug information at the bottom of the page.
     */
    public function replyToDoAfterPage( $oFactory ) {
            
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug() ) {
            return;
        }
        echo "<h3 style='display:block; clear:both;'>" 
                . __( 'Debug Info', 'amazon-auto-links' ) 
            .  "</h3>";
        $oFactory->oDebug->dump( $oFactory->getValue() );
        
    }
        
}
