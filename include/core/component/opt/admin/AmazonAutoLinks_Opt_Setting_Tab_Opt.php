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
 * Adds the 'Opt' in-page tab to the 'Settings' page.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Opt_Setting_Tab_Opt extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since  4.7.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'opt',
            'title'     => __( 'Opt', 'amazon-auto-links' ),
            'order'     => 100,
            // 'style'     => AmazonAutoLinks_CustomOEmbed_Loader::$sDirPath . '/asset/css/settings.css',
        );
    }

    /**
     * @param $oAdminPage
     * @sinec 4.7.0
     */
    protected function _doTab( $oAdminPage ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()
            . "</div>";
    }

}