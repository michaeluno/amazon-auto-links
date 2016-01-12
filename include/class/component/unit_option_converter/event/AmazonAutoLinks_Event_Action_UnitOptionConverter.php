<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Converts template options
 * 
 * @package      Amazon Auto Links
 * @since        3
 * @action       aal_action_event_convert_template_options
 */
class AmazonAutoLinks_Event_Action_UnitOptionConverter extends AmazonAutoLinks_Event_Action_Base {
        
    /**
     * 
     * @callback        action        aal_action_event_convert_imot_options
     */
    public function doAction( /* $iUnitID, $aInput */ ) {
        
        $_aParams  = func_get_args() + array( 0, array() );
        $_iUnitID  = $_aParams[ 0 ];
        $_aInput   = $_aParams[ 1 ];

        if ( ! $_iUnitID ) {
            return;
        }
        $_bUpdated = false;
        foreach( $_aInput as $_sMetaKey => $_mValue ) {
            if ( ! is_string( $_sMetaKey ) ) {
                continue;
            }
            update_post_meta(
                $_iUnitID, // post id
                $_sMetaKey, // meta key
                $_mValue    // value
            );    
            $_bUpdated = true;
        }       

        if ( $_bUpdated ) {
            AmazonAutoLinks_Event_Scheduler::prefetch( $_iUnitID );
        }
        
    }   
    
}