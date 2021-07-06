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
 * A scratch class for HTTP request cache database.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_Database_aal_products extends AmazonAutoLinks_Scratch_Base {

    /**
     * @return mixed|string
     * @purpose The table engine must be `InnoDB`.
     * @tags engine
     */
    public function scratch_engineType() {
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_products;
        return $_oTable->getTableStatus();
    }

}