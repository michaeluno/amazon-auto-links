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
 * Creates plugin specific database tables.
 * 
 * @since       3
 */
class AmazonAutoLinks_DatabaseTable_aal_products extends AmazonAutoLinks_DatabaseTable_Utility {

    /**
     * Returns the table arguments.
     * @return      array
     * @since       3.5.0
     */
    protected function _getArguments() {
        return AmazonAutoLinks_Registry::$aDatabaseTables[ 'aal_products' ];
    }

    /**
     * Represents the structure of a row of the table.
     *
     * @remark      accessed from outside to construct a row array to insert data into the table.
     */
    static public $aStructure_Row = array(
        'object_id'                     => null,   // (integer)
        'product_id'                    => null,   // (string) 4.3.0 XXXXXXXXXX_XX|XXX|xx-XX
        'asin'                          => null,   // (string) XXXXXXXXXX
        'asin_locale'                   => null,   // @deprecated 4.3.0 (string) XXXXXXXXXX_XX
        'locale'                        => null,   // (string) XX      e.g. US
        'modified_time'                 => null,   // (string) dddd-dd-dd dd:dd:dd
        'expiration_time'               => null,   // (string) dddd-dd-dd dd:dd:dd
        'title'                         => null,   // (string) The product name.
        'links'                         => null,   // (string)
        'rating'                        => null,   // (integer)    e.g. 45
        'rating_image_url'              => null,   // (string)  // @todo deprecate it
        'rating_html'                   => null,   // (string)  // @todo deprecate it
        'price'                         => null,   // (integer)    Listed price. e.g. 46.56
        'price_formatted'               => null,   // (string)
        'currency'                      => null,   // (string)  e.g. USD
        'sales_rank'                    => null,   // (integer)
        'lowest_new_price'              => null,   // (integer)
        'lowest_new_price_formatted'    => null,   // (string)
        'lowest_used_price'             => null,   // (integer)
        'lowest_used_price_formatted'   => null,   // (string)
        'discounted_price'              => null,
        'discounted_price_formatted'    => null,
        'count_new'                     => null,   // (integer)
        'count_used'                    => null,   // (integer)
        'description'                   => null,   // (string) product details
        'images'                        => null,   // (string) serialized array containing product lists
        'similar_products'              => null,   // (string) serialized array containing product lists
        'editorial_reviews'             => null,   // (string) serialized array containing editorial review text.
        'customer_review_url'           => null,   // (string)  // @todo deprecate it
        'customer_review_charset'       => null,   // (string)
        'customer_reviews'              => null,   // (string) serialized array containing user reviews.
        'number_of_reviews'             => null,   // (integer) number of customer reviews.
        'features'                      => null,   // (string) 3.8.0
        'categories'                    => null,   // (string) 3.8.0
        'is_prime'                      => null,   // (boolean) 3.9.0
        'is_adult'                      => null,   // (boolean) 3.9.0
        'language'                      => null,   // (string) 3.9.0 displaying language,  e.g. de_DE
        'preferred_currency'            => null,   // (string) 3.9.0
        'error'                         => null,   // (string) 3.9.0
        'delivery_free_shipping'        => null,   // (boolean) 3.10.0
        'delivery_fba'                  => null,   // (boolean) 3.10.0
    );

    /**
     *
     * @return      string
     * @since       3
     */
    public function getCreationQuery() {
        // asin_locale varchar(13) UNIQUE NOT NULL,
        // asin_locale varchar(13) NOT NULL,
        return "CREATE TABLE " . $this->aArguments[ 'table_name' ] . " (
            object_id bigint(20) unsigned NOT NULL auto_increment,
            product_id varchar(128) UNIQUE,            
            asin varchar(10),
            asin_locale varchar(13) NOT NULL,
            locale varchar(4),            
            language varchar(10),            
            modified_time datetime NOT NULL default '0000-00-00 00:00:00',
            expiration_time datetime NOT NULL default '0000-00-00 00:00:00',
            title text,
            links text,
            rating tinyint unsigned,
            rating_image_url text,
            rating_html blob,
            preferred_currency varchar(10),
            currency varchar(10),
            sales_rank bigint(20),
            price bigint(20) unsigned,
            price_formatted tinytext,
            lowest_new_price bigint(20) unsigned,
            lowest_new_price_formatted tinytext,
            lowest_used_price bigint(20) unsigned,
            lowest_used_price_formatted tinytext,
            discounted_price bigint(20) unsigned,
            discounted_price_formatted tinytext,
            count_new bigint(20) unsigned,
            count_used bigint(20) unsigned,
            description mediumtext,
            images mediumtext,
            similar_products mediumtext,
            editorial_reviews mediumblob,
            customer_review_url text,
            customer_review_charset varchar(20),
            customer_reviews mediumblob,
            number_of_reviews bigint(20) unsigned,
            categories text,
            features text,
            is_prime tinyint(1),            
            is_adult tinyint(1),
            error varchar(800),
            delivery_free_shipping tinyint(1),            
            delivery_fba tinyint(1),                            
            PRIMARY KEY  (object_id) 
        ) " . $this->_getCharactersetCollation() . ";";
    }

    /**
     * Sets a row.
     * @param  string        $sASINLocale A combination of ASIN + underscore + upper case locale notation.
     * @param  array         $aRow The row data.
     * @param  string        $sCurrency
     * @param  string        $sLanguage
     * @param  array|string  $asFormat Placeholders that indicate row array element data types. Leave it `null` to apply auto-generated formats.
     * @return integer       The object id if successfully set; otherwise, 0;
     * @since  3
     * @since  3.9.0         Added the currency and language parameters. Moved the `$asFormat` parameter to the last.
     */
    public function setRowByASINLocale( $sASINLocale, $aRow, $sCurrency='', $sLanguage='', $asFormat=null ) {

        $sCurrency = $sCurrency
            ? $sCurrency
            : ( isset( $aRow[ 'preferred_currency' ] ) ? $aRow[ 'preferred_currency' ] : '' );
        $sLanguage = $sLanguage
            ? $sLanguage
            : ( isset( $aRow[ 'language' ] ) ? $aRow[ 'language' ] : '' );
        $_iID  = $this->getIDByASINLocale( $sASINLocale, $sCurrency, $sLanguage );
        if ( $_iID ) {
            $_iCountSetRows = $this->update( 
                $aRow, // data
                array( 'object_id' => $_iID ),   // where
                $asFormat,   // row format
                array( '%d' ) // WHERE format - %d: digit
            );
        } 
        // If it is a new item,
        else {
            $_iCountSetRows = $this->replace(
                $aRow,
                $asFormat
            );
            $_iID  = $this->getIDByASINLocale( $sASINLocale, $sCurrency, $sLanguage );
        }     
        // This method is supposed to edit only one row 
        // so when the method returns a value that yields true, return the object ID.
        return $_iCountSetRows
            ? $_iID
            : 0;
            
    }

    /**
     * @param string $sASIN
     * @param string $sLocale
     * @param string $sCurrency
     * @param string $sLanguage
     * @return integer The object ID of the record.
     * @deprecated 4.3.0 Unused.
     */
/*    public function getID( $sASIN, $sLocale, $sCurrency='', $sLanguage='' ) {
        $_sASINLocale = $sASIN . '_' . strtoupper( $sLocale );
        return $this->getIDByASINLocale( $_sASINLocale, $sCurrency, $sLanguage );
    }*/

    /**
     * @param string $sASINLocale
     * @param string $sCurrency
     * @param string $sLanguage
     * @return mixed
     */
    public function getIDByASINLocale( $sASINLocale, $sCurrency='', $sLanguage='' ) {

        $_sQuery = "SELECT object_id "
            . "FROM {$this->aArguments[ 'table_name' ]} "
            . "WHERE asin_locale = '{$sASINLocale}'";

        // @since 3.9.0 Added `language` and `preferred_currency` columns
        $_sCurrentVersion = get_option( "aal_products_version", '0' );
        if ( version_compare( $_sCurrentVersion, '1.2.0b01', '>=')) {
            if ( $sLanguage ) {
                $_sQuery .= " AND language='{$sLanguage}'";
            }
            if ( $sCurrency ) {
                $_sQuery .= " AND preferred_currency='{$sCurrency}'";
            }
        }

        return $this->getVariable( $_sQuery );
    }

    /**
     * Checks whether a row exists or not by ASIN and locale.
     * @param  string $sASIN
     * @param  string $sLocale
     * @param  string $sCurrency
     * @param  string $sLanguage
     * @return boolean
     * @deprecated 4.3.0 Unused.
     */
/*    public function doesRowExist( $sASIN, $sLocale, $sCurrency='', $sLanguage='' ) {
        return ( boolean ) $this->getID( $sASIN, $sLocale, $sCurrency, $sLanguage );
    }*/

    /**
     * @param  array    $aProductIDs
     * @param  callable $cCallable
     * @return array    An array holding retrieved rows.
     * @since  4.3.0
     */
    public function getRowsByProductID( array $aProductIDs, $cCallable=null ) {
        $_sProductIDs = "('" . implode( "','", $aProductIDs ) . "')";
        $_sQuery = "SELECT * "
            . "FROM `" . $this->getTableName() . "` "
            . "WHERE product_id in {$_sProductIDs}";
        return $this->getRows( $_sQuery, 'ARRAY_A', $cCallable );
    }

    /**
     * @remark This is for backward-compatibility with the table version below 1.4.0.
     * @param  array        $aASINLocales   Format: {ASIN}_{locale} e.g. XXXXXXXXXX_IT
     * @param  array|string $asCurrencies
     * @param  array|string $asLanguages
     * @param  callable     $cCallable
     * @since  4.3.0
     * @return array
     */
    public function getRowsByASINLocaleCurLang( array $aASINLocales, $asCurrencies, $asLanguages, $cCallable=null ) {

        $_sASINLocales = "('" . implode( "','", $aASINLocales ) . "')";
        $_sQuery       = "SELECT * "
            . "FROM `" . $this->getTableName() . "` "
            . "WHERE asin_locale in {$_sASINLocales}";

        // @since 3.9.0 Added `language` and `preferred_currency` columns
        if ( version_compare( $this->getVersion(), '1.2.0b01', '>=' ) ) {
            $_sQuery .= $this->___getQueryPartForCurrency( $asCurrencies );
            $_sQuery .= $this->___getQueryPartForLanguage( $asLanguages );
        }

        return $this->getRows( $_sQuery, 'ARRAY_A', $cCallable );

    }
        /**
         * @param array|string $asCurrencies
         * @return string
         * @since 4.3.0
         */
        private function ___getQueryPartForCurrency( $asCurrencies ) {
            if ( empty( $asCurrencies ) ) {
                return '';
            }
            $_aCurrencies  = ( array ) $asCurrencies;
            $_aCurrencies  = array_unique( $_aCurrencies );
            $_sCurrencies  = "('" . implode( "','", $_aCurrencies ) . "')";
            return ( 1 < count( $_aCurrencies ) )
                ? " AND preferred_currency in '{$_sCurrencies}'"
                : " AND preferred_currency='" . reset( $_aCurrencies ) . "'";
        }
        /**
         * @param array|string $asLanguages
         * @return string
         * @since 4.3.0
         */
        private function ___getQueryPartForLanguage( $asLanguages ) {
            if ( empty( $asLanguages ) ) {
                return '';
            }
            $_aLanguages   = ( array ) $asLanguages;
            $_aLanguages   = array_unique( $_aLanguages );
            $_sLanguages   = "('" . implode( "','", $_aLanguages ) . "')";
            return ( 1 < count( $_aLanguages ) )
                ? " AND language in '{$_sLanguages}'"
                : " AND language='" . reset( $_aLanguages ) . "'";
        }

}