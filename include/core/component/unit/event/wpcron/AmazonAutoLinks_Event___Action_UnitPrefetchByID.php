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
 * Creates caches for the unit.
 * 
 * @package      Amazon Auto Links
 * @since        3
 * @since        3.5.0      Renamed from `AmazonAutoLinks_Event_Action_UnitPrefetchByID`.
 */
class AmazonAutoLinks_Event___Action_UnitPrefetchByID extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_unit_prefetch';

    /**
     * 
     * @callback        action        aal_action_unit_prefetch
     */
    protected function _doAction( /* $aArguments */ ) {

        $_aParams    = func_get_args() + array( array() );
        $_aArguments = $this->getAsArray( $_aParams[ 0 ] );
        AmazonAutoLinks( $_aArguments, false );

    }   
    
}