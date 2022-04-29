<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * A class that provides methods to format product item output.
 *
 * @since 3.5.0
 */
class AmazonAutoLinks_UnitOutput__ItemFormatter extends AmazonAutoLinks_UnitOutput_Utility {

    private $___oUnitOutput;
    private $___aProduct    = array();
    private $___aCacheDBRow = array();

    /**
     * @var string
     */
    static private $___sSiteDateFormat;
    static private $___sGMTOffset;

    /**
     * AmazonAutoLinks_UnitOutput__ItemFormatter constructor.
     *
     * @param AmazonAutoLinks_UnitOutput_Base $oUnitOutput
     * @param array $aProduct
     * @param array $aCacheDBRow
     * @since 3.7.5 Added the `$aCacheDBRow` parameter.
     */
    public function __construct( $oUnitOutput, array $aProduct, array $aCacheDBRow ) {
        $this->___oUnitOutput = $oUnitOutput;
        $this->___aProduct    = $aProduct;
        $this->___aCacheDBRow = $aCacheDBRow;
        self::$___sSiteDateFormat = isset( self::$___sSiteDateFormat ) ? self::$___sSiteDateFormat : get_option( 'date_format' );
        self::$___sGMTOffset      = isset( self::$___sGMTOffset ) ? self::$___sGMTOffset : 'GMT ' . $this->getGMTOffsetString();
    }

    /**
     * @return string
     */
    public function get() {
        return $this->___getProductOutputFormatted( $this->___aProduct );
    }

    /**
     * Returns the formatted product HTML block.
     * @since  2.1.1
     * @since  3.5.0  Changed the visibility scope from protected.
     * @since  3.5.0  Renamed from `_formatProductOutput()`.
     * @since  3.5.0  Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     * @param  array  $aProduct
     * @return string
     */
    private function ___getProductOutputFormatted( array $aProduct ) {

        $_iUpdatedTime  = $this->___getProductUpdatedTime( $aProduct[ 'updated_date' ] );
        $_sUpdatedDate  = $this->getSiteReadableDate( $_iUpdatedTime, self::$___sSiteDateFormat . ' H:i', true );
        $_sUpdatedDate  = 'n/a' === $_sUpdatedDate ? $_sUpdatedDate : $_sUpdatedDate . ' ' . self::$___sGMTOffset;
        $_aReplacements = array(
            '%href%'                        => esc_url( $aProduct[ 'product_url' ] ),
            '%title_text%'                  => $aProduct[ 'title' ],
            '%description_text%'            => $aProduct[ 'text_description' ],
            '%title%'                       => $aProduct[ 'formatted_title' ],
            '%image%'                       => $aProduct[ 'formatted_thumbnail' ],
            '%description%'                 => $aProduct[ 'description' ],
            '%rating%'                      => $aProduct[ 'formatted_rating' ],
            '%review%'                      => $aProduct[ 'review' ],
            '%price%'                       => $aProduct[ 'formatted_price' ],
            '%button%'                      => $aProduct[ 'button' ],
            '%image_set%'                   => $aProduct[ 'image_set' ],
            '%content%'                     => $aProduct[ 'content' ],                                  // [3.3.0+]
            '%meta%'                        => $aProduct[ 'meta' ],                                     // [3.3.0+]
            '%similar%'                     => $aProduct[ 'similar_products' ],                         // [3.3.0+]
            '%category%'                    => $aProduct[ 'category' ],                                 // [3.8.0+]
            '%feature%'                     => $aProduct[ 'feature' ],                                  // [3.8.0+]
            '%rank%'                        => $aProduct[ 'sales_rank' ],                               // [3.8.0+]
            '%date%'                        => $_sUpdatedDate,                                          // [3.8.0+] The date that the data is retrieved and updated.
            '%disclaimer%'                  => $this->___getPricingDisclaimer( $_sUpdatedDate ), // [3.2.0+]
            '%prime%'                       => $this->getPrimeMark( $aProduct ),                        // [3.9.0+]
            '<!-- %_review_rate% -->'       => '',                                                      // [3.9.2+]
            '<!-- %_discount_rate% -->'     => '',                                                      // [3.9.2+]
            '%image_size%'                  => $this->___oUnitOutput->oUnitOption->get( 'image_size' ), // [4.1.0+]
            '%author%'                      => $this->___getAuthorOutput( $aProduct ),                  // [4.1.0+]
            '%author_text%'                 => $this->getElement( $aProduct, array( 'author' ), '' ),   // [4.7.10+]
            '%discount%'                    => $aProduct[ 'formatted_discount' ], // [4.7.8+] // $this->getElement( $aProduct, 'discount', '' ),
            '<!-- %_updated_time% -->'      => "<span class='amazon-product-updated-time' data-updated-timestamp='" . esc_attr( $_iUpdatedTime ) . "' data-updated-time='" . esc_attr( $_sUpdatedDate ) . "'></span>", // 5.1.4
        );
        $_aReplacements = apply_filters( 'aal_filter_unit_item_format_tag_replacements', $_aReplacements, $aProduct, $this->___oUnitOutput ); // [4.4.2+] Allows third parties to add custom tags.
        $_sOutput       = str_replace(
            array_keys( $_aReplacements ),
            array_values( $_aReplacements ),
            apply_filters(
                'aal_filter_unit_item_format',
                $this->___oUnitOutput->oUnitOption->get( 'item_format' ),
                $aProduct,
                $this->___oUnitOutput->oUnitOption->get()
            )
        );
        return apply_filters(
            'aal_filter_unit_product_formatted_html',
            $_sOutput,
            $aProduct[ 'ASIN' ] . '|' . $this->___oUnitOutput->oUnitOption->get( 'country' ) . '|' . $this->___oUnitOutput->oUnitOption->get( 'preferred_currency' ) . '|' . $this->___oUnitOutput->oUnitOption->get( 'language' ), // product ID
            $this->___oUnitOutput,
            $aProduct,
            $this->___aCacheDBRow
        );
    }

