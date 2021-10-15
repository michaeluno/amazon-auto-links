<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Creates Amazon product links by category.
 * 
 * @package Amazon Auto Links
 * @since   unknown
 * @since   3           Changed the name from `AmazonAutoLinks_UnitOutput_Category`.
 * @since   3.8.1       deprecated
 * @since   3.9.0       Serves as a base class for `AmazonAutoLinks_UnitOutput_category3`
 * @since   4.3.4       Merged with `AmazonAutoLinks_UnitOutput_category3`.
 */
class AmazonAutoLinks_UnitOutput_category extends AmazonAutoLinks_UnitOutput_Base_ElementFormat {

    /**
     * Stores the unit type.
     * @remark The base constructor creates a unit option object based on this value.
     */
    public $sUnitType = 'category';

    /**
     * Unit type specific product structure.
     * @var array
     */
    public static $aStructure_Product = array();

    /* @deprecated 4.3.4 Seems unnecessary. */
    /* public function get( $aURLs=array(), $sTemplatePath=null ) {
        return parent::get( $aURLs );
    }*/

    /**
     * Stores modified dates for HTTP requests so these can be applied to the product updated date.
     * @since 3.9.0
     * @since 4.0.0 Changed the scope to protected as the Embed unit type extends this class and uses this property.
     * @since 4.3.4 Moved from `AmazonAutoLinks_UnitOutput_category3`.
     * @since 5.0.0 Changed the scope to public from protected as delegator classes access it.
     * @var   array
     */
    public $aModifiedDates = array();

