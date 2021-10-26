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
 * Adds the 'Disclosure' in-page tab to the 'Settings' page.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Disclosure_Setting_Tab_Disclosure extends AmazonAutoLinks_AdminPage_Tab_Base {
    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'disclosure',
            'title'     => __( 'Disclosure', 'amazon-auto-links' ),
            'order'     => 15,
            'style'     => AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css',
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    protected function _loadTab( $oAdminPage ) {
        new AmazonAutoLinks_Select2CustomFieldType( $oAdminPage->oProp->sClassName );
        new AmazonAutoLinks_Disclosure_Setting_Section_Disclosure( $oAdminPage, $this->sPageSlug );
    }

    protected function _doTab( $oAdminPage ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()
            . "</div>";
    }

}