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
 * Check PA-API connection
 * @since   4.5.0
 *
 */
class AmazonAutoLinks_Admin_Settings_Event_Ajax_PAAPICheck extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookSuffix = 'aal_action_ajax_paapi_check';
    protected $_bLoggedIn         = true;
    protected $_bGuest            = false;

    protected function _construct() {}

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.6.18
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'locale'        => sanitize_text_field( $this->getElement( $aPost, array( 'locale' ), '' ) ),
            'associate_id'  => sanitize_text_field( $this->getElement( $aPost, array( 'associate_id' ), '' ) ),
            'access_key'    => sanitize_text_field( $this->getElement( $aPost, array( 'access_key' ), '' ) ),
            'secret_key'    => sanitize_text_field( $this->getElement( $aPost, array( 'secret_key' ), '' ) ),
        );
    }

    /**
     * @return array
     * @throws Exception Throws a string value of an error message.
     * @since  4.5.0
     * @param  array     $aPost Sanitized POST array including the `locale`, `associate_id`, `access_key`, and `secret_key` elements.
     */
    protected function _getResponse( array $aPost ) {

        $_sLocale      = $this->getElement( $aPost, array( 'locale' ), '' );
        $_sAssociateID = $this->getElement( $aPost, array( 'associate_id' ), '' );
        $_sAccessKey   = $this->getElement( $aPost, array( 'access_key' ), '' );
        $_sSecretKey   = $this->getElement( $aPost, array( 'secret_key' ), '' );

        $_iNow         = time();
        $_aResponse    = array(
            'error'                  => 0,
            'last_checked'           => $_iNow,
            'last_checked_readable'  => $this->getSiteReadableDate( $_iNow, get_option( 'date_format' ) . ' H:i:s', true ),
        );
        try {
            $this->___getPAAPITested( $_sLocale, $_sAssociateID, $_sAccessKey, $_sSecretKey );
        } catch ( Exception $oException ) {
            return array(
                'error'     => 1,
                'message'   => $oException->getMessage(),
            ) + $_aResponse;
        }
        return array(
            'message'                => __( 'OK', 'amazon-auto-links' )
        ) + $_aResponse;

    }
        /**
         * @param  string    $sLocale
         * @param  string    $sAssociateID
         * @param  string    $sAccessKey
         * @param  string    $sSecretKey
         * @throws Exception
         * @since  4.5.0
         * @return true
         */
        private function ___getPAAPITested( $sLocale, $sAssociateID, $sAccessKey, $sSecretKey ) {
            $_aParameterNames = array(
                0 => __( 'locale', 'amazon-auto-links' ),
                1 => __( 'Associate ID', 'amazon-auto-links' ),
                2 => __( 'access key', 'amazon-auto-links' ),
                3 => __( 'secret access key', 'amazon-auto-links' ),
            );
            $_aParameters = func_get_args();
            foreach( $_aParameterNames as $_iIndex => $_sName ) {
                if ( empty( $_aParameters[ $_iIndex ] ) ) {
                    throw new Exception( sprintf( __( 'The option, %1$s, is empty.', 'amazon-auto-links' ), $_sName ) );
                }
            }
            $_oAmazonAPI  = new AmazonAutoLinks_PAAPI50( $sLocale, $sAccessKey, $sSecretKey, $sAssociateID );
            $_bsResult    = $_oAmazonAPI->test( 0 );
            if ( is_string( $_bsResult ) ) {
                throw new Exception( $_bsResult );
            }
            return true;
        }

}