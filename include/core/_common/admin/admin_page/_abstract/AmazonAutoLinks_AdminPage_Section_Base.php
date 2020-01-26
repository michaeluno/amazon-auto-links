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
 * Provides an abstract base for adding form sections.
 * 
 * @since       3
 */
abstract class AmazonAutoLinks_AdminPage_Section_Base extends AmazonAutoLinks_AdminPage_RootBase {

    /**
     * Stores the factory object.
     */
    public $oFactory;

    /**
     * Stores the associated page slug with the adding section.
     */
    public $sPageSlug;    

    /**
     * Stores the associated tab slug with the adding section.
     */
    public $sTabSlug;    

    /**
     * Stores the section ID.
     */
    public $sSectionID;    

    private $___aSection = array(
        'tab_slug'      => '',
        'section_id'    => '',
    );

    /**
     * Sets up hooks and properties.
     */
    public function __construct( $oFactory, $sPageSlug, array $aSectionDefinition=array() ) {
        
        $this->oFactory     = $oFactory;
        $this->sPageSlug    = $sPageSlug;
        $aSectionDefinition = $aSectionDefinition + $this->_getArguments() + $this->___aSection;
        $this->sTabSlug     = $aSectionDefinition[ 'tab_slug' ];
        $this->sSectionID   = $aSectionDefinition[ 'section_id' ];
        
        if ( ! $this->sSectionID ) {
            return;
        }
        $this->_addSection( $oFactory, $sPageSlug, $aSectionDefinition );
        
        $this->_construct( $oFactory );
        
    }

    /**
     * @since   3.11.1
     * @return array
     */
    protected function _getArguments() {
        return array()
               + $this->_getSection(); // for backward compatibility;
    }
        /**
         * @return array
         * @since   3.7.9
         *
         * @deprecated 3.11.1   Use getArguments().
         */
        protected function _getSection() {
            return array();
        }

    private function _addSection( $oFactory, $sPageSlug, array $aSectionDefinition ) {
        
        add_action( 
            'validation_' . $oFactory->oProp->sClassName . '_' . $this->sSectionID,
            array( $this, 'validate' ), 
            10, 
            4 
        );
        
        $oFactory->addSettingSections(
            $sPageSlug,    // target page slug
            $aSectionDefinition
        );        
        
        // Set the target section id
        $oFactory->addSettingFields(
            $this->sSectionID
        );
        
        // Call the user method
        $this->_addFields( $oFactory, $this->sSectionID );

    }

    /**
     * Called when adding fields.
     * @remark      This method should be overridden in each extended class.
     */
    protected function _addFields( $oFactory, $sSectionID ) {}
 
    /**
     * Called upon form validation.
     * 
     * @callback        filter      'validation_{class name}_{section id}'
     */
    public function validate( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
                 
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInput;
        }
                
        return $aInput;     
        
    }        
        
 
}