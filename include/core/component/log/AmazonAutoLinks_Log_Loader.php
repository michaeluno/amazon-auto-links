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
 * Loads the log component.
 *  
 * @since       4.3.0
*/
class AmazonAutoLinks_Log_Loader extends AmazonAutoLinks_PluginUtility {

    /**
     * Stored the component directory path.
     *
     * Referred to enqueue resources.
     *
     * @var string
     * @since   4.3.0
     */
    static public $sDirPath = '';

    /**
     */
    public function __construct() {

        self::$sDirPath = dirname( __FILE__ );

        new AmazonAutoLinks_Log_Error_Loader;
        new AmazonAutoLinks_Log_Debug_Loader;

    }

}