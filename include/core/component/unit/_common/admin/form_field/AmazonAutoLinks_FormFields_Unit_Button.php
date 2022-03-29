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
 * Provides the definitions of form fields.
 *
 * @since 5.2.0
 */
class AmazonAutoLinks_FormFields_Unit_Button extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Sets up properties and hooks.
     */
    protected function _construct() {
        if ( ! isset( $this->oFactory ) ) {
            return;
        }
        add_filter( 'field_definition_' . $this->oFactory->oProp->sClassName . '_button_id', array( $this, 'replyToSetActiveButtonLabels' ) );
    }

    /**
     * Returns field definition arrays.
     *
     * @param   string $sFieldIDPrefix
     * @return  array
     */
    public function get( $sFieldIDPrefix='' ) {
        return apply_filters( 'aal_filter_admin_unit_fields_buttons_in_unit_definition', array(), $this->oFactory );
    }

    /**
     * Modifies the 'button_id' field to add labels for selection.
     * @since  3.3.0
     * @since  5.2.0 Moved from `AmazonAutoLinks_UnitPostMetaBox_CommonAdvanced`.
     * @return array
     */
    public function replyToSetActiveButtonLabels( $aFieldset ) {
        $aFieldset[ 'label' ] = $this->getActiveButtonLabelsForFields();
        return $aFieldset;
    }

}