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
 * Provides common methods for option objects.
 * 
 * @since       3
 */
class AmazonAutoLinks_Option_Base extends AmazonAutoLinks_WPUtility {
    
    /**
     * Stores the option values.
     * 
     * @access      public      Let the data being modified from outside.
     */
    public $aOptions = array(        
    );  
    
    /**
     * Represents the options array structure and their default values.
     * 
     * @remark      Override this value in an extended class.
     */
    public $aDefault = array(
    );
    
    /**
     * stores the option key for this plugin. 
     */
    protected $sOptionKey = '';        
         
    /**
     * Stores whether the currently loading page is in the network admin area.
     */
    protected $bIsNetworkAdmin = false;     
         
    /**
     * Sets up properties.
     */
    public function __construct( $sOptionKey ) {
        
        $this->bIsNetworkAdmin  = false; // disabled.
        $this->sOptionKey       = $sOptionKey;
        $this->aOptions         = $this->_getFormattedOptions( $sOptionKey );
        
    }     
        /**
         * Returns the formatted options array.
         * @remark  Override this method in an extended class.
         * @return  array
         */    
        protected function _getFormattedOptions( $sOptionKey ) {
            return $this->uniteArrays(
                $this->getAsArray(
                    $this->bIsNetworkAdmin
                        ? get_site_option( $sOptionKey, array() )
                        : get_option( $sOptionKey, array() )
                ),
                $this->aDefault
            );
        }
        
    /**
     * Checks the version number
     * 
     * @since       3
     * @return      boolean        True if yes; otherwise, false.
     * @remrk       not used at the moment
     */
    public function hasUpgraded() {
        
        $_sOptionVersion        = $this->get( 'version_saved' );
        if ( ! $_sOptionVersion ) {
            return false;
        }
        $_sOptionVersion        = $this->_getVersionByDepth( $_sOptionVersion );
        $_sCurrentVersion       = $this->_getVersionByDepth( AmazonAutoLinks_Registry::VERSION );
        return version_compare( $_sOptionVersion, $_sCurrentVersion, '<' );
        
    }
        /**
         * Returns a stating part of version by the given depth.
         * @since       3
         */
        private function _getVersionByDepth( $sVersion, $iDepth=2 ) {
            if ( ! $iDepth ) {
                return $sVersion;
            }
            $_aParts = explode( '.', $sVersion );
            $_aParts = array_slice( $_aParts, 0, $iDepth );
            return implode( '.', $_aParts );
        }    
    
    /**
     * Deletes the option from the database.
     */
    public function delete()  {
        return $this->bIsNetworkAdmin
            ? delete_site_option( $this->sOptionKey )
            : delete_option( $this->sOptionKey );
    }
    
    /**
     * Saves the options.
     * 
     * @return      boolean     true on success; otherwise, false.
     */
    public function save( $aOptions=null ) {

        $_aOptions = $aOptions 
            ? $aOptions 
            : $this->aOptions;
        return $this->bIsNetworkAdmin
            ? update_site_option(
                $this->sOptionKey, 
                $_aOptions
            )
            : update_option( 
                $this->sOptionKey, 
                $_aOptions
            );
    }
    
    /**
     * Sets the options.
     */
    public function set( /* $asKeys, $mValue */ ) {
        
        $_aParameters   = func_get_args();
        if ( ! isset( $_aParameters[ 0 ], $_aParameters[ 1 ] ) ) {
            return;
        }
        $_asKeys        = $_aParameters[ 0 ];
        $_mValue        = $_aParameters[ 1 ];
        
        // string, integer, float, boolean
        if ( ! is_array( $_asKeys ) ) {
            $this->aOptions[ $_asKeys ] = $_mValue;
            return;
        }
        
        // the keys are passed as an array
        $this->setMultiDimensionalArray( 
            $this->aOptions, 
            $_asKeys,
            $_mValue 
        );

    }
    
    /**
     * Sets and save the options.
     */
    public function update( /* $asKeys, $mValue */ ) {
        
        $_aParameters = func_get_args();
        call_user_func_array( array( $this, 'set' ), $_aParameters );
        $this->save();

    }

    /**
     * Returns the specified option value.
     * 
     * @since       3
     * @return      mixed
     */
    public function get( /* $sKey1, $sKey2, $sKey3, ... OR $aKeys, $vDefault */ ) {
        
        $_mDefault     = null;
        $_aKeys        = func_get_args() + array( null );
        if ( ! isset( $_aKeys[ 0 ] ) ) {
            return $this->aOptions;
        }
        if ( is_array( $_aKeys[ 0 ] ) ) {
            $_aKeys[ 1 ] = isset( $_aKeys[ 1 ] ) ? $_aKeys[ 1 ] : null;
            $_mDefault = $_aKeys[ 1 ];
            $_aKeys    = $_aKeys[ 0 ];
        }
        return $this->getArrayValueByArrayKeys( 
            $this->aOptions, 
            $_aKeys,
            $_mDefault
        );
        
    }
  
    
}