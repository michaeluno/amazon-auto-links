<?php
/**
 * Amazon Auto Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * 
 */

/**
 * Handles `feed` unit options.
 * 
 * @since       4.0.0

 */
class AmazonAutoLinks_UnitOption_feed extends AmazonAutoLinks_UnitOption_Base {

    /**
     * Stores the unit type.
     */
    public $sUnitType = 'feed';

    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */
    static public $aStructure_Default = array(
        'feed_urls'             => '',      // (string|array) The URL(s) of the importing feed.
        'sort'                  => 'raw',   // 5.0.0
    );

}