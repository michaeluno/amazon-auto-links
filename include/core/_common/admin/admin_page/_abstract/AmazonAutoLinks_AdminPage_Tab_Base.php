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
abstract class AmazonAutoLinks_AdminPage_Tab_Base extends AmazonAutoLinks_AdminPage_RootBase {
    
    /**
     * Stores the caller object.
     */
    public $oFactory;

    /**
     * Stores the associated page slug.
     */
    public $sPageSlug;
    
    /**
     * Stores the associated tab slug.
     */
    public $sTabSlug;
    
    /**
     * Stores callback method names.
     */
    protected $_aMethods = array(
        'replyToLoadTab',
        'replyToDoTab',
        'replyToDoAfterTab',
        'validate',
    );

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sPageSlug, array $aTabDefinition=array() ) {
        
        $this->oFactory     = $oFactory;
        $this->sPageSlug    = $sPageSlug;
        $aTabDefinition     = $aTabDefinition
            + $this->_getArguments()
            + $this->_getTab();
        $this->sTabSlug     = isset( $aTabDefinition['tab_slug'] )
            ? $aTabDefinition['tab_slug'] 
            : '';
        
        if ( ! $this->sTabSlug ) {
            return;
        }
                  
        $this->___addTab( $this->sPageSlug, $aTabDefinition );
        $this->_construct( $oFactory );
        
    }
        /**
         * @since   3.7.9
         * @return  array A tab definition array.
         * @deprecated  use `_getArguments()`.
         */
        protected function _getTab() {
            return array();
        }
    
    private function ___addTab( $sPageSlug, $aTabDefinition ) {
        
        $this->oFactory->addInPageTabs(
            $sPageSlug,
            $aTabDefinition + array(
                'tab_slug'          => null,
                'title'             => null,
                'parent_tab_slug'   => null,
                'show_in_page_tab'  => null,
            )
        );
            
        if ( $aTabDefinition[ 'tab_slug' ] ) {
            add_action( 
                "load_{$sPageSlug}_{$this->sTabSlug}",
                array( $this, 'replyToLoadTab' ) 
            );
            add_action( 
                "do_{$this->sPageSlug}_{$this->sTabSlug}", 
                array( $this, 'replyToDoTab' ) 
            );
            add_action( 
                "do_after_{$this->sPageSlug}_{$this->sTabSlug}", 
                array( $this, 'replyToDoAfterTab' ) 
            );      
            add_filter(
                "validation_{$this->sPageSlug}_{$this->sTabSlug}",
                array( $this, 'validate' ),
                10,
                4
            );                  
        }
        
    }

    /**
     * Called when the in-page tab loads.
     * 
     * @remark      This method should be overridden in each extended class.
     */
     public function replyToLoadTab( $oFactory ) {
         $this->_loadTab( $oFactory );
     }
     public function replyToDoTab( $oFactory ) {
         $this->_doTab( $oFactory );
     }
     public function replyToDoAfterTab( $oFactory ) {
         $this->_doAfterTab( $oFactory );
     }

    /**
     * @param $oFactory
     * @since   3.7.9
     */
    protected function _loadTab( $oFactory ) {}
    protected function _doTab( $oFactory ) {}
    protected function _doAfterTab( $oFactory ) {}


//    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
//        return $aInputs;
//    }

}