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
 * Echoes or returns the output of Amazon product links.
 * @since      1
 * @since      3           Added the second parameter to let the user choose whether it should echo or return the output.
 * @param      array       $aArguments
 * @param      boolean     $bEcho
 * @return     string|void
 * @deprecated 5.2.2       Use `apply_filters( 'aal_filter_output', '', $arguments );` This way, even if the plugin is deactivated, an error will not occur.
 */
function AmazonAutoLinks( $aArguments, $bEcho=true ) {
    if ( $bEcho ) {
        AmazonAutoLinks_Output::getInstance( $aArguments )->render();
        return;
    }
    return AmazonAutoLinks_Output::getInstance( $aArguments )->get();
}