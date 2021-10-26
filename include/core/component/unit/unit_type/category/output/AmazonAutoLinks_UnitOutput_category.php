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
 * Creates Amazon product links by category.
 *
 * @since ?
 * @since 3     Changed the name from `AmazonAutoLinks_UnitOutput_Category`.
 * @since 3.8.1 deprecated
 * @since 3.9.0 Serves as a base class for `AmazonAutoLinks_UnitOutput_category3`
 * @since 4.3.4 Merged with `AmazonAutoLinks_UnitOutput_category3`.
 */
class AmazonAutoLinks_UnitOutput_category extends AmazonAutoLinks_UnitOutput_Base_ElementFormat {

    /**
     * Stores the unit type.
     * @remark The base constructor creates a unit option object based on this value.
     * @var    string
     */
    public $sUnitType = 'category';

    /**
     * Stores modified dates for HTTP requests so these can be applied to the product updated date.
     * @since 3.9.0
     * @since 4.0.0 Changed the scope to protected as the Embed unit type extends this class and uses this property.
     * @since 4.3.4 Moved from `AmazonAutoLinks_UnitOutput_category3`.
     * @since 5.0.0 Changed the scope to public from protected as delegator classes access it.
     * @var   array
     */
    public $aModifiedDates = array();

    /**
     * Sets up type-specific properties.
     */
    protected function _setProperties() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->getPAAPIStatus( $this->oUnitOption->get( 'country' ) ) ) {
            $this->_aItemFormatDatabaseVariables[] = '%description%'; // updated in `replyToFormatProductWithDBRow()`.
            $this->_aItemFormatDatabaseVariables[] = '%content%';
            $this->_aItemFormatDatabaseVariables[] = '%feature%';     // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%category%';    // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%rank%';        // 3.8.0
            $this->_aItemFormatDatabaseVariables[] = '%prime%';       // 3.9.0
            $this->_aItemFormatDatabaseVariables[] = '%discount%';    // 4.7.8
        }
    }

}