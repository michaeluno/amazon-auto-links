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
 * A scratch class for HTTP request cache database.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_Database_aal_request_cache extends AmazonAutoLinks_Scratch_Base {

    /**
     * @return mixed|string
     * @purpose The table engine must be `InnoDB`.
     * @tags engine
     */
    public function scratch_engineType() {
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        return $_oTable->getTableStatus();
    }

    /**
     * @purpose Shows cache with the given argument. Set a cache name in the first argument input field.
     * @tags cache
     * @throws Exception
     */
    public function scratch_showCacheByCacheName() {
        $_sCacheName = trim( func_get_arg( 0 ) );
        if ( ! $_sCacheName ) {
            throw new Exception( 'A cache name is not passed. Set a cache name in the first argument input field.' );
        }
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $this->_outputDetails( $_oTable->getCache( $_sCacheName ) );
    }

    /**
     * @purpose Searches HTTP request caches by URL up to 1000 items.
     * @tags    search, url, cache name
     * @throws  Exception
     */
    public function scratch_getCacheNamesByURLSubstring() {
        $_aParameters   = func_get_args() + array( null, null );
        $_sURLSubString = trim( $_aParameters[ 0 ] );
        if ( strlen( $_sURLSubString ) <= 5 ) {
            throw new Exception( 'Type more than 5 characters that represents sub-string of a URL to perform search.' );
        }
        $_sRequestType  = trim( $_aParameters[ 1 ] );
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_sQuery        = "SELECT name, request_uri, type"
            . " FROM `{$_oTable->aArguments[ 'table_name' ]}`"
            . " WHERE request_uri LIKE '%s'"
            . ( $_sRequestType ? " AND type = '%s'" : '' )
            . " LIMIT 1000;";
        /**
         * @var wpdb $_oWPDB
         */
        $_oWPDB   = $GLOBALS[ 'wpdb' ];
        $_aParams = array( $_sQuery, '%' . $_sURLSubString . '%' );
        if (  $_sRequestType ) {
            $_aParams[] = $_sRequestType;
        }
        $_sQuery  = call_user_func_array( array( $_oWPDB, 'prepare' ), $_aParams );
        $this->_outputDetails( $_oTable->getCachesByQuery( $_sQuery ) );
    }

}