    /**
     * Sets up type-specific properties.
     */
    protected function _setProperties() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->getPAAPIStatus( $this->oUnitOption->get( 'country' ) ) ) {
            $this->_aItemFormatDatabaseVariables[] = '%description%'; // updated in `replyToFormatProductWithDBRow()`.
            $this->_aItemFormatDatabaseVariables[] = '%content%';
            $this->_aItemFormatDatabaseVariables[] = '%feature%';     // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%category%';    // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%rank%';        // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%prime%';       // 3.9.0
            $this->_aItemFormatDatabaseVariables[] = '%discount%';    // 4.7.8
        }
    }

    /**
     * Fetches and returns the associative array containing the output of product links.
     *
     * If the first parameter is not given,
     * it will determine the RSS urls by the post IDs from the given arguments set in the constructor.
     *
     * @return array The array contains product information.
     * @since  4.3.4 Moved from `AmazonAutoLinks_UnitOutput_category3`.
     * @since  5.0.0 Removed the first parameter of `$aURLs`.
     */
    public function fetch() {

        $_sLocale            = ( string ) $this->oUnitOption->get( 'country' );
        $_sAssociateID       = ( string ) $this->oUnitOption->get( 'associate_id' );
        $_iCountUserSet      = ( integer ) $this->oUnitOption->get( 'count' );
        $_iCount             = $_iCountUserSet < 10 ? 10 : $_iCountUserSet;     // 4.6.14 Fetch at least 10 to reduce http requests and database queries

        $_aProducts          = apply_filters( 'aal_filter_unit_output_products_from_source_' . $this->sUnitType, array(), $this );
        $_aProducts          = $this->_getProducts( $_aProducts, $_sLocale, $_sAssociateID, $_iCount );
        return array_slice( $_aProducts, 0, $_iCountUserSet ); // truncate items

    }

        /**
         * @param  array   $aItems
         * @param  string  $sLocale       The `country` unit argument value
         * @param  string  $sAssociateID
         * @param  integer $iCount
         * @return array
         * @since  3.9.0
         * @since  4.0.0   Changed the scope to protected for the Embed unit type to extend this class.
         */
        protected function _getProducts( array $aItems, $sLocale, $sAssociateID, $iCount ) {

            // First Iteration - Extract displaying ASINs.
            $_aASINLocaleCurLangs = array();  // stores added product ASINs, locales, currencies and languages for performing a custom database query.
            $_aProducts           = array();

            $_sLocale             = $sLocale ? strtoupper( $sLocale ) : strtoupper( $this->oUnitOption->get( array( 'country' ), 'US' ) );
            $_sCurrency           = $this->oUnitOption->get( array( 'preferred_currency' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale ) );
            $_sLanguage           = $this->oUnitOption->get( array( 'language' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale ) );

            foreach ( $aItems as $_isIndex => $_aItem ) {

                $_sASIN = $this->getElement( $_aItem, array( 'ASIN' ), '' );

                // This parsed item is no longer needed and must be removed once it is parsed
                // as this method is called recursively.
                unset( $aItems[ $_isIndex ] );

                try {

                    $_aProduct = $this->___getProduct( $_aItem, $sLocale, $sAssociateID );

                } catch ( Exception $_oException ) {
                    // When the items are filtered out, this is reached
                    if ( false !== strpos( $_oException->getMessage(), '(product filter)' ) ) {
                        $this->aBlockedASINs[ $_sASIN ] = $_sASIN; // for error message
                    }
                    continue;   // skip
                }


                $_aASINLocaleCurLang    = "{$_aProduct[ 'ASIN' ]}|{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
                $_aASINLocaleCurLangs[ $_aASINLocaleCurLang ] = array(
                    'asin'      => $_aProduct[ 'ASIN' ],
                    'locale'    => $_sLocale,
                    'currency'  => $_sCurrency,
                    'language'  => $_sLanguage,
                );

                // Store the product
                $_aProducts[]           = $_aProduct;

                // @deprecated 4.6.14 - this causes too many database queries
                // when product filter options are set. For example, * in the blacklist title and a few items in the White List ASIN.
                // the items will be truncated later
                // Max Number of Items
                // if ( count( $_aProducts ) >= $iCount ) {
                //     break;
                // }

            }

            // Second iteration
            return $this->___getProductsFormatted( $aItems, $_aProducts, $_aASINLocaleCurLangs, $sLocale, $sAssociateID, $iCount );

        }

            /**
             * Second iteration
             * @param  array   $aRawItems               Raw items fetched from sources. When the initial format of an item is done, the item is removed from this array.
             * @param  array   $aProducts               Initially formatted items. (There are two formatting iterations and the second one is done in this method).
             * @param  array   $aASINLocaleCurLangs     Holding the information of ASIN, locale, currency and language for a database query.
             * @param  string  $sLocale
             * @param  string  $sAssociateID
             * @param  integer $iUserSetMaxCount        The user set max count.
             * @return array
             * @since  3.9.0
             */
            private function ___getProductsFormatted( $aRawItems, $aProducts, $aASINLocaleCurLangs, $sLocale, $sAssociateID, $iUserSetMaxCount ) {

                add_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFormatProductWithDBRow' ), 10, 3 );
                add_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFilterProducts' ), 100, 1 );
                $_iCountFormatBefore = count( $aProducts );
                try {

                    $aProducts          = $this->_getProductsFormatted( $aProducts, $aASINLocaleCurLangs, $sLocale, $sAssociateID );
                    $_iCountFormatAfter = count( $aProducts );
                    if (
                           count( $aRawItems )                        // The fetched items still exist. $aRawItems element are deleted after formatting in the above _getProducts() method.
                        && $iUserSetMaxCount > $_iCountFormatAfter    // If the resulting count (after format) does not meet the expected count.
                        && $_iCountFormatBefore > $_iCountFormatAfter // This means that at least one item is filtered out with filters in the above _getProductsFormatted() method.
                    ) {
                        throw new Exception( $iUserSetMaxCount - $_iCountFormatAfter ); // passing a count for another call
                    }

                } catch ( Exception $_oException ) {

                    // Recursive call
                    $_aAdditionalProducts = $this->_getProducts(
                        $aRawItems,
                        $sLocale,
                        $sAssociateID,
                        ( integer ) $_oException->getMessage() // the number of items to retrieve
                    );
                    $aProducts = array_merge( $aProducts, $_aAdditionalProducts );

                }

                // These removals are necessary as the hooks might not be called so the remove_filter() inside the callback method does not get triggered.
                remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFormatProductWithDBRow' ), 10 );
                remove_filter( 'aal_filter_unit_each_product_with_database_row', array( $this, 'replyToFilterProducts' ), 100 );

                return $aProducts;

            }

            /**
             *
             * @param  array  $_aItem
             * @param  string $_sLocale
             * @param  string $_sAssociateID
             * @return array
             * @throws Exception
             * @since  3.9.0
             */
            private function ___getProduct( $_aItem, $_sLocale, $_sAssociateID ) {

                $_aProduct = $_aItem + self::$aStructure_Product + self::$aStructure_ProductCommon;

                // ASIN - required to detect duplicated items.
                if ( $this->isASINBlocked( $_aProduct[ 'ASIN' ] ) ) {
                    throw new Exception( '(product filter) The ASIN is black-listed: ' . $_aProduct[ 'ASIN' ] );
                }

                // Product Link (hyperlinked url) - ref=nosim, linkstyle, associate id etc.
                $_bPAAPIKeysSet = $this->oOption->isPAAPIKeySet( $_sLocale );
                $_aProduct[ 'product_url' ] = $this->getProductLinkURLFormatted(
                    $_aProduct[ 'product_url' ],
                    $_aProduct[ 'ASIN' ],
                    $_bPAAPIKeysSet ? $this->oUnitOption->get( 'language' ) : '',
                    $_bPAAPIKeysSet ? $this->oUnitOption->get( 'preferred_currency' ) : ''
                );

                // Title
                $_aProduct[ 'raw_title' ] = $this->getElement( $_aProduct, 'title' );
                $_aProduct[ 'title' ]     = $this->getTitleSanitized( $_aProduct[ 'raw_title' ], $this->oUnitOption->get( 'title_length' ) );

                // At this point, update the black&white lists as this item is parsed.
                $this->setParsedASIN( $_aProduct[ 'ASIN' ] );

                // Thumbnail
                $_aProduct[ 'thumbnail_url' ]    = $this->getProductImageURLFormatted(
                    $_aProduct[ 'thumbnail_url' ],
                      $this->oUnitOption->get( 'image_size' ),
                      strtoupper( $this->oUnitOption->get( 'country' ) )  // locale
                );

                // Format the item
                // Thumbnail
                $_aProduct[ 'formatted_thumbnail' ] = $this->_getProductThumbnailFormatted( $_aProduct );

                // Title
                $_aProduct[ 'formatted_title' ]     = $this->getProductTitleFormatted( $_aProduct, $this->oUnitOption->get( 'title_format' ) );

                // Button - check if the %button% variable exists in the item format definition.
                // It accesses the database, so if not found, the method should not be called.
                if ( $this->oUnitOption->hasItemFormatTags( array( '%button%', ) ) ) {
                    $_aProduct[ 'button' ] = $this->_getButton(
                        $this->oUnitOption->get( 'button_type' ),
                        $this->_getButtonID(),
                        $_aProduct[ 'product_url' ],
                        $_aProduct[ 'ASIN' ],
                        $_sLocale,
                        $_sAssociateID,
                        $this->oOption->getPAAPIAccessKey( $_sLocale ), // public access key
                        $this->oUnitOption->get( 'override_button_label' ) ? $this->oUnitOption->get( 'button_label' ) : null
                    );
                }

                /**
                 * Let third-parties filter products.
                 * @since 3.4.13
                 */
                $_aProduct = apply_filters(
                    'aal_filter_unit_each_product',
                    $_aProduct,
                    array(
                        'locale'        => $_sLocale,
                        'asin'          => $_aProduct[ 'ASIN' ],
                        'associate_id'  => $_sAssociateID,
                        'asin_locale'   => $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale ),
                    ),
                    $this
                );
                if ( empty( $_aProduct ) ) {
                    throw new Exception( 'The product array is empty. Most likely it is filtered out.' );
                }
                return $_aProduct;

            }

    /**
     * @remark   The timing of filtering items by image and title is changed in order to support resuming with caches.
     * @param    array $aProduct
     * @return   array        A product array, empty when filtered out.
     * @callback add_filter() aal_filter_unit_each_product_with_database_row
     * @since    4.2.8
     * @since    4.3.4 Moved from `AmazonAutoLinks_UnitOutput_category3`.
     */
    public function replyToFilterProducts( $aProduct ) {

        if ( empty( $aProduct ) ) {
            return array();
        }
        // Check whether no-image should be skipped.
        if ( ! $this->isImageAllowed( $this->getElement( $aProduct, array( 'thumbnail_url' ) ) ) ) {
            return array();
        }

        $_sTitleRaw     = $this->getElement( $aProduct, array( 'raw_title' ) );
        $_sDescription  = $aProduct[ 'text_description' ];
        if (
            // If blacklisted
            (
                $this->isTitleBlocked( $_sTitleRaw )
                || $this->isDescriptionBlocked( $_sDescription )
            ) &&
            // And not white listed
            ! (
              $this->isWhiteListed( $aProduct[ 'ASIN' ], $_sTitleRaw, $_sDescription )
            )
        ) {
            $this->aBlockedASINs[ $aProduct[ 'ASIN' ] ] = $aProduct[ 'ASIN' ];
            return array();
        }

        return $aProduct;
    }

    /**
     * Called when the unit has access to the plugin custom database table.
     *
     * Sets the 'content' and 'description' elements in the product (item) array which require plugin custom database table.
     *
     * @param array $aProduct
     * @param array $aDBRow
     * @param array $aScheduleIdentifier
     * @return      array
     * @callback    add_filter      aal_filter_unit_each_product_with_database_row
     * @since       3.3.0
     */
    public function replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier=array() ) {

        if ( empty( $aProduct ) ) {
            return array(); // probably filtered out.
        }

        $aProduct[ 'content' ]      = $this->___getContents( $aProduct, $aDBRow, $aScheduleIdentifier );
        $_sDescriptionExtracted     = $this->_getDescriptionSanitized(
            $aProduct[ 'content' ],
            $this->oUnitOption->get( 'description_length' ),
            $this->_getReadMoreText( $aProduct[ 'product_url' ] )
        );

        $_sDescriptionExtracted     = $_sDescriptionExtracted
            ? "<div class='amazon-product-description'>"
                . $_sDescriptionExtracted
            . "</div>"
            : '';
        $_sDescription              = ( $aProduct[ 'description' ] || $_sDescriptionExtracted )
            ? trim( $aProduct[ 'description' ] . " " . $_sDescriptionExtracted ) // only the meta is added by default
            : ''; // 3.10.0 If there is no description, do not even add the div element, which cause an extra margin as a block element.
        $aProduct[ 'description' ]      = $_sDescription;
        $aProduct[ 'text_description' ] = strip_tags( $aProduct[ 'description' ] );
        return $aProduct;

    }
        /**
         * @return string
         * @since  3.3.0
         * @since  4.3.4  Changed the visibility to private from protected.
         * @param  array  $aProduct
         */
        private function ___getContents( $aProduct /*, $aDBRow, $aScheduleIdentifier */ ) {

            $_aParams            = func_get_args();
            $aDBRow              = $_aParams[ 1 ];
            $aScheduleIdentifier = $_aParams[ 2 ];
            $_oRow               = new AmazonAutoLinks_UnitOutput___Database_Product(
                $aScheduleIdentifier[ 'asin' ],
                $aScheduleIdentifier[ 'locale' ],
                $aScheduleIdentifier[ 'associate_id' ],
                $aDBRow,
                $this->oUnitOption
            );

            $_ansReviews         = $_oRow->getCell( 'editorial_reviews', array() );
            if ( $this->___hasEditorialReviews( $_ansReviews ) ) {
                $_oContentFormatter = new AmazonAutoLinks_UnitOutput__Format_content(
                    $_ansReviews,
                    $this->oDOM,
                    $this->oUnitOption
                );
                $_sContents = $_oContentFormatter->get();
                return "<div class='amazon-product-content'>"
                        . $_sContents
                    . "</div>";
            }
            $_snFeatures = $_oRow->getCell( 'features', '' );
            return $_snFeatures
                ? "<div class='amazon-product-content'>"
                    . $_snFeatures
                . "</div>"
                : '';

        }
            /**
             * For backward compatibility of a case that still the editorial reviews are stored in the cache.
             * @param  $anReviews
             * @remark This element is deprecated in PA-API 5.
             * @return bool
             * @since  3.10.0
             */
            private function ___hasEditorialReviews( $anReviews ) {
                // if null, the product data is not inserted in the plugin's database table.
                if ( is_null( $anReviews ) ) {
                    return false;
                }

                if ( is_string( $anReviews ) && $anReviews ) {
                    return true;
                }
                return is_array( $anReviews );
            }

}