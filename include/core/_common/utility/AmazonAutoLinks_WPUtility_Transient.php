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
  * Deals with WordPress transients.
  * @since      2
  * @since      3       Changed the name from `AmazonAutoLinks_WPUtility_Transient`.
  */
class AmazonAutoLinks_WPUtility_Transient extends AmazonAutoLinks_WPUtility_Path {

    /**
     * Deletes transient items by prefix of a transient key.
     * 
     * @since   2.0.0
     * @since   2.0.7       Moved from `AmazonAutoLinks_Transients`.
     * @remark  for the deactivation hook. Also used by the Clear Caches submit button.
     */
    public static function cleanTransients( $asPrefixes=array( 'AAL' ) ) {    

        // This method also serves for the deactivation callback and in that case, an empty value is passed to the first parameter.
        $_aPrefixes = is_array( $asPrefixes )
            ? $asPrefixes
            : ( array ) $asPrefixes;
        
        foreach( $_aPrefixes as $_sPrefix ) {
            $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_%{$_sPrefix}%' )" );
            $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$_sPrefix}%' )" );
        }
    
    }

}