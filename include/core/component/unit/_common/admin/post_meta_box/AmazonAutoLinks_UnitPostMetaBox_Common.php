<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Defines the meta box that contains common advanced unit options.
 * 
 * @since       3.1.0
 */
class AmazonAutoLinks_UnitPostMetaBox_Common extends AmazonAutoLinks_UnitPostMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_aClasses = array(
            'AmazonAutoLinks_FormFields_Unit_Common',
        );
        foreach( $_aClasses as $_sClassName ) {
            $_oFields = new $_sClassName( $this );
            $_aFields = $_oFields->get();
            foreach( $_aFields as $_aField ) {           
                $this->addSettingFields( $_aField );
            }            
        }            
        
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {
        $aInputs[ 'link_style_custom_path' ]        = filter_var( $aInputs[ 'link_style_custom_path' ], FILTER_SANITIZE_URL);
        $aInputs[ 'link_style_custom_path' ]        = untrailingslashit( $aInputs[ 'link_style_custom_path' ] );
        $aInputs[ 'link_style_custom_path_review' ] = filter_var( $aInputs[ 'link_style_custom_path_review' ], FILTER_SANITIZE_URL);
        $aInputs[ 'link_style_custom_path_review' ] = untrailingslashit( $aInputs[ 'link_style_custom_path_review' ] );
        return $aInputs;
    }
    
}