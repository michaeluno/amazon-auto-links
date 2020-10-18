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
 * Captures an error of unknown unit types.
 *
 * @since        4.3.5
 */
class AmazonAutoLinks_Main_Event_Filter_Debug_UnknownUnitType extends AmazonAutoLinks_PluginUtility {


    public function __construct() {

        $_oOption  = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isDebug() ) {
            return;
        }
        add_filter( 'aal_filter_unit_output_unknown', array( $this, 'replyToUnknownUnitTypeOutputs' ), 10, 2 );

    }

    /**
     * Called when the unit type is unknown.
     * @param  string $sOutput
     * @param  array  $aRawUnitOptions
     * @return string
     * @since  4.0.0
     */
    public function replyToUnknownUnitTypeOutputs( $sOutput, array $aRawUnitOptions ) {
        return $sOutput
            . "<h3>" . __( 'Debug', 'amazon-auto-links' ) . "</h3>"
                . "<h4>" . __( 'Unit Arguments', 'amazon-auto-links' ) . "</h4>"
                . "<div>"
                    . AmazonAutoLinks_Debug::getDetails( $aRawUnitOptions )
                . "</div>";
    }

}