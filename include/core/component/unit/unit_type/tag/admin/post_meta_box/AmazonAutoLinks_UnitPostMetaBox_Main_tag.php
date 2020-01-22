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
 * Defines the meta box added to the 'tag' unit definition page.
 */
class AmazonAutoLinks_UnitPostMetaBox_Main_tag extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'tag' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_TagUnit_Main;
        foreach( $_oFields->get() as $_aField ) {
            if ( 'unit_title' === $_aField[ 'field_id' ] ) {
                continue;
            }
            $this->addSettingFields( $_aField );
        }
        
        // 3.2.0+
        $_sMessage            = esc_attr( __( 'Warning!', 'amazon-auto-links' ) );
        $_sExclamationIconURL = AmazonAutoLinks_Registry::getPluginURL( 'asset/image/exclamationmark_16x16.png' );
        new AmazonAutoLinks_AdminPageFramework_AdminNotice(
            "<img src='{$_sExclamationIconURL}' alt='{$_sMessage}' /> "
            . sprintf(
                __( 'Amazon has deprecated the <a href="%1$s" target="_blank">tags</a> feature. So this is no longer functional.', 'amazon-auto-links' ),
                'https://www.amazon.com/gp/help/customer/display.html?nodeId=16238571'
            )
        );
        
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInput, $aOriginal, $oFactory ) {    
        
        // Formats the options
        $_oUnitOption = new AmazonAutoLinks_UnitOption_tag(
            null,
            $aInput
        );
        $_aFormatted = $_oUnitOption->get();
        
        // Drop unsent keys.
        foreach( $_aFormatted as $_sKey => $_mValue ) {
            if ( ! array_key_exists( $_sKey, $aInput ) ) {
                unset( $_aFormatted[ $_sKey ] );
            }
        }
        
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInput !== $aOriginal ) {
            AmazonAutoLinks_Event_Scheduler::prefetch(
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }
        
        return $_aFormatted + $aInput;
        
    }
    
}