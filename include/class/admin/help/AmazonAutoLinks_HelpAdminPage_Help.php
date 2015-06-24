<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the `Help` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_HelpAdminPage_Help extends AmazonAutoLinks_AdminPage_Page_Base {


    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    public function construct( $oFactory ) {
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        
        // Tabs
        new AmazonAutoLinks_HelpAdminPage_Help_Support( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'support',
                'title'     => __( 'Support', 'amazon-auto-links' ),
            )
        );        
        new AmazonAutoLinks_HelpAdminPage_Help_FAQ( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'faq',
                'title'     => __( 'FAQ', 'amazon-auto-links' ),
            )
        );
        new AmazonAutoLinks_HelpAdminPage_Help_Tips( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'tips',
                'title'     => __( 'Tips', 'amazon-auto-links' ),
            )
        );   
        new AmazonAutoLinks_HelpAdminPage_Help_ChangeLog(
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'change_log',
                'title'     => __( 'Change Log', 'amazon-auto-links' ),
            )        
        );
        if ( ! $_oOption->isAdvancedAllowed() ) {            
            new AmazonAutoLinks_HelpAdminPage_Help_GetPro( 
                $this->oFactory,
                $this->sPageSlug,
                array( 
                    'tab_slug'  => 'get_pro',
                    'title'     => __( 'Get Pro', 'amazon-auto-links' ),
                )
            );       
        }

    }   
    
    /**
     * 
     * @callback        action      do_after_{page slug}
     */
    public function replyToDoAfterPage( $oFactory ) {
    }
        
}
