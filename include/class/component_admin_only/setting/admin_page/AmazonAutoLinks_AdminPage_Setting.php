<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the `Settings` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting extends AmazonAutoLinks_AdminPage_Page_Base {


    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    public function construct( $oFactory ) {
        
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
