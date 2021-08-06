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
 * Adds the auto-insert setting page that has 'Add New Auto-insert' and 'Edit Auto-insert' tabs.
 * 
 * @since       3
 */
class AmazonAutoLinks_AutoInsertAdminPage_AutoInsert extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'page_slug'     => AmazonAutoLinks_Registry::$aAdminPages[ 'auto_insert' ],
            'title'         => __( 'Add/Edit Auto-insert', 'amazon-auto-links' ),
            'screen_icon'   => AmazonAutoLinks_Registry::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            'show_in_menu'  => false,
            'capability'    => $_oOption->get( array( 'capabilities', 'create_units' ), 'edit_pages' ),
            'script'        => array(
                array(
                    'src'           => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/accordion.js',
                    'dependencies'  => array( 'jquery', 'jquery-ui-accordion', ),
                    'in_footer'     => true,
                ),
            ),
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
        new AmazonAutoLinks_AutoInsertAdminPage_AutoInsert_New( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'      => 'new',
                'title'         => __( 'Add New Auto-insert', 'amazon-auto-links' ),
                'description'   => __( 'Define where you want units to be inserted.', 'amazon-auto-links' ),
            )
        );

        new AmazonAutoLinks_AutoInsertAdminPage_AutoInsert_Edit( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'      => 'edit',
                'title'         => __( 'Edit Auto-insert', 'amazon-auto-links' ),
                'description'   => __( 'Define where you want units to be inserted.', 'amazon-auto-links' ),
            )
        );
       
    }   
    
 
    public function replyToLoadPage( $oFactory ) {
    
        $oFactory->setPageHeadingTabsVisibility( true );
        $oFactory->setPageTitleVisibility( true ); 
        $oFactory->setInPageTabsVisibility( false );      
      
    }
 
    public function replyToDoAfterPage( $oFactory ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug() ) {
            return;
        }
        echo "<div class='aal-accordion'>"
            . "<h3>Debug</h3>"
            . "<div>" . $oFactory->oDebug->get(
                $oFactory->oProp->aOptions 
            ) . "</div>"
            . "</div>";
    }
}
