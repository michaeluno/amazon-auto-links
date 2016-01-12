<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Defines the meta box that contains common advanced unit options.
 */
class AmazonAutoLinks_UnitPostMetaBox_ProductFilter extends AmazonAutoLinks_UnitPostMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_sSectionID = 'product_filters';
        
        $this->addSettingSections(
            array(
                'section_id'    => $_sSectionID,
                'tip'           => array(
                    __( 'Set the criteria to filter fetched items.', 'amazon-auto-links' ),
                ),
            )
        );
        
        // Set the target section.
        $this->addSettingFields( $_sSectionID );        
        
        $_aClassNames = array(
            'AmazonAutoLinks_FormFields_ProductFilter',
            'AmazonAutoLinks_FormFields_ProductFilter_Image',
        );
        $this->_addFieldsByClasses( $_aClassNames );
                    
    }
        /**
         * Adds form fields.
         * @since       3.1.0
         */
        private function _addFieldsByClasses( $aClassNames ) {   
            foreach( $aClassNames as $_sClassName ) {
                $_oFields = new $_sClassName;
                foreach( $_oFields->get() as $_aField ) {
                    $this->addSettingFields( $_aField );
                }   
            }                 
        }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        return $aInput;        
    }
    
}