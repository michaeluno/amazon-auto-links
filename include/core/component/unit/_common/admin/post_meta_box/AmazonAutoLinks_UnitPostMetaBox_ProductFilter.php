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
 * Defines the meta box that contains product filter unit options.
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
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        return $aInputs;
    }

}