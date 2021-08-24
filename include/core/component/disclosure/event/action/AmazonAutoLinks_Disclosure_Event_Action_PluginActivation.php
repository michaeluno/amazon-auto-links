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
class AmazonAutoLinks_Disclosure_Event_Action_PluginActivation extends AmazonAutoLinks_Disclosure_Utility {

    public function __construct() {
        add_action( 'aal_action_plugin_activated', array( $this, 'replyToDoOnPluginActivation' ) );
    }

    /**
     * @since 4.7.0
     */
    public function replyToDoOnPluginActivation() {
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