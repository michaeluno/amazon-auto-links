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
 * Saves unusable proxies.
 *
 * @package      Amazon Auto Links
 * @since        4.3.0
 */
class AmazonAutoLinks_DatabaseUpdater_Action_PluginActivation_aal_products_140 extends AmazonAutoLinks_PluginUtility {

    /**
     * @assmes  Already checked whether the proxy option is enabled or not.
     * @since   4.2.0
     */
    public function __construct() {

        add_action( 'aal_action_plugin_activated', array( $this, 'replyToDoAction' ), 10 );

    }

    /**
     * If the user downgraded the plugin after the table is updated and then display some units,
     * `asin` and `product_id` fields will be all null, which should not be.
     */
    public function replyToDoAction() {

        $_sProductsTableVersion = get_option( 'aal_products_version', '0' );
        if ( ! $_sProductsTableVersion ) {
            return;
        }
        if ( version_compare( $_sProductsTableVersion, '1.4.0b', '<' ) ) {
            return;
        }
        $_aQueries      = array();
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_sTableName    = $_oTable->getTableName();
        $_sColumn       = 'product_id';
        $_iCountRow     = $_oTable->getVariable(
            "SELECT COUNT(*) FROM `{$_sTableName}` WHERE `{$_sColumn}` IS NULL OR `{$_sColumn}` = ' ';"
        );
        if ( $_iCountRow ) {
            $_aQueries[] = "UPDATE IGNORE `{$_sTableName}`"
                . " SET product_id = CONCAT( SUBSTRING( asin_locale, 1, 10 ), '|', SUBSTRING( asin_locale, 12, 2 ), '|', preferred_currency, '|', language )"
                . " WHERE product_id IS NULL OR product_id = ''"
                . " ;";
        }
        $_sColumn       = 'asin';
        $_iCountRow     = $_oTable->getVariable(
            "SELECT COUNT(*) FROM `{$_sTableName}` WHERE `{$_sColumn}` IS NULL OR `{$_sColumn}` = ' ';"
        );
        if ( $_iCountRow ) {
            $_aQueries[] = "UPDATE `{$_sTableName}`"
            . " SET asin = CONCAT( SUBSTRING( asin_locale, 1, 10 ) )"
            . " WHERE asin IS NULL OR asin = ''"
            . " ;";
        }
        foreach( $_aQueries as $_sQuery ) {
            $_oTable->getVariable( $_sQuery );
        }

    }

}