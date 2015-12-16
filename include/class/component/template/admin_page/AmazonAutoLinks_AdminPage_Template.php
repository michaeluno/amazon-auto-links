<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
 */

/**
 * Adds the `Templates` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Template extends AmazonAutoLinks_AdminPage_Page_Base {


    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    public function construct( $oFactory ) {
        
        // Tabs
        new AmazonAutoLinks_AdminPage_Template_ListTable( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'table',
                'title'     => __( 'Installed', 'amazon-auto-links' ),
            )
        );
        new AmazonAutoLinks_AdminPage_Template_GetNew( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'  => 'get',
                'title'     => __( 'Get New', 'amazon-auto-links' ),
            )
        );

    }   
    

        
}
