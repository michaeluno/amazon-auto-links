<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Add additional information to contact Email messages.
 * @since   4.7.0
 */
class AmazonAutoLinks_Admin_Settings_Event_Contact extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'amazon-auto-links_action_before_sending_form_email';
    protected $_iCallbackParameters = 3;

    /**
     * @return bool
     * @since 4.7.0
     */
    protected function _shouldProceed( /* $aArguments */ ) {
        return true;
    }

    protected function _doAction( /* $aArguments */  ) {

        $_aParameters = func_get_args() + array( array(), array(), '' );
        $_aEmailOptions = $_aParameters[ 0 ];
        $_aInputs       = $_aParameters[ 1 ];
        $_sSectionID    = $_aParameters[ 2 ];
        if ( ! ( boolean ) $this->getElement( $_aInputs, array( $_sSectionID, 'allow_sending_system_information' ) ) ) {
            return;
        }
        add_filter( 'wp_mail', array( $this, 'replyToGetWPMailArguments' ) );
        add_action( 'amazon-auto-links_action_after_sending_form_email', array( $this, 'replyToCleanUp' ) );
    }

    /**
     * @var   array
     * @since 4.7.0
     */
    private $___aEmail = array();

    /**
     * @since 4.7.0
     */
    public function replyToCleanUp( $bSent ) {
        remove_action( 'amazon-auto-links_action_after_sending_form_email', array( $this, 'replyToCleanUp' ) );
        // If the main mail failed, do nothing
        if ( ! $bSent ) {
            return;
        }
        @include_once( ABSPATH . 'wp-admin/includes/class-wp-debug-data.php' );
        // @deprecated some sites fail to send emails with large data
        // $_aData    = AmazonAutoLinks_SiteInformation::get( false ); // not including extra as it can cause failure on sending emails on some servers.
        // unset( $_aData[ 'Plugin' ] );
        // $_aData    = array(
            // 'WordPress'         => class_exists( 'WP_Debug_Data' ) ? WP_Debug_Data::debug_data() : array(),
            // 'General Options'   => $this->___getGeneralOptions(),
            // 'Template Options'  => $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ] ) ),
            // 'Tools Options'     => $this->___getToolsOptions(),
            // 'PHP'               => $this->getPHPInfo(),
            // 'MySQL'             => $this->getMySQLInfo(),
        // );
        $_aData  = class_exists( 'WP_Debug_Data' )
            ? WP_Debug_Data::debug_data()
            : array();
        $_sTable = $this->getTableOfArray(
            $_aData,
            array(
                'p'  => array(
                    'style' => 'height: 1em; margin: 0.2em 0.2em 0.2em 0;',
                ),
                'td' => array(
                    array(
                        'style' => 'vertical-align: top; width: 12%;',
                    ),
                    array(
                        'style' => 'vertical-align: top;',
                    ),
                ),
            )
        );
        wp_mail(
            $this->getElement( $this->___aEmail, 'to' ),
            sprintf( 'Reporting Issue of %1$s (Site Information)', AmazonAutoLinks_Registry::NAME . ' ' . AmazonAutoLinks_Registry::VERSION ),
            $_sTable
        );
    }
        private function ___getGeneralOptions() {
            $_aOptions = $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ] ) );
            foreach( $this->getElementAsArray( $_aOptions, 'associates'  ) as $_sLocale => $_aAssociate ) {
                if ( ! is_array( $_aAssociate ) ) {
                    continue;
                }
                $_sAccessKey = $this->getElement( $_aAssociate, array( 'paapi', 'access_key' ) );
                if ( $_sAccessKey ) {
                    $_aOptions[ 'associates' ][ $_sLocale ][ 'paapi' ][ 'access_key' ] = '********';
                }
                $_sSecretKey = $this->getElement( $_aAssociate, array( 'paapi', 'secret_key' ) );
                if ( $_sSecretKey ) {
                    $_aOptions[ 'associates' ][ $_sLocale ][ 'paapi' ][ 'secret_key' ] = '********';
                }
            }
            return $_aOptions;
        }
        private function ___getToolsOptions() {
            $_aToolsOptions    = $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ] ) );
            $_sUnusableProxies = $this->getElement( $_aToolsOptions, array( 'proxies', 'unusable' ) );
            if ( $_sUnusableProxies ) {
                $_aToolsOptions[ 'proxies' ][ 'unusable' ] = count( explode( PHP_EOL, $_sUnusableProxies ) ) . ' items';
            }
            return $_aToolsOptions;
        }

    /**
     * @param $aWPMail
     * @return mixed
     */
    public function replyToGetWPMailArguments( $aWPMail ) {
        $this->___aEmail = $aWPMail;
        return $aWPMail;
    }

}