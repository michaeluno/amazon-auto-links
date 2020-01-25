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
    public function __construct( $oFactory, array $aPageArguments=array() ) {
        
        $this->oFactory     = $oFactory;
        $_aPageArguments    = $this->_getArguments() + $aPageArguments + array(
            'page_slug'     => null,
            'title'         => null,
            'screen_icon'   => null,
        );
        $this->sPageSlug    = $_aPageArguments[ 'page_slug' ];
        $this->___addPage( $_aPageArguments );
        $this->_construct( $oFactory );
                
    }
    
    private function ___addPage( array $aPageArguments ) {
        
        $this->oFactory->addSubMenuItems( $aPageArguments );
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

        $_sPageStyleSheetPath = AmazonAutoLinks_Registry::$sDirPath . '/asset/css/' . $this->sPageSlug . '.css';
        $this->oFactory->enqueueStyle( $_sPageStyleSheetPath, $this->sPageSlug );
        
    }
    
    /**
     * Called when the page loads.
     *
     */
     public function replyToLoadPage( $oFactory ) {
         $this->_loadPage( $oFactory );
     }
     public function replyToDoPage( $oFactory ) {
         $this->_doPage( $oFactory );
     }
     public function replyToDoAfterPage( $oFactory ) {
         $this->_doAfterPage( $oFactory );
     }


    /**
     * @param $oFactory
     * @since   3.7.9
     */
    protected function _loadPage( $oFactory ) {}
    protected function _doPage( $oFactory ) {}
    protected function _doAfterPage( $oFactory ) {}


}