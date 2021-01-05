<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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

        if ( ! $this->___shouldProceed() ) {
            return;
        }

        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_iCountRow     = $this->___getCountOfEmptyCell( $_oTable, 'product_id' );
        if ( $_iCountRow ) {
            $this->___updateProductIDColumn( $_oTable );
        }
        $_iCountRow     = $this->___getCountOfEmptyCell( $_oTable, 'asin' );
        if ( $_iCountRow ) {
            $this->___updateASINColumn( $_oTable );
        }

    }
        private function ___shouldProceed() {
            $_sProductsTableVersion = get_option( 'aal_products_version', '0' );
            if ( ! $_sProductsTableVersion ) {
                return false;
            }
            if ( version_compare( $_sProductsTableVersion, '1.4.0b', '<' ) ) {
                return false;
            }
            return true;
        }

        private function ___updateProductIDColumn( AmazonAutoLinks_DatabaseTable_aal_products $oTable ) {
            $_sTableName = $oTable->getTableName();
            $_sQuery     = "UPDATE IGNORE `{$_sTableName}`"
                . " SET product_id = CONCAT( SUBSTRING( asin_locale, 1, 10 ), '|', SUBSTRING( asin_locale, 12, 2 ), '|', preferred_currency, '|', language )"
                . " WHERE product_id IS NULL OR product_id = ''"
                . " ;";
            return $oTable->getVariable( $_sQuery );
        }
        private function ___updateASINColumn( AmazonAutoLinks_DatabaseTable_aal_products $oTable ) {
            $_sTableName = $oTable->getTableName();
            $_sQuery     = "UPDATE `{$_sTableName}`"
                . " SET asin = CONCAT( SUBSTRING( asin_locale, 1, 10 ) )"
                . " WHERE asin IS NULL OR asin = ''"
                . " ;";
            return $oTable->getVariable( $_sQuery );
        }
        /**
         * @param  AmazonAutoLinks_DatabaseTable_aal_products $oTable
         * @param  string $sColumnName
         * @return integer
         * @since  4.3.3
         */
        private function ___getCountOfEmptyCell( AmazonAutoLinks_DatabaseTable_aal_products $oTable, $sColumnName ) {
            $_sTableName    = $oTable->getTableName();
            return ( integer ) $oTable->getVariable(
                "SELECT COUNT(*) FROM `{$_sTableName}` WHERE `{$sColumnName}` IS NULL OR `{$sColumnName}` = ' ';"
            );
        }

}