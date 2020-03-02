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
 * Adds the 'Authentication Keys' section to the 'Authentication' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Authentication_AuthenticationKeys extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'authentication_keys',
            'title'         => __( 'AWS Access Key Identifiers', 'amazon-auto-links' ),
            'description'   => sprintf(
                    __( 'Credentials are required to perform search requests with Amazon <a href="%1$s" target="_blank">Product Advertising API</a>.', 'amazon-auto-links' ),
                    'https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html'
                )
                . ' ' . sprintf(
                    __( 'The keys can be obtained by logging in to the <a href="%1$s" target="_blank">Amazon Web Services web site</a>.', 'amazon-auto-links' ),
                    'http://aws.amazon.com/'
                )
                . ' ' . sprintf(
                    __( 'The instruction is documented <a href="%1$s" target="_blank">here</a>.', 'amazon-auto-links' ),
                    '?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] . '&page=aal_help&tab=tips#How_to_Obtain_Access_Key_and_Secret_Key'
                ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function _construct( $oFactory ) {}

    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $_bConnected         = ! $this->___shouldTestAPIConnection()
            ? false
            : true === $this->___getAPIConnectionStatus();

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'          => 'api_authentication_status',
                'title'             => __( 'Status', 'amazon-auto-links' ),
                'type'              => 'hidden',
                'value'             => $_bConnected,
                'label'             => $this->___getStatus( $_bConnected ),
                'description'       => sprintf(
                    __( 'If you get an error, try testing your keys with <a href="%1$s" target="_blank">Scratchpad</a>.', 'amazon-auto-links' ),
                    'https://webservices.amazon.com/paapi5/scratchpad/'
                ),
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
                'type'              => 'password',
                'tip'               => __( 'The private key consisting of 40 alphabetic characters.', 'amazon-auto-links' ),
                'description'       => 'e.g.<code>kWcrlUX5JEDGM/LtmEENI/aVmYvHNif5zB+d9+ct</code>',
                'attributes'        => array(
                    'size' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
            ),
            array(  // 3.6.7+
                'field_id'          => 'associates_test_tag',
                'title'             => __( 'Associate ID', 'amazon-auto-links' ),
                    // . ' ' . __( '(optional)', 'amazon-auto-links' ),
                'type'              => 'text',
                'description'       => __( 'The Amazon Associates tag.', 'amazon-auto-links' ) . ' '
                    . __( 'Some locales may require this to be set.', 'amazon-auto-links' ) . ' '
                    . 'e.g.<code>example-20</code>',
            ),
            array(
                'field_id'          => 'server_locale',
                'title'             => __( 'Server Locale', 'amazon-auto-links' ),
                'type'              => 'select',
                'description'       => __( 'The region of the API server. If you are unsure, select <code>US</code>.', 'amazon-auto-links' ),
                'label'             => AmazonAutoLinks_PAAPI50___Locales::getHostLabels(),
                 'default'           => 'US',
            ),                
            array(
                'field_id'          => 'disclaimer',
                'title'             => __( 'Disclaimer', 'amazon-auto-links' ),
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
                'save'              => false,
            )            
        );          
        
    }
        /**
         * @since   3.9.0
         * @return  array
         * @deprecated  3.9.1
         */
