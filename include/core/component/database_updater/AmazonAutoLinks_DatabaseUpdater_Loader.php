<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * The bootstrap of the database updater component.
 *
 * @package      Amazon Auto Links
 * @since        3.8.0
 */
class AmazonAutoLinks_DatabaseUpdater_Loader {

    static $sComponentDirPath;

    public function __construct() {

        self::$sComponentDirPath = dirname( __FILE__ );

        new AmazonAutoLinks_DatabaseUpdater_Event_Ajax_Updater;

        new AmazonAutoLinks_DatabaseUpdater_AdminNotice(
            AmazonAutoLinks_Registry::$aDatabaseTables[ 'aal_products' ][ 'name' ],     // aal_products
            AmazonAutoLinks_Registry::$aDatabaseTables[ 'aal_products' ][ 'version' ]   //
        );

        // 3.10.0
        new AmazonAutoLinks_DatabaseUpdater_aal_products_121;

    }

}