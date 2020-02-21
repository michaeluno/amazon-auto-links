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

        $_oFields = new AmazonAutoLinks_FormFields_ItemLookupUnit_Main;
        foreach( $_oFields->get() as $_aField ) {
            if ( in_array( $_aField[ 'field_id' ], array( 'unit_title', 'country' ), true ) ) {
                continue;
            }
            $this->addSettingFields( $_aField );
        }
        
        // Callbacks to modify field definitions
        add_filter( 'field_definition_' . $this->oProp->sClassName . '_IdType', array( $this, 'replyToModifyField_IdType' ) );
        add_filter( 'field_definition_' . $this->oProp->sClassName . '_SearchIndex', array( $this, 'replyToModifyField_SearchIndex' ) );
                    
    }
        /**
         * @since       3.4.0       
         * @return      array
         */
        public function replyToModifyField_IdType( $aField ) {
            $_bUPCAllowed  = 'CA' !== $this->oForm->aSavedData[ 'country' ];
            $_bISBNAllowed = 'US' === $this->oForm->aSavedData[ 'country' ];
            return array(
                'label'         => array(
                    'ASIN'  => 'ASIN',
                    'SKU'   => 'SKU',
                    'UPC'   => '<span class="' . ( $_bUPCAllowed ? "" : "disabled" ) . '">UPC <span class="description">(' . __( 'Not available in the CA locale.', 'amazon-auto-links' ) . ')</span></span>',
                    'EAN'   => 'EAN',
                    'ISBN'  => '<span class="' . ( $_bISBNAllowed ? "" : "disabled" ) . '">ISBN <span class="description">(' . __( 'The US locale only, when the search index is Books.', 'amaozn-auto-links' ) .')</span></span>',
                ),
                'attributes' => array(              
                    'UPC' => array(
                        'disabled' => $_bUPCAllowed 
                            ? null 
                            : 'disabled',
                    ),                
                    'ISBN' => array(
                        'disabled' => $_bISBNAllowed 
                            ? null 
                            : 'disabled',
                    ),                                    
                ),
            ) + $aField;
            
        }
        
        /**
         * @return      array
         * @since       3.4.0
         */
        public function replyToModifyField_SearchIndex( $aField ) {
            return array(            
                'label'         => AmazonAutoLinks_Property::getSearchIndexByLocale( 
                    isset( $this->oForm->aSavedData[ 'country' ] ) 
                        ? $this->oForm->aSavedData[ 'country' ] 
                        : null 
                    ),            
            ) + $aField;
        }
    
    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {    

        // 3.4.0+ Find ASINs from the user input.
        $aInputs[ 'ItemId' ] = $this->___getItemIdSanitized( $aInputs, $oFactory );
    
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
        private function ___getItemIdSanitized( $aInputs, $oFactory ) {

            $_sItemId = $oFactory->oUtil->getElement( $aInputs, array( 'ItemId' ), '' );
            return AmazonAutoLinks_PluginUtility::getASINsExtracted( $_sItemId, PHP_EOL );
            
        }    
    
}