        /**
         * @since  4.1.0
         * @param  array  $aProduct
         * @return string
         */
        private function ___getAuthorOutput( array $aProduct ){
            if ( ! isset( $aProduct[ 'author' ] ) ) {
                return '';
            }
            return '<div class="amazon-product-meta">'
                . '<span class="amazon-product-author">'
                    . $aProduct[ 'author' ]
                    . '</span>'
                . '</div>';
        }
        /**
         * @param  integer|string $isResponseDate
         * @return integer
         * @since  3.8.0
         */
        private function ___getProductUpdatedTime( $isResponseDate ) {
            if ( is_numeric( $isResponseDate ) ) {
                return ( integer ) $isResponseDate;
            }
            if ( $isResponseDate ) {
                return ( integer ) strtotime( $isResponseDate );
            }
            $_sCacheModTime = $this->getElement( $this->___aCacheDBRow, 'modified_time' );
            $_iTime         = $this->___oUnitOutput->bDBTableAccess && $_sCacheModTime
                ? strtotime( $_sCacheModTime )
                : strtotime( $isResponseDate );
            return ( integer ) $_iTime;
        }

        /**
         * @since  3.2.0
         * @since  3.5.0   Changed the visibility scope from protected.
         * @since  3.5.0   Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
         * @since  3.7.5   Made the date the cached time
         * @param  string  $sUpdatedDate Human-readable time of the updated time
         * @return string
         */
        private function ___getPricingDisclaimer( $sUpdatedDate ) {
            return "<span class='pricing-disclaimer'>"
                . "("
                    . sprintf(
                        __( 'as of %1$s', 'amazon-auto-links' ),
                       $sUpdatedDate
                    )
                    . ' - '
                    . $this->___getDisclaimerTooltip()
                . ")"
                . "</span>";
        }
            /**
             * @since  3.2.0
             * @since  3.5.0  Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
             * @return string
             */
            private function ___getDisclaimerTooltip() {
                $_sText   = __( "Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on [relevant Amazon Site(s), as applicable] at the time of purchase will apply to the purchase of this product.", 'amazon-auto-links' );
                $_sURL    = apply_filters( 'aal_filter_unit_output_disclaimer_link_url', '' );
                $_sHref   = $_sURL ? "href='" . esc_url( $_sURL ) . "'" : '';
                $_sTarget = $_sURL ? " target='_blank'" : '';
                return "<a {$_sHref}{$_sTarget} class='amazon-disclaimer-tooltip'>"
                        . __( 'More info', 'amazon-auto-links' )
                        . "<span class='amazon-disclaimer-tooltip-content'>"
                            . "<span class='amazon-disclaimer-tooltip-content-text'>"   // needed for widget CSS
                                . apply_filters( 'aal_filter_unit_output_disclaimer_text', $_sText, $this->___oUnitOutput->oUnitOption ) // 4.7.0
                            . "</span>"
                        . "</span>"
                    . "</a>";
            }

}