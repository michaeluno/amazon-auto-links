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
 * Inserts messages in the admin footer
 *
 * @since        4.7.3
 */
class AmazonAutoLinks_Opt_Event_Action_SurveyPluginDeactivation extends AmazonAutoLinks_WPUtility {

    /**
     * Sets up hooks.
     * @since 4.7.0
     */
    public function __construct() {

        if ( ! is_admin() ) {
            return;
        }
        add_action( 'set_up_AmazonAutoLinks_AdminPage', array( $this, 'replyToSetUpAdminPages' ) );

    }

    /**
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @since 4.7.3
     */
    public function replyToSetUpAdminPages( $oFactory ) {
        if ( 'plugins.php' !== $oFactory->oProp->sPageNow ) {
            return;
        }
        if ( ! $this->___shouldProceed() ) {
            return;
        }
        add_action( 'admin_enqueue_scripts', array( $this, 'replyToEnqueueScripts' ) );
        add_action( 'admin_footer', array( $this, 'replyToDoInAdminFooter' ) );
    }
        /**
         * @return boolean
         * @since  4.7.3
         */
        private function ___shouldProceed() {
            $_iUserID = get_current_user_id();
            if ( ! ( boolean ) get_user_meta( $_iUserID, 'aal_surveys', true ) ) {
                return false;
            }
            return true;
        }

