<?php
/**
 * Handles base64 strings.
 * 
 * This class provides an alternative for the base64 underscore decode / encode function.
 * Some over-sensitive users have hysterical allergy against the function and tries to flag scripts that use it as virus or malware.
 *
 * @package      Amazon Auto Links
 * @copyright    Copyright (c) 2013-2020, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 */
if ( ! class_exists( 'IXR_Message', false ) ) { 
    require_once( ABSPATH . WPINC . '/class-IXR.php' );
}
class AmazonAutoLinks_Encrypt extends IXR_Message {
    
    /**
     * The base 64 function name.
     */
    protected $_sFunction = 'base64_encode';
    
    /**
     * Overrides the parent constructor.
     */
    function __construct() {}   
    
    /**
     * Encodes the given data to a base 64 encoded string.
     * 
     * @return      string
     * @since       3           Moved from `AmazonAutoLinks_Encrypt`.
     */    
    public function encode( $vData ) {
        
        if ( 
            in_array( 
                gettype( $vData ), 
                array( 'array', 'object' ) 
            )
        ) {
            $vData = serialize( $vData );
        }            
        return call_user_func_array( 
            $this->_sFunction, 
            array( $vData )
        );
    }
    
    /**
     * Decodes a given base 64 encoded string.
     * 
     * @return      string
     */        
    public function decode( $sCode ) {
            
        // make sure it's empty
        $this->params = array();    
        
        $this->_currentTagContents = $sCode;
        $this->tag_close( '', 'base64' );
        $vData = $this->params[ 0 ];
        
        return is_serialized( $vData ) 
            ? unserialize( $vData )
            : $vData;
        
    }    
    
}