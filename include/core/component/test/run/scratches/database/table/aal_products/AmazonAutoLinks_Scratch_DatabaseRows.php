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
class AmazonAutoLinks_Scratch_DatabaseRows extends AmazonAutoLinks_Scratch_Base {

    /**
     * @tags product, row
     * @throws Exception
     */
    public function scratch_getProductRow() {
        $_aProductIDs = func_get_args();
        if ( empty( $_aProductIDs ) ) {
            throw new Exception( 'Product IDs must be passed in the argument input field.' );
        }
        foreach( $_aProductIDs as $_sProductID ) {
            $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
            $this->_outputDetails( $_oTable->getRowsByProductID( array( $_sProductID ) ) );
        }
    }

}