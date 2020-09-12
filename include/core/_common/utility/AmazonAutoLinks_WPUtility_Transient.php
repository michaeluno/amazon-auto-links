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
     * @param   array|string    $asPrefixes
     * @retuen  void
     */
    public static function cleanTransients( $asPrefixes=array( 'AAL' ) ) {    

        // This method also serves for the deactivation callback and in that case, an empty value is passed to the first parameter.
        $_aPrefixes = is_array( $asPrefixes )
            ? $asPrefixes
            : ( array ) $asPrefixes;
        
        foreach( $_aPrefixes as $_sPrefix ) {
            $GLOBALS[ 'wpdb' ]->query( "DELETE FROM `" . $GLOBALS[ 'table_prefix' ] . "options` WHERE `option_name` LIKE ( '_transient_%{$_sPrefix}%' )" );
            $GLOBALS[ 'wpdb' ]->query( "DELETE FROM `" . $GLOBALS[ 'table_prefix' ] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$_sPrefix}%' )" );
        }
    
    }

    /**
     * Retrieve the transient value directly from the database.
     *
     * Similar to the built-in get_transient() method but this one does not use the stored cache in the memory.
     * Used for checking a lock in a sub-routine that should not run simultaneously.
     *
     * @see wp-cron.php
     * @see _get_cron_lock()
     * @param   string  $sTransientKey
     * @sicne   4.2.10
     * @return  mixed|false `false` on failing to retrieve the transient value.
     */
    static public function getTransientWithoutCache( $sTransientKey ) {

        if ( wp_using_ext_object_cache() ) {
            // Skip local cache and force re-fetch of doing_cron transient in case
            // another processes updated the cache
            return wp_cache_get( $sTransientKey, 'transient', true );
        }

        $_oRow = $GLOBALS[ 'wpdb' ]->get_row(
            $GLOBALS[ 'wpdb' ]->prepare(
                "SELECT option_value FROM {$GLOBALS[ 'wpdb' ]->options} WHERE option_name = %s LIMIT 1",
                '_transient_' . $sTransientKey
            )
        );
        return is_object( $_oRow ) ? $_oRow->option_value: false;

    }


}