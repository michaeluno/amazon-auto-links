<?php
/**
 * Auto Amazon Links
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

        $_sLocale      = ( string ) $this->oUnitOutput->oUnitOption->get( 'country' );
        $_asKeywords   = $this->oUnitOutput->oUnitOption->get( array( 'Keywords' ), array() );
        $_aKeywords    = is_array( $_asKeywords )
            ? $_asKeywords
            : explode( ',', $_asKeywords );

        $_oAdWidgetAPI = new AmazonAutoLinks_AdWidgetAPI_Search(
            $_sLocale,
            ( integer ) $this->oUnitOutput->oUnitOption->get( 'cache_duration' )
        );
        $_aResponse    = $_oAdWidgetAPI->get( $_aKeywords );
        return $this->getElementAsArray( $_aResponse, array( 'results' ) );

    }

}