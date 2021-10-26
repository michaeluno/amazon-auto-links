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
class AmazonAutoLinks_Scratch_Database_set_aal_products extends AmazonAutoLinks_Scratch_Base {

    /**
     * @tags set
     * @throws Exception
     */
    public function scratch_setProductRow() {
        $_aProductIDs = func_get_args();
        if ( empty( $_aProductIDs ) ) {
            throw new Exception( 'Set a product ID in the first parameter, and a title for the second parameter.' );
        }
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_aRows  = array(
            array(
                'product_id'            => func_get_arg( 0 ),
                'title'                 => func_get_arg( 1 ),
            ),
        );
        return $_oTable->setRows( $_aRows );
    }

}