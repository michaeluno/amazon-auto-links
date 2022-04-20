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
 * Creates caches for the unit.
 * 
 * @since 3
 * @since 3.5.0 Renamed from `AmazonAutoLinks_Event_Action_UnitPrefetchByID`.
 */
class AmazonAutoLinks_Event___Action_UnitPrefetchByID extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_unit_prefetch';

    /**
     * @callback add_action aal_action_unit_prefetch
     */
    protected function _doAction( /* $aArguments */ ) {
        $_aParams    = func_get_args() + array( array() );
        $_aArguments = $this->getAsArray( $_aParams[ 0 ] );
        apply_filters( 'aal_filter_output', '', $_aArguments ); // no need to output anything for a background task
    }
    
}