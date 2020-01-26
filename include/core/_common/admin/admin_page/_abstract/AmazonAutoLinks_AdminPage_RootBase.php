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
    protected $_aMethods = array(
        'replyToLoadPage',
        'replyToDoPage',
        'replyToDoAfterPage',
        'replyToLoadTab',
        'replyToDoTab',
        'validate',
    );

    /**
     * @since   3.11.1
     * @return array
     */
    protected function _getArguments() {
        return array();
    }

    /**
     * Handles callback methods.
     * @since       3
     * @return      mixed
     */
    public function __call( $sMethodName, $aArguments ) {
        
        if ( in_array( $sMethodName, $this->_aMethods ) ) {
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
     * @since       3.7.9   Renamed to `_construct` from `construct`.
     * @return      void
     */
    protected function _construct( $oFactory ) {}


    protected function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        return $this->_validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo );
    }
    /**
     * @param $aInputs
     * @param $aOldInputs
     * @param $oFactory
     * @param $aSubmitInfo
     *
     * @return array
     * @since   3.7.9
     */
    protected function _validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        return $aInputs;
    }

}