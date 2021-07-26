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
 * Updates products with the Ad Widget API.
 *
 * @since   4.6.9
 */
class AmazonAutoLinks_Unit_Event_Action_UpdateProductsWithAdWidgetAPI extends AmazonAutoLinks_PluginUtility {

    /**
     * @since  4.6.9
     */
    public function __construct() {
        add_action( 'aal_action_update_products_with_ad_widget_api', array( $this, 'replyToUpdateProducts' ), 10, 2 );
    }

    /**
     * @since 4.6.9
     */
    public function replyToUpdateProducts( $sLocaleSlug, $aItems ) {

        // Should proceed?
        $_aAdWidgetLocales  = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport();
        if ( ! in_array( $sLocaleSlug, $_aAdWidgetLocales, true ) ) {
            return;
        }

        $_aResponse         = $this->___getAPIResponse( $sLocaleSlug, array_keys( $aItems ) );
        $_aProducts         = $this->getElementAsArray( $_aResponse, array( 'results' ) );

        // do_action( 'aal_action_debug_log', 'UPDATE_PRODUCTS_ADWIDGETAPI', "{$sLocaleSlug}, {$_sCurrency}, {$_sLanguage}: " . implode( ', ', $_aASINs ), $_aResponse, current_filter(), true );
        $this->___setProductsIntoDatabase( $_aProducts, $aItems, $sLocaleSlug );

    }
        /**
         * @param array  $aProducts     The API response products
         * @param array  $aItems        The passed item array to the action hook, holding the currency, language, ASIN info.
         * @param string $sLocaleSlug
         * @since 4.6.9
         */
        private function ___setProductsIntoDatabase( array $aProducts, array $aItems, $sLocaleSlug ) {
            $_aStoredRows = $this->___getStoredRows( $aItems, $sLocaleSlug );
            $_aRows       = $this->___getRowsFormatted( $aProducts, $aItems, $sLocaleSlug, $_aStoredRows );
            $this->setProductDatabaseRows( $_aRows );
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
             * @since  4.6.9
             * @return array
             * @remark The column names must much across al rows to properly insert into the table without errors.
             */
            private function ___getRowsFormatted( array $aProducts, array $aItems, $sLocaleSlug, array $aStoredRows ) {

                $_oLocale           = new AmazonAutoLinks_PAAPI50_Locale( $sLocaleSlug );
                $_sDefaultCurrency  = $_oLocale->getDefaultCurrency();
                $_sDefaultLanguage  = $_oLocale->getDefaultLanguage();
                $_oOption           = AmazonAutoLinks_Option::getInstance();
                $_iCacheDuration    = $_oOption->get( 'unit_default', 'cache_duration' );

                $_aRows = array();
                foreach( $aProducts as $_aProduct ) {
                    $_aItem           = $aItems[ $_aProduct[ 'ASIN' ] ];
                    $_sKey            = "{$_aProduct[ 'ASIN' ]}|{$sLocaleSlug}|{$_sDefaultCurrency}|{$_sDefaultLanguage}";
                    $_aStoredRow      = $this->getElementAsArray( $aStoredRows, array( $_sKey ) );
                    $_aRow            = $this->___getRowFormatted( $_aProduct, $_aStoredRow, $_iCacheDuration, $_aProduct[ 'ASIN' ], $sLocaleSlug, $_sDefaultCurrency, $_sDefaultLanguage );
                    $_aRows[ $_sKey ] = $_aRow;
                    // If the user has different language and currency preference than the default one,
                    if ( $_sDefaultCurrency !== $_aItem[ 'currency' ] ) {
                        $_aRow[ 'price' ] = $_aRow[ 'price_formatted' ] = $_aRow[ 'discounted_price' ] =  $_aRow[ 'discounted_price_formatted' ] = null;
                    }
                    // with different language than the default one,
                    if ( $_sDefaultLanguage !== $_aItem[ 'language' ] ) {
                        $_aRow[ 'title' ] = null;
                    }
                    $_sKey            = "{$_aProduct[ 'ASIN' ]}|{$sLocaleSlug}|{$_aItem[ 'currency' ]}|{$_aItem[ 'language' ]}";
                    if ( version_compare( get_option( "aal_products_version", '0' ), '1.4.0b01', '>=' ) ) {
                        $_aRow[ 'product_id' ] = $_sKey;
                    }

                    $_aRows[ $_sKey ] = $_aRow;
                }
                return $_aRows;

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
                    );

                    $_aRow    = array(
                        'asin_locale'        => $sASIN . '_' . $sLocale,
                        'locale'             => $sLocale,
                        'modified_time'      => date( 'Y-m-d H:i:s' ),
                        'title'              => $aProduct[ 'Title' ],
                        'links'              => empty( $aStoredRow[ 'links' ] ) ? $aProduct[ 'DetailPageURL' ] : $aStoredRow[ 'links' ],
                        'images'             => empty( $aStoredRow[ 'images' ] ) ? ( $aProduct[ 'ImageUrl' ] ? array(
                            'main' => array(
                                'MediumImage' => $aProduct[ 'ImageUrl' ],
                            )
                        ) : null ) : $aStoredRow[ 'images' ],
                        'currency'           => $sCurrency,
                        'price'              => strlen( $aProduct[ 'ListPrice' ] )
                            ? ( integer ) preg_replace("/[^0-9]/", '', $aProduct[ 'ListPrice' ] )
                            : $aStoredRow[ 'price' ],
                        'price_formatted'    => strlen( $aProduct[ 'ListPrice' ] ) ? $aProduct[ 'ListPrice' ] : $aStoredRow[ 'price_formatted' ],
                        'discounted_price'   => strlen( $aProduct[ 'Price' ] ) ? ( integer ) preg_replace("/[^0-9]/", '', $aProduct[ 'Price' ] ) : $aStoredRow[ 'discounted_price' ],
                        'discounted_price_formatted' => strlen( $aProduct[ 'Price' ] ) ? $aProduct[ 'Price' ] : $aStoredRow[ 'discounted_price_formatted' ],

                        'rating'             => ( integer ) ( ( ( double ) $aProduct[ 'Rating' ] ) * 10 ),
                        'number_of_reviews'  => ( integer ) $aProduct[ 'TotalReviews' ],
                    );

                    // if `0` is passed for the cache duration, it just renews the cache and do not update the expiration time.
                    if ( $iCacheDuration ) {
                        $_aRow[ 'expiration_time' ] = date( 'Y-m-d H:i:s', time() + $iCacheDuration );
                    }

                    $_sCurrentVersion = get_option( "aal_products_version", '0' );

                    // If the table version is 1.2.0b01 or above,
                    if ( version_compare( $_sCurrentVersion, '1.2.0b01', '>=') ) {
                        $_aRow[ 'is_prime' ] = $aProduct[ 'IsPrimeEligible' ];
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
         * @param  string $sLocale
         * @param  array $aASINs
         * @return array
         * @since  4.6.9
         */
        private function ___getAPIResponse( $sLocale, array $aASINs ) {
            sort($aASINs ); // for caching. The request URL should be the same to find its cache.
            $_oOption           = AmazonAutoLinks_Option::getInstance();
            $_iCacheDuration    = $_oOption->get( 'unit_default', 'cache_duration' );
            $_oAdWidgetAPI      = new AmazonAutoLinks_AdWidgetAPI_Search( $sLocale, $_iCacheDuration );
            return $_oAdWidgetAPI->get( $aASINs );
        }

}