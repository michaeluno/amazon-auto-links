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
 * A class that filters out items that are not eligible for free shipping.
 *
 * @since   3.10.0
 */
class AmazonAutoLinks_UnitOutput__ProductFilter_ByFreeShipping extends AmazonAutoLinks_UnitOutput__ProductFilter_Base {

    /**
     * @return boolean
     */
    protected function _shouldProceed() {
        if ( ! $this->_oUnitOutput->bDBTableAccess ) {
            return false;
        }
        $_sCurrentVersion = get_option( "aal_products_version", '0' );
        if ( ! version_compare( $_sCurrentVersion, '1.3.0b01', '>=')) {
            return false;
        }
        return ( boolean ) $this->_oUnitOutput->oUnitOption->get( '_filter_by_free_shipping' );
    }

    /**
     *
     * @param $aProduct
     * @param $aRow
     * @param $aRowIdentifier
     * @callback    add_filter  aal_filter_unit_each_product_with_database_row
     * @return      array       The filtered product definition array. If it does not meet the user-set criteria, an empty array will be returned to be filtered out.
     */
    public function replyToFilterProduct( $aProduct, $aRow, $aRowIdentifier ) {

        // Case: already set. Feed units can have this value already.
        if ( isset( $aProduct[ 'delivery_free_shipping' ] ) ) {
            return ( boolean ) $aProduct[ 'delivery_free_shipping' ]
                ? $aProduct
                : array();
        }

        $_oRow = new AmazonAutoLinks_UnitOutput___Database_Product(
            $aRowIdentifier[ 'asin' ],
            $aRowIdentifier[ 'locale' ],
            $aRowIdentifier[ 'associate_id' ],
            $aRow,
            $this->_oUnitOutput->oUnitOption
        );
        return ( boolean ) $_oRow->getCell( 'delivery_free_shipping' )
            ? $aProduct
            : array();

    }

}