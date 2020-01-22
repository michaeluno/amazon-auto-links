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
 * Converts template options
 * 
 * @package      Amazon Auto Links
 * @since        3
 * @since        3.5.0      Renamed from `AmazonAutoLinks_Event_Action_UnitOptionConverter`.
 *
 * @action       aal_action_event_convert_template_options
 */
class AmazonAutoLinks_Event___Action_UnitOptionConverter extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName     = 'aal_action_event_convert_unit_options';
    protected $_iCallbackParameters = 2;

    /**
     * 
     * @callback        action        aal_action_event_convert_unit_options
     */
    protected function _doAction( /* $iUnitID, $aInput */ ) {
        
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

        // @deprecated 3.9.0
        // When thousands of units are converted, it causes PHP timeout.
//        if ( $_bUpdated ) {
//            AmazonAutoLinks_Event_Scheduler::prefetch( $_iUnitID );
//        }
        
    }   
    
}