<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
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
        
        $_bConnected = $this->_isConnected();
    
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'api_authentication_status',
                'title'             => __( 'Status', 'amazon-auto-links' ),
                'type'              => 'hidden',
                'value'             => $_bConnected,
                'before_field'      => $this->_getStatus( $_bConnected ),
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
         * 
         * @since       3
         * @return      boolean
         */
        private function _isConnected( $sPublicKey='', $sPrivateKey='' ) {
            
            $_oOption    = AmazonAutoLinks_Option::getInstance();
            $_oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 
                'US',   // locale
                $sPublicKey
                    ? $sPublicKey
                    : $_oOption->get( array( 'authentication_keys', 'access_key' ), '' ), 
                $sPrivateKey
                    ? $sPrivateKey
                    : $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' ) 
            );
            return ( boolean ) $_oAmazonAPI->test();                
        
        }
        /**
         * 
         * @since       3
         * @return      string
         */
        private function _getStatus( $bConnected ) {
            // $_bIsConnected    = $this->_isConnected();
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
                
        if ( ! $this->_isConnected( $_sPublicKey, $_sPrivateKey ) ) {
            
            $_aErrors[ $this->sSectionID ]['access_key'] = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $_sPublicKey;
            $_aErrors[ $this->sSectionID ]['access_key_secret'] = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $_sPrivateKey;            
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice( __( 'Failed authentication.', 'amazon-auto-links' ) );
            return $aOldInput;
            
        } 
                
        $aInput[ 'api_authentication_status' ] = true;
        unset( $aInput[ 'submit_connect_to_api_server' ] );
        return $aInput;     
        
    }
     
}