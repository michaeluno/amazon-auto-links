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
 *  Returns field definitions for button settings for the unit definition editing screen.
 *  
 *  @since 5.2.0
 */
class AmazonAutoLinks_Button_Event_Filter_FieldsetsUnitDefinition extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_admin_unit_fields_buttons_in_unit_definition', array( $this, 'replyToGetFields' ), 10, 2 );
    }

    /**
     * @since  5.2.0
     * @param  array $aFields
     * @param  AmazonAutoLinks_AdminPageFramework_Factory $oFactory
     * @return array
     */
    public function replyToGetFields( $aFields, $oFactory ) {
        $_oField = new AmazonAutoLinks_FormFields_Button_Selector( $oFactory );
        return array_merge( $_oField->get(), $aFields );
    }

}