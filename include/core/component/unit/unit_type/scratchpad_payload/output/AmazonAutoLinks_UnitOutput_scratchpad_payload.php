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
 * Generates the output of the `scratchpad_payload` unit type.
 * 
 * @package         Amazon Auto Links
 * @since   4.1.0
 */
class AmazonAutoLinks_UnitOutput_scratchpad_payload extends AmazonAutoLinks_UnitOutput_item_lookup {
    
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */    
    public $sUnitType = 'scratchpad_payload';

    /**
     * The array element key name that contains `Items` element.
     * PA-API 5 operations such as `GetItems`, `SearchItems` have different key names such as `ItemsResult` abd `SearchResult`.
     *
     * For this unit type, the value depends on the user's input.
     *
     * @var string
     */
    protected $_sResponseItemsParentKey = '';

    /**
     * Performs API requests and get response.
     *
     * @return      array
     */
    protected function _getResponses( array $aURLs=array() ) {

        $_sPayloadJSON = $this->oUnitOption->get( 'payload' );
        $_aPayload     = ( array ) json_decode( $_sPayloadJSON,true );
        $_aResponse    = $this->___getRequest( $_aPayload, $this->oUnitOption->get( 'count' ) );
        $this->_sResponseItemsParentKey = $this->getItemsKey( $_aResponse );
        return $_aResponse;

    }
        /**
         * Searches the key name that holds the `Items` element
         * @param array $aPayload
         * @return  string  The key name
         */
        private function getItemsKey( array $aPayload ) {
            foreach( $aPayload as $_sKey => $_aItem ) {
                if ( isset( $_aItem[ 'Items' ] ) ) {
                    return $_sKey;
                }
            }
            return '';
        }

        /**
         * @param array $aPayload
         * @param integer $iCount
         * @since 4.1.0
         * @return array
         */
        private function ___getRequest( array $aPayload, $iCount ) {

            $_sAssociateID = $this->oUnitOption->get( 'associate_id' );
            $_sAssociateID = $_sAssociateID ? $_sAssociateID : $this->getElement( $aPayload, 'PartnerTag' );

            $aPayload[ 'Resources' ] = AmazonAutoLinks_PAAPI50___Payload::$aResources
                + $this->getElementAsArray( $aPayload, 'Resources' );

            $_sLocale = AmazonAutoLinks_Locales::getLocaleByDomain( $this->getElement( $aPayload, 'Marketplace' ) );
            $_oAPI    = new AmazonAutoLinks_PAAPI50(
                $_sLocale,
                $this->oOption->getPAAPIAccessKey( $_sLocale ),
                $this->oOption->getPAAPISecretKey( $_sLocale ),
                $_sAssociateID
            );
            return $_oAPI->request(
                $aPayload,
                $this->oUnitOption->get( 'cache_duration' ),
                $this->oUnitOption->get( '_force_cache_renewal' )
            );

        }

}