    public function replyToEnqueueScripts() {
        $_sMin = $this->isDebugMode() ? '' : '.min';
        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_style(
            'aal-survey-tooltip',
            AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Opt_Loader::$sDirPath . "/asset/css/tooltip-survey{$_sMin}.css", true )
        );
        wp_enqueue_script( 'wp-pointer' );
        wp_enqueue_script(
            'aal-pointer',
            AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/js/pointer-tooltip{$_sMin}.js", true ),
            array( 'jquery', 'wp-pointer', ),
            false,
            true
        );
        wp_enqueue_script(
            'aal-survey-tooltip',
            AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Opt_Loader::$sDirPath . "/asset/js/tooltip-survey{$_sMin}.js", true ),
            array( 'jquery', 'aal-pointer' ),
            false,
            true
        );
        wp_enqueue_script(
            'aal-opt-survey-plugin-deactivation',
            AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Opt_Loader::$sDirPath . "/asset/js/opt-survey-plugin-deactivation{$_sMin}.js", true ),
            array( 'jquery', 'aal-pointer', 'aal-survey-tooltip' ),
            false,
            true
        );
        $_aData = array(
            'pluginName'        => AmazonAutoLinks_Registry::NAME,
            'debugMode'         => AmazonAutoLinks_Option::getInstance()->isDebug( 'js' ),
            'ajaxURL'           => admin_url( 'admin-ajax.php' ),
            'actionHookSuffix'  => 'aal_action_opt_survey_plugin_deactivation',
            'nonce'             => wp_create_nonce( 'aal_action_opt_survey_plugin_deactivation' ),
            'spinnerURL'        => admin_url( 'images/loading.gif' ),
            'label'             => array(
                'title'   => __( 'Quick Feedback', 'amazon-auto-links' ),
                'loading' => __( 'Loading...', 'amazon-auto-links' ),
            ),
        );
        wp_localize_script( 'aal-opt-survey-plugin-deactivation', 'aalSurveyPluginDeactivation',  $_aData );
    }

    public function replyToDoInAdminFooter() {
        $_sFields = '';
        foreach( $this->___getFields() as $_aField ) {
            $_aField  = $_aField + array( 'label' => '', 'value' => '', 'type' => 'radio,' );
            $_sInput  = "<input type='radio' name='plugin_deactivation[reason]' value='" . esc_attr( $_aField[ 'value' ] ) . "' />";
            $_sInput .= $_aField[ 'label' ];
            $_sInput  = "<label>" . $_sInput . "</label>";
            if ( ! empty( $_aField[ 'sub' ] ) ) {
                $_aSub      = $_aField[ 'sub' ] + array( 'type' => '', 'placeholder' => '', 'label' => '', 'required' => false, );
                $_sName     = "plugin_deactivation[" . $_aField[ 'value' ] . "]";
                $_sSub      = $_aSub[ 'label' ]
                    ? "<strong>" . $_aSub[ 'label' ] . "</strong>"
                    : "";
                $_sRequired = $_aSub[ 'required' ] ? " data-required='required'" : '';
                $_sSub     .= 'text' === $_aSub[ 'type' ]
                    ? "<input type='text' name='" . esc_attr( $_sName ) . "' placeholder='" . esc_attr( $_aSub[ 'placeholder' ] ) . "' {$_sRequired}/>"
                    : "<textarea name='" . esc_attr( $_sName ) . "' placeholder='" . esc_attr( $_aSub[ 'placeholder' ] ) . "' {$_sRequired}></textarea>";
                $_sInput   .= "<label class='sub-field'>" . $_sSub . "</label>";
            }
            $_sFields .= "<p>"
                    . $_sInput
                . "</p>";
        }
        echo "<div id='aal-survey-plugin-deactivation' style='display:none;'>"
                . "<form>"
                . "<h3>" . esc_html__( 'Quick Feedback', 'amazon-auto-links' ) . "</h3>"
                . "<h4>" . esc_html__( 'If you have a moment, please let us know why you are deactivating.', 'amazon-auto-links' ) . "</h4>"
                . "<div class='tooltip-body'>"
                    . $_sFields
                . "</div>"
                . "<div class='tooltip-footer'>"
                    . "<div class='left'>"
                        . "<label class='extra-field hidden'>"
                            . "<input type='checkbox' name='plugin_deactivation[anonymous]' value='1'>"
                            . esc_html__( 'Anonymous feedback', 'amazon-auto-links' )
                        . "</label>"
                    . "</div>"
                    . "<div class='right'>"
                        . "<button class='button button-secondary' data-action='deactivate'>" . __( 'Skip & Deactivate', 'amazon-auto-links' ) . "</button>"
                        . "<button class='button button-secondary hidden' data-action='submit'>" . __( 'Submit & Deactivate', 'amazon-auto-links' ) . "</button>"
                        . "<button class='button button-secondary' data-action='cancel'>" . __( 'Cancel', 'amazon-auto-links' ) . "</button>"
                    . "</div>"
                . "</div>"
                . "</form>"
            . "</div>";
    }
        /**
         * @return array[]
         * @since  4.7.3
         */
        private function ___getFields() {
            return array(
                array(
                    'label' => esc_html__( 'The plugin is not working.', 'amazon-auto-links' ),
                    'value' => 'not_working',
                    'sub'   => array(
                        'type'        => 'textarea',
                        'placeholder' => __( 'Kindly share what didn\'t work so we can fix it for future users...', 'amazon-aut-links' ),
                    ),
                ),
                array(
                    'label' => esc_html__( 'I found a better plugin.', 'amazon-auto-links' ),
                    'value' => 'found_better_plugin',
                    'sub'   => array(
                        'type'        => 'text',
                        'placeholder' => __( 'What\'s the plugin\'s name?', 'amazon-auto-links' ),
                    ),
                ),
                array(
                    'label' => esc_html__( 'The plugin is great. But I need a specific feature that is not supported.', 'amazon-auto-links' ),
                    'value' => 'missing_feature',
                    'sub'   => array(
                        'type'        => 'textarea',
                        'placeholder' => __( 'What feature?', 'amazon-auto-links' ),
                    ),
                ),
                array(
                    'label' => esc_html__( 'I couldn\'t understand how to make it work.', 'amazon-auto-links' ),
                    'value' => 'could_not_understand',
                ),
                array(
                    'label' => esc_html__( 'The plugin didn\'t work as expected.', 'amazon-auto-links' ),
                    'value' => 'not_as_expected',
                    'sub'   => array(
                        'type'        => 'textarea',
                        'placeholder' => __( 'What did you expect?', 'amazon-auto-links' ),
                    ),
                ),
                array(
                    'label' => esc_html__( 'It\'s not what I was looking for.', 'amazon-auto-links' ),
                    'value' => 'not_something_looking_for',
                    'sub'   => array(
                        'type'        => 'textarea',
                        'placeholder' => __( 'What you\'ve been looking for?', 'amazon-auto-links' ),
                    ),
                ),
                array(
                    'label' => esc_html__( 'It\'s a temporary deactivation. I\'m just debugging an issue.', 'amazon-auto-links' ),
                    'value' => 'temporary_deactivation',
                ),
                array(
                    'label' => esc_html__( 'Other.', 'amazon-auto-links' ),
                    'value' => 'other',
                    'sub'   => array(
                        'type'        => 'text',
                        'label'       => esc_html__( 'Kindly tell us the reason so we can improve.', 'amazon-auto-links' ),
                        'placeholder' => __( 'Kindly tell us the reason so we can improve.', 'amazon-auto-links' ),
                        'required'    => true,
                    ),
                ),
            );
        }

}