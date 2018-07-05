<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Creates caches for the unit.
 * 
 * @package      Amazon Auto Links
 * @since        3.3.0
 * @since        3.5.0      Renamed from `AmazonAutoLinks_Event_Action_UnitPrefetchByArguments`.
 */
class AmazonAutoLinks_Event___Action_UnitPrefetchByArguments extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_unit_prefetch_by_arguments';

    /**
     *
     */
    protected function _doAction( /* $aArguments */ ) {
        
        $_aParams    = func_get_args() + array( null );
        $_aArguments = $_aParams[ 0 ];

        // Just call the output.
        AmazonAutoLinks_Output::getInstance( $_aArguments )->get();
        
    }   
    
}