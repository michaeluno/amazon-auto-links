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
 * Adds the 'Add Unit by Category' page.
 * 
 * @since       3
 */
class AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    public function construct( $oFactory ) {
        
        // Tabs
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_First( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'      => 'first',
                'title'         => __( 'Add Unit by Category', 'amazon-auto-links' ),
                'description'   => __( 'Fill basic information', 'amazon-auto-links' ),
            )
        );
        new AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_Second( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'      => 'second',
                'title'         => __( 'Add Unit by Category', 'amazon-auto-links' ),
                'description'   => __( 'Select categories.', 'amazon-auto-links' ),
            )
        );
       
        $this->_doPageSettings();
        
    }   
    
        private function _doPageSettings() {
            
            $this->oFactory->setPageHeadingTabsVisibility( true );       
            $this->oFactory->setPageTitleVisibility( true ); 
            $this->oFactory->setInPageTabsVisibility( false );
            
        }
 
    public function replyToDoPage( $oFactory ) {}
    public function replyToDoAfterPage( $oFactory ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug() ) {
            return;
        }        
        echo "<h3>" 
                . __( 'Debug', 'amazon-auto-links' ) 
                . ": " . __( 'Form Options', 'amazon-auto-links' )
            . "</h3>"
            . $oFactory->oDebug->get( 
                // $oFactory->oProp->aOptions 
                $oFactory->getSavedOptions()
            );
      
    }
}
