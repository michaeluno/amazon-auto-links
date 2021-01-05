<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A class that provides utility methods for the admin pages of the unit component.
 * @since   3.9.0
 */
class AmazonAutoLinks_Unit_Admin_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     * Makes the fields disabled.
     * @since   4.1.0
     * @retun   array
     */
    static public function getFieldsDisabled( array $aFields ) {

        $_sOpeningTag  = "<div class='upgrade-to-pro' style='margin:0; padding:0; display: inline-block;' title='" . __( 'Please consider upgrading to Pro to use this feature!', 'amazon-auto-links' ) . "'>";
        $_sClosingTag  = "</div>";
        foreach( $aFields as &$_aField ) {
            $_aField = array(
                    'before_field' => $_sOpeningTag,
                    'after_field'  => $_sClosingTag,
                )
                + $_aField
                + array( 'attributes' => array() )
            ;
            $_aField[ 'attributes' ] = array(
                'disabled'  => 'disabled',
                'class'     => 'disabled read-only',
            ) + $_aField[ 'attributes' ];
        }
        return $aFields;

    }

    /**
     * Redirect the user to the API connect page.
     *
     * @param $oFactory
     * @since 3.9.0
     */
    static public function checkAPIKeys( $oFactory ) {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->isPAAPIConnectedByAnyLocale() ) {
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