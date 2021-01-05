<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A scratch class for the database.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_Database extends AmazonAutoLinks_Scratch_Base {

     /**
     * @return bool
     * @tags    innodb
     */
    public function scratch_InnoDBSupport() {
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_sSQLQuery     = "SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE = 'InnoDB';";
        return $_oTable->getVariable( $_sSQLQuery );
//        return in_array( $_sValue, array( 'DEFAULT', 'YES' ), true );
    }

}