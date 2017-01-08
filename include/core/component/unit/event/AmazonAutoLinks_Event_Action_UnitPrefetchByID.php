<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Creates caches for the unit.
 * 
 * @package      Amazon Auto Links
 * @since        3
 * @action       aal_action_unit_prefetch
 */
class AmazonAutoLinks_Event_Action_UnitPrefetchByID extends AmazonAutoLinks_Event_Action_Base {
        
    /**
     * 
     * @callback        action        aal_action_unit_prefetch
     */
    public function doAction( /* $aArguments */ ) {
        
        $_aParams    = func_get_args() + array( array() );
        $_aArguments = $this->getAsArray( $_aParams[ 0 ] );
        AmazonAutoLinks( $_aArguments, false );

//        $_sUnitType  = get_post_meta(
//            $iUnitID,
//            'unit_type',
//            true
//        );
//        if ( ! $_sUnitType ) {
//            return;
//        }
// Just call the output.
//        $_sUnitOptionClassName = "AmazonAutoLinks_UnitOption_" . $_sUnitType;
//        $_oUnitOptions         = new $_sUnitOptionClassName( $iUnitID );
//        $_aUnitOptions         = $_oUnitOptions->get();
//        AmazonAutoLinks_Output::getInstance( $_aUnitOptions )->get();
        
    }   
    
}