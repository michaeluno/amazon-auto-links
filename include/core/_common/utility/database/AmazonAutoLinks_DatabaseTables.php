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
 * Creates custom database tables for the plugin.
 * 
 * @since 5.2.6
 */
class AmazonAutoLinks_DatabaseTables {

    /**
     * @since 5.2.6
     * @param false $bForce
     */
    public function installAll( $bForce=false ) {
        foreach( AmazonAutoLinks_Registry::$aDatabaseTables as $_sKey => $_aArguments ) {
            $_sClassName = "AmazonAutoLinks_DatabaseTable_{$_sKey}";
            /**
             * @var AmazonAutoLinks_DatabaseTable_aal_products|AmazonAutoLinks_DatabaseTable_aal_request_cache|AmazonAutoLinks_DatabaseTable_aal_tasks $_oTable
             */
            $_oTable     = new $_sClassName;
            $_oTable->install( $bForce );
        }
    }

    /**
     * @since 5.2.6
     */
    public function uninstallAll() {
        foreach( AmazonAutoLinks_Registry::$aDatabaseTables as $_sKey => $_aArguments ) {
            $_sClassName = "AmazonAutoLinks_DatabaseTable_{$_sKey}";
            /**
             * @var AmazonAutoLinks_DatabaseTable_aal_products|AmazonAutoLinks_DatabaseTable_aal_request_cache|AmazonAutoLinks_DatabaseTable_aal_tasks $_oTable
             */
            $_oTable     = new $_sClassName;
            $_oTable->uninstall();
        }
    }

}