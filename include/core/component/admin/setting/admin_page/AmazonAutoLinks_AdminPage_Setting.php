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
 * Adds the `Settings` page.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
            'title'         => __( 'Settings', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            'order'         => 50,
        );
    }

    /**
     * Gets load when the page starts loading.
     * @callback    load_{page slug}
     * @return      void
     */
    public function replyToLoadPage( $oFactory ) {

        new AmazonAutoLinks_RevealerCustomFieldType( $oFactory->oProp->sClassName );

        // Tabs
        new AmazonAutoLinks_AdminPage_Setting_Authentication( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Setting_General( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Setting_Default( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Setting_Cache( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Setting_Misc( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Setting_3rdParty( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_AdminPage_Setting_Reset( $this->oFactory, $this->sPageSlug );
      
    }
    
    /**
     * Prints debug information at the bottom of the page.
     */
    protected function _doAfterPage( $oFactory ) {
            
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug() ) {
            return;
        }
        echo "<h3 style='display:block; clear:both;'>"
                . __( 'Debug Info', 'amazon-auto-links' ) 
            . "</h3>"
            . "<h4 style='display:block; clear:both;'>"
                . __( 'Saved Options', 'amazon-auto-links' )
                .  "</h4>";
        $oFactory->oDebug->dump( $oFactory->getValue() );

    }
        
}
