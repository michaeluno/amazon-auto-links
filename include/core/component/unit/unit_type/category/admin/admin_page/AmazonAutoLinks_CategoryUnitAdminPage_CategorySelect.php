<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the 'Add Unit by Category' page.
 * 
 * @since 3
 */
class AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'category_select' ],
            'title'         => __( 'Add Unit by Category', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/image/icon/screen_icon_32x32.png", true ),
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
            'order'         => 40,
        );
    }

    /**
     * A user constructor.
     * 
     * @since 3
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _construct( $oFactory ) {
        
        // Tabs
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_First( $this->oFactory, $this->sPageSlug );
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_Second( $this->oFactory, $this->sPageSlug );
       
        $this->_doPageSettings( $this->oFactory );
        
    }
        /**
         * @param AmazonAutoLinks_AdminPageFramework $oFactory
         */
        protected function _doPageSettings( $oFactory ) {
            
            $this->oFactory->setPageHeadingTabsVisibility( true );       
            $this->oFactory->setPageTitleVisibility( true ); 
            $this->oFactory->setInPageTabsVisibility( false );

        }
 
    public function replyToDoPage( $oFactory ) {}
    public function replyToDoAfterPage( $oFactory ) {}

}
