<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles the plugin deactivation survey.
 * @since   4.7.3
 */
class AmazonAutoLinks_Opt_Event_Ajax_SurveyPluginDeactivation extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookSuffix = 'aal_action_opt_survey_plugin_deactivation';

    protected $_bGuest = false;

    private $___iUserID = 0;
    /**
     * @var WP_User
     */
    private $___oUser;

    private $___bAnonymous = false;

    /**
     * @param  array $aPost Passed POST data.
     * @return array
     * @since  4.7.3
     */
    protected function _getPostSanitized( array $aPost ) {
        return array(
            'form' => $this->___getFormDataParsed( $this->getElementAsArray( $aPost, 'form' ) ),
        );
    }
        /**
         * @param  array $aForm
         * ```
         *   Array (
         *       [3] => Array(
         *           [name] => (string, length: 8) _wpnonce
         *           [value] => (string, length: 10) 253674ce0a
         *       )
         *       [4] => Array(
         *           [name] => (string, length: 16) _wp_http_referer
         *           [value] => (string, length: 94) /test-admin-page-framework/wp-admin/edit.php?post_type=apf_posts&page=apf_contact&tab=feedback
         *       )
         *       [5] => Array(
         *           [name] => (string, length: 24) APF_Demo[feedback][name]
         *           [value] => (string, length: 0)
         *       )
         * ```
         * @return array
         * @since  4.7.3
         */
        private function ___getFormDataParsed( array $aForm ) {
            $_aForm = array();
            $aForm  = array_reverse( $aForm );  // to preserver checkbox checked values as checkbox inputs have a preceding hidden input with the same name.
            foreach( $aForm as $_iIndex => $_aNameValue ) {
                parse_str( $_aNameValue[ 'name' ] . '=' . $_aNameValue[ 'value' ], $_a );
                $_aForm = $this->uniteArrays( $_aForm, $_a );
            }
            return array_reverse( $_aForm );    // @note inner nested elements are still in the reversed order
        }

    /**
     * @return string
     * @throws Exception
     * @param  array     $aPost Unused at the moment.
     * @since  4.7.3
     */
    protected function _getResponse( array $aPost ) {

        $_aForm    = $this->getElementAsArray( $aPost, array( 'form', 'plugin_deactivation' ) );
        $_aForm    = array_filter( $_aForm );
        $_sReason  = $this->getElement( $_aForm, 'reason' );

        $this->___iUserID    = get_current_user_id();
        $this->___oUser      = get_userdata( $this->___iUserID );
        $this->___bAnonymous = $this->getElement( $_aForm, 'anonymous' );

        $_aData    = array(
            $_sReason . ':'   => $this->getElement( $_aForm, $_sReason ),
            'Plugin:'         => AmazonAutoLinks_Registry::NAME . ' ' . AmazonAutoLinks_Registry::VERSION,
            'Site:'           => $this->___bAnonymous
                ? null
                : site_url(),
            'WordPress:'      => $GLOBALS[ 'wp_version' ],
            'PHP:'            => phpversion(),
            'MySQL:'          => $GLOBALS[ 'wpdb' ]->get_var( "SELECT VERSION();" ),
            'Usage Duration:' => $this->___getUsageDuration() . ' days',
        );

        $_sMessage = $this->getTableOfArray(
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
        add_filter( 'wp_mail_content_type', array( $this, 'replyToSetMailContentTypeToHTML' ) );
        add_filter( 'wp_mail_from', array( $this, 'replyToSetEmailSenderAddress' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'replyToSetEmailSenderName' ) );

        $_bSent    = wp_mail(
            'aal-support@michaeluno.jp',
            sprintf( 'Plugin Deactivation Feedback of %1$s', AmazonAutoLinks_Registry::NAME ),
            $_sMessage
        );
        if ( ! $_bSent ) {
            throw new Exception( 'Failed to update a user meta. User ID: ' . $this->___iUserID );
        }
        return $_bSent;

    }
        /**
         * @return integer
         * @since  4.7.3
         */
        private function ___getUsageDuration() {
            $_oOption     = AmazonAutoLinks_Option::getInstance();
            $_iFirstSaved = ( integer ) $_oOption->get( array( 'user_actions', 'first_saved' ), 0 );
            return ( integer ) round( ( time() - $_iFirstSaved ) / 86400 , 0 ); // days
        }

    /**
     * @param  string $sContentType
     * @return string
     * @since  4.7.3
     */
    public function replyToSetMailContentTypeToHTML( $sContentType ) {
        return 'text/html';
    }

    /**
     * @return string
     * @sicne  4.7.3
     */
    public function replyToSetEmailSenderAddress( $sEmailAddress ) {
        if ( ! $this->___bAnonymous ) {
            return $this->___oUser->user_email;
        }
        $_sHost = parse_url( site_url(), PHP_URL_HOST );
        $_sHost = 'localhost' === $_sHost
            ? '127.0.0.1'
            : $_sHost;
        $_sHost = false === strpos( $_sHost, '.' )
            ? $_sHost
            : gethostbyname( $_sHost );
        return 'wordpress@' . $_sHost;
    }

    /**
     * @return string
     * @sicne  4.7.3
     */
    public function replyToSetEmailSenderName( $sSenderName ) {
        if ( $this->___bAnonymous ) {
            return 'WordPress';
        }
        return $this->___oUser->first_name && $this->___oUser->last_name
            ? $this->___oUser->first_name . ' ' . $this->___oUser->last_name
            : $this->___oUser->display_name;
    }

}