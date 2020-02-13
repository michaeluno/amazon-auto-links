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
 * A class that filters out items that are not Amazon Prime eligible.
 *
 * @since   3.10.0
 */
class AmazonAutoLinks_UnitOutput__ProductFilter_ByPrimeEligibility extends AmazonAutoLinks_UnitOutput__ProductFilter_Base {

    /**
     * @return boolean
     */
    protected function _shouldProceed() {
        if ( ! $this->_oUnitOutput->bDBTableAccess ) {
            return false;
        }
        return ( boolean ) $this->_oUnitOutput->oUnitOption->get( '_filter_by_prime_eligibility' );
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

        // Case: already set. Search units already have this value. Also, feed units can already have this value.
        if ( isset( $aProduct[ 'is_prime' ] ) ) {
            return ( boolean ) $aProduct[ 'is_prime' ]
                ? $aProduct
                : array();
        }

        $_sCurrentVersion = get_option( "aal_products_version", '0' );
        if ( ! version_compare( $_sCurrentVersion, '1.2.0b01', '>=')) {
            return array();
        }

        $_oRow = new AmazonAutoLinks_UnitOutput___Database_Product(
            $aRowIdentifier[ 'asin' ],
            $aRowIdentifier[ 'locale' ],
            $aRowIdentifier[ 'associate_id' ],
            $aRow,
            $this->_oUnitOutput->oUnitOption
        );
        return ( boolean ) $_oRow->getCell( 'is_prime' )
            ? $aProduct
            : array();

    }

}