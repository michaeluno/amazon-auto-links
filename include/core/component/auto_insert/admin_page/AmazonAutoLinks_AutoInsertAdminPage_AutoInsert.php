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
 * Adds the auto-insert setting page that has 'Add New Auto-insert' and 'Edit Auto-insert' tabs.
 * 
 * @since       3
 */
class AmazonAutoLinks_AutoInsertAdminPage_AutoInsert extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    public function construct( $oFactory ) {
        
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
        echo "<p>Debug</p>"
            . $oFactory->oDebug->get( 
                $oFactory->oProp->aOptions 
            );       
    }
}
