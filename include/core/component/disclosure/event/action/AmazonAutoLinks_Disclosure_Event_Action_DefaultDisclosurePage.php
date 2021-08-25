<?php
/**
 * Amazon Auto Links
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
        add_action( 'aal_action_plugin_activated', array( $this, 'replyToDoOnPluginActivation' ) );
        add_action( 'upgrader_process_complete', array( $this, 'replyToDoOnPluginUpdate' ), 10, 2 );
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
        if ( $this->hasBeenCalled( get_class( $this ) ) ) {
            return;
        }
        $this->___doTheTask();
    }

    /**
     * @since 4.7.0
     */
    public function replyToDoOnPluginActivation() {
        if ( $this->hasBeenCalled( get_class( $this ) ) ) {
            return;
        }
        $this->___doTheTask();
    }
        /**
         *
         */
        private function ___doTheTask() {
            if ( $this->___hasDisclosurePage() ) {
                return;
            }
            $this->___createDisclosurePage();
        }
            /**
             * @return boolean
             * @since  4.7.0
             */
            private function ___hasDisclosurePage() {
                $_aDisclosurePage = AmazonAutoLinks_Disclosure_Utility::getPostByGUID( AmazonAutoLinks_Disclosure_Loader::$sDisclosureGUID, 'ID' );
                return ( boolean ) $this->getElement( $_aDisclosurePage, 'ID' );
            }

            /**
             * @since  4.7.0
             */
            private function ___createDisclosurePage() {
                $this->createPost(
                    'page',
                    array( // post columns
                        'post_title'    => __( 'Affiliate Disclosure', 'amazon-auto-links' ),
                        'post_content'  => $this->___getPageContent(),
                        'guid'          => AmazonAutoLinks_Disclosure_Loader::$sDisclosureGUID,
                    )
                );
            }
                private function ___getPageContent() {
                    return <<<PAGECONTENT
<!-- wp:shortcode -->
[aal_disclosure]
<!-- /wp:shortcode -->
PAGECONTENT;
                }

}