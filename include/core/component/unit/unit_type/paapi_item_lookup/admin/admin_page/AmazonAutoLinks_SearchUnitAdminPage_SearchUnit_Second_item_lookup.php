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
 * Adds a tab to a setting page.
 * 
 * @since 3
 */
class AmazonAutoLinks_SearchUnitAdminPage_SearchUnit_Second_item_lookup extends AmazonAutoLinks_Unit_UnitType_Admin_Tab_SearchUnit_Second_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'item_lookup',
            'title'         => __( 'Add Unit by PA-API Item Look-up', 'amazon-auto-links' ),
            'description'   => __( 'Create a search unit.', 'amazon-auto-links' ),
        );
    }

    /**
     * @return array
     * @since  3
     * @since  5.0.0 Changed the visibility scope from privated to protected.
     */
    protected function _getFormFieldClasses() {
        return array(
            'AmazonAutoLinks_FormFields_ItemLookupUnit_Main',
            'AmazonAutoLinks_FormFields_Unit_Common',
            'AmazonAutoLinks_FormFields_Unit_Credit',
            'AmazonAutoLinks_FormFields_Unit_AutoInsert',
            'AmazonAutoLinks_FormFields_SearchUnit_CreateButton',
        );
    }
        
    /**
     * @since    3.4.0
     * @callback add_filter() validation_{page slug}_{tab slug}
     */            
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        
        // Find ASINs from the user input.
        $aInputs[ 'ItemId' ] = $this->___getItemIdSanitized( $aInputs, $oFactory );
        
        return parent::validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo );
        
    }
        /**
         * @since  3.4.0
         * @return string
         */
        private function ___getItemIdSanitized( $aInputs, $oFactory ) {
            $_sItemId = $oFactory->oUtil->getElement( $aInputs, array( 'ItemId' ), '' );
            return $this->getASINsExtracted( $_sItemId, PHP_EOL );
        }

}