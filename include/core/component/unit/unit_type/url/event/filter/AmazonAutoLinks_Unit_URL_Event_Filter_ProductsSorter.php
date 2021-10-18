<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Sort products array.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_URL_Event_Filter_ProductsSorter extends AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsSorter {

    /**
     * @var string
     * @since 5.0.0
     */
    public $sUnitType = 'url';

}