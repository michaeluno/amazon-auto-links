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
 * Provides an abstract base for adding pages.
 * 
 * @since       3
 */
abstract class AmazonAutoLinks_AdminPage_Page_Base extends AmazonAutoLinks_AdminPage_RootBase {

    /**
     * Stores the factory object.
     */
    public $oFactory;

    /**
     * Stores the associated page slug with the adding section.
     */
    public $sPageSlug;    

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, array $aPageArguments ) {
        
        $this->oFactory     = $oFactory;
        $this->sPageSlug    = $aPageArguments['page_slug'];
        $this->_addPage( $aPageArguments );
        $this->construct( $oFactory );
                
    }
    
    private function _addPage( array $aPageArguments ) {
        
        $this->oFactory->addSubMenuItems(
            $aPageArguments
            + array(
                'page_slug'     => null,
                'title'         => null,
                'screen_icon'   => null,
            )                
        );
        add_action( "load_{$this->sPageSlug}", array( $this, 'replyToSetResources' ) );
        add_action( "load_{$this->sPageSlug}", array( $this, 'replyToLoadPage' ) );
        add_action( "do_{$this->sPageSlug}", array( $this, 'replyToDoPage' ) );
        add_action( "do_after_{$this->sPageSlug}", array( $this, 'replyToDoAfterPage' ) );
        add_filter( "validation_{$this->sPageSlug}", array( $this, 'validate' ), 10, 4 );
        
    }
    
    /**
     * @callback    action      load_{page slug}
     */
    public function replyToSetResources( $oFactory ) {
        
        $this->oFactory->enqueueStyle( 
            AmazonAutoLinks_Registry::getPluginURL( 'asset/css/' . $this->sPageSlug . '.css' ),
            $this->sPageSlug
        );            
        
    }
    
    /**
     * Called when the page loads.
     * 
     * @remark      This method should be overridden in each extended class.
     */
    // public function replyToLoadPage( $oFactory ) {}
    // public function replyToDoPage( $oFactory ) {}
    // public function replyToDoAfterPage( $oFactory ) {}
    // public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ){}
    
}