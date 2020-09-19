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
     * @param  $sTransientKey
     * @param  mixed $mDefault
     * @return array
     * @since  4.3.0
     */
    static public function getTransientAsArray( $sTransientKey, $mDefault=null ) {
        return self::getAsArray(
            self::getTransient( $sTransientKey, $mDefault )
        );
    }

    /**
     * @param $sTransientKey
     * @param null $mDefault
     * @return array
     */
    static public function getTransientWithoutCacheAsArray( $sTransientKey, $mDefault=null ) {
        return self::getAsArray(
            self::getTransientWithoutCache( $sTransientKey, $mDefault )
        );
    }

    /**
     * Retrieve the transient value directly from the database.
     *
     * Similar to the built-in get_transient() method but this one does not use the stored cache in the memory.
     * Used for checking a lock in a sub-routine that should not run simultaneously.
     *
     * @param   string  $sTransientKey
     * @param   mixed   $mDefault
     * @sicne   4.2.10
     * @since   4.3.0   Added the `$mDefault` parameter.
     * @return  mixed|false `false` on failing to retrieve the transient value.
     */
    static public function getTransientWithoutCache( $sTransientKey, $mDefault=null ) {

        /**
         * @var wpdb $_oWPDB
         */
        $_oWPDB         = $GLOBALS[ 'wpdb' ];
        $_sTableName    = $_oWPDB->options;
        $_sSQLQuery     = "SELECT o1.option_value FROM `{$_sTableName}` o1"
            . " INNER JOIN `{$_sTableName}` o2"
            . " WHERE o1.option_name = %s "
            . " AND o2.option_name = %s "
            . " AND o2.option_value >= UNIX_TIMESTAMP() " // timeout value >= current time
            . " LIMIT 1";
        $_mData = $_oWPDB->get_var(
            $_oWPDB->prepare(
                $_sSQLQuery,
                '_transient_' . $sTransientKey,
                '_transient_timeout_' . $sTransientKey
            )
        );
        return is_null( $_mData )
            ? $mDefault
            : maybe_unserialize( $_mData );

    }


}