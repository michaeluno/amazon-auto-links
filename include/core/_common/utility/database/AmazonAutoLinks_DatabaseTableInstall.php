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
 * Creates custom database tables for the plugin.
 * 
 * @since       3
 */
class AmazonAutoLinks_DatabaseTableInstall {

    /**
     * 
     */
    public function __construct( $bInstallOrUninstall ) {

        $_sMethodName = $bInstallOrUninstall
            ? 'install'
            : 'uninstall';
            
        foreach( AmazonAutoLinks_Registry::$aDatabaseTables as $_sKey => $_aArguments ) {
            $_sClassName = "AmazonAutoLinks_DatabaseTable_{$_sKey}";
            $_oTable     = new $_sClassName;
            $_oTable->$_sMethodName();
        }
 
    }
   
}