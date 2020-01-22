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
 * A base class for classes which handle filtering products.
 *
 * @since       3.5.0
 */
abstract class AmazonAutoLinks_UnitOutput__ProductFilter_Base extends AmazonAutoLinks_UnitOutput__DelegationBase {

    /**
     * @return array
     */
    protected function _getFilterArguments() {
        return array(
            array(
                'aal_filter_unit_each_product_with_database_row',
                array( $this, 'replyToFilterProduct' ),
                100, // priority - set low as there are other callbacks with 10
                3    // number of parameters
            ),
        );
    }


    /**
     *
     * @param $aProduct
     * @param $aRow
     * @param $aRowIdentifier
     * @callback        add_filter      aal_filter_unit_each_product_with_database_row
     * @return      array       The filtered product definition array. If it does not meet the user-set criteria, an empty array will be returend to be filtered out.
     */
    public function replyToFilterProduct( $aProduct, $aRow, $aRowIdentifier ) {
        return $aProduct;
    }

}