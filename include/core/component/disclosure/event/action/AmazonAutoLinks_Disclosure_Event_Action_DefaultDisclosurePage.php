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
 * Performs initial set-ups on plugin activation
 *
 * @since 4.7.0
 */
class AmazonAutoLinks_Disclosure_Event_Action_DefaultDisclosurePage extends AmazonAutoLinks_Disclosure_Utility {

    /**
     * @since 4.7.0
     */
    public function __construct() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->get( 'disclosure', 'never_create_page' ) ) {
            return;
        }
        add_action( 'aal_action_plugin_activated', array( $this, 'replyToDoOnPluginActivation' ) );
        add_action( 'upgrader_process_complete', array( $this, 'replyToDoOnPluginUpdate' ), 10, 2 );
        add_action( 'upgrader_overwrote_package', array( $this, 'replyToDoOnPluginOverwrite' ), 10, 3 );
    }

    /**
     * @param string $sPackagePath
     * @param array  $aPluginInfo  Takes the following strucure.
     * ```
     * array(
     *    [Name] => (string, length: 47) Auto Amazon Links - Drop Products without Price
     *    [PluginURI] => (string, length: 42) https://en.michaeluno.jp/amazon-auto-links
     *    [Version] => (string, length: 5) 1.0.0
     *    [Description] => (string, length: 30) Drops products without a price
     *    [Author] => (string, length: 23) Michael Uno (miunosoft)
     *    [AuthorURI] => (string, length: 21) https://michaeluno.jp
     *    [TextDomain] => (string, length: 0)
     *    [DomainPath] => (string, length: 0)
     *    [Network] => (boolean) false
     *    [RequiresWP] => (string, length: 0)
     *    [RequiresPHP] => (string, length: 0)
     *    [UpdateURI] => (string, length: 0)
     *    [Title] => (string, length: 47) Auto Amazon Links - Drop Products without Price
     *    [AuthorName] => (string, length: 23) Michael Uno (miunosoft)
     * )
     * ```
     * @param string $sContext
     * @since 4.7.0
     * @see   wp-admin/includes/class-plugin-upgrader.php
     * @see   Plugin_Upgrader::install()
     */
    public function replyToDoOnPluginOverwrite( $sPackagePath, $aPluginInfo, $sContext ) {
        if ( 'plugin' !== $sContext ) {
            return;
        }
        $_aOurPlugin = get_plugin_data( AmazonAutoLinks_Registry::$sFilePath, false, false );
        if ( $this->getElement( $_aOurPlugin, 'Name' ) !== $this->getElement( $aPluginInfo, 'Name' ) ) {
            return;
        }
        $this->___doTheTask();
    }

    /**
     * @since    4.7.0
     * @callback add_action() upgrader_process_complete
     * @param    WP_Upgrader  $oWPUpgrader
     * @param    array        $aHookExtra
     * @see      wp-admin/includes/class-wp-upgrader.php
     */
    public function replyToDoOnPluginUpdate( $oWPUpgrader, array $aHookExtra ) {
        if ( 'update' !== $this->getElement( $aHookExtra, 'action' ) ) {
            return;
        }
        if ( 'plugin' !== $this->getElement( $aHookExtra, 'type' ) ) {
            return;
        }
        $_sThePluginName = plugin_basename( AmazonAutoLinks_Registry::$sFilePath );
        $_bDetected      = false;
        foreach( $this->getElementAsArray( $aHookExtra, array( 'plugins' ) ) as $_sPlugin ) {
            if( $_sPlugin === $_sThePluginName ) {
                $_bDetected = true;
            }
        }
        if ( ! $_bDetected ) {
            return;
        }
        $this->___doTheTask();
    }

    /**
     * @since 4.7.0
     */
    public function replyToDoOnPluginActivation() {
        $this->___doTheTask();
    }
        /**
         *
         */
        private function ___doTheTask() {
            if ( $this->hasBeenCalled( get_class( $this ) ) ) {
                return;
            }
            if ( $this->___hasDisclosurePage() ) {
                return;
            }
            $this->getDisclosurePageCreated();
        }
            /**
             * @return boolean
             * @since  4.7.0
             */
            private function ___hasDisclosurePage() {
                $_aDisclosurePage = AmazonAutoLinks_Disclosure_Utility::getPostByGUID( AmazonAutoLinks_Disclosure_Loader::$sDisclosureGUID, 'ID' );
                return ( boolean ) $this->getElement( $_aDisclosurePage, 'ID' );
            }

}