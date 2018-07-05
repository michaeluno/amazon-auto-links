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
 * Provides an abstract base for bases.
 * 
 * @since       3
 */
abstract class AmazonAutoLinks_AdminPage_RootBase extends AmazonAutoLinks_PluginUtility {
    
    /**
     * Stores callback method names.
     * 
     * @since   3
     */
    protected $aMethods = array(
        'replyToLoadPage',
        'replyToDoPage',
        'replyToDoAfterPage',
        'replyToLoadTab',
        'replyToDoTab',
        'validate',
    );

    /**
     * Handles callback methods.
     * @since       3
     * @return      mixed
     */
    public function __call( $sMethodName, $aArguments ) {
        
        if ( in_array( $sMethodName, $this->aMethods ) ) {
            return isset( $aArguments[ 0 ] ) 
                ? $aArguments[ 0 ] 
                : null;
        }       
        
        trigger_error( 
            AmazonAutoLinks_Registry::NAME . ' : ' . sprintf( 
                __( 'The method is not defined: %1$s', 'amazon-auto-links' ),
                $sMethodName 
            ), 
            E_USER_WARNING 
        );        
    }
   
    /**
     * A user constructor.
     * @since       3
     * @return      void
     */
    protected function construct( $oFactory ) {}
    
}