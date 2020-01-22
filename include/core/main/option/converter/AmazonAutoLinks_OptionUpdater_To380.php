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
 * Updates the plugin options.
 *
 * @since       3.8.0
 */
class AmazonAutoLinks_OptionUpdater_To380 extends AmazonAutoLinks_PluginUtility {

    public function __construct() {
        add_action( 'aal_action_update_plugin_database_table', array( $this, 'replyToUpdateOptions' ), 10, 3 );
    }

    /**
     * This is called when the user clicks on the database table update link in the notification message
     * and the action hook is fired.
     * @param $sTableName
     * @param $sVersionFrom
     * @param $sVersionTo
     */
    public function replyToUpdateOptions( $sTableName, $sVersionFrom, $sVersionTo ) {

//        if ( version_compare( '1.1.0b01', $sVersionTo, '<' ) ) {
//            return;
//        }

        $_baOptions          = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ] );
        if ( ! $_baOptions ) {
            // If options are not set, do nothing.
            return;
        }

        $_aOptions           = $this->getAsArray( $_baOptions );
        if ( ! isset( $_aOptions[ 'unit_default' ] ) ) {
            // If the subject key is not set, do nothing.
            return;
        }

        // At this point, the user has already saved the options.
        $_sItemFormat        = $this->getElement( $_aOptions, array( 'unit_default', 'item_format' ), '' );
        $_aLegacyItemFormats = $this->___getLegacyDefaultItemFormats();
        foreach( $_aLegacyItemFormats as $_iIndex => $_sThisItemFormat ) {
            // Remove line feeds as Windows and Unix handles PHP_EOL differently.
            $_sThisItemFormat = str_replace( array( "\r", "\n", ), '', $_sThisItemFormat );
            $_sItemFormat     = str_replace( array( "\r", "\n", ), '', $_sItemFormat );
            if ( $_sThisItemFormat !== $_sItemFormat ) {
                continue;
            }
            // If they equal each other, update the option
            $_aOptions[ 'unit_default' ][ 'item_format' ] = $this->___getNewItemFormat( $_iIndex );
        }
        update_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], $_aOptions );
    }
        /**
         * @param string $sOldItemFormat
         * @param integer $iIndex
         * @return string
         */
        private function ___getNewItemFormat( $iIndex ) {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            if ( 1 === $iIndex ) {
                return $_oOption->getDefaultItemFormatConnected();
            }
            return $_oOption->getDefaultItemFormatDisconnected();
        }
        /**
         * @return array
         */
        private function ___getLegacyDefaultItemFormats() {
            return array(
                '%image%' . PHP_EOL
                    . '%image_set%' . PHP_EOL
                    . '%rating%' . PHP_EOL
                    . '%title%' . PHP_EOL
                    . '%description%' . PHP_EOL
                    . '%disclaimer%',    // 3.2.0+
                '%image%' . PHP_EOL    // since the
                    . '%title%' . PHP_EOL
                    . '%description%' . PHP_EOL
                    . '%disclaimer%'    // 3.2.0+
            );
        }

}