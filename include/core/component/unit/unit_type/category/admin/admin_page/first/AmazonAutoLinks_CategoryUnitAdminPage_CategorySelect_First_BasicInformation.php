<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the 'Category' form section to the 'Add Unit by Category' tab.
 * 
 * @since 3
 */
class AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_First_BasicInformation extends AmazonAutoLinks_AdminPage_Section_Base {
    
    /**
     * A user constructor.
     * @since  3
     */
    protected function _construct( $oFactory ) {
        add_filter(
            "validation_{$this->sPageSlug}_{$this->sTabSlug}",
            array( $this, 'validateTabForm' ),
            5,  // higher priority
            4   // number of parameters
        );    
    }
    
    /**
     * Adds form fields.
     * @since 3
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @param string $sSectionID
     */
    protected function _addFields( $oFactory, $sSectionID ) {
        
        $_oFieldsBasicInformation = new AmazonAutoLinks_FormFields_CategoryUnit_BasicInformation( $oFactory );
        $_oFieldsCommon           = new AmazonAutoLinks_FormFields_Unit_Common( $oFactory );
        $_oFieldsCredit           = new AmazonAutoLinks_FormFields_Unit_Credit( $oFactory );
        $_aFields                 = array_merge(
            array(
                array(
                    'field_id'      => 'unit_title',
                    'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                    'type'          => 'text',
                    'description'   => 'e.g. <code>My Category Unit</code>',
                    'class'         => array(
                        'input' => 'width-full',
                        'field' => 'width-half',
                    ),
                    // the previous value should not appear
                    'value'         => isset( $_GET[ 'transient_id' ] ) // sanitization unnecessary as just checking
                        ? ''
                        : null,
                ),                       
            ),
            $_oFieldsBasicInformation->get(),
            $_oFieldsCommon->get(),
            $_oFieldsCredit->get()
        );
        
        foreach( $_aFields as $_aField ) {
            if ( 'associate_id' === $_aField[ 'field_id' ] ) {
                continue;
            }
            $oFactory->addSettingFields(
                $sSectionID, // the target section id    
                $_aField
            );
        }

    }
           
    
    /**
     * Validates the submitted form data.
     * 
     * @since 3
     */
    public function validateTabForm( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {

        $_oOption   = AmazonAutoLinks_Option::getInstance();

        $aInput[ 'associate_id' ] = $_oOption->getAssociateID( $aInput[ 'country' ] );

        // Evacuate some extra field items.
        $_aInputTemp = $aInput;
            
        // Format the unit options - this will also drop unnecessary keys for units.
        $_oUnitOptions = new AmazonAutoLinks_UnitOption_category(
            null,   // unit id
            $aInput
        );
        return $_oUnitOptions->get() + $_aInputTemp;

    }   
    
}