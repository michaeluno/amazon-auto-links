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
 * Adds the 'Authentication Keys' section to the 'Authentication' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Authentication_AuthenticationKeys extends AmazonAutoLinks_AdminPage_Section_Base {
    
    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    public function addFields( $oFactory, $sSectionID ) {

        $_bConnected         = true === $this->___getAPIConnectionStatus();

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'api_authentication_status',
                'title'             => __( 'Status', 'amazon-auto-links' ),
                'type'              => 'hidden',
                'value'             => $_bConnected,
                'label'             => $this->_getStatus( $_bConnected ),
            ),
            array(
                'field_id'          => 'access_key',
                'title'             => __( 'Access Key', 'amazon-auto-links' ),
                'type'              => 'text',
                'tip'               => __( 'The public key consisting of 20 alphabetic characters.', 'amazon-auto-links' ),
                'description'       => 'e.g.<code>022QF06E7MXBSH9DHM02</code>',
                'attributes'        => array(
                    'size' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 20 
                        : 40,
                ),                
            ),
            array(
                'field_id'          => 'access_key_secret',
                'title'             => __( 'Secret Access Key', 'amazon-auto-links' ),
                'type'              => 'text',
                'tip'               => __( 'The private key consisting of 40 alphabetic characters.', 'amazon-auto-links' ),
                'description'       => 'e.g.<code>kWcrlUX5JEDGM/LtmEENI/aVmYvHNif5zB+d9+ct</code>',
                'attributes'        => array(
                    'size' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
            ),
            array(
                'field_id'          => 'server_locale',
                'title'             => __( 'Server Locale', 'amazon-auto-links' ),
                'type'              => 'select',
                'description'       => __( 'The region of the API server to use. If you are unsure, select <code>US</code>.', 'amazon-auto-links' ),
                'label'             => array(
                    'CA'    => 'CA - webservices.amazon.ca',
                    'CN'    => 'CN - webservices.amazon.cn',
                    'DE'    => 'DE - webservices.amazon.de',
                    'ES'    => 'ES - webservices.amazon.es',
                    'FR'    => 'FR - webservices.amazon.fr',
                    'IT'    => 'IT - webservices.amazon.it',
                    'JP'    => 'JP - webservices.amazon.co.jp',
                    'UK'    => 'UK - webservices.amazon.co.uk',
                    'US'    => 'US - webservices.amazon.com',
                    'IN'    => 'IN - webservices.amazon.in',            
                    'BR'    => 'BR - webservices.amazon.com.br',        
                    'MX'    => 'MX - webservices.amazon.com.mx',
                    'AU'    => 'AU - webservices.amazon.com.au',    // 3.5.5b01+
                ),
                // 'default'           => 'US',
            ),                
            array(
                'field_id'          => 'disclaimer',
                'title'             => __( 'Disclaimer', 'amazon-auto-links' ),
                'type'              => 'custom_content',
                
                'content'           => "<p class='notice disclaimer'>"
                        . "CERTAIN CONTENT THAT APPEARS [IN THIS APPLICATION or ON THIS SITE, as applicable] COMES FROM AMAZON SERVICES LLC. THIS CONTENT IS PROVIDED 'AS IS' AND IS SUBJECT TO CHANGE OR REMOVAL AT ANY TIME."
                    . "</p>"
                    . "<p>"
                        . sprintf(
                            __( 'Please check the <a href="%1$s" target="_blank">Amazon.com Product Advertising API License Agreement</a> to be safe.', 'amazon-auto-links' ),
                            'https://affiliate-program.amazon.com/gp/advertising/api/detail/agreement.html/ref=amb_link_83957651_1?ie=UTF8&rw_useCurrentProtocol=1&pf_rd_m=ATVPDKIKX0DER&pf_rd_s=assoc-center-1&pf_rd_r=&pf_rd_t=501&pf_rd_p=&pf_rd_i=assoc-api-detail-5-v2'
                        )
                    . "</p>"
            ),
            array(
                'field_id'          => 'submit_connect_to_api_server',
                'type'              => 'submit',
                'value'             => $_bConnected
                    ? __( 'Disconnect', 'amazon-auto-links' )
                    : __( 'Connect', 'amazon-auto-links' ),
                'label_min_width'   => 0,
                'attributes'        => array(
                    'class' => $_bConnected
                        ? 'button-secondary'
                        : 'button-primary',
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),                      
            )            
        );          
        
    }

        /**
         * Checks Amazon Product Advertising API connection.
         *
         * @param string $sPublicKey
         * @param string $sPrivateKey
         * @param string $sLocale
         * @since   3.5.6
         * @return      boolean|string      True if connected; otherwise, an error message.
         */
        private function ___getAPIConnectionStatus( $sPublicKey='', $sPrivateKey='', $sLocale='' ) {
            $_oOption    = AmazonAutoLinks_Option::getInstance();
            $_sLocale    = $sLocale
                ? $sLocale
                : $_oOption->get( array( 'authentication_keys', 'server_locale' ), 'US' );
            $_sPublicKey = $sPublicKey
                ? $sPublicKey
                : $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $sPrivateKey
                ? $sPrivateKey
                : $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
            if ( ! $_sPublicKey || ! $_sPrivateKey ) {
                return __( 'Either a public key or private key is not set.', 'amazon-auto-links' );
            }
            $_oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( $_sLocale, $_sPublicKey, $_sPrivateKey );
            $_bsStatus   = $_oAmazonAPI->test();
            return true === $_bsStatus
                ? true
                : $_bsStatus;
        }
        /**
         * @since       3
         * @since       3.4.4       Added the `$sLocale` third parameter.
         * @return      boolean|string      True if connected; otherwise, an error message.
         * @deprecated  3.5.6   Use the `___getAPIConnectionStatus()` method.
         */
        private function _isConnected( $sPublicKey='', $sPrivateKey='', $sLocale='' ) {
            $_oOption    = AmazonAutoLinks_Option::getInstance();
            $_sLocale    = $sLocale
                ? $sLocale
                : $_oOption->get( array( 'authentication_keys', 'server_locale' ), 'US' );
            $_sPublicKey = $sPublicKey
                ? $sPublicKey
                : $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $sPrivateKey
                ? $sPrivateKey
                : $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );        
            if ( ! $_sPublicKey || ! $_sPrivateKey ) {
                return false;
            }
            $_oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( $_sLocale, $_sPublicKey, $_sPrivateKey );
            return ( boolean ) $_oAmazonAPI->test();
        }

        /**
         * 
         * @since       3
         * @return      string
         */
        private function _getStatus( $bConnected ) {
            
            $_bIsConnected    = $bConnected;
            $_sStatusSelector = $_bIsConnected
                ? 'connected'
                : 'disconnected';
            $_sLabel          = $_bIsConnected
                ? __( 'Connected', 'amazon-auto-links' )
                : __( 'Disconnected', 'amazon-auto-links' );
            return '<span class="' . $_sStatusSelector . '">' 
                    . $_sLabel
                .'</span>';
        }
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3
     */
    public function validate( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();

        // If the connection status is true, it means the user pressed the Disconnect button.
        if ( $aInput[ 'api_authentication_status' ] ) {
            
            $aInput[ 'api_authentication_status' ] = false;
            $aInput[ 'access_key' ] = '';
            $aInput[ 'access_key_secret' ] = '';
            $oAdminPage->setSettingNotice( 
                __( 'Disconnected.', 'amazon-auto-links' ),
                'updated'
            );
            return $aInput;
        }
        
        // Access Key must be 20 characters
        $aInput[ 'access_key' ] = trim( $aInput[ 'access_key' ] );
        $_sPublicKey = $aInput[ 'access_key' ];
        if ( strlen( $_sPublicKey ) != 20 ) {            
            $_aErrors[ $this->sSectionID ][ 'access_key' ] = __( 'The Access Key ID must consist of 20 characters.', 'amazon-auto-links' ) . ' ';
            $_bVerified = false;            
        }
        
        // Access Secret Key must be 40 characters.        
        $aInput[ 'access_key_secret' ] = trim( $aInput[ 'access_key_secret' ] );
        $_sPrivateKey = $aInput[ 'access_key_secret' ];
        if ( strlen( $_sPrivateKey ) != 40 ) {
            $_aErrors[ $this->sSectionID ][ 'access_key_secret' ] = __( 'The Secret Access Key must consist of 40 characters.', 'amazon-auto-links' ) . ' ';
            $_bVerified = false;            
        }                

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInput;
        }

        $_bsConnectionStatus = $this->___getAPIConnectionStatus( $_sPublicKey, $_sPrivateKey, $aInput[ 'server_locale' ] );
        if ( true !== $_bsConnectionStatus ) {
//        if ( ! $this->_isConnected( $_sPublicKey, $_sPrivateKey, $aInput[ 'server_locale' ] ) ) {

            $_aErrors[ $this->sSectionID ][ 'access_key' ]        = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $_sPublicKey;
            $_aErrors[ $this->sSectionID ][ 'access_key_secret' ] = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $_sPrivateKey;            
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice(
                __( 'Failed authentication.', 'amazon-auto-links' ) . '<br />'
                . sprintf( 'Error: %1$s', $_bsConnectionStatus )
            );
            return $aOldInput;
            
        } 
                
        $aInput[ 'api_authentication_status' ] = true;
        unset( $aInput[ 'submit_connect_to_api_server' ] );
        return $aInput;     
        
    }
     
}