/*        private function ___getLocaleLabels() {
            $_oLocales = new AmazonAutoLinks_PAAPI50___Locales;
            $_aLabels  = array();
            foreach( $_oLocales->aHosts as $_sKey => $_sHost ) {
                $_aLabels[ $_sKey ] = $_sKey . ' - ' . $_sHost;
            }
            return $_aLabels;
        }*/
        /**
         * If API keys are set and the previous status is false, this means the user intentionally disconnected the connection.
         * In this case, do not perfomr a test.
         * @since 3.9.0
         */
        private function ___shouldTestAPIConnection() {

            if ( ! $this->___isAPIKeySet() ) {
                return false;
            }

            /**
             * @todo    3.9.0 Check a saved flag value (make one somewhere) that indicates an API connection error.
             * And if it occurred in a few hours, then consider it reached the API rate limit.
             */

            $_oOption    = AmazonAutoLinks_Option::getInstance();
            return ( boolean ) $_oOption->get( array( 'authentication_keys', 'api_authentication_status' ), false );

        }
        /**
         * Checks Amazon Product Advertising API connection.
         *
         * @param string $sPublicKey
         * @param string $sPrivateKey
         * @param string $sAssociateID  3.6.7+
         * @param string $sLocale
         * @since   3.5.6
         * @return      boolean|string      True if connected; otherwise, an error message.
         */
        private function ___getAPIConnectionStatus( $sPublicKey='', $sPrivateKey='', $sAssociateID='', $sLocale='' ) {

            $_oOption     = AmazonAutoLinks_Option::getInstance();
            $_sPublicKey  = $sPublicKey
                ? $sPublicKey
                : $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
            $_sPrivateKey = $sPrivateKey
                ? $sPrivateKey
                : $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
            if ( ! $this->___isAPIKeySet( $_sPublicKey, $_sPrivateKey ) ) {
                return __( 'Either a public key or private key is not set.', 'amazon-auto-links' );
            }

            $_sLocale    = $sLocale
                ? $sLocale
                : $_oOption->get( array( 'authentication_keys', 'server_locale' ), 'US' );
            $_sAssociateID = $sAssociateID
                ? $sAssociateID
                : $_oOption->get( array( 'authentication_keys', 'associates_test_tag' ), '' );

            // @since   3.9.0 Use PA-API5.0 as PA-API 4.0 has been deprecated
            $_oAmazonAPI = new AmazonAutoLinks_PAAPI50( $_sLocale, $_sPublicKey, $_sPrivateKey, $_sAssociateID );
            $_bsStatus   = $_oAmazonAPI->test();

            return true === $_bsStatus
                ? true
                : $_bsStatus;

        }
            /**
             * Checks if keys are set in the options or passed to the parameters.
             * @since   3.9.0
             * @return  boolean
             */
            private function ___isAPIKeySet( $sPublicKey='', $sPrivateKey='' ) {
                if ( $sPublicKey && $sPrivateKey ) {
                    return true;
                }
                $_oOption    = AmazonAutoLinks_Option::getInstance();
                $_sPublicKey = $sPublicKey
                    ? $sPublicKey
                    : $_oOption->get( array( 'authentication_keys', 'access_key' ), '' );
                $_sPrivateKey = $sPrivateKey
                    ? $sPrivateKey
                    : $_oOption->get( array( 'authentication_keys', 'access_key_secret' ), '' );
                if ( ! $_sPublicKey || ! $_sPrivateKey ) {
                    return false;
                }
                return true;
            }

        /**
         * 
         * @since       3
         * @return      string
         */
        private function ___getStatus( $bConnected ) {
            
            $_bIsConnected    = $bConnected;
            $_sStatusSelector = $_bIsConnected
                ? 'connected'
                : 'disconnected';
            $_sLabel          = $_bIsConnected
                ? __( 'Connected', 'amazon-auto-links' )
                : __( 'Disconnected', 'amazon-auto-links' );
            return '<p><span class="' . $_sStatusSelector . '">'
                    . $_sLabel
                .'</span></p>';

        }
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();

        $aInputs[ 'associates_test_tag' ] = trim( $aInputs[ 'associates_test_tag' ] ); // 3.6.7+
        
        // If the connection status is true, it means the user pressed the Disconnect button.
        if ( $aInputs[ 'api_authentication_status' ] ) {
            
            $aInputs[ 'api_authentication_status' ] = false;
            $aInputs[ 'access_key' ]                = '';
            $aInputs[ 'access_key_secret' ]         = '';
            $oAdminPage->setSettingNotice( 
                __( 'Disconnected.', 'amazon-auto-links' ),
                'updated'
            );
            return $aInputs;

        }
        
        // Access Key must be 20 characters
        $aInputs[ 'access_key' ] = trim( $aInputs[ 'access_key' ] );
        $_sPublicKey = $aInputs[ 'access_key' ];
        if ( strlen( $_sPublicKey ) != 20 ) {            
            $_aErrors[ $this->sSectionID ][ 'access_key' ] = __( 'The Access Key ID must consist of 20 characters.', 'amazon-auto-links' ) . ' ';
            $_bVerified = false;            
        }
        
        // Access Secret Key must be 40 characters.        
        $aInputs[ 'access_key_secret' ] = trim( $aInputs[ 'access_key_secret' ] );
        $_sPrivateKey = $aInputs[ 'access_key_secret' ];
        if ( strlen( $_sPrivateKey ) != 40 ) {
            $_aErrors[ $this->sSectionID ][ 'access_key_secret' ] = __( 'The Secret Access Key must consist of 40 characters.', 'amazon-auto-links' ) . ' ';
            $_bVerified = false;            
        }                

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }

        $_bsConnectionStatus = $this->___getAPIConnectionStatus(
            $_sPublicKey,
            $_sPrivateKey,
            $aInputs[ 'associates_test_tag' ],
            $aInputs[ 'server_locale' ]
        );

        if ( true !== $_bsConnectionStatus ) {

            $_aErrors[ $this->sSectionID ][ 'access_key' ]        = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $_sPublicKey;
            $_aErrors[ $this->sSectionID ][ 'access_key_secret' ] = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $_sPrivateKey;            
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice(
                __( 'Failed authentication.', 'amazon-auto-links' ) . '<br />'
                . sprintf( 'Error: %1$s', $_bsConnectionStatus )
            );
            $aOldInputs[ 'api_authentication_status' ] = false;
            return $aOldInputs;
            
        } 
                
        $aInputs[ 'api_authentication_status' ] = true;
        unset( $aInputs[ 'submit_connect_to_api_server' ] );
        return $aInputs;     
        
    }
     
}
