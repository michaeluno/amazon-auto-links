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
 * Updates products with the Ad Widget API.
 *
 * @since 4.6.9
 */
class AmazonAutoLinks_Unit_Event_Action_UpdateProductsWithAdWidgetAPI extends AmazonAutoLinks_PluginUtility {

    /**
     * @since 4.6.9
     */
    public function __construct() {
        add_action( 'aal_action_update_products_with_ad_widget_api', array( $this, 'replyToUpdateProducts' ), 10, 4 );
    }

    /**
     * @since 4.6.9
     */
    public function replyToUpdateProducts( $sLocaleSlug, $aItems, $iCacheDuration, $bForceRenew ) {

        // Should proceed?
        $_aAdWidgetLocales  = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport();
        if ( ! in_array( $sLocaleSlug, $_aAdWidgetLocales, true ) ) {
            return;
        }

        $_oOption           = AmazonAutoLinks_Option::getInstance();
        $iCacheDuration     = isset( $iCacheDuration ) ? ( integer ) $iCacheDuration : ( integer ) $_oOption->get( 'unit_default', 'cache_duration' );
        $_aASINsToSearch    = array_keys( $aItems );
        $_aResponse         = $this->___getAPIResponse( $sLocaleSlug, $_aASINsToSearch, $iCacheDuration, $bForceRenew );
        // @todo consider a case that a response failed
        $_aProducts         = $this->getElementAsArray( $_aResponse, array( 'results' ) );
        $_aProducts         = array_merge( $_aProducts, $this->___getMissedProducts( $_aASINsToSearch, $_aProducts ) );
        $this->___setProductsIntoDatabase( $_aProducts, $aItems, $sLocaleSlug, $iCacheDuration );

    }
        /**
         * Construct empty product data.
         * Without adding empty data, the plugin keeps checking to update product elements front the front-end.
         * @return array A simulated response products array of products that are missed in the response.*
         * @since  4.7.9
         */
        private function ___getMissedProducts( $aASINsToSearch, $aProducts ) {
            $_aASINsOfResponse = wp_list_pluck( $aProducts, 'ASIN' );
            $_aASINsMissed     = array_diff( $aASINsToSearch, $_aASINsOfResponse );
            $_aProductsMissed  = array();
            foreach( $_aASINsMissed as $_sASIN ) {
                $_aProductsMissed[] = array(
                    'ASIN'              => $_sASIN, 'Title'             => '',
                    'Price'             => '',      'ListPrice'         => '',
                    'ImageUrl'          => '',      'DetailPageURL'     => '',
                    'Rating'            => '',      'TotalReviews'      => '',
                    'Subtitle'          => '',      'IsPrimeEligible'   => '',
                );
            }
            return $_aProductsMissed;
        }
        /**
         * @param array   $aProducts     The API response products
         * @param array   $aItems        The passed item array to the action hook, holding the currency, language, ASIN info.
         * @param string  $sLocaleSlug
         * @param integer $iCacheDuration
         * @since 4.6.9
         */
        private function ___setProductsIntoDatabase( array $aProducts, array $aItems, $sLocaleSlug, $iCacheDuration ) {
            $_aStoredRows = $this->___getStoredRows( $aItems, $sLocaleSlug );
            $_aRowsSets   = $this->___getRowsSets( $aProducts, $aItems, $sLocaleSlug, $_aStoredRows, $iCacheDuration );
            foreach( $_aRowsSets as $_aRowsSet ) {
                $this->setProductDatabaseRows( $_aRowsSet );
            }
        }
            private function ___getStoredRows( $aItems, $sLocaleSlug ) {

                $_oLocale             = new AmazonAutoLinks_PAAPI50_Locale( $sLocaleSlug );
                $_sDefaultCurrency    = $_oLocale->getDefaultCurrency();
                $_sDefaultLanguage    = $_oLocale->getDefaultLanguage();
                $_aASINLocaleCurLangs = array();
                foreach( $aItems as $_sASIN => $_aItem ) {
                    $_aItem = $_aItem + array( 'asin' => $_sASIN );
                    $_sKey  = "{$_sASIN}|{$sLocaleSlug}|{$_sDefaultCurrency}|{$_sDefaultLanguage}";
                    $_aASINLocaleCurLangs[ $_sKey ] = $_aItem;
                }
                $_oProducts = new AmazonAutoLinks_ProductDatabase_Rows( $_aASINLocaleCurLangs );
                return $_oProducts->get();

            }
            /**
             * @param  array   $aProducts
             * @param  array   $aItems
             * @param  string  $sLocaleSlug
             * @param  array   $aStoredRows
             * @param  integer $iCacheDuration
             * @since  4.6.9
             * @return array
             * @remark The column names must much across all the rows to properly insert into the table without errors.
             */
            private function ___getRowsSets( array $aProducts, array $aItems, $sLocaleSlug, array $aStoredRows, $iCacheDuration ) {

                $_oLocale           = new AmazonAutoLinks_PAAPI50_Locale( $sLocaleSlug );
                $_sDefaultCurrency  = $_oLocale->getDefaultCurrency();
                $_sDefaultLanguage  = $_oLocale->getDefaultLanguage();

                $_aRowsSets         = array(
                    'default' => array(),
                );
                foreach( $aProducts as $_aProduct ) {
                    $_sASIN           = $_aProduct[ 'ASIN' ];
                    if ( ! isset( $aItems[ $_sASIN ] ) ) {  // the search result may contain unspecified ASINs
                        continue;
                    }
                    $_aItem           = $aItems[ $_sASIN ];
                    $_sKey            = "{$_sASIN}|{$sLocaleSlug}|{$_sDefaultCurrency}|{$_sDefaultLanguage}";
                    $_aStoredRow      = $this->getElementAsArray( $aStoredRows, array( $_sKey ) );
                    $_aRow            = $this->___getRowFormatted( $_aProduct, $_aStoredRow, $iCacheDuration, $_aProduct[ 'ASIN' ], $sLocaleSlug, $_sDefaultCurrency, $_sDefaultLanguage );
                    $_aRowsSets[ 'default' ][ $_sKey ] = $_aRow;

                    // If the user has different language and currency preference than the default one,
                    $_sTableVersion   = get_option( "aal_products_version", '0' );
                    if ( $_sDefaultCurrency !== $_aItem[ 'currency' ] ) {
                        unset(
                            $_aRow[ 'price' ],              $_aRow[ 'price_formatted' ],
                            $_aRow[ 'discounted_price' ],   $_aRow[ 'discounted_price_formatted' ]
                        );
                        $_aRow[ 'currency' ] = $_aItem[ 'currency' ];
                        if ( version_compare( $_sTableVersion, '1.2.0b01', '>=') ) {
                            $_aRow[ 'preferred_currency' ] = $_aItem[ 'currency' ];
                        }
                    }
                    // with different language than the default one,
                    if ( $_sDefaultLanguage !== $_aItem[ 'language' ] ) {
                        unset( $_aRow[ 'title' ] );
                        if ( version_compare( $_sTableVersion, '1.2.0b01', '>=') ) {
                            $_aRow[ 'language' ] = $_aItem[ 'language' ];
                        }
                    }
                    $_sKey            = "{$_aProduct[ 'ASIN' ]}|{$sLocaleSlug}|{$_aItem[ 'currency' ]}|{$_aItem[ 'language' ]}";
                    if ( version_compare( $_sTableVersion, '1.4.0b01', '>=' ) ) {
                        $_aRow[ 'product_id' ] = $_sKey;
                    }

                    if ( ! $this->___shouldUpdateRow( $_aRow, $_aStoredRow, $iCacheDuration ) ) {
                        unset( $_aRowsSets[ 'default' ][ $_sKey ] );
                        continue;
                    }

                    if ( $_aRowsSets[ 'default' ][ $_sKey ] == $_aRow ) {   // two operators '==' for arrays don't care about the array order
                        continue;
                    }

                    // Multiple SQL queries are necessary per combinations of columns
                    $_aColumnNames = array_keys( $_aRow );
                    sort( $_aColumnNames );
                    $_sColumns = implode( '|', $_aColumnNames );
                    $_aRowsSets[ $_sColumns ] = isset( $_aRowsSets[ $_sColumns ] ) ? $_aRowsSets[ $_sColumns ] : array();
                    $_aRowsSets[ $_sColumns ][ $_sKey ] = $_aRow;

                }

                // It could be an empty array with the above check with ___shouldUpdateRow() and the empty entry should be removed not to perform redundant database insertions.
                if ( empty( $_aRowsSets[ 'default' ] ) ) {
                    unset( $_aRowsSets[ 'default' ] );
                }
                return $_aRowsSets;

            }
                /**
                 * @since  5.2.2
                 * @param  array   $aRowToSet
                 * @param  array   $aStoredRow
                 * @param  integer $iCacheDuration
                 * @return boolean
                 */
                private function ___shouldUpdateRow( $aRowToSet, $aStoredRow, $iCacheDuration ) {
                    if ( $this->isExpired( $this->getElement( $aStoredRow, array( 'expiration_time' ), 0 ) ) ) {
                        return true;
                    }
                    $_aColumnsToCompare = array(
                        'preferred_currency' => null, 'language'   => null,
                        'title'              => null, 'links'      => null,
                        'is_prime'           => null, 'product_id' => null,
                        'number_of_reviews'  => null, 'rating'     => null,
                        'price'              => null,
                        // 'images'     => null,    // images should not be compared as PA-API inserts multiple items while Ad Widget Search inserts only one
                    );
                    $_aToSet  = array_intersect_key( $aRowToSet, $_aColumnsToCompare );
                    $_aStored = array_intersect_key( $aStoredRow, $_aColumnsToCompare );

                    if ( $_aToSet != $_aStored ) { // the double operators are used, not triple (!==) as the array element order does not matter here.
                        return true;
                    }
                    $_iLastModified = ( integer ) strtotime( $this->getElement( $aStoredRow, array( 'modified_time' ), '' ) );
                    if ( time() <= $_iLastModified + 600 ) { // 600 = 10 minutes
                        return false;
                    }
                    // 10 minutes have passed from the last update. The updating values are the same but the modified date will be updated which needs to be displayed in the front-end with prices.
                    return true;

                }
                /**
                 * @since 4.6.9
                 */
                private function ___getRowFormatted( array $aProduct, array $aStoredRow, $iCacheDuration, $sASIN, $sLocale, $sCurrency, $sLanguage ) {

                    $aProduct = $aProduct + array(
                        'ASIN'          => null,    'Title'             => null,
                        'Price'         => null,    'ListPrice'         => null,
                        'ImageUrl'      => null,    'DetailPageURL'     => null,
                        'Rating'        => null,    'TotalReviews'      => null,
                        'Subtitle'      => null,    'IsPrimeEligible'   => null,
                    );
                    $aStoredRow = $aStoredRow + array(
                        'links'  => null,           'images' => null,
                        'price'  => null,           'price_formatted' => null,
                        'discounted_price' => null, 'discounted_price_formatted' => null,
                        'title'  => null,
                    );

                    $_aRow    = array(
                        'asin_locale'        => $sASIN . '_' . $sLocale,
                        'locale'             => $sLocale,
                        'modified_time'      => date( 'Y-m-d H:i:s' ),
                        'title'              => strlen( $aProduct[ 'Title' ] ) ? $aProduct[ 'Title' ] : $aStoredRow[ 'title' ],
                        'links'              => empty( $aStoredRow[ 'links' ] ) ? $aProduct[ 'DetailPageURL' ] : $aStoredRow[ 'links' ],
                        'images'             => empty( $aStoredRow[ 'images' ] )
                            ? (
                                $aProduct[ 'ImageUrl' ]
                                    ? array(
                                        'main' => array(
                                            'MediumImage' => $aProduct[ 'ImageUrl' ],
                                        )
                                    )
                                    : ''    // needs to be a string value for ___shouldUpdateRow() to compare
                            )
                            : $aStoredRow[ 'images' ],
                        'currency'           => $sCurrency,
                        'price'                      => $this->___getColumnValueOfPriceAmount( $aProduct, $aStoredRow ),
                        'price_formatted'            => $this->___getColumnValueOfFormattedPrice( $aProduct, $aStoredRow ),
                        'discounted_price'           => $this->___getColumnValueOfDiscountPriceAmount( $aProduct, $aStoredRow ),
                        'discounted_price_formatted' => $this->___getColumnValueOfFormattedDiscountPrice( $aProduct, $aStoredRow ),
                        'rating'             => ( integer ) ( ( ( double ) $aProduct[ 'Rating' ] ) * 10 ),
                        'number_of_reviews'  => ( integer ) $aProduct[ 'TotalReviews' ],
                        // These are deprecated columns but if left unset, a background rating update task will be kept triggering. So set an empty string.
                        'rating_image_url'   => '',
                        'rating_html'        => '',
                    );

                    // if `0` is passed for the cache duration, it just renews the cache and do not update the expiration time.
                    if ( $iCacheDuration ) {
                        $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
                    }

                    $_sCurrentVersion = get_option( "aal_products_version", '0' );

                    // If the table version is 1.2.0b01 or above,
                    if ( version_compare( $_sCurrentVersion, '1.2.0b01', '>=') ) {
                        $_aRow[ 'is_prime' ] = $aProduct[ 'IsPrimeEligible' ] ? '1' : '0';  // needs to be a string value to be compared in ___shouldUpdateRow()
                        $_aRow[ 'language' ] = $sLanguage;
                        $_aRow[ 'preferred_currency' ] = $sCurrency;
                    }

                    if ( version_compare( $_sCurrentVersion, '1.4.0b01', '>=' ) ) {
                        $_aRow[ 'asin' ]       = $sASIN;
                        $_aRow[ 'product_id' ] = "{$sASIN}|{$sLocale}|{$sCurrency}|{$sLanguage}";
                    }

                    return $_aRow;
                }
                    /**
                     * @param  array $aProduct
                     * @param  array $aStoredRow
                     * @return integer|string
                     * @since  5.1.3
                     */
                    private function ___getColumnValueOfPriceAmount( array $aProduct, array $aStoredRow ) {
                        // Case: when the 'ListPrice' has a value, it is always the proper price (no discount).
                        if ( strlen( $aProduct[ 'ListPrice' ] ) ) {
                            return AmazonAutoLinks_Unit_Utility::getPriceAmountExtracted( $aProduct[ 'ListPrice' ] );
                        }
                        // Case: 'ListPrice' (for proper price) is empty but 'Price' has a value.
                        if ( strlen( $aProduct[ 'Price' ] ) ) {
                            return AmazonAutoLinks_Unit_Utility::getPriceAmountExtracted( $aProduct[ 'Price' ] );
                        }
                        // Otherwise, a price is not found.
                        return $aStoredRow[ 'price' ];
                    }
                    private function ___getColumnValueOfFormattedPrice( array $aProduct, array $aStoredRow ) {
                        // Case: when the 'ListPrice' has a value, it is always the proper price (no discount).
                        if ( strlen( $aProduct[ 'ListPrice' ] ) ) {
                            return $aProduct[ 'ListPrice' ];
                        }
                        // Case: 'ListPrice' (for proper price) is empty but 'Price' has a value.
                        if ( strlen( $aProduct[ 'Price' ] ) ) {
                            return $aProduct[ 'Price' ];
                        }
                        // Otherwise, a price is not found.
                        return $aStoredRow[ 'price_formatted' ];
                    }
                    private function ___getColumnValueOfDiscountPriceAmount( array $aProduct, array $aStoredRow ) {
                        // There is a case that `ListPrice` is empty but `Price` has a value. In that case, the `Price` is the proper price.
                        if ( strlen( $aProduct[ 'Price' ] ) && strlen( $aProduct[ 'ListPrice' ] ) ) {
                            return AmazonAutoLinks_Unit_Utility::getPriceAmountExtracted( $aProduct[ 'Price' ] );
                        }
                        return $aStoredRow[ 'discounted_price' ];
                    }
                    private function ___getColumnValueOfFormattedDiscountPrice( array $aProduct, array $aStoredRow ) {
                        // There is a case that `ListPrice` is empty but `Price` has a value. In that case, the `Price` is the proper price.
                        if ( strlen( $aProduct[ 'Price' ] ) && strlen( $aProduct[ 'ListPrice' ] ) ) {
                            return $aProduct[ 'Price' ];
                        }
                        return $aStoredRow[ 'discounted_price_formatted' ];
                    }

        /**
         * @param  string  $sLocale
         * @param  array   $aASINs
         * @param  integer $iCacheDuration
         * @param  boolean $bForceRenew
         * @return array
         * @since  4.6.9
         */
        private function ___getAPIResponse( $sLocale, array $aASINs, $iCacheDuration, $bForceRenew ) {
            sort($aASINs ); // for caching. The request URL should be the same to find its cache.
            $_aArguments        = array(
                'renew_cache' => $bForceRenew
            );
            $_oAdWidgetAPI      = new AmazonAutoLinks_AdWidgetAPI_Search( $sLocale, $iCacheDuration, $_aArguments );
            return $_oAdWidgetAPI->get( $aASINs );
        }

}