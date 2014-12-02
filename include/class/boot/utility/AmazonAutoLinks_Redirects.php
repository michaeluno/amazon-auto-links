<?php

if ( ! class_exists( 'IXR_Message' ) ) require_once( ABSPATH . WPINC . '/class-IXR.php' );
final class AmazonAutoLinks_Redirects extends IXR_Message {

    function __construct() {}    // needs it to override the parent constructor.

    public function go( $strEncodedURL, $strEncodeType='base64' ) {
        
        if ( $strEncodeType == 'base64' )
            $strURL = $this->alterBase64( $strEncodedURL );
        else 
            $strURL = $strEncodedURL;
            
        $this->redirect( $strURL );
        exit;
        
    }
    protected function redirect( $strURL, $strStatus=302 ) {
        
        // the no-filter version of wp_redirect()
        
        global $is_IIS;

        if ( !$strURL ) // allows the wp_redirect filter to cancel a redirect
            return false;


        if ( !$is_IIS && php_sapi_name() != 'cgi-fcgi' )
            status_header( $strStatus ); // This causes problems on IIS and some FastCGI setups

        header( "Location: $strURL", true, $strStatus );
        
    }
    
    public function alterBase64( $bin ) {

        // Some over-sensitive users have hysterical allergy against the base64 decode function so avoid using that. 
        // Instead, use the code of the core. I don't get why we should not use it in plugins while the core is using it. 
    
        $this->params = array();    // make sure it's empty
        $this->_currentTagContents = $bin;
        $this->tag_close( '', 'base64' );
        return $this->params[0];
        
    }    
    
}