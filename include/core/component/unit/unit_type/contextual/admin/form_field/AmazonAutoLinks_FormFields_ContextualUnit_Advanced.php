<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno; Licensed GPLv2
 *
 */

/**
 * Provides the definitions of form fields for the advanced section of the 'contextual' unit type.
 *
 * @since           4.14.0
 */
class AmazonAutoLinks_FormFields_ContextualUnit_Advanced extends AmazonAutoLinks_FormFields_SearchUnit_ProductSearch {

    /**
     * Returns field definition arrays.
     *
     * Pass an empty string to the parameter for meta box options.
     *
     * @return      array
     */
    public function get( $sFieldIDPrefix='', $aUnitOptions=array()  ) {

        $_aFields           = array();
        $_aSearchUnitFields = parent::get( $sFieldIDPrefix );

        // Remove unnecessary fields
        foreach( $_aSearchUnitFields as $_iIndex => $_aFieldset ) {
            if ( ! isset( $_aFieldset[ 'field_id' ] ) ) {
                continue;
            }
            $_sFieldID = $_aFieldset[ 'field_id' ];
            if ( $this->hasSuffix( 'SearchIndex', $_sFieldID ) ) {
                $_aFields[] = $_aFieldset;
                continue;
            }
            if ( $this->hasSuffix( 'Sort', $_sFieldID ) ) {
                $_aFields[] = $_aFieldset;
                continue;
            }
        }

        $_oOption     = $this->oOption;
        $_bIsAllowed  = $_oOption->isAdvancedContextualAllowed();
        if ( ! $_bIsAllowed ) {
            $_aFields = AmazonAutoLinks_Unit_Admin_Utility::getFieldsDisabled( $_aFields );
        }

        return $_aFields;

    }

}