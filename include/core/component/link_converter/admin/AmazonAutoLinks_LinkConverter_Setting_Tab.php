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
 * Adds an in-page tab to a setting page.
 *
 * @since       3.8.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_LinkConverter_Setting_Tab extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return  array
     * @since   3.8.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'link_converter',
            'title'     => __( 'Converter', 'amazon-auto-links' ),
            'order'     => 30,
        );
    }

    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {

        // Form sections
        new AmazonAutoLinks_LinkConverter_Setting_Section_Convert(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
            )
        );

        // Setting Notification
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        $_sAssociateID  = $_oOption->getAssociateID( $_oOption->getMainLocale() );
        if ( ! $_sAssociateID ) {
            $_sMessage = "<strong>" . AmazonAutoLinks_Registry::NAME . "</strong>: "
                . __( 'A main Associate ID needs to be set.', 'amazon-auto-links' ) . ' '
                . sprintf(
                    __( 'Go to <a href="%1$s">set</a>.', 'amazon-auto-links' ),
                    esc_url( $this->getAPIAuthenticationPageURL() )
                );
            $oAdminPage->setAdminNotice( $_sMessage );
        }
    }

    public function replyToDoTab( $oFactory ) {
        echo "<div class='right-submit-button'>"
                . get_submit_button()
            . "</div>";
    }

}