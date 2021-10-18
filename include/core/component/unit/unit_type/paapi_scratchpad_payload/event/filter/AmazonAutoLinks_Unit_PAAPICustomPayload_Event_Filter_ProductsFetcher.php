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
class AmazonAutoLinks_Unit_PAAPICustomPayload_Event_Filter_ProductsFetcher extends AmazonAutoLinks_Unit_PAAPIItemLookUp_Event_Filter_ProductsFetcher {

    /**
     * @var   string
     * @since 5.0.0
     */
    public $sUnitType = 'scratchpad_payload';

    /**
     * @var   AmazonAutoLinks_UnitOutput_scratchpad_payload
     * @since 5.0.0
     */
    public $oUnitOutput;

    /**
     * The array element key name that contains `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     *
     * For this unit type, the value depends on the user's input.
     * The value is automatically assigned with the `___getItemsKey()` method.
     *
     * @var string
     * @since ?
     * @since 5.0.0 Moved from `AmazonAutoLinks_UnitOutput_scratchpad_payload`.
     */
    protected $_sResponseItemsParentKey = '';

    /**
     * Performs API requests and get response.
     *
     * @return array
     * @since  ?
     * @since  5.0.0  Removed the first parameter of `$aURLs`. Moved from `AmazonAutoLinks_UnitOutput_scratchpad_payload`.
     */
    protected function _getResponses() {

        $_sPayloadJSON = $this->oUnitOutput->oUnitOption->get( 'payload' );
        $_aPayload     = ( array ) json_decode( $_sPayloadJSON,true );
        $_aResponse    = $this->___getRequest( $_aPayload, $this->oUnitOutput->oUnitOption->get( 'count' ) );
        $this->_sResponseItemsParentKey = $this->___getItemsKey( $_aResponse );
        return $_aResponse;

    }
        /**
         * Searches the key name that holds the `Items` element
         * @param  array  $aPayload
         * @return string The key name
         * @since  ?
         * @since  Moved from `AmazonAutoLinks_UnitOutput_scratchpad_payload`.
         */
        private function ___getItemsKey( array $aPayload ) {
            foreach( $aPayload as $_sKey => $_aItem ) {
                if ( isset( $_aItem[ 'Items' ] ) ) {
                    return $_sKey;
                }
            }
            return '';
        }

        /**
         * @param  array   $aPayload
         * @param  integer $iCount
         * @return array
         * @since  4.1.0
         * @since  5.0.0   Moved from `AmazonAutoLinks_UnitOutput_scratchpad_payload`.
         */
        private function ___getRequest( array $aPayload, $iCount ) {

            $_sAssociateID = $this->oUnitOutput->oUnitOption->get( 'associate_id' );
            $_sAssociateID = $_sAssociateID ? $_sAssociateID : $this->getElement( $aPayload, 'PartnerTag' );

            $aPayload[ 'Resources' ] = AmazonAutoLinks_PAAPI50___Payload::$aResources
                + $this->getElementAsArray( $aPayload, 'Resources' );

            $_sLocale = AmazonAutoLinks_Locales::getLocaleByDomain( $this->getElement( $aPayload, 'Marketplace' ) );
            $_oAPI    = new AmazonAutoLinks_PAAPI50(
                $_sLocale,
                $this->oUnitOutput->oOption->getPAAPIAccessKey( $_sLocale ),
                $this->oUnitOutput->oOption->getPAAPISecretKey( $_sLocale ),
                $_sAssociateID
            );
            return $_oAPI->request(
                $aPayload,
                $this->oUnitOutput->oUnitOption->get( 'cache_duration' ),
                $this->oUnitOutput->oUnitOption->get( '_force_cache_renewal' )
            );

        }
    
}