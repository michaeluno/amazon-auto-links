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
 * Handles sorting products.
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_Output_Sort extends AmazonAutoLinks_Unit_Utility {

    /**
     * @var   array
     * @since 5.0.0
     */
    public $aProducts = array();

    /**
     * @var   string
     * @since 5.0.0
     */
    public $sSortType = 'raw';

    /**
     * @var   string
     * @since 5.0.0
     */
    public $sTitleKey = 'title';

    /**
     * @var   array
     * @since 5.3.4
     */
    public $aKeywords = array();

    /**
     * Sets up properties and hooks.
     * @since  5.0.0
     * @since  5.3.4  Added the $aKeywords parameter
     * @param  array  $aProducts The product data to sort.
     * @param  string $sSortType The sort order.
     * @param  string $sTitleKey The array key for the title used for the title (ascending and descending) sort order.
     * @param  array  $aKeywords Custom keywords to be used to sort. Used for the `asin` sort order.
     */
    public function __construct( array $aProducts, $sSortType, $sTitleKey='title', array $aKeywords=array() ) {
        $this->aProducts = $aProducts;
        $this->sSortType = $sSortType;
        $this->sTitleKey = $sTitleKey;
        $this->aKeywords = $aKeywords;
    }

    /**
     * @return array
     * @since  5.0.0
     */
    public function get() {
        $_sMethodName = "_getItemsSorted_{$this->sSortType}";
        if ( ! method_exists( $this, $_sMethodName ) ) {
            return $this->aProducts;
        }
        return $this->{$_sMethodName}( $this->aProducts );
    }

    /**
     * @since  3.2.1
     * @since  3.5.0 Moved from `AmazonAutoLinks_UnitOutput_url`.
     * @since  3.9.3 Changed the scope to `protected`. Moved from `AmazonAutoLinks_UnitOutput_item_lookup`.
     * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_Base_ProductFilter`.
     * @param  array $aProducts
     * @return array
     */
    protected function _getItemsSorted_( $aProducts ) {
        return $this->_getItemsSorted_raw( $aProducts );
    }
    protected function _getItemsSorted_title_ascending( $aProducts ) {
        return $this->_getItemsSorted_title( $aProducts );
    }
    protected function _getItemsSorted_title( $aProducts ) {
        uasort( $aProducts, array( $this, 'replyToSortProductsByTitle' ) );
        return $aProducts;
    }
    protected function _getItemsSorted_title_descending( $aProducts ) {
        uasort( $aProducts, array( $this, 'replyToSortProductsByTitleDescending' ) );
        return $aProducts;
    }
    protected function _getItemsSorted_random( $aProducts ) {
        shuffle( $aProducts );
        return $aProducts;
    }
    protected function _getItemsSorted_raw( $aProducts ) {
        return $aProducts;
    }
        public function replyToSortProductsByTitle( $aProductA, $aProductB ) {
            $_sTitleA = $this->getElement( $aProductA, $this->sTitleKey );
            $_sTitleB = $this->getElement( $aProductB, $this->sTitleKey );
            return strnatcasecmp( $_sTitleA, $_sTitleB );
        }
        public function replyToSortProductsByTitleDescending( $aProductA, $aProductB ) {
            $_sTitleA = $this->getElement( $aProductA, $this->sTitleKey );
            $_sTitleB = $this->getElement( $aProductB, $this->sTitleKey );
            return strnatcasecmp( $_sTitleB, $_sTitleA );
        }

    /**
     * @param  array $aProducts
     * @since  5.3.4
     * @return array
     */
    protected function _getItemsSorted_asin( $aProducts ) {
        $_aSortedProducts = array();
        foreach( $this->aKeywords as $_sASIN ) {
            $_aProduct = $this->getElement( $aProducts, $_sASIN );
            if ( empty( $_aProduct ) ) {
                continue;
            }
            $_aSortedProducts[ $_sASIN ] = $_aProduct;
        }
        return $_aSortedProducts;

        // @note If unrelated items which matched the passed ASINs are needed,
        // use this instead of using the newly created array.
        // return $_aSortedProducts + $aProducts;
    }

}