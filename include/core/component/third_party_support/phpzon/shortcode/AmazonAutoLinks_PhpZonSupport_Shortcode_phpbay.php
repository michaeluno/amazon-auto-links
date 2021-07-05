<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles PhpZon's shortcodes.
 * 
 * @package     Amazon Auto Links
 * @since       4.1.0
 */
class AmazonAutoLinks_PhpZonSupport_Shortcode_phpbay extends AmazonAutoLinks_PhpZonSupport_Shortcode_phpzon {

    public $sShortcode = 'phpbay';

    /**
     * Returns the output based on the shortcode arguments.
     *
     * ### Example
     *
     * [phpbay
     *      keywords=””
     *      num=”9″
     *      customid=”Kitchen Aid Coffee Maker Water Filter Basket-Zononly-sbpm-SEKW-”
     *      templatename=”columns”
     *      columns="3"
     * ]
     *
     * @param array $aArguments The shortcode arguments.
     *
     * @return string|void
     * @since       4.1.0
     */
    public function replyToGetOutput( $aArguments ) {
        return parent::replyToGetOutput( $aArguments );
    }    

}