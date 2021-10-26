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
 * Tests for the plugin database.
 *  
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_DatabaseTable_Base extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @return mixed|string
     * @purpose To set rows.
     * @tags rows
     */
    public function test_setRows() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_aRows  = array(
            array(
                'product_id'            => 'B00VLN9IC6|IT|EUR|it_IT',
                'asin_locale'           => 'B00VLN9IC6_IT',
                'asin'                  => 'B00VLN9IC6',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'TESTING ROW 1'
            ),
            array(
                'product_id'            => 'XXXXXXXXXX|IT|EUR|it_IT',
                'asin_locale'           => 'XXXXXXXXXX_IT',
                'asin'                  => 'XXXXXXXXXX',
                'locale'                => 'IT',
                'language'              => 'it_IT',
                'preferred_currency'    => 'EUR',
                'title'                 => 'TESTING ROW 2'
            ),
        );
        return $_oTable->setRows( $_aRows );
    }
    /**
     * @return mixed|string
     * @purpose To get rows.
     * @tags rows
     */
    public function test_getRows() {
        $_aProductIDs = array( 'B00VLN9IC6|IT|EUR|it_IT', 'XXXXXXXXXX|IT|EUR|it_IT' );
        return $this->___getRowsByProductID( $_aProductIDs );
    }
        private function ___getRowsByProductID( array $aProductIDs, $cCallable=null ) {
            $_sProductIDs = "('" . implode( "','", $aProductIDs ) . "')";
            $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
            $_sQuery = "SELECT * "
                . "FROM `" . $_oTable->getTableName() . "` "
                . "WHERE product_id in {$_sProductIDs}";
            return $_oTable->getRows( $_sQuery, 'ARRAY_A', $cCallable );
        }
    /**
     * @purpose To check if setting a callback functions.
     * @tags rows
     */
    public function test_getRowsWithCallback() {
        $_aProductIDs = array( 'B00VLN9IC6|IT|EUR|it_IT', 'XXXXXXXXXX|IT|EUR|it_IT' );
        return $this->___getRowsByProductID( $_aProductIDs, array( $this, 'replyToFormatRow' ) );
    }
        public function replyToFormatRow( array $aRow, &$aRows, $asIndex, &$aNewRows ) {
            unset( $aRows[ $asIndex ] );
            $_sASIN              = $aRow[ 'asin' ];
            $_sLocale            = $aRow[ 'locale' ];
            $_sCurrency          = $aRow[ 'preferred_currency' ];
            $_sLanguage          = $aRow[ 'language' ];
            $_sKey               = "{$_sASIN}|{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
            $aNewRows[ $_sKey ]  = $aRow;
        }

    /**
     * @tags rows
     */
    public function test_deleteRows() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_oTable->delete( array( 'product_id' => 'B00VLN9IC6|IT|EUR|it_IT' ) );
        $_oTable->delete( array( 'product_id' => 'XXXXXXXXXX|IT|EUR|it_IT' ) );
        $_aRows = $this->___getRowsByProductID( array( 'B00VLN9IC6|IT|EUR|it_IT', 'XXXXXXXXXX|IT|EUR|it_IT' ) );
        return empty( $_aRows );
    }

}