<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */

/**
 * Provides base methods for plugin event actions.
 
 * @package      Amazon Auto Links
 * @since        3
 */
abstract class AmazonAutoLinks_Event_Action_Base extends AmazonAutoLinks_WPUtility {
    
    /**
     * Sets up hooks.
     * @since       3
     * @param       string      $sActionHookName
     * @param       integer     $iParameters        The number of parameters.
     */
    public function __construct( $sActionHookName, $iParameters=1 ) {

        add_action( 
            $sActionHookName, 
            array( 
                $this, 
                'doAction' 
            ),
            10, // priority
            $iParameters
        );    

    }
    
    /**
     * 
     * @callback        action       
     */
    public function doAction( /* $aArguments */ ) {
        
        $_aParams = func_get_args() + array( null );
        // AmazonAutoLinks_Debug::log( $_aParams );
        
    }
    
}