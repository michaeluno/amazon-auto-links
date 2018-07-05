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
 * Adds the `Templates` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Template extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @callback        action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {
        
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
