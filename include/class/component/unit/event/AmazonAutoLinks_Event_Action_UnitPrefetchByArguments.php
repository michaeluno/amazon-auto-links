<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Creates caches for the unit.
 * 
 * @package      Amazon Auto Links
 * @since        3.3.0
 * @action       aal_action_unit_prefetch_by_arguments
 */
class AmazonAutoLinks_Event_Action_UnitPrefetchByArguments extends AmazonAutoLinks_Event_Action_Base {
        
    /**
     * 
     * @callback        action        aal_action_unit_prefetch
     */
    public function doAction( /* $aArguments */ ) {
        
        $_aParams   = func_get_args() + array( null );
        $aArguments = $_aParams[ 0 ];

        // Just call the output.
        AmazonAutoLinks_Output::getInstance( $aArguments )->get();
        
    }   
    
}