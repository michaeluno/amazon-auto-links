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
 * Adds a setting page.
 * 
 * @since       3
 */
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit extends AmazonAutoLinks_AdminPage_Page_Base {

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    public function construct( $oFactory ) {}
    
    /**
     * 
     * @callback        action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {
        
        $this->_checkAPIKeys();       
        
        // Tabs
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_First( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'      => 'first',
                'title'         => __( 'Add Unit by Search', 'amazon-auto-links' ),
                'description'   => __( 'Select the search type.', 'amazon-auto-links' ),
            )
        );
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_search_products( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'      => 'search_products',
                'title'         => __( 'Add Unit by Search', 'amazon-auto-links' )
                    . ' - ' . __( 'Product Search', 'amazon-auto-links' ),
                'description'   => __( 'Create a search unit.', 'amazon-auto-links' ),
            )
        );        
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_item_lookup( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'      => 'item_lookup',
                'title'         => __( 'Add Unit by Search', 'amazon-auto-links' )
                    . ' - ' . __( 'Item Look-up', 'amazon-auto-links' ),
                'description'   => __( 'Create a search unit.', 'amazon-auto-links' ),
            )
        );
        new AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_similarity_lookup( 
            $this->oFactory,
            $this->sPageSlug,
            array( 
                'tab_slug'      => 'similarity_lookup',
                'title'         => __( 'Add Unit by Search', 'amazon-auto-links' )
                    . ' - ' . __( 'Similarity Look-up', 'amazon-auto-links' ),                
                'description'   => __( 'Create a search unit.', 'amazon-auto-links' ),
            )
        );        
 
        $this->_doPageSettings();
        
    }
    
        private function _doPageSettings() {
            
            $this->oFactory->setPageHeadingTabsVisibility( false );
            $this->oFactory->setPageTitleVisibility( false ); 
            $this->oFactory->setInPageTabsVisibility( false );
            
        }    
    
        private function _checkAPIKeys() {
            
            $_oOption = AmazonAutoLinks_Option::getInstance();    
            if ( $_oOption->isAPIConnected() ) {
                return;
            }
            
            $this->oFactory->setSettingNotice( 
                __( 'You need to set API keys first to create a Search unit', 'amazon-auto-links' ), 
                'updated'
            );
            
            // Go to the Authentication tab of the Settings page.
            AmazonAutoLinks_PluginUtility::goToAPIAuthenticationPage();
            
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
