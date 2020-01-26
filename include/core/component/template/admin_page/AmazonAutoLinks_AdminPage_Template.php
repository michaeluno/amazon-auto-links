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
 * Adds the `Templates` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Template extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'template' ],
            'title'     => __( 'Templates', 'amazon-auto-links' ),
            'order'     => 60,
        );
    }

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
                'script'    => array(
                    dirname( __FILE__ ) . '/lightbox2/js/lightbox.js',
                ),
                'style'     => array(
                    dirname( __FILE__ ) . '/lightbox2/css/lightbox.css',
                ),
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
