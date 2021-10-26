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
 * Inserts a debug output below each product output.
 * @since   4.4.0
 */
class AmazonAutoLinks_Unit_Event_Filter_UnitOutput_Warning extends AmazonAutoLinks_PluginUtility {

    /**
     * @since  4.4.0
     */
    public function __construct() {
        add_filter( 'aal_filter_unit_output', array( $this, 'replyToInsertWarning' ), 10, 2 );
    }

    /**
     * @param  string $sOutput
     * @param  array  $aUnitOptions
     * @return string
     * @since  4.4.0
     */
    public function replyToInsertWarning( $sOutput, $aUnitOptions ) {
        return $this->___getWarnings( $aUnitOptions ) . $sOutput;
    }
        /**
         * @param  array $aUnitOptions
         * @return string
         * @since  4.4.0
         */
        private function ___getWarnings( array $aUnitOptions ) {
            if ( $this->getElement( $aUnitOptions, array( 'associate_id' ) ) ) {
                return '';
            }
            $_iShowErrorMode = ( integer ) $this->getElement( $aUnitOptions,array( 'show_errors' ), 1 );
            $_iShowErrorMode = ( integer ) apply_filters( 'aal_filter_unit_show_error_mode', $_iShowErrorMode, $aUnitOptions );
            if ( ! $_iShowErrorMode ) {
                return '';
            }
            $_sMessage = AmazonAutoLinks_Registry::NAME . ' ' . __( 'The Associate tag is not set. Please check your unit settings.', 'amazon-auto-links' );
            return 2 === $_iShowErrorMode
                ? "<!-- "
                    . AmazonAutoLinks_Registry::NAME. ': ' . $_sMessage
                  . " -->"
                : "<div class='warning'><p>"
                    . AmazonAutoLinks_Registry::NAME. ': ' . $_sMessage
                  . "</p></div>";
        }

}