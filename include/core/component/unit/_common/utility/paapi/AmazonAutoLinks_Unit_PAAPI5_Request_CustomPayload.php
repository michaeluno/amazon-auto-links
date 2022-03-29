<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Performs PA-API 5 requests with a custom payload.
 *
 * @since   5.0.0
 */
class AmazonAutoLinks_Unit_PAAPI5_Request_CustomPayload extends AmazonAutoLinks_Unit_PAAPI5_Request_Base {

    /**
     * The array element key name that contains `Items` element.
     * For a custom payload, this is unknown.
     * @var   string
     * @since 5.0.0
     */
    protected $_sResponseItemsParentKey = '';
    
    /**
     * Performs an Amazon Product API request.
     *
     * @since  5.0.0
     * @param  integer $iCount
     * @return array
     */
    public function getPAAPIResponse( $iCount ) {

        $_sPayloadJSON = $this->oUnitOption->get( 'payload' );
        $_aPayload     = ( array ) json_decode( $_sPayloadJSON,true );        
        
        $_sAssociateID = $this->oUnitOption->get( 'associate_id' );
        $_sAssociateID = $_sAssociateID ? $_sAssociateID : $this->getElement( $_aPayload, 'PartnerTag' );
        if ( ! $_sAssociateID ) {
            return array(
                'Error' => array(
                    'Code'    => $this->oUnitOption->sUnitType,
                    'Message' => 'An Associate tag is not set.',
                )
            );
        }
        
        $_aPayload[ 'Resources' ] = AmazonAutoLinks_PAAPI50___Payload::$aResources
            + $this->getElementAsArray( $_aPayload, 'Resources' );

        $_sLocale = AmazonAutoLinks_Locales::getLocaleByDomain( $this->getElement( $_aPayload, 'Marketplace' ) );
        $_oAPI    = new AmazonAutoLinks_PAAPI50( $_sLocale, $this->sPublicKey, $this->sSecretKey, $_sAssociateID );
        return $_oAPI->request(
            $_aPayload,
            $this->oUnitOption->get( 'cache_duration' ),
            $this->oUnitOption->get( '_force_cache_renewal' )
        );
            
    }

}