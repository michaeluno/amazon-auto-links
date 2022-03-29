<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Formats products array.
 *
 * @since 5.0.0
 * @deprecated 5.0.0
 */
class AmazonAutoLinks_Unit_Embed_Event_Filter_ProductsFormatter extends AmazonAutoLinks_Unit_Category_Event_Filter_ProductsFormatter {

    /**
     * @var string
     */
    public $sUnitType = 'embed';

    /**
     * Unit type specific product structure.
     * @var array
     */
    public static $aStructure_Product = array();

    /**
     * Called when the unit has access to the plugin custom database table.
     *
     * Sets the 'content' and 'description' elements in the product (item) array which require plugin custom database table.
     *
     * @since    4.2.10
     * @since    5.0.0  Moved from ``.
     * @return   array
     * @callback add_filter()      aal_filter_unit_each_product_with_database_row
     * @param    array $aProduct
     * @param    array $aDBRow
     * @param    array $aScheduleIdentifier
     */
    public function replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier=array() ) {

        if ( empty( $aProduct ) ) {
            return array();
        }

        $aProduct[ 'content' ]          = empty( $aProduct[ 'content' ] )
            ? AmazonAutoLinks_Unit_Utility::getContent( $aProduct )
            : $aProduct[ 'content' ];
        if ( empty( $aProduct[ 'description' ] ) ) {
            $_sDescriptionExtracted         = $this->oUnitOutput->getDescriptionFormatted(
                $aProduct[ 'content' ],
                $this->oUnitOutput->oUnitOption->get( 'description_length' ),
                $this->oUnitOutput->getReadMoreText( $aProduct[ 'product_url' ] )
            );
            $aProduct[ 'description' ]      = $_sDescriptionExtracted
                ? "<div class='amazon-product-description'>"
                        . $_sDescriptionExtracted
                    . "</div>"
                : '';
        }

        $aProduct[ 'text_description' ] = strip_tags( $aProduct[ 'description' ] );
        if ( $this->oUnitOutput->isDescriptionBlocked( $aProduct[ 'text_description' ] ) ) {
            $this->oUnitOutput->aBlockedASINs[ $aProduct[ 'ASIN' ] ] = $aProduct[ 'ASIN' ];
            return array(); // will be dropped
        }
        return $aProduct;

    }

}