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
 * Loads the unit option converter component.
 *
 * @package      Amazon Auto Links
 * @since        3.8.0
 */
class AmazonAutoLinks_Loader_LinkConverter {

    public function __construct() {

        if ( is_admin() ) {
            $this->___loadAdminComponents();
        }

        new AmazonAutoLinks_LinkConverter;

    }

        private function ___loadAdminComponents() {
            add_action(
                'load_' . AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                array( $this, 'replyToLoadPage' )
            );
        }
        public function replyToLoadPage( $oAdminPage ) {
            new AmazonAutoLinks_LinkConverter_Setting_Tab(
                $oAdminPage,
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ]
            );
        }



}