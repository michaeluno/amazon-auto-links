<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
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
            
        foreach( AmazonAutoLinks_Registry::$aDatabaseTables as $_sKey => $_sTableName ) {
            $_sClassName = "AmazonAutoLinks_DatabaseTable_$_sKey";
            $_oTable     = new $_sClassName(
                $_sTableName,
                AmazonAutoLinks_Registry::$aDatabaseTableVersions[ $_sKey ] // version
            );
            $_oTable->$_sMethodName();
        }
 
    }
   
}