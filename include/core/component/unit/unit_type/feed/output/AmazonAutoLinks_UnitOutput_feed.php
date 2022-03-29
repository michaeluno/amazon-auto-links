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
 * Generates Amazon product links for feed outputs.
 *
 * @since       4.0.0
 */
class AmazonAutoLinks_UnitOutput_feed extends AmazonAutoLinks_UnitOutput_category {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'feed';

    /**
     * Lists the tags (variables) used in the Item Format unit option that require to access the custom database.
     * @remark  For this `feed` unit type, this must be empty not to trigger unnecessary database access.
     * @see AmazonAutoLinks_UnitOutput_Base::___hasCustomDBTableAccess()
     * @var array
     */
    protected $_aItemFormatDatabaseVariables = array();

}