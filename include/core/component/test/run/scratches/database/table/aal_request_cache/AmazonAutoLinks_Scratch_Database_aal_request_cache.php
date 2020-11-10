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
    public function scratch_showCache() {
        $_sCacheName = trim( func_get_arg( 0 ) );
        if ( ! $_sCacheName ) {
            throw new Exception( 'A cache name is not passed. Set a cache name in the first argument input field.' );
        }
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $this->_outputDetails( $_oTable->getCache( $_sCacheName ) );
    }

}