<?php
/**
 * Amazon Auto Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * 
 */

/**
 * Handles ad_widget_search unit options.
 * 
 * @since 5.0.0

 */
class AmazonAutoLinks_UnitOption_ad_widget_search extends AmazonAutoLinks_UnitOption_Base {

    /**
     * @var string
     */
    public $sUnitType = 'ad_widget_search';

    /**
     * Stores the default structure and key-values of the unit.
     * @remark Accessed from the base class constructor to construct a default option array.
     */
    static public $aStructure_Default = array(
        'Keywords'              => '',
        'sort'                  => 'raw', // title, title_descending
    );

}