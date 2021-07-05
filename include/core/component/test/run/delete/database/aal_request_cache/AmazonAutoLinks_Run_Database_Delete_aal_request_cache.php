<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A scratch class to delete a database table.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.5
*/
class AmazonAutoLinks_Run_Database_Delete_aal_request_cache extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Deletes the task table and version option.
     * @return  boolean
     */
    public function scratch_uninstall() {
        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache();
        $_oTaskTable->uninstall();
        return ! $_oTaskTable->tableExists();
    }

}