<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Formats products array.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_UnitType_AdWidgetSearch_Event_Filter_ProductsFormatter extends AmazonAutoLinks_Unit_Category_Event_Filter_ProductsFormatter {

    /**
     * @var   string
     * @since 5.0.0
     */
    public $sUnitType = 'ad_widget_search';

    /**
     * @var   AmazonAutoLinks_UnitOutput_ad_widget_search
     * @since 5.2.0
     */
    public $oUnitOutput;

    /**
     * Unit type specific product structure.
     * @var array
     * @since 5.0.0
     */
    public static $aStructure_Product = array(
        'ASIN'             => null,
        'Title'            => null,
        'Price'            => null,
        'ListPrice'        => null,
        'ImageUrl'         => null,
        'DetailPageURL'    => null,
        'Rating'           => null,
        'TotalReviews'     => null,
        'Subtitle'         => null,
        'IsPrimeEligible'  => null,
    );

    /**
     * @param  array  $aItem
     * @param  string $sLocale
     * @param  string $sAssociateID
     * @return array
     * @throws Exception
     * @since  3.9.0
     * @since  5.0.0  Moved from `AmazonAutoLinks_UnitOutput_category`. Changed the visibility to protected from private as an extended class access this.
     */
    protected function _getProduct( $aItem, $sLocale, $sAssociateID ) {

        $_aProduct = $aItem + self::$aStructure_Product + AmazonAutoLinks_UnitOutput_Base_ElementFormat::$aStructure_ProductCommon;

        // ASIN - required to detect duplicated items.
        if ( $this->oUnitOutput->isASINBlocked( $_aProduct[ 'ASIN' ] ) ) {
            throw new Exception( '(product filter) The ASIN is black-listed: ' . $_aProduct[ 'ASIN' ] );
        }

        // Product Link (hyperlinked url) - ref=nosim, linkstyle, associate id etc.
        $_bPAAPIKeysSet = $this->oUnitOutput->oOption->isPAAPIKeySet( $sLocale );
        $_aProduct[ 'product_url' ] = $this->oUnitOutput->getProductLinkURLFormatted(
            $_aProduct[ 'DetailPageURL' ],
            $_aProduct[ 'ASIN' ],
            $_bPAAPIKeysSet ? $this->oUnitOutput->oUnitOption->get( 'language' ) : '',
            $_bPAAPIKeysSet ? $this->oUnitOutput->oUnitOption->get( 'preferred_currency' ) : ''
        );

        // Title
        $_aProduct[ 'raw_title' ] = $this->getElement( $_aProduct, 'Title' );
        $_aProduct[ 'title' ]     = $this->oUnitOutput->getTitleSanitized( $_aProduct[ 'raw_title' ], $this->oUnitOutput->oUnitOption->get( 'title_length' ) );

        // At this point, update the black&white lists as this item is parsed.
        $this->oUnitOutput->setParsedASIN( $_aProduct[ 'ASIN' ] );

        // Thumbnail
        $_aProduct[ 'thumbnail_url' ]    = $this->oUnitOutput->getProductImageURLFormatted(
            $_aProduct[ 'ImageUrl' ],
              $this->oUnitOutput->oUnitOption->get( 'image_size' ),
              strtoupper( $this->oUnitOutput->oUnitOption->get( 'country' ) )  // locale
        );

        // Format the item
        // Thumbnail
        $_aProduct[ 'formatted_thumbnail' ] = $this->oUnitOutput->getProductThumbnailFormatted( $_aProduct );

        // Title
        $_aProduct[ 'formatted_title' ]     = $this->oUnitOutput->getProductTitleFormatted( $_aProduct, $this->oUnitOutput->oUnitOption->get( 'title_format' ) );

        // Price
        $_aProduct[ 'formatted_price' ]     = AmazonAutoLinks_Unit_Utility::getPrice( $_aProduct[ 'ListPrice' ], null, null, $_aProduct[ 'Price' ], $_aProduct[ 'Price' ] );

        // Discount
        $_aProduct[ 'formatted_discount' ]  = $this->___getDiscountFormatted( $_aProduct[ 'ListPrice' ], $_aProduct[ 'Price' ] );

        // Prime
        $_aProduct[ 'is_prime' ] = ( boolean ) $_aProduct[ 'IsPrimeEligible' ];

        // Rating
        $_aProduct[ 'rating' ] = ( ( double ) $_aProduct[ 'Rating' ] ) * 10;
        $_aProduct[ 'number_of_reviews' ] = ( integer ) $_aProduct[ 'TotalReviews' ];

        // Button - check if the %button% variable exists in the item format definition.
        // It accesses the database, so if not found, the method should not be called.
        if ( $this->oUnitOutput->oUnitOption->hasItemFormatTags( array( '%button%', ) ) ) {
            $_aProduct[ 'button' ] = $this->oUnitOutput->getButtonFormatted(
                $this->oUnitOutput->oUnitOption->get( 'button_type' ),
                $this->oUnitOutput->getButtonID(),
                $_aProduct[ 'product_url' ],
                $_aProduct[ 'ASIN' ],
                $sLocale,
                $sAssociateID,
                $this->oUnitOutput->oOption->getPAAPIAccessKey( $sLocale ), // public access key
                $this->oUnitOutput->oUnitOption->get( 'override_button_label' )
                    ? $this->oUnitOutput->oUnitOption->get( 'button_label' )
                    : null
            );
        }

        $_aProduct[ 'updated_date' ] = $this->oUnitOutput->iLastModified;

        /**
         * Let third-parties filter products.
         * @since 5.0.0
         */
        $_aProduct = apply_filters(
            'aal_filter_unit_each_product',
            $_aProduct,
            array(
                'locale'        => $sLocale,
                'asin'          => $_aProduct[ 'ASIN' ],
                'associate_id'  => $sAssociateID,
                'asin_locale'   => $_aProduct[ 'ASIN' ] . '_' . strtoupper( $sLocale ),
            ),
            $this
        );
        if ( empty( $_aProduct ) ) {
            throw new Exception( 'The product array is empty. Most likely it is filtered out.' );
        }
        return $_aProduct;

    }
        /**
         * @param  string $sProperPrice     Readable proper price
         * @param  string $sDiscountPrice   Readable discounted price
         * @since  5.0.0
         * @return string
         */
        private function ___getDiscountFormatted( $sProperPrice, $sDiscountPrice ) {
            return $this->getFormattedDiscount(
                $this->___getDiscountPercentage(
                    $this->getPriceAmountExtracted( $sProperPrice ),
                    $this->getPriceAmountExtracted( $sDiscountPrice )
                )
            );
        }
            /**
             * @param  integer $iProperPrice
             * @param  integer $iDiscounted
             * @return integer
             */
            private function ___getDiscountPercentage( $iProperPrice, $iDiscounted ) {
                $_dDiscountPercentage = $iProperPrice
                    ? 100 - round( ( $iDiscounted / $iProperPrice ) * 100, 2 )
                    : 0;
                return ( integer ) $_dDiscountPercentage;
            }
    
}