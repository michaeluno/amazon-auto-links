<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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
        new AmazonAutoLinks_AdminPage_Template_ListTable( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Template_GetNew( $this->oFactory, $this->sPageSlug );

    }
        
}
