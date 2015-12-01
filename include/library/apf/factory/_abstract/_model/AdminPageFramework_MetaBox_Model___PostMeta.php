<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to retrieve post meta data for meta box form fields.
 * 
 * @since       DEVVER
 * @package     AmazonAutoLinks_AdminPageFramework
 * @subpackage  MetaBox
 * @extends     AmazonAutoLinks_AdminPageFramework_Property_Base
 * @internal
 */
class AmazonAutoLinks_AdminPageFramework_MetaBox_Model___PostMeta extends AmazonAutoLinks_AdminPageFramework_WPUtility {

    public $iPostID         = array();
    
    public $aFieldsets      = array();
    
    /**
     * Sets up hooks.
     * 
     * @since       DEVVER
     */
    public function __construct( /* $iPostID, array $aFieldsets */ ) {
        
        $_aParameters = func_get_args() + array( 
            $this->iPostID,
            $this->aFieldsets, 
        );
        $this->iPostID          = $_aParameters[ 0 ];
        $this->aFieldsets       = $_aParameters[ 1 ];
        
    }
    
    /**
     * @since       DEVVER
     * @return      array
     */
    public function get() {

        if ( ! $this->iPostID ) {
            return array();
        }
        return $this->_getSavedDataFromFieldsets(
            $this->iPostID,
            $this->aFieldsets
        );
    }
        /**
         * Returns an array hodlding post meta data associated with the given post ID and constructed with the given fieldsets.
         * 
         * @since       DEVVER
         * @uses        get_post_meta()
         * @return      array
         */
        private function _getSavedDataFromFieldsets( $iPostID, $aFieldsets ) {
            
            $_aMetaKeys = $this->getAsArray( 
                get_post_custom_keys( $iPostID )  // returns array or null
            );     

            $_aMetaData = array();
            foreach( $aFieldsets as $_sSectionID => $_aFieldsets ) {
                
                if ( '_default' == $_sSectionID  ) {
                    foreach( $_aFieldsets as $_aFieldset ) {
                        if ( ! in_array( $_aFieldset[ 'field_id' ], $_aMetaKeys ) ) {
                            continue;
                        }
                        $_aMetaData[ $_aFieldset[ 'field_id' ] ] = get_post_meta( 
                            $iPostID, 
                            $_aFieldset[ 'field_id' ], 
                            true 
                        );    
                    }
                }
                if ( ! in_array( $_sSectionID, $_aMetaKeys ) ) {
                    continue;
                }                
                $_aMetaData[ $_sSectionID ] = get_post_meta( 
                    $iPostID, 
                    $_sSectionID, 
                    true 
                );
                
            }            
            return $_aMetaData;
            
        }
        
}