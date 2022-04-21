<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Loads the output component
 *
 * @since 5.2.6
*/
class AmazonAutoLinks_Main_Output_Loader {

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_output', array( $this, 'replyToGetOutput' ), 10, 2 );
        add_action( 'aal_action_output', array( $this, 'replyToPrintOutput' ) );
    }

    /**
     * Returns the final output of Amazon product links generated by the plugin.
     * @since  5.2.6
     * @return string
     */
    function replyToGetOutput( $sOutput, $aArguments ) {
        return AmazonAutoLinks_Output::getInstance( $aArguments )->get();
    }

    /**
     * Prints the final output of Amazon product links generated by the plugin.
     * @since  5.2.2
     */
    public function replyToPrintOutput( $aArguments ) {
        AmazonAutoLinks_Output::getInstance( $aArguments )->render();
    }

}