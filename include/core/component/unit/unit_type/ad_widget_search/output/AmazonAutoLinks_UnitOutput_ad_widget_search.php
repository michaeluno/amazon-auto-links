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
     * @return array
     * @since  5.0.0
     */
    public function fetch() {

        // If the user wants to display products with non-default currency or language, use PA-API
        if ( $this->___shouldUsePAAPI() ) {
            // @todo Might be better to instantiate a PA-API search unit output class rather than emulating the fetch() method of the unit type.
            $_aProducts = apply_filters( 'aal_filter_unit_output_products_from_source_' . 'search', array(), $this );
            $_aProducts = apply_filters( 'aal_filter_unit_output_products_format_' . 'search', $_aProducts, $this );
            return $this->getAsArray( $_aProducts );
        }        
        return parent::fetch();

    }
        /**
         * @return boolean
         * @since  5.0.0
         */
        private function ___shouldUsePAAPI() {
            $_sLocale            = ( string ) $this->oUnitOption->get( 'country' );
            if ( ! in_array( $_sLocale, AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport(), true ) ) {
                return true;
            }
            $_sCurrencyDefault   = AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale );
            $_sCurrency          = $this->oUnitOption->get( array( 'preferred_currency' ), $_sCurrencyDefault );
            if ( $_sCurrencyDefault !== $_sCurrency ) {
                return true;
            }
            $_sLanguageDefault   = AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale );
            $_sLanguage          = $this->oUnitOption->get( array( 'language' ), $_sLanguageDefault );
            return $_sLanguageDefault !== $_sLanguage;
        }

}