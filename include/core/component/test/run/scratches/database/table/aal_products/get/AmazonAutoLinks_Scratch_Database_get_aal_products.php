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
 * @since       4.4.3
*/
class AmazonAutoLinks_Scratch_Database_get_aal_products extends AmazonAutoLinks_Scratch_Base {

    /**
     * @tags get
     * @throws Exception
     */
    public function scratch_getProductRow() {
        $_aProductIDs = func_get_args();
        if ( empty( $_aProductIDs ) ) {
            throw new Exception( 'Product IDs must be passed in the argument input field.' );
        }
        $_aProductIDs = array_filter( $_aProductIDs );
        foreach( $_aProductIDs as $_sProductID ) {
            $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
            $_aProduct = $_oTable->getRowsByProductID( array( $_sProductID ) );
            if ( empty( $_aProduct ) ) {
                throw new Exception( 'Failed to retrieve the product data for ' . $_sProductID );
            }
            $this->_outputDetails( $_aProduct );
        }
    }

}