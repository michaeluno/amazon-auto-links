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
class AmazonAutoLinks_Opt_Event_Action_AdminFooter {

    /**
     * Sets up hooks.
     * @since 4.7.0
     */
    public function __construct() {

        if ( ! is_admin() ) {
            return;
        }

        add_action( 'load_' . 'AmazonAutoLinks_AdminPage', array( $this, 'replyToLoadAdminPages' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_Unit', array( $this, 'replyToLoadPostType' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_Button', array( $this, 'replyToLoadPostType' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_AutoInsert', array( $this, 'replyToLoadPostType' ) );

    }

    public function replyToLoadPostType( $oPostType ) {
        if ( ! $oPostType->isInThePage() ) {
            return;
        }
        if ( ! $this->___shouldProceed() ) {
            return;
        }
        add_filter( 'footer_left_' . $oPostType->oProp->sClassName, array( $this, 'replyToModifyFooterLeft' ) );
        $this->___enqueuePageResources( $oPostType );
    }
    
    public function replyToLoadAdminPages( $oFactory ) {
        if ( ! $this->___shouldProceed() ) {
            return;
        }
        add_filter( 'footer_left_' . $oFactory->oProp->sClassName, array( $this, 'replyToModifyFooterLeft' ) );
        $this->___enqueuePageResources( $oFactory );
    }

        /**
         * @return boolean
         * @since  4.7.3
         */
        private function ___shouldProceed() {
            return ( boolean ) get_user_meta( get_current_user_id(), 'aal_first_saved', true );
        }

        /**
         * @since 4.6.6
         * @since 4.7.0 Moved from `AmazonAutoLinks_AdminPage`.
         * @since 4.7.3 Moved from `AmazonAutoLinks_Opt_Out_Setting`.
         */
        private function ___enqueuePageResources( $oFactory ) {
            $_sMin = $oFactory->oUtil->isDebugMode() ? '' : '.min';
            wp_enqueue_script(
                'aal-rating-prompt',
                $oFactory->oUtil->getResolvedSRC( AmazonAutoLinks_Opt_Loader::$sDirPath . "/asset/js/rating-prompt{$_sMin}.js" ), array( 'jquery' ),
                false,
                true
            );
            $_sActionHookSuffix = 'aal_action_rating_prompt';
            $_aData = array(
                'actionHookSuffix' => $_sActionHookSuffix,
                'nonce'            => wp_create_nonce( $_sActionHookSuffix ),
                'ajaxURL'          => admin_url( 'admin-ajax.php' ),
            );
            wp_localize_script( 'aal-rating-prompt', 'aalRatingPrompt', $_aData );
        }

    /**
     * @param  string $sFooterHTML
     * @return string
     * @since  4.6.6
     * @since  4.7.0 Moved from `AmazonAutoLinks_AdminPage`.
     * @since  4.7.3 Moved from `AmazonAutoLinks_Opt_Out_Setting`.
     */
    public function replyToModifyFooterLeft( $sFooterHTML ) {

        $_iUserID = get_current_user_id();
        $_iTimeFirstSaved = get_user_meta( $_iUserID, 'aal_first_saved', true );
        if ( ! $_iTimeFirstSaved ) {
            return $sFooterHTML;
        }
        if ( time() < $_iTimeFirstSaved + ( 86400 * 30 ) ) { // 30 days past
            return $sFooterHTML;
        }
        if ( get_user_meta( $_iUserID, 'aal_rated', true ) ) {
            return "<span class='aal-thanks-for-rating'>"
                   . sprintf( __( 'Thanks for <a href="%1$s" target="_blank">rating</a>', 'amazon-auto-links' ), 'https://wordpress.org/support/plugin/amazon-auto-links/reviews/' )
                . "</span>"
                . $sFooterHTML;
        }
        return "<span class='aal-rating-prompt'>"
            . "<span class='aal-have-you-rated'>"
                . "<span class='icon-info dashicons dashicons-editor-help'></span>"
                . __( 'Is the plugin useful?', 'amazon-auto-links' )
            . "</span>"
            . "<span class='aal-have-you-rated-answer'>"
                . "<span>"
                    . AmazonAutoLinks_Opt_Message::getGiveThePlugin5Stars()
                . "</span>"
            . "</span>"
            . "<span id='aal-rating-prompt-dismissal' class='aal-have-you-rated-dismiss'>"
               . "<a href='#aal-rating-prompt-dismissal' data-rated='" . time() . "'>"
                   . "<span class='icon-dismiss dashicons dashicons-dismiss'></span>"
                   . __( 'Dismiss', 'amazon-auto-links' )
               . "</a>"
            . "</span>"
        . "</span>" // .aal-rating-prompt
        . "<span class='aal-footer-left-original'>"
           . "<span class='aal-thanks-for-rating'>"
                . sprintf(
                    __( 'Thanks for <a href="%1$s" target="_blank">rating</a> %2$s', 'amazon-auto-links' ),
                    'https://wordpress.org/support/plugin/amazon-auto-links/reviews/',
                    ''  // the plugin name follows this output so leave it empty.
               )
           .  "</span>"
           . $sFooterHTML
        . "</span>";

    }

}