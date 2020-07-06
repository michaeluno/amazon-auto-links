<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Handles `scratchpad_payload` unit options.
 * 
 * @since       4.1.0

 */
class AmazonAutoLinks_UnitOption_scratchpad_payload extends AmazonAutoLinks_UnitOption_Base {

    public $sUnitType = 'scratchpad_payload';

    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */
    static public $aStructure_Default = array(
        'payload'             => '',   // (string) payload json
    );

}