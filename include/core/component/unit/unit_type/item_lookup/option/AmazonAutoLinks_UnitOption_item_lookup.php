<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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

        // Advanced fields
        'MerchantId'    => 'All',
        'Condition'     => 'New',
        
        'search_per_keyword'    => true,    // 3.2.0+


        '_sort'          => 'raw',  // 3.5.0+ (string) or `random`
    );

    /**
     * @since       3.4.6
     */
    public static $aShortcodeArgumentKeys = array(
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
            + parent::$aStructure_Default
            + AmazonAutoLinks_UnitOption_search::$aStructure_Default;
    }

    /**
     * 
     * @since       3 
     */
    protected function format( array $aUnitOptions ) {

        $aUnitOptions = $this->getShortcodeArgumentKeysSanitized( $aUnitOptions, self::$aShortcodeArgumentKeys );
        $aUnitOptions = parent::format( $aUnitOptions )
            + AmazonAutoLinks_UnitOutput_similarity_lookup::$aStructure_APIParameters;
        $aUnitOptions = $this->sanitize( $aUnitOptions );
        return $aUnitOptions;
        
    }    
    
        /**
         * Sanitizes the unit options of the item_lookup unit type.
         * 
         * @since       2.0.2
         * @since       3           Moved from ``.
         * @return      array
         */
        protected function sanitize( array $aUnitOptions ) {
            
            // if the ISDN is spceified, the search index must be set to Books.
            if ( 
                isset( $aUnitOptions[ 'IdType' ], $aUnitOptions[ 'SearchIndex' ] )
                && 'ISBN' === $aUnitOptions[ 'IdType' ]
            ) {
                $aUnitOptions[ 'SearchIndex' ] = 'Books';
            }
            $aUnitOptions[ 'ItemId' ] =  trim( 
                $this->trimDelimitedElements( 
                    $aUnitOptions[ 'ItemId' ], 
                    ',' 
                ) 
            );
            return $aUnitOptions;
            
        }            
    

}