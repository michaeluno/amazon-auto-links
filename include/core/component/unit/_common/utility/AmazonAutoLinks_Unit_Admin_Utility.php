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
 * A class that provides utility methods for the admin pages of the unit component.
 * @since   3.9.0
 */
class AmazonAutoLinks_Unit_Admin_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * Redirect the user to the API connect page.
     *
     * @param $oFactory
     * @since   3.9.0
     */
    static public function checkAPIKeys( $oFactory ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->isAPIConnected() ) {
            return;
        }

        $oFactory->setSettingNotice(
            __( 'You need to set API keys first.', 'amazon-auto-links' ),
            'updated'
        );

        // Go to the Authentication tab of the Settings page.
        self::goToAPIAuthenticationPage();

    }

}
