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
 * Defines the meta box,
 */
class AmazonAutoLinks_UnitPostMetaBox_Main_similarity_lookup extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 
        'similarity_lookup',
    );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_SimilarityLookupUnit_Main;
        foreach( $_oFields->get() as $_aField ) {
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
        $_oUnitOption = new AmazonAutoLinks_UnitOption_similarity_lookup(
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
            
            return AmazonAutoLinks_PluginUtility::getASINsExtracted( 
                $oFactory->oUtil->getElement( $aInputs, array( 'ItemId' ), '' ), 
                PHP_EOL 
            );
            
        }     
    
}