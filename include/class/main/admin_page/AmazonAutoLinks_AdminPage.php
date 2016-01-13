<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */


/**
 * Deals with the plugin admin pages.
 * 
 * @since       2.0.5
 */
class AmazonAutoLinks_AdminPage extends AmazonAutoLinks_AdminPageFramework {

    /**
     * User constructor.
     */
    public function start() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
        add_filter( 
            "options_" . $this->oProp->sClassName,
            array( $this, 'replyToSetOptions' )
        );
        
    }
        /**
         * Sets the default option values for the setting form.
         * @return      array       The options array.
         */
        public function replyToSetOptions( $aOptions ) {
            
            $_oOption    = AmazonAutoLinks_Option::getInstance();
            return $aOptions + $_oOption->aDefault;
            
        }
    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
        
        add_action( 
            'load_' . $this->oProp->sClassName,
            array( $this, 'replyToDoPageSettings' )
        );
        
        if ( 'plugins.php' === $this->oProp->sPageNow ) {
            $this->addLinkToPluginDescription(
                "<a href='https://wordpress.org/support/plugin/amazon-auto-links' target='_blank'>" 
                        . __( 'Support', 'amazon-auto-links' ) 
                    . "</a>"
            );         
        }
        
    }
        
        /**
         * Page styling
         * @since       3
         * @return      void
         */
        public function replyToDoPageSettings() {
                        
            $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $this->setInPageTabTag( 'h2' );                
            $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
            $this->setDisallowedQueryKeys( array( 'aal-option-upgrade', 'bounce_url' ) );            
        
        }
          
}