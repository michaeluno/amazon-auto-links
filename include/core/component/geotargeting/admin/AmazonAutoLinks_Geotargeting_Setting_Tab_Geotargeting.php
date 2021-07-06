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
 * Adds the 'Geotargeting' in-page tab to the 'Settings' page.
 *
 * @since       4.6.0
 */
class AmazonAutoLinks_Geotargeting_Setting_Tab_Geotargeting extends AmazonAutoLinks_AdminPage_Tab_Base {
    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'geotargeting',
            'title'     => __( 'Geotargeting', 'amazon-auto-links' ),
            'order'     => 20,
            'style'     => AmazonAutoLinks_CustomOEmbed_Loader::$sDirPath . '/asset/css/settings.css',
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oAdminPage ) {
        new AmazonAutoLinks_Geotargeting_Setting_Section_Geotargeting( $oAdminPage, $this->sPageSlug );
    }

    protected function _doTab( $oAdminPage ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()
            . "</div>";
    }

}