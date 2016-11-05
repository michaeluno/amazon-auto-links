<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Echoes or returns the output of Amazon product links.
 * @since       1
 * @since       3       Added the second parameter to let the user choose whether it should echo or return the output.
 */
function AmazonAutoLinks( $aArguments, $bEcho=true ) {

    if ( $bEcho ) {
        AmazonAutoLinks_Output::getInstance( $aArguments )->render();
        return;
    }
    
    return AmazonAutoLinks_Output::getInstance( $aArguments )->get();
        
}