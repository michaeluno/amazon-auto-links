<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A scratch class for aal_products database table.
 *  
 * @package     Auto Amazon Links
 * @since       4.4.3
*/
class AmazonAutoLinks_Scratch_Database_delete_aal_products extends AmazonAutoLinks_Scratch_Base {

    /**
     * @tags set
     * @throws Exception
     */
    public function scratch_setProductRow() {
        $_aProductIDs = func_get_args();
        if ( empty( $_aProductIDs ) ) {
            throw new Exception( 'Set a product IDs in the arguments field.' );
        }
        $_aProductIDs = array_filter( $_aProductIDs, 'strlen' );
        $_sInProducts = "('" . implode( "','", $_aProductIDs ) . "')";
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        return $_oTable->getVariable(
            "DELETE FROM `{$_oTable->aArguments[ 'table_name' ]}` "
            . "WHERE product_id IN {$_sInProducts}"     // not using NOW() as NOW() is GMT compatible
        );
    }

}