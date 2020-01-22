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
 * Defines the meta box that contains locale unit options.
 * 
 * @since       3.10.0
 */
class AmazonAutoLinks_UnitPostMetaBox_Locale extends AmazonAutoLinks_UnitPostMetaBox_Base {
        
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_aClasses = array(
            'AmazonAutoLinks_FormFields_Unit_Locale',
        );
        foreach( $_aClasses as $_sClassName ) {
            $_oFields = new $_sClassName;
            $_aFields = $_oFields->get();
            foreach( $_aFields as $_aField ) {           
                $this->addSettingFields( $_aField );
            }            
        }            

        add_filter( 'fields_' . $this->oProp->sClassName, array( $this, 'replyToModifyFields' ) );

    }

    public function replyToModifyFields( $aAllFields ) {
        if ( ! $this->_iPostID ) {
            return $aAllFields;
        }
        $_sLocale = get_post_meta( $this->_iPostID, 'country', true );
        $_aFields = $this->oUtil->getElementAsArray( $aAllFields, array( '_default' ) );
        foreach( $_aFields as $_sFieldID => $_aField ) {
            if ( 'language' === $_sFieldID ) {
                $_aFields[ 'language' ][ 'label'   ] = AmazonAutoLinks_PAAPI50___Locales::getLanguagesByLocale( $_sLocale );
                $_aFields[ 'language' ][ 'default' ] = AmazonAutoLinks_PAAPI50___Locales::getLanguagesByLocale( $_sLocale );
                continue;
            }
            if ( 'preferred_currency' === $_sFieldID ) {
                $_aFields[ 'preferred_currency' ][ 'label' ]   = AmazonAutoLinks_PAAPI50___Locales::getCurrenciesByLocale( $_sLocale );
                $_aFields[ 'preferred_currency' ][ 'default' ] = AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale );
                continue;
            }
        }

        $aAllFields[ '_default' ] = $_aFields;
        return $aAllFields;
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {    
        return $aInputs;
    }
    
}