<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * A class that provides methods to format product item output.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput__ItemFormatter extends AmazonAutoLinks_UnitOutput_Utility {

    private $___oUnitOutput;

    private $___aProduct = array();

    public function __construct( $oUnitOutput, array $aProduct ) {
        $this->___oUnitOutput = $oUnitOutput;
        $this->___aProduct    = $aProduct;
    }

    /**
     * @return      string
     */
    public function get() {
        return $this->___getProductOutputFormatted( $this->___aProduct );
    }

    /**
     * Returns the formatted product HTML block.
     * @since       2.1.1
     * @since       3.5.0       Changed the visiblity scope from protected.
     * @since       3.5.0       Renamed from `_formatProductOutput()`.
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     * @return      string
     */
    private function ___getProductOutputFormatted( array $aProduct ) {

        $_sOutput = str_replace(
            array(
                "%href%",
                "%title_text%",
                "%description_text%",
                "%title%",
                "%image%",
                "%description%",
                "%rating%",
                "%review%",
                "%price%",
                "%button%",
                "%image_set%",
                "%disclaimer%",     // 3.2.0+
                "%content%",        // 3.3.0+
                "%meta%",           // 3.3.0+
                "%similar%",        // 3.3.0+
            ),
            array(
                $aProduct[ 'product_url' ],
                $aProduct[ 'title' ],
                $aProduct[ 'text_description' ],
                $aProduct[ 'formatted_title' ],
                $aProduct[ 'formatted_thumbnail' ],
                $aProduct[ 'description' ],
                $aProduct[ 'rating' ],
                $aProduct[ 'review' ],
                $aProduct[ 'price' ],
                $aProduct[ 'button' ],
                $aProduct[ 'image_set' ],
                $this->___getPricingDisclaimer( $aProduct[ 'updated_date' ] ), // 3.2.0+
                $aProduct[ 'content' ], // 3.3.0+
                $aProduct[ 'meta' ], // 3.3.0+
                $aProduct[ 'similar_products' ], // 3.3.0+
            ),
            apply_filters(
                'aal_filter_unit_item_format',
                $this->___oUnitOutput->oUnitOption->get( 'item_format' ),
                $aProduct,
                $this->___oUnitOutput->oUnitOption->get()
            )
        );

        return apply_filters(
            'aal_filter_unit_product_formatted_html',    // filter hook name
            $_sOutput, // 1. filtering value
            $aProduct[ 'ASIN' ], // 2.
            $this->___oUnitOutput->oUnitOption->get( 'country' ) // 3.
        );
    }

        /**
         * @since       3.2.0
         * @since       3.5.0       Changed the visibility scope from protected.
         * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
         * @return      string
         */
        private function ___getPricingDisclaimer( $sResponseDate ) {
            return "<span class='pricing-disclaimer'>"
                . "("
                    . sprintf(
                        __( 'as of %1$s', 'amazon-auto-links' ),
                        $this->getSiteReadableDate( strtotime( $sResponseDate ) )
                    )
                    . ' - '
                    . $this->___getDisclaimerTooltip()
                . ")"
                . "</span>";
        }
            /**
             * @since       3.2.0
             * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
             * @return      string
             */
            private function ___getDisclaimerTooltip() {
                return "<a href='#' class='amazon-disclaimer-tooltip'>"
                        . __( 'More info', 'amazon-auto-links' )
                        . "<span class='amazon-disclaimer-tooltip-content'>"
                            . "<span class='amazon-disclaimer-tooltip-content-text'>"   // needed for widget CSS
                                . __( "Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on [relevant Amazon Site(s), as applicable] at the time of purchase will apply to the purchase of this product.", 'amazon-auto-links' )
                            . "</span'>"
                        . "</span>"
                    . "</a>";
            }


}