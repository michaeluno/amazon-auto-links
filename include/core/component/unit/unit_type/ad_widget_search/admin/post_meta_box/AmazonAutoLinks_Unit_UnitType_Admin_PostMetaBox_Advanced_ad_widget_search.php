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
 * Defines the meta box added to the category unit definition page.
 * @since 5.2.0
 */
class AmazonAutoLinks_Unit_UnitType_Admin_PostMetaBox_Advanced_ad_widget_search extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'ad_widget_search' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        $_oFields = new AmazonAutoLinks_FormFields_AdWidgetSearchUnit_Advanced( $this );
        foreach( $_oFields->get() as $_aField ) {
            $this->addSettingFields( $_aField );
        }
    }
    
    /**
     * Validates submitted form data.
     * @return array
     */
    public function validate( $aInputs, $aOldInputs, $oFactory ) {
        return $aInputs;
    }
    
}