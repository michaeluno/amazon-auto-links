<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Handles `embed` unit options.
 * 
 * @since       4.0.0

 */
class AmazonAutoLinks_UnitOption_embed extends AmazonAutoLinks_UnitOption_Base {

    /**
     * Stores the default structure and key-values of the unit.
     * @remark      Accessed from the base class constructor to construct a default option array.
     */
    static public $aStructure_Default = array(
        'uri'                  => '',   // (string) The URL passed to oEmbed iframe request.
        // @deprecated not used at the moment
//        'maxwidth'             => 600,  // (integer) the iframe width in pixel
//        'maxheight'            => 320,  // (integer) the iframe height in pixel
    );

}