<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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
     * @remark      This is accessed by the row class (`AmazonAutoLinks_ProductDatabase_Row`).
     * @var array
     */
    static public $aCaches = array();

    /**
     * Stores the product plugin custom table object.
     * @var object
     */
    private $___oProductTable;

    /**
     * Performs necessary set-ups.
     */
    public function __construct( array $aArguments ) {

        parent::__construct( $aArguments );

        $this->___oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;

    }

    /**
     * Retrieves cached rows.
     * @param  string|array $asASINLocales
     * @return array
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
        $_aASINLocaleIndex = array_flip( $aASINLocales );
        return array_intersect_key(
            self::$aCaches,
            $_aASINLocaleIndex
        );
    }

    /**
     * Retrieves the cached item.
     *
     * @param string $sASINLocale
     * @return      array
     */
    static public function getCache( $sASINLocale ) {
        return isset( self::$aCaches[ $sASINLocale ] )
            ? self::$aCaches[ $sASINLocale ]
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
            $_aCaches        = $this->getCachedRows( $this->_aArguments );
            $_aUncachedItems = $this->___getUncached( $_aCaches, $this->_aArguments );
            $_aResult        = $this->___getRowsFromDatabaseTable( $_aUncachedItems );
            return $_aResult + $_aCaches;
        }

        /**
         * @param array $aCached
         * @param array $aRequested
         * @return array
         */
        private function ___getUncached( array $aCached, array $aRequested ) {
            $_aCachedIndex = array_keys( $aCached );
            return array_diff( $aRequested, $_aCachedIndex );
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
            $_aResults     = $this->___oProductTable->getRows(
                "SELECT * "
                . "FROM {$_sTableName} "
                . "WHERE asin_locale in {$_sASINLocales}"
            );
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
                    $_aNewRows[ $_aRow[ 'asin_locale' ] ] = $_aRow;

                }
                return $_aNewRows;
            }

}