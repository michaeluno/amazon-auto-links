<?php
/**
 *    Deals with WordPress transients.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0 
 */

class AmazonAutoLinks_WPUtilities_Transient extends AmazonAutoLinks_Utilities {

    /**
     * 
     * @since   2.0.0
     * @since   2.0.7       Moved from `AmazonAutoLinks_Transients`.
     */
    public static function cleanTransients( $vPrefixes=array() ) {    // for the deactivation hook.

        // This method also serves for the deactivation callback and in that case, an empty value is passed to the first parameter.        
        $arrPrefixes = empty( $arrPrefixes ) ? array( AmazonAutoLinks_Commons::TransientPrefix ) : ( array ) $vPrefixes;
        
        foreach( $arrPrefixes as $strPrefix ) {
            $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_%{$strPrefix}%' )" );
            $GLOBALS['wpdb']->query( "DELETE FROM `" . $GLOBALS['table_prefix'] . "options` WHERE `option_name` LIKE ( '_transient_timeout_%{$strPrefix}%' )" );
        }
    
    }
    
    /**
     * Stores whether the page is loaded in the network admin or not.
     * @since 2.0.7
     */
    static private $_bIsNetworkAdmin;
    
    /**
     * Deletes the given transient.
     *
     * @since 2.0.7
     */
    static public function deleteTransient( $sTransientKey ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();

        $_vTransient = ( self::$_bIsNetworkAdmin ) ? delete_site_transient( $sTransientKey ) : delete_transient( $sTransientKey );

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return $_vTransient;

    }
    /**
     * Retrieves the given transient.
     * 
     * @since 2.0.7
     */    
    static public function getTransient( $sTransientKey, $vDefault=null ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();

        $_vTransient = ( self::$_bIsNetworkAdmin ) ? get_site_transient( $sTransientKey ) : get_transient( $sTransientKey );    

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return null === $vDefault 
            ? $_vTransient
            : ( false === $_vTransient 
                ? $vDefault
                : $_vTransient
            );
        
    }
    /**
     * Sets the given transient.
     * @since 2.0.7
     */
    static public function setTransient( $sTransientKey, $vValue, $iExpiration=0 ) {

        // temporarily disable $_wp_using_ext_object_cache
        global $_wp_using_ext_object_cache;  
        $_bWpUsingExtObjectCacheTemp = $_wp_using_ext_object_cache; 
        $_wp_using_ext_object_cache = false;

        self::$_bIsNetworkAdmin = isset( self::$_bIsNetworkAdmin ) ? self::$_bIsNetworkAdmin : is_network_admin();
        
        // Do not allow 0 because the table row will be autoloaded and it consumes the server memory.
        $iExpiration = 0 == $iExpiration ? 99999 : $iExpiration;
        
        $_vTransient = ( self::$_bIsNetworkAdmin ) 
            ? set_site_transient( $sTransientKey, $vValue, $iExpiration ) 
            : set_transient( $sTransientKey, $vValue, $iExpiration );

        // reset prior value of $_wp_using_ext_object_cache
        $_wp_using_ext_object_cache = $_bWpUsingExtObjectCacheTemp; 

        return $_vTransient;     
    }
    
}