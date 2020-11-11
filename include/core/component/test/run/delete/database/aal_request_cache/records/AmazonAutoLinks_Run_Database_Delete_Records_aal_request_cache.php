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
 * A scratch class to delete a database table records of `aal_request_cache`.
 *  
 * @package     Amazon Auto Links
 * @since       4.4.0
*/
class AmazonAutoLinks_Run_Database_Delete_Records_aal_request_cache extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Deletes specified records of the `aal_request_cache` table.
     * @throws Exception
     */
    public function scratch_deleteRecords() {
        $_aCacheNames = array_filter( func_get_args() );
        if ( empty( $_aCacheNames ) ) {
            throw new Exception( 'Set cache names in the Arguments field.' );
        }
        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache();
        $_oTaskTable->deleteCache( $_aCacheNames );

        $_sNames   = "('" . implode( "','", $_aCacheNames ) . "')";
        $_aResults = $_oTaskTable->getRows(
            "SELECT name"
            . " FROM `{$_oTaskTable->aArguments[ 'table_name' ]}`"
            . " WHERE name in {$_sNames};"
        );
        if ( empty( $_aResults ) ) {
            $this->_outputDetails( 'Deleted: ', $_aCacheNames );
            return; // look good
        }
        throw new Exception( 'The following items could not be deleted: ' . implode( ', ', array_keys( $_aResults ) ) );

    }

}