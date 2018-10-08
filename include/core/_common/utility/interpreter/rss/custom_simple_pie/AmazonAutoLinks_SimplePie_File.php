<?php


class AmazonAutoLinks_SimplePie_File extends WP_SimplePie_File {

    var $url;
    var $useragent;
    var $success = true;
    var $headers = array();
    var $body;
    var $status_code;
    var $redirects = 0;
    var $error;
    var $method = SIMPLEPIE_FILE_SOURCE_REMOTE;
    
    protected $aArgs = array(
        'timeout'     => 5,
        'redirection' => 5,
        'httpversion' => '1.0',
        'user-agent'  => null,
        'blocking'    => true,
        'headers'     => array(),
        'cookies'     => array(),
        'body'        => null,
        'compress'    => false,
        'decompress'  => true,
        'sslverify'   => true,
        'stream'      => false,
        'filename'    => null
    ); 
    
    public function __construct( $sURL, $iTimeout=10, $iRedirects=5, $aHeaders=null, $sUserAgent=null, $bForceFsockOpen=false ) {

        $this->timeout   = $iTimeout;
        $this->redirects = $iRedirects;
        $this->headers   = $aHeaders;
        $this->useragent = $sUserAgent;        
        $this->url       = $sURL;

        // If the scheme is not http or https.
        if ( ! preg_match( '/^http(s)?:\/\//i', $sURL ) ) {
            $this->error = '';
            $this->success = false;            
            return;
        }
            
        // Arguments
        $aArgs     = array(
            'timeout'       => $this->timeout,
            'redirection'   => $this->redirects, true,
            'sslverify'     => false, // this is missing in WP_SimplePie_File
        );

        if ( ! empty( $this->headers ) ) {
            $aArgs['headers'] = $this->headers;
        }

        if ( SIMPLEPIE_USERAGENT != $this->useragent ) {
            $aArgs['user-agent'] = $this->useragent;
        }
        
        // Request
        $res = function_exists( 'wp_safe_remote_request' )
            ? wp_safe_remote_request( $sURL, $aArgs )
            : wp_remote_get( $sURL, $aArgs );

        if ( is_wp_error( $res ) ) {
            
            $this->error   = 'WP HTTP Error: ' . $res->get_error_message();
            $this->success = false;
            return;
            
        } 
            
        $this->headers     = wp_remote_retrieve_headers( $res );
        $this->body        = wp_remote_retrieve_body( $res );
        $this->status_code = wp_remote_retrieve_response_code( $res );    
        
    }
    
}