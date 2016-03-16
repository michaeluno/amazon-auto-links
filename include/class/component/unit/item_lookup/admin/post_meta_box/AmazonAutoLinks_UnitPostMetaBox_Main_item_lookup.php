<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Defines the meta box,
 */
class AmazonAutoLinks_UnitPostMetaBox_Main_item_lookup extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 
        'item_lookup',
    );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {

// @todo Set unit options as they need to be parsed.
$_aUnitOptions = array();
        $_oFields = new AmazonAutoLinks_FormFields_ItemLookupUnit_Main;
        foreach( $_oFields->get( '', $_aUnitOptions ) as $_aField ) {
            if ( 'unit_title' === $_aField[ 'field_id' ] ) {
                continue;
            }
            $this->addSettingFields( $_aField );
        }
                    
    }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {    

        // 3.4.0+ Find ASINs from the user input.
        $aInputs[ 'ItemId' ] = $this->_getItemIdSanitized( $aInputs, $oFactory );
    
        // Formats the options
        $_oUnitOption = new AmazonAutoLinks_UnitOption_item_lookup(
            null,
            $aInputs
        );
        $_aFormatted = $_oUnitOption->get();
        
        // Drop unsent keys.
        foreach( $_aFormatted as $_sKey => $_mValue ) {
            if ( ! array_key_exists( $_sKey, $aInputs ) ) {
                unset( $_aFormatted[ $_sKey ] );
            }
        }
        
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInputs !== $aOriginal ) {
            AmazonAutoLinks_Event_Scheduler::prefetch( 
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }        
        
        return $_aFormatted + $aInputs;
        
    }
    
        /**
         * @since       3.4.0
         * @return      string
         */
        private function _getItemIdSanitized( $aInputs, $oFactory ) {
            
            $_sIdType = $oFactory->oUtil->getElement( $aInputs, array( 'IdType' ), '' );
            $_sItemId = $oFactory->oUtil->getElement( $aInputs, array( 'ItemId' ), '' );
            
            if ( 'ASIN' !== $_sIdType ) {
                return $_sItemId;
            }
            return AmazonAutoLinks_PluginUtility::getASINsExtracted( $_sItemId, PHP_EOL );
            
        }    
    
}