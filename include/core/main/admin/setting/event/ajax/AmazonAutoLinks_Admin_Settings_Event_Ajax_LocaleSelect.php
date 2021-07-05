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
 * Updates the language and currency options based on the selected locale (country) unit option.
 * @since   4.3.0
 *
 */
class AmazonAutoLinks_Admin_Settings_Event_Ajax_LocaleSelect extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookSuffix = 'aal_action_ajax_locale_select';
    protected $_bLoggedIn         = true;
    protected $_bGuest            = false;

    protected function _construct() {}

    /**
     * @param  array $aPost
     *
     * @return array
     * @throws Exception        Throws a string value of an error message.
     * @since  4.3.0
     */
    protected function _getResponse( array $aPost ) {

        $_sLocale = $this->getElement( $aPost, array( 'locale' ), '' );

//        $_aErrors = array();
//        if ( ! empty( $_aErrors ) ) {
//            throw new Exception( implode( '', $_aErrors ) );
//        }

        return array(
            'language'  => $this->___getLanguageSelectOptionsByLocale( $_sLocale ),
            'currency'  => $this->___getCurrencySelectOptionsByLocale( $_sLocale ),
        );

    }

        /**
         * @param string $sLocale
         *
         * @since   4.3.0
         * @return string   The `<option>` tag elements to be placed inside the `<select>` tag.
         */
        private function ___getLanguageSelectOptionsByLocale( $sLocale ) {
            $_sOptions   = '';
            $_aLanguages = AmazonAutoLinks_PAAPI50___Locales::getLanguagesByLocale( $sLocale );
            $_sDefault   = AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $sLocale );;
            foreach( $_aLanguages as $_sLanguageCode => $_sLanguage  ) {
                $_sSelected = $_sLanguageCode === $_sDefault ? "selected='selected'" : '';
                $_sOptions .= "<option value='" . esc_attr( $_sLanguageCode ) . "' {$_sSelected}>{$_sLanguage}</option>";
            }
            return $_sOptions;
        }
        /**
         * @param string $sLocale
         *
         * @since   4.3.0
         * @return string   The `<option>` tag elements to be placed inside the `<select>` tag.
         */
        private function ___getCurrencySelectOptionsByLocale( $sLocale ) {
            $_sOptions      = '';
            $_sCurrencies   = AmazonAutoLinks_PAAPI50___Locales::getCurrenciesByLocale( $sLocale );
            $_sDefault      = AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $sLocale );
            foreach( $_sCurrencies as $_sCurrencyCode => $_sCurrencyLabel  ) {
                $_sSelected = $_sCurrencyCode === $_sDefault ? "selected='selected'" : '';
                $_sOptions .= "<option value='" . esc_attr( $_sCurrencyCode ) . "' {$_sSelected}>{$_sCurrencyLabel}</option>";
            }
            return $_sOptions;
        }


}