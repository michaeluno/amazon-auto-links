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
 * @since       3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_ProductFilter extends AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_sNestedSectionID = 'product_filters';
        $this->addSettingSections(
            array(
                'section_id'    => $this->_sSectionID,
                'content'       => array(
                    array(
                        'section_id'    => $_sNestedSectionID,
                        'tip'           => array(
                            __( 'Set the criteria to filter fetched items.', 'amazon-auto-links' ),
                        ),
                    )
                )
            )
        );        
              
        // Set the target section.
        $this->addSettingFields( array( $this->_sSectionID, $_sNestedSectionID ) );
        
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
