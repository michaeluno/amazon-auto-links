<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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
     * Sets up properties and hooks.
     * @since  5.0.0
     */
    public function __construct( array $aProducts, $sSortType, $sTitleKey='title' ) {
        $this->aProducts = $aProducts;
        $this->sSortType = $sSortType;
        $this->sTitleKey = $sTitleKey;
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

}