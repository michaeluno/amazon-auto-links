<?php


class AmazonAutoLinks_Proxy_Fetch_Base extends AmazonAutoLinks_PluginUtility {


    protected $_sURL = 'https://www.proxy-list.download/api/v1/get?type=http';
    protected $_iCacheLifespan = 3600;
    protected $_sScheme = 'http://';

    /**
     * @return array
     */
    public function get() {

        $_oHTTP = new AmazonAutoLinks_HTTPClient(
            $this->_sURL,
            0, // @todo revert the change for testing  3600,   // 1 hour
            array(
                'timeout'       => 20,
            ),
            AmazonAutoLinks_Proxy_Loader::$sHTTPRequestType // request type
        );
        $_sResponse = $_oHTTP->get();   // an http body as string or an error message

        $_sOutput   = strip_tags( $_sResponse );
        $_aProxies  = preg_split( '/[\r\n]+/i', $_sOutput, -1, PREG_SPLIT_NO_EMPTY );
        if ( ! is_array( $_aProxies ) ) {
            return [];
        }
        $_aProxies = array_unique( $_aProxies );
        foreach( $_aProxies as $_iIndex => $_sProxy ) {
            $_sProxy = trim( $_sProxy );
            $_sProxy = $this->_sScheme . $_sProxy;
            if ( ! filter_var( $_sProxy, FILTER_VALIDATE_URL ) ){
                continue;
            }
            $_aProxies[ $_iIndex ] = $_sProxy;
        }
        return $_aProxies;

    }

}