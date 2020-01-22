<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

// Make sure that SimplePie has been already loaded. This is very important. Without this line, the cache setting breaks. 
// Do not include class-simplepie.php, which causes an unknown class warning.
if ( ! class_exists( 'WP_SimplePie_File', false ) ) {
    include_once( ABSPATH . WPINC . '/class-feed.php' );
}

/**
 * Extends the SimplePie library. 
 * @since        2.0.0
 * Custom Hooks
 * - aal_action_simplepie_renew_cache : the event action that renew caches in the background.
 * - SimplePie_filter_cache_transient_lifetime_{FileID} : applies to cache transients. FileID is md5( $url ).
 * 
 * Global Variables
 * - $aSimplePieCacheModTimestamps : stores mod timestamps of cached data. This will be stored in a transient when WordPress exits, 
 *   which prevents multiple calls of get_transiet() that performs a database query ( slows down the page load ).
 * - $aSimplePieCacheExpiredItems : stores expired cache items' file IDs ( md5( $url ) ). This will be saved in the transient at the WordPress shutdown action event.
 *   the separate cache renewal event with WP Cron will read it and renew the expired caches.
 * 
 * */
abstract class AmazonAutoLinks_SimplePie_Base_Base extends SimplePie {
    
    /*
     * For sort
     * */
    public static $sortorder        = 'random';
    public static $bKeepRawTitle    = false;
    public static $sCharEncoding    = 'UTF-8';
    
    public function set_sortorder( $sortorder ) {
        self::$sortorder = $sortorder;
    }
    public function set_keeprawtitle( $bKeepRawTitle ) {
        self::$bKeepRawTitle = $bKeepRawTitle;        
    }
    public function set_charset_for_sort( $sCharEncoding ) {
        self::$sCharEncoding = $sCharEncoding;        
    }

    public static function sort_items_by_title( $a, $b ) {
        $a_title = ( self::$bKeepRawTitle ) ? $a->get_title() : preg_replace('/#\d+?:\s?/i', '', $a->get_title());
        $b_title = ( self::$bKeepRawTitle ) ? $b->get_title() : preg_replace('/#\d+?:\s?/i', '', $b->get_title());
        $a_title = html_entity_decode( trim( strip_tags( $a_title ) ), ENT_COMPAT, self::$sCharEncoding );
        $b_title = html_entity_decode( trim( strip_tags( $b_title ) ), ENT_COMPAT, self::$sCharEncoding );
        return strnatcasecmp( $a_title, $b_title );    
    }
    public static function sort_items_by_title_descending( $a, $b ) {
        $a_title = ( self::$bKeepRawTitle ) ? $a->get_title() : preg_replace('/#\d+?:\s?/i', '', $a->get_title());
        $b_title = ( self::$bKeepRawTitle ) ? $b->get_title() : preg_replace('/#\d+?:\s?/i', '', $b->get_title());
        $a_title = html_entity_decode( trim( strip_tags( $a_title ) ), ENT_COMPAT, self::$sCharEncoding );
        $b_title = html_entity_decode( trim( strip_tags( $b_title ) ), ENT_COMPAT, self::$sCharEncoding );
        return strnatcasecmp( $b_title, $a_title );
    }
    
}