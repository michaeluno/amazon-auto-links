<?php
/**
 * Auto Amazon Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 *
 */

/**
 * Provides methods to retrieve rows stored in the plugin custom database table.
 *
 * @remark      Retrieved data will be cached.
 * @since       3.4.13
 */
class AmazonAutoLinks_ProductDatabase_Rows {

    /**
     * Stores items of ASIN|Locale|Currency|Language
     *
     * @var array Structure:
     * ```
     * array(
     *      array( 'asin' => '...', 'locale' => '...', 'currency' => '...', 'language' => '...' ),
     *      array( 'asin' => '...', 'locale' => '...', 'currency' => '...', 'language' => '...' ),
     *      ...
     * )
     * ```
     */
    private $___aItems = array();

    /**
     * Stores row by asin_locale.
     *
     * @remark      This is accessed by the row class (`AmazonAutoLinks_ProductDatabase_Row`).
     * @var array
     */
    static public $aCaches = array(
        // asin_locale_currency_lang => data
    );

    /**
     * Stores the product plugin custom table object.
     * @var AmazonAutoLinks_DatabaseTable_aal_products
     */
    private $___oProductTable;

    /**
     * @var string
     */
    private $___sTableVersion = '0';

    // @deprecated
//    private $___sCurrency     = '';     // the desired currency
//    private $___sLanguage     = '';     // the desired language

    /**
     * @var string
     */
    private $___sDefaultCurrency = '';  // the default currency for the locale
    /**
     * @var string
     */
    private $___sDefaultLanguage = '';  // the default language for the locale

    /**
     * Performs necessary set-ups.
     * @since   unknown
     * @since   3.9.0   Added the `$sLanguage` and `$sCurrency` parameters
     * @since   4.3.0   Deprecated the `$sLanguage` and `$sCurrency` parameters. Renamed the first parameter to `$aItems` form `$aArguments`. Changed the structure of the first parameter.
     * @param   array   $aItems    Retrieving products from the database table with the key of ASIN|locale|currency|language
     * @param   string  $sCurrency The default currency.
     * @param   string  $sLanguage The default language.
     */
    public function __construct( array $aItems, $sCurrency='', $sLanguage='' ) {
        
        $this->___aItems           = $aItems;
        $this->___oProductTable    = new AmazonAutoLinks_DatabaseTable_aal_products;
        $this->___sTableVersion    = $this->___oProductTable->getVersion();
        $this->___setDefaultLanguageAndCurrency( ( array ) reset( $aItems ), $sCurrency, $sLanguage );

    }
        /**
         * @param array $aItem
         * @param $sCurrency
         * @param $sLanguage
         */
        private function ___setDefaultLanguageAndCurrency( array $aItem, $sCurrency, $sLanguage ) {
            $aItem = $aItem + array( 'currency' => '', 'language' => '' );
            $this->___sDefaultCurrency = $sCurrency ? $sCurrency : $aItem[ 'currency' ];
            $this->___sDefaultLanguage = $sLanguage ? $sLanguage : $aItem[ 'language' ];
        }

    /**
     * Get cached rows stored in the `AmazonAutoLinks_ProductDatabase_Row` class.
     * @param       array $aASINLocaleCurLangs
     * @return      array
     * @since       3.4.13
     * @since       4.3.0   Changed the scope to private.
     */
    private function ___getCachedProductRows( array $aASINLocaleCurLangs ) {
        return array_intersect_key( self::$aCaches, $aASINLocaleCurLangs );
    }

    /**
     * Retrieves a single cached item.
     *
     * @param string $sASINLocaleCurLang
     * @return      array
     * @since
     * @since       4.3.0       Changed the scope to private.
     */
    private function ___getProductCache( $sASINLocaleCurLang ) {
        return isset( self::$aCaches[ $sASINLocaleCurLang ] )
            ? self::$aCaches[ $sASINLocaleCurLang ]
            : array();
    }

    /**
     * @param   array|string $asASINLocaleCurLangs
     * @return  array
     */
    public function get( $asASINLocaleCurLangs='' ) {

        if ( ! empty( $asASINLocaleCurLangs ) ) {
            // Make sure all items are retrieved.
            $this->get();   // passing no parameter to retrieve all.
            return $this->___getProductCaches( $asASINLocaleCurLangs );
        }

        $_aResult = $this->___get();
        $this->___setCaches( $_aResult );
        return $_aResult;

    }
        /**
         * Retrieves multiple cached rows.
         * @param  string|array $asASINLocaleCurLangs
         * @return array
         * The caches will be messed up so a solution is needed.
         */
        private function ___getProductCaches( $asASINLocaleCurLangs='' ) {

            // If the keys are not specified, return all items.
            if ( empty( $asASINLocaleCurLangs ) ) {
                return self::$aCaches;
            }
            return is_array( $asASINLocaleCurLangs )
                ? $this->___getCachedProductRows( $asASINLocaleCurLangs )
                : $this->___getProductCache( $asASINLocaleCurLangs );

        }
        /**
         * Updates caches.
         * @return      void
         * @param       array $aNewRows
         */
        private function ___setCaches( array $aNewRows ) {
            self::$aCaches = $aNewRows + self::$aCaches;
        }

        /**
         * Retrieves the items of the specified asin locales set to the argument array.
         * @return      array
         */
        private function ___get() {
            $_aCaches   = $this->___getCachedProductRows( $this->___aItems );
            $_aUncached = $this->___getUncached( $_aCaches, $this->___aItems );
            return $this->___getRowsFromDatabaseTable( $_aUncached ) + $_aCaches;
        }

        /**
         * @param  array $aCached    key of asin_locale_cur_lang and value of row
         * @param  array $aRequested key of asin_locale_cur_lang and value of asin_locale
         * @return array An array holding uncached ASIN_locale items.
         */
        private function ___getUncached( array $aCached, array $aRequested ) {
            return array_diff_key( $aRequested, $aCached );
        }

        /**
         * Retrieve rows from the plugin custom product database table.
         *
         * @since       3
         * @since       3.4.13  Moved from `AmazonAutoLinks_UnitOutput_Base_CustomDBTable`.
         * @since       3.5.0   Renamed from `_getProductsFromCustomDBTable()`.
         * @return      array
         * @param       array   $aASINLocaleCurLangs    array( 'asin' => '', 'locale' => '', 'currency' => '', 'language' => '' ),
         */
        private function ___getRowsFromDatabaseTable( array $aASINLocaleCurLangs ) {

            if ( empty( $aASINLocaleCurLangs ) ) {
                return array();
            }
            $_aASINLocales = $_aProductIDs = $_aCurrencies = $_aLanguages = array();
            $this->___updateQueryElements( $aASINLocaleCurLangs, $_aProductIDs, $_aASINLocales, $_aCurrencies, $_aLanguages );
            return version_compare( $this->___sTableVersion, '1.4.0b01', '<' )
                ? $this->___oProductTable->getRowsByASINLocaleCurLang( $_aASINLocales, $_aCurrencies, $_aLanguages, array( $this, 'replyToFormatRow' ) )
                : $this->___oProductTable->getRowsByProductID( $_aProductIDs, array( $this, 'replyToFormatRow' ) );

        }
            /**
             * @param array $aASINLocaleCurLangs
             * @param array $aProductIDs
             * @param array $aASINLocales
             * @param array $aCurrencies
             * @param array $aLanguages
             */
            private function ___updateQueryElements( array &$aASINLocaleCurLangs, array &$aProductIDs, array &$aASINLocales, array &$aCurrencies, array &$aLanguages ) {
                $_aErrors = array();
                foreach( $aASINLocaleCurLangs as $_sASINLocaleCurLang => $_aItem ) {
                    $_aItem         = array_filter( $_aItem );
                    if ( ! isset( $_aItem[ 'asin' ], $_aItem[ 'locale' ], $_aItem[ 'currency' ], $_aItem[ 'language' ] ) ) {
                        unset( $_sASINLocaleCurLang[ $_sASINLocaleCurLang ] );
                        $_aErrors[] = $_sASINLocaleCurLang;
                        continue;
                    }
                    $aProductIDs[]  = "{$_aItem[ 'asin' ]}|{$_aItem[ 'locale' ]}|{$_aItem[ 'currency' ]}|{$_aItem[ 'language' ]}";
                    $aASINLocales[] = $_aItem[ 'asin' ] . '_' . $_aItem[ 'locale' ];
                    $aCurrencies[]  = $_aItem[ 'currency' ];
                    $aLanguages[]   = $_aItem[ 'language' ];
                }
                $aCurrencies  = array_unique( $aCurrencies );
                $aLanguages   = array_unique( $aLanguages );
                if ( ! empty( $_aErrors ) ) {
                    new AmazonAutoLinks_Error( 'RETRIEVE_PRODUCTS', 'The array structure is not valid.', $aASINLocaleCurLangs, true );
                }
            }

        /**
         * Formats each retrieved database row.
         * @param  array   $aRow
         * @param  array   &$aRows       Passed by reference so that the row (array element) can be unset.
         * @param  integer $asIndex
         * @param  integer &$aNewRows    Allows the method to inject new rows with a new index key.
         * @callback       AmazonAutoLinks_DatabaseTable_aal_products::getRows()
         * @return array   Although an array is returned, the element is unset so the returned value doesn't matter.
         */
        public function replyToFormatRow( array $aRow, &$aRows, $asIndex, &$aNewRows ) {

            // Discard the raw row element.
            unset( $aRows[ $asIndex ] );

            // Required Keys
            if ( ! isset( $aRow[ 'asin_locale' ] ) ) {
                return array();
            }

            // Format the row.
            $aRow = $aRow + array(
                'product_id'         => null,    // the table version 1.4.0 or above
                'asin'               => null,    // the table version 1.4.0 or above
                'locale'             => null,
                'preferred_currency' => null,
                'language'           => null,
            );

            // Generate a new index key and update the new array.
            // @todo there seems to be a case that `$_aASINLocale` does not hold a proper value as causing $_aASINLocale[ 1 ] to be undefined offset: 1 of a PHP notice.
            $_aASINLocale        = explode( '_', $aRow[ 'asin_locale' ] ) + array( null, null );
            $_sASIN              = $aRow[ 'asin' ] ? $aRow[ 'asin' ] : $_aASINLocale[ 0 ];
            $_sLocale            = $aRow[ 'locale' ] ? $aRow[ 'locale' ] : $_aASINLocale[ 1 ];
            $_sCurrency          = $aRow[ 'preferred_currency' ] ? $aRow[ 'preferred_currency' ] : $this->___sDefaultCurrency;
            $_sLanguage          = $aRow[ 'language' ] ? $aRow[ 'language' ] : $this->___sDefaultLanguage;
            $_sKey               = "{$_sASIN}|{$_sLocale}|{$_sCurrency}|{$_sLanguage}";
            $aNewRows[ $_sKey ]  = $aRow;

            return array();

        }

}