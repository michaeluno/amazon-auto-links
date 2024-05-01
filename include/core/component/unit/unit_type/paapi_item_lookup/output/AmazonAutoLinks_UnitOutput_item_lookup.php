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
 * Creates Amazon product links by Item Look-up.
 * 
 */
class AmazonAutoLinks_UnitOutput_item_lookup extends AmazonAutoLinks_UnitOutput_search {
    
    /**
     * Stores the unit type.
     * @remark Note that the base constructor will create a unit option object based on this value.
     * @var    string
     */    
    public $sUnitType = 'item_lookup';

    /**
     * @param  integer $iCount
     * @return array   API response array
     * @since  5.0.0
     * @remark This is also accessed from a background routine.
     */
    public function getAPIResponse( $iCount ) {

        // Drop blocked ASIN items
        $_aItemIDs      = array_filter( $this->oUnitOption->get( 'ItemIds' ), array( $this, 'isASINAllowed' ) );
        $this->oUnitOption->set( 'ItemIds', $_aItemIDs );

        // Perform a request
        $_sLocale       = $this->oUnitOption->get( 'country' );
        $_oPAAPIRequest = new AmazonAutoLinks_Unit_PAAPI5_Request_GetItems(
            $this->oUnitOption,
            $this->oOption->getPAAPIAccessKey( $_sLocale ),
            $this->oOption->getPAAPISecretKey( $_sLocale )
        );

        return $_oPAAPIRequest->getPAAPIResponse( $this->oUnitOption->get( '_ignore_count' ) ? -1 : $iCount );

    }

}