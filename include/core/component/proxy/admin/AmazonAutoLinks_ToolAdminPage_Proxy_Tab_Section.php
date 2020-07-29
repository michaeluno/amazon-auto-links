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
 * Adds the 'Proxies' form section to the 'Proxies' tab.
 * 
 * @since       4.2.0
 */
class AmazonAutoLinks_ToolAdminPage_Proxy_Tab_Section extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * A user constructor.
     * 
     * @since       4.2.0
     * @return      void
     */
    protected function _construct( $oFactory ) {}

    /**
     * Adds form fields.
     * @since       4.2.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'        => 'enable',
                'title'           => __( 'Enable', 'amazon-auto-links' ),
                'type'            => 'checkbox',
                'label'           => __( 'Enable proxies for regular HTTP requests that this plugin performs. API requests will not use proxies.', 'amazon-auto-links' ),
//                'attributes'      => array(
//                    'disabled'  => defined( 'WP_PROXY_HOST' ) ? 'disabled' : null,
//                ),
//                'description'     => defined( 'WP_PROXY_HOST' )
//                    ? '<span class="warning">' . __( 'This option cannot be enabled as the site global proxy is set.', 'amazon-auto-links' ) . '</span>'
//                    : null,
            ),
            array( 
                'field_id'        => 'proxy_list',
                'title'           => __( 'Proxy List', 'amazon-auto-links' ),
                'type'            => 'textarea',
                'attributes'      => array(
                    'style'     => 'height: 400px; width: 100%;',
                    'class'     => 'proxy-list',
                ),
                'description'     => array(
                    __( 'Enter proxy addresses one per line. Accepts up to 10000 items at a maximum.', 'amazon-auto-links' ),
                    sprintf( __( 'The format: <code>%1$s</code>', 'amazon-auto-links' ), '{scheme}://{username}:{password}@{ip address}:{port number}' ),
                ),
            ),
            array(
                'field_id'        => '_load',
                'type'            => 'submit',
                'value'           => __( 'Load', 'amazon-auto-links' ),
                'save'            => false,
                'attributes'      => array(
                    'class' => 'button secondary button_load_proxies',
                ),
            ),
            array(
                'field_id'        => 'unusable',
                'title'           => __( 'Unusable', 'amazon-auto-links' ),
                'type'            => 'textarea',
                'attributes'      => array(
                    'style'     => 'height: 300px; width: 100%;',
                    'class'     => 'unusable-list',
                ),
                'description'     => array(
                    __( 'Unusable items will be listed here and updated automatically when they are found defunct.', 'amazon-auto-links' ),
                    __( 'To clear this unusable proxy list, just remove the input and save.', 'amazon-auto-links' ),
                ),
            ),
            array(
                'field_id'        => '_clear',
                'type'            => 'submit',
                'value'           => __( 'Clear', 'amazon-auto-links' ),
                'save'            => false,
                'attributes'      => array(
                    'class' => 'button secondary button_clear_unusable',
                ),
            ),
            array(
                'field_id'        => 'automatic_updates',
                'title'           => __( 'Automatic Updates', 'amazon-auto-links' ),
                'type'            => 'checkbox',
                'select_type'     => 'checkbox',
                'label'           => __( 'Enable automatic updates for the proxy list.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'        => '_last_updated',
                'title'           => __( 'Last Updated', 'amazon-auto-links' ),
                'type'            => 'text',
                'save'            => false,
                'attributes'      => array(
                    'readonly'  => 'readonly',
                ),
            ),
            array(
                'field_id'        => '_save',
                'type'            => 'submit',
                'value'           => __( 'Save', 'amazon-auto-links' ),
                'save'            => false,
            ),
            array()
        );

    }


    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {

        // @deprecated 4.2.0
//        if ( $this->getElement( $aInputs, 'enable' ) ) {
//            $this->___deleteCaptchaBlockedCaches();
//        }

        // If the Clear button is pressed,
        if ( '_clear' === $this->getElement( $aSubmitInfo, 'field_id' ) ) {
            $aInputs[ 'unusable' ] = '';
        }

        // Sanitize the proxy list.
        $aInputs = $this->___getProxiesSanitized( $aInputs );

        // Return the saving data.
        return $aInputs;

    }

        private function ___getProxiesSanitized( array $aInputs ) {

            $_sProxies   = ( string ) $this->getElement( $aInputs, 'proxy_list' );
            $_aProxies   = preg_split( "/\s+/", trim( ( string ) $_sProxies ), 0, PREG_SPLIT_NO_EMPTY );
            $_aSanitized = array();
            foreach( $_aProxies as $_iIndex => $_sAddress ) {
                if ( ! filter_var( $_sAddress, FILTER_VALIDATE_URL ) ){
                    continue;
                }
                $_aSanitized[] = $_sAddress;
            }
            $_aSanitized = array_unique( $_aSanitized );

            // Keep up to 10000
            $_aSanitized = array_slice( $_aSanitized, -10000, 10000, true );

            $_sProxies   = implode( PHP_EOL, $_aSanitized );
            $aInputs[ 'proxy_list' ] = $_sProxies;
            return $aInputs;

        }

        /**
         * @since   4.2.0
         * @todo    complete this method
         * @deprecated 4.2.0    The cached HTTP response bodies are character-encoded so the database search does not work.
         */
        private function ___deleteCaptchaBlockedCaches() {

        }

}