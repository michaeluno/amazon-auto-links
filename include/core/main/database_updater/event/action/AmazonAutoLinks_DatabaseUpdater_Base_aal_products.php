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
 * Updates the `aal_products` plugin database table.
 *
 * @since       4.3.0
 */
class AmazonAutoLinks_DatabaseUpdater_Base_aal_products extends AmazonAutoLinks_PluginUtility {

    public function __construct() {

        // aal_action_updated_plugin_database_table_ + {table name suffix}
        add_action( 'aal_action_updated_plugin_database_table_aal_products', array( $this, 'replyToUpdateDatabase' ), 10, 3 );

    }

    /**
     * Called when the user clicks on the database table update link in the notification message
     * and the action hook is fired after database table update is done.
     *
     * Removes an index for the `asin_locale` column which has been present as it has been a unique value constraint.
     * As of v3.9.0, that constraint has been removed. So the index must be deleted as well.
     *
     * @param string $sVersionFrom
     * @param string $sVersionTo
     * @param array $aResult
     * @callback add_action aal_action_updated_plugin_database_table_aal_products
     */
    public function replyToUpdateDatabase( $sVersionFrom, $sVersionTo, $aResult ) {

        if ( ! $this->_shouldProceed( $sVersionFrom, $sVersionTo, $aResult ) ) {
            return;
        }
        $this->_doUpdate();

    }

    /**
     * Override this method.
     * @return boolean
     * @param string $sVersionFrom
     * @param string $sVersionTo
     * @param array $aResult
     * @since 4.3.0
     */
    protected function _shouldProceed( $sVersionFrom, $sVersionTo, $aResult ) {
        return true;
    }

    /**
     * Override this method.
     * @since 4.3.0
     * @return void
     */
    protected function _doUpdate() {}

}