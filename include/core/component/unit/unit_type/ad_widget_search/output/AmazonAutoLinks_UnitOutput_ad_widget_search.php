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
 * Handles outputs of ad-widget search units.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_UnitOutput_ad_widget_search extends AmazonAutoLinks_UnitOutput_category {

    /**
     * Stores the unit type.
     * @remark The base constructor creates a unit option object based on this value.
     * @sicne  5.0.0
     */
    public $sUnitType = 'ad_widget_search';

    /**
     * @return string
     */
    public function get() {
        if ( $this->___shouldUsePAAPI() ) {
            return $this->___getOutputByPAAPI();
        }
        return parent::get();
    }
        /**
         * @return string
         * @since  5.0.0
         */
        private function ___getOutputByPAAPI() {
            $_sUnitType = $this->___getPAAPIUnitTypeFromArguments( $this->oUnitOption->aRawOptions );
            $_sClass      = "AmazonAutoLinks_UnitOutput_" . $_sUnitType;
            $_oUnitOutput = new $_sClass( $this->oUnitOption->aRawOptions );
            return $_oUnitOutput->get();
        }
            private function ___getPAAPIUnitTypeFromArguments( array $aRawArguments ) {
                if ( isset( $aRawArguments[ 'asin' ] ) ) {
                    return 'item_lookup';
                }
                return 'search';
            }

        /**
         * @return boolean
         * @since  5.0.0
         */
        private function ___shouldUsePAAPI() {

            $_bAPIStatus         = false !== $this->oOption->getPAAPIStatus();
            $_sLocale            = ( string ) $this->oUnitOption->get( 'country' );
            if ( ! in_array( $_sLocale, AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport(), true ) ) {
                return $_bAPIStatus;
            }
            // If the user wants to display products with non-default currency or language, use PA-API
            $_sCurrencyDefault   = AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale );
            $_sCurrency          = $this->oUnitOption->get( array( 'preferred_currency' ), $_sCurrencyDefault );
            if ( $_sCurrencyDefault !== $_sCurrency ) {
                return $_bAPIStatus;
            }
            $_sLanguageDefault   = AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale );
            $_sLanguage          = $this->oUnitOption->get( array( 'language' ), $_sLanguageDefault );
            if ( $_sLanguageDefault !== $_sLanguage ) {
                return $_bAPIStatus;
            }
            if ( $this->hasAdvancedSearchOptions() ) {
                return $_bAPIStatus;
            }
            return false;

        }
            /**
             * @return boolean Whether the unit arguments contain advanced search options which require PA-API.
             * @since  5.0.0
             */
            private function hasAdvancedSearchOptions() {
                $_aAdvancedArgumentKeys = array(
                    // The value doesn't matter
                    'SearchIndex'       => true,
                    'BrowseNode'        => true,
                    'Availability'      => true,
                    'Condition'         => true,
                    'MaximumPrice'      => true,
                    'MinimumPrice'      => true,
                    'MinPercentageOff'  => true,
                    'MerchantId'        => true,
                    'MinReviewsRating'  => true,
                    'DeliveryFlags'     => true,
                );
                $_aAdvancedArguments   = array_intersect_key( $this->oUnitOption->get(), $_aAdvancedArgumentKeys );
                $_aAdvancedDefaults    = array_intersect_key( $this->oUnitOption->aDefault, $_aAdvancedArgumentKeys );
                $_aNonDefaults         = array_diff_assoc( $_aAdvancedArguments, $_aAdvancedDefaults );
                return ! empty( $_aNonDefaults );
            }

}