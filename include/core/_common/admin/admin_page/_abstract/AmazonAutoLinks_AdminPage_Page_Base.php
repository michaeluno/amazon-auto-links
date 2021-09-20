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
 * Provides an abstract base for adding pages.
 * 
 * @since 3
 */
abstract class AmazonAutoLinks_AdminPage_Page_Base extends AmazonAutoLinks_AdminPage_RootBase {

    /**
     * Stores the factory object.
     * @var AmazonAutoLinks_AdminPageFramework
     */
    public $oFactory;

    /**
     * Stores the associated page slug with the adding section.
     */
    public $sPageSlug;

    /**
     * @var array
     * @since 4.7.9
     */
    public $aArguments = array();

    /**
     * Sets up hooks and properties.
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @param array $aPageArguments
     */
    public function __construct( $oFactory, array $aPageArguments=array() ) {
        
        $this->oFactory     = $oFactory;
        $this->aArguments   = $this->_getArguments() + $aPageArguments + array(
            'page_slug'     => null,
            'title'         => null,
            'screen_icon'   => null,
        );
        $this->sPageSlug    = $this->aArguments[ 'page_slug' ];
        $this->___addPage( $this->aArguments );
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
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action()      load_{page slug}
     */
    public function replyToSetResources( $oFactory ) {

        $_sPageStyleSheetPath = $this->isDebugMode()
            ? AmazonAutoLinks_Registry::$sDirPath . '/asset/css/' . $this->sPageSlug . '.css'
            : AmazonAutoLinks_Registry::$sDirPath . '/asset/css/' . $this->sPageSlug . '.min.css';
        if ( file_exists( $_sPageStyleSheetPath ) ) {
            $this->oFactory->enqueueStyle( $_sPageStyleSheetPath, $this->sPageSlug );
        }

    }
    
    /**
     * Called when the page loads.
     *
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
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
     * Page styling
     * @since   4.4.0
     * @remark  Override this method in an extended class/
     * @param   AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _doPageSettings( $oFactory ) {

        $oFactory->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $oFactory->setInPageTabTag( 'h2' );
        $oFactory->setPluginSettingsLinkLabel( '' ); // pass an empty string to disable it.

    }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @since 3.7.9
     */
    protected function _loadPage( $oFactory ) {}
    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @since 3.7.9
     */
    protected function _doPage( $oFactory ) {}
    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @since 3.7.9
     */
    protected function _doAfterPage( $oFactory ) {}

}