<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Shows notices to the user.
 *
 * @since        4.7.3
 */
class AmazonAutoLinks_Opt_Event_Action_AdminNotices extends AmazonAutoLinks_Event_Action_AdminNotices_Base {

    /**
     * @since 4.7.3
     * @param AmazonAutoLinks_AdminPageFramework_Factory $oFactory
     */
    public function replyToDo( $oFactory ) {

        if ( $this->getHTTPQueryGET( 'tab' ) === 'opt' ) {
            return;
        }
        $_iUserID = get_current_user_id();
        $this->___askSurveys( $_iUserID );

    }
        /**
         * @param integer $iUserID
         */
        private function ___askSurveys( $iUserID ) {

            if ( ! $this->___canAskSurveyPermission( $iUserID ) ) {
                return;
            }
            $_sMessage = __( 'Hi!', 'amazon-auto-links' )
                . ' ' . __( 'The plugin needs to improve.', 'amazon-auto-links' )
                . ' ' . __( 'Is it okay to ask you questions about plugin improvements from time to time?', 'amazon-auto-links' )
                . "<label>"
                    . "<button class='button button-secondary button-small button-opt-survey-permission' style='margin: 0 0.4em 0 0.8em;' data-answer='1'>"
                        . __( 'Yes', 'amazon-auto-links' )
                    . "</button>"
                    . "<button class='button button-secondary button-small button-opt-survey-permission' style='margin: 0 0.2em;' data-answer='0'>"
                        . __( 'No', 'amazon-auto-links' )
                    . "</button>"
                . "</label>";
            AmazonAutoLinks_Registry::setAdminNotice(
                $_sMessage,
                'bell',
                'bell'
            );
            $this->___enqueueScript();
        }
            /**
             * @since  4.7.3
             * @return boolean
             */
            private function ___canAskSurveyPermission( $iUserID ) {
                $_iTimeFirstSaved = get_user_meta( $iUserID, 'aal_first_saved', true );
                if ( ! $_iTimeFirstSaved ) {
                    return false;
                }
                if ( time() < $_iTimeFirstSaved + ( 86400 * 7 ) ) { // 7 days past
                    return false;
                }
                if ( get_user_meta( $iUserID, 'aal_never_ask_surveys', true ) ) {
                    return false;
                }
                if ( get_user_meta( $iUserID, 'aal_surveys', true ) ) {
                    return false;
                }
                return true;
            }

        private function ___enqueueScript() {
            $_sMin = $this->isDebugMode() ? '' : '.min';
            wp_enqueue_script(
                'aal-opt-ask-survey-permission',
                AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Opt_Loader::$sDirPath . "/asset/js/opt-survey-permission{$_sMin}.js", true ),
                array( 'jquery' ),
                false,
                true
            );
            $_sActionHookSuffix = 'aal_action_opt_survey_permission';
            $_aData = array(
                'actionHookSuffix' => $_sActionHookSuffix,
                'nonce'            => wp_create_nonce( $_sActionHookSuffix ),
                'ajaxURL'          => admin_url( 'admin-ajax.php' ),
                'spinnerURL'       => admin_url( 'images/loading.gif' ),
                'pluginName'       => AmazonAutoLinks_Registry::NAME,
                'debugMode'        => AmazonAutoLinks_Option::getInstance()->isDebug( 'js' ),
            );
            wp_localize_script( 'aal-opt-ask-survey-permission', 'aalOptSurveyPermission', $_aData );
        }

}