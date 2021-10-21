<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 *
 */

/**
 * Fetches product data from outside source.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_UnitType_AdWidgetSearch_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_UnitType_Common_Event_Filter_ProductsFetcher_Base {

    public $sUnitType = 'ad_widget_search';

    /**
     * @var AmazonAutoLinks_UnitOutput_category
     */
    public $oUnitOutput;

    /**
     * @param  array $aProducts
     * @return array
     * @since  5.0.0
     */
    protected function _getItemsFromSource( $aProducts ) {

        $_sLocale            = ( string ) $this->oUnitOutput->oUnitOption->get( 'country' );
        // @todo do PA-API search
        // $_sCurrencyDefault   = AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale );
        // $_sCurrency          = $this->oUnitOutput->oUnitOption->get( array( 'preferred_currency' ), $_sCurrencyDefault );
        // $_sLanguageDefault   = AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale );
        // $_sLanguage          = $this->oUnitOutput->oUnitOption->get( array( 'language' ), $_sLanguageDefault );
        // if ( $_sCurrencyDefault !== $_sCurrency || $_sLanguageDefault !== $_sLanguage ) {
        //
        // }

        $_asKeywords   = $this->oUnitOutput->oUnitOption->get( array( 'Keywords' ), array() );
        $_aKeywords    = is_array( $_asKeywords )
            ? $_asKeywords
            : explode( ',', $_asKeywords );

        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search(
            $_sLocale,
            ( integer ) $this->oUnitOutput->oUnitOption->get( 'cache_duration' )
        );
        $_aResponse    =  $_oAdWidgetAPI->get( $_aKeywords );
        return $this->getElementAsArray( $_aResponse, array( 'results' ) );

    }

}