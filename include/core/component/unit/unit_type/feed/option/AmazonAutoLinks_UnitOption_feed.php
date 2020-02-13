<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Handles `feed` unit options.
 * 
 * @since       4.0.0

 */
class AmazonAutoLinks_UnitOption_feed extends AmazonAutoLinks_UnitOption_Base {

    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */
    static public $aStructure_Default = array(
        'feed_urls'             => '',   // (string|array) The URL(s) of the importing feed.
    );

}