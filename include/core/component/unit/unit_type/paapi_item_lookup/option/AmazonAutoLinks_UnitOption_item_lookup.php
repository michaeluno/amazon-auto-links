<?php
/**
 * Auto Amazon Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 * 
 */

/**
 * Handles ItemLookUp unit options.
 * 
 * @since       3

 */
class AmazonAutoLinks_UnitOption_item_lookup extends AmazonAutoLinks_UnitOption_search {

    /**
     * Stores the unit type.
     */
    public $sUnitType = 'item_lookup';

    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */
    public static $aStructure_Default = array(
        
        // Main fields
        'ItemId'        => null,
        'IdType'        => 'ASIN',
        'Operation'     => 'ItemLookup',
        'SearchIndex'   => 'All',

        // For PA-API 5
        'ItemIds'       => array(),

        // Advanced fields
        'MerchantId'    => 'All',
        'Condition'     => 'Any',
        
        'search_per_keyword'    => false,    // 3.2.0+ // 3.11.0 changed it to false to reduce API calls

        '_sort'          => 'raw',  // 3.5.0+ (string) or `random`

        'product_title'  => null,   // 4.0.0+ (string) Alter the product title.

    );

    /**
     * @since       3.4.6
     */
    public static $aShortcodeArgumentKeys = array(
        'asin'                  => 'ASIN',          // 5.0.0 the shortcode argument
        'operation'             => 'Operation',
        'searchindex'           => 'SearchIndex',
        'merchantid'            => 'MerchantId',     
        'condition'             => 'Condition',         
        'itemid'                => 'ItemId',
        'idtype'                => 'IdType',        
    );    
    
    /**
     * 
     * @return  array
     */
    protected function getDefaultOptionStructure() {
        return self::$aStructure_Default 
            + parent::$aStructure_Default;
    }

    /**
     * 
     * @since   3
     * @since   4.0.0 Renamed from `format()` as it was too general.
     * @param   array $aUnitOptions
     * @param   array $aDefaults
     * @param   array $aRawOptions
     * @return  array
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults, array $aRawOptions ) {
        $aUnitOptions = $this->_getShortcodeArgumentKeysSanitized( $aUnitOptions, self::$aShortcodeArgumentKeys );

        // 5.3.8 When redirected from an ad_widget_search unit, the `Keywords` argument may contain ASINs. This is when multiple ASINs are passed to the Keywords argument
        if ( empty( $aUnitOptions[ 'ASIN' ] ) && ! empty( $aUnitOptions[ 'Keywords' ] ) ) {
            $aUnitOptions[ 'ASIN' ] = $aUnitOptions[ 'Keywords' ];
        }

        $aUnitOptions = $this->___getShortcodeArgumentsConverted( $aUnitOptions );
        $aUnitOptions = parent::_getUnitOptionsFormatted( $aUnitOptions, $aDefaults, $aRawOptions );
        $aUnitOptions[ 'Operation' ]      = 'GetItems';
        return $this->___getOptionsFormatted( $aUnitOptions );
    }
        /**
         * @param  array $aUnitOptions
         * @return array
         * @since  5.0.0
         */
        private function ___getShortcodeArgumentsConverted( $aUnitOptions ) {
            if ( ! isset( $aUnitOptions[ 'ASIN' ] ) ) {
                return $aUnitOptions;
            }
            $_aASINs = $this->getStringIntoArray( $aUnitOptions[ 'ASIN' ], ',' );
            $aUnitOptions[ 'ItemId' ]         = implode( ',', $_aASINs );
            $aUnitOptions[ '_allowed_ASINs' ] = $_aASINs;
            return $aUnitOptions;
        }
        /**
         * Formats the unit-type-specific options of the item_lookup unit type.
         * 
         * @since  2.0.2
         * @since  3      Moved from ``.
         * @since  5      Renamed from `sanitize()`.
         * @return array
         * @param  array  $aUnitOptions
         */
        protected function ___getOptionsFormatted( array $aUnitOptions ) {
            
            // if an ISDN is specified, the search index must be set to Books.
            if ( 
                isset( $aUnitOptions[ 'IdType' ], $aUnitOptions[ 'SearchIndex' ] )
                && 'ISBN' === $aUnitOptions[ 'IdType' ]
            ) {
                $aUnitOptions[ 'SearchIndex' ] = 'Books';
            }
            $aUnitOptions[ 'ItemId' ] =  trim( $this->getEachDelimitedElementTrimmed( $aUnitOptions[ 'ItemId' ], ',' ) );

            $_aItemIDs = array_merge(
                $this->getElementAsArray( $aUnitOptions, 'ItemIds' ),
                $this->getStringIntoArray( str_replace( PHP_EOL, ',', $aUnitOptions[ 'ItemId' ] ), ',' )
            );
            $aUnitOptions[ 'ItemIds' ] = array_unique( $_aItemIDs );
            return $aUnitOptions;
            
        }            

}