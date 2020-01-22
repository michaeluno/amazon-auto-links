<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * Provides methods to retrieve rows stored in the plugin custom database table.
 *
 * @since       3.4.13
 */
class AmazonAutoLinks_ProductDatabase_Rows extends AmazonAutoLinks_ProductDatabase_Base {

    /**
     * Stores row by asin_locale.
     *
     * @todo    Support currency and language
     * @remark      This is accessed by the row class (`AmazonAutoLinks_ProductDatabase_Row`).
     * @var array
     */
    static public $aCaches = array(
        // asin_locale_currency_lang => data
    );

    /**
     * Stores the product plugin custom table object.
     * @var object
     */
    private $___oProductTable;

    private $___sCurrency = '';     // the desired currency
    private $___sLanguage = '';     // the desired language
    private $___sDefaultCurrency = '';  // the default currency for the locale
    private $___sDefaultLanguage = '';  // the default language for the locale

    /**
     * Performs necessary set-ups.
     * @since   unknown
     * @since   3.9.0       Added the $sLanguage and $sCurrency parameters
     * @param   array   $aArguments ASIN_locale items with the key of ASIN|locale|currency|language
     */
    public function __construct( array $aArguments, $sLanguage, $sCurrency ) {

        parent::__construct( $aArguments );

        $this->___oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $this->___sLanguage = $sLanguage;
        $this->___sCurrency = $sCurrency;

        $this->___setDefaultLanguageAndCurrency( $aArguments );

    }
        private function ___setDefaultLanguageAndCurrency( $aArguments ) {
            foreach( $aArguments as $_sASINLocaleCurLang => $_sASINLocale ) {
                $_aParts = explode( '|', $_sASINLocaleCurLang );
                $this->___sDefaultCurrency = $_aParts[ 2 ];
                $this->___sDefaultLanguage = $_aParts[ 3 ];
                break;
            }
        }

    /**
     * Retrieves cached rows.
     * @param  string|array $asASINLocales
     * @return array
     * The caches will be messed up so a solution is needed.
     */
    static public function getCaches( $asASINLocales='' ) {

        // If the keys are not specified, return all items.
        if ( empty( $asASINLocales ) ) {
            return self::$aCaches;
        }

        // If there are keys, retrieve them.
        if ( is_array( $asASINLocales ) ) {
            return self::getCachedRows( $asASINLocales );
        }

        // If a single key is specified, return its row.
        return self::getCache( $asASINLocales );

    }

    /**
     * Get cached rows stored in the `AmazonAutoLinks_ProductDatabase_Row` class.
     * @return      array
     */
    static public function getCachedRows( array $aASINLocales ) {
        return array_intersect_key( self::$aCaches, $aASINLocales );
//        $_aASINLocaleIndex = array_flip( $aASINLocales );
//        return array_intersect_key(
//            self::$aCaches,
//            $_aASINLocaleIndex
//        );
    }

    /**
     * Retrieves the cached item.
     *
     * @param string $sASINLocaleCurLang
     * @return      array
     */
    static public function getCache( $sASINLocaleCurLang ) {
        return isset( self::$aCaches[ $sASINLocaleCurLang ] )
            ? self::$aCaches[ $sASINLocaleCurLang ]
            : array();
    }

    /**
     * @param
     * @return array
     */
    public function get( $asASINLocales='' ) {

        if ( ! empty( $asASINLocales ) ) {
            // Make sure all items are retrieved.
            $this->get();   // no parameter - retrieve all.
            return $this->getCaches( $asASINLocales );
        }

        $_aResult = $this->___get();
        $this->___setCaches( $_aResult );
        return $_aResult;

    }

        /**
         * Updates caches.
         * @return      void
         */
        private function ___setCaches( array $aNewRows ) {
            self::$aCaches   = $aNewRows + self::$aCaches;
        }

        /**
         * Retrieves the items of the specified asin locales set to the argument array.
         * @return      array
         */
        private function ___get() {
            $_aCaches              = $this->getCachedRows( $this->_aArguments );
            $_aUncachedASINLocales = $this->___getUncached( $_aCaches, $this->_aArguments );
            $_aResult              = $this->___getRowsFromDatabaseTable( $_aUncachedASINLocales );
            return $_aResult + $_aCaches;
        }

        /**
         * @param array $aCached    key of asin_locale_cur_lang and value of row
         * @param array $aRequested key of asin_locale_cur_lang and value of asin_locale
         * @return array    An array holding uncached ASIN_locale items.
         * @todo    Test the method
         */
        private function ___getUncached( array $aCached, array $aRequested ) {
            return array_diff_key( $aRequested, $aCached );
//            $_aCachedIndex = array_keys( $aCached );
//            return array_diff( $aRequested, $_aCachedIndex );
        }

        /**
         * Retrieve rows from the plugin custom product database table.
         *
         * @since       3
         * @since       3.4.13      Moved from `AmazonAutoLinks_UnitOutput_Base_CustomDBTable`.
         * @since       3.5.0       Renamed from `_getProductsFromCustomDBTable()`.
         * @return      array
         */
        private function ___getRowsFromDatabaseTable( array $aASINLocales ) {

            if ( empty( $aASINLocales ) ) {
                return array();
            }
            $_sTableName   = $this->___oProductTable->getTableName();

            $_sASINLocales = "('" . implode( "','", $aASINLocales ) . "')";
            $_sQuery       = "SELECT * "
                . "FROM {$_sTableName} "
                . "WHERE asin_locale in {$_sASINLocales}";

            // @since 3.9.0 Added `language` and `preferred_currency` columns
            $_sCurrentVersion = get_option( "aal_products_version", '0' );
            if ( version_compare( $_sCurrentVersion, '1.2.0b01', '>=')) {
                if ( $this->___sLanguage ) {
                    $_sQuery .= " AND language='{$this->___sLanguage}'";
                }
                if ( $this->___sCurrency ) {
                    $_sQuery .= " AND preferred_currency='{$this->___sCurrency}'";
                }
            }


            $_aResults     = $this->___oProductTable->getRows( $_sQuery );
            return $this->___getRowsFormatted( $_aResults );

        }
            /**
             * Modifies the array keys to asin_locale from numeric index.
             * @return      array
             */
            private function ___getRowsFormatted( array $aRows ) {
                $_aNewRows = array();
                foreach( $aRows as $_iIndex => &$_aRow ) {
                    if ( ! isset( $_aRow[ 'asin_locale' ] ) ) {
                        continue;
                    }
                    $_aASINLocale = explode( '_', $_aRow[ 'asin_locale' ] );
                    $_sKey = $_aASINLocale[ 0 ] . '|' . $_aASINLocale[ 1 ] . '|' . $this->___sDefaultCurrency . '|' . $this->___sDefaultLanguage;
                    $_aNewRows[ $_sKey ] = $_aRow;
                }
                return $_aNewRows;
            }

}