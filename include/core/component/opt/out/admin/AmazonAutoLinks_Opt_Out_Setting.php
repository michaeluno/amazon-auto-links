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
 * Loads the admin pages of the `Opt` component.
 *
 * @package      Amazon Auto Links/Opt
 * @since        4.7.0
 */
class AmazonAutoLinks_Opt_Out_Setting {

    public $sPageSlug;
    public $sTabSlug;

    /**
     * Sets up hooks.
     * @since 4.7.0
     */
    public function __construct() {

        add_action( 'load_' . 'AmazonAutoLinks_AdminPage', array( $this, 'replyToLoadAdminPages' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_Unit', array( $this, 'replyToLoadPostType' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_Button', array( $this, 'replyToLoadPostType' ) );
        add_action( 'load_' . 'AmazonAutoLinks_PostType_AutoInsert', array( $this, 'replyToLoadPostType' ) );

        $this->sPageSlug = AmazonAutoLinks_Registry::$aAdminPages[ 'main' ];
        $this->sTabSlug  = 'opt';

    }

    public function replyToLoadPostType( $oPostType ) {
        if ( ! $oPostType->isInThePage() ) {
            return;
        }
        add_filter( 'footer_left_' . $oPostType->oProp->sClassName, array( $this, 'replyToModifyFooterLeft' ) );
        $this->___enqueuePageResources( $oPostType );
    }
    
    public function replyToLoadAdminPages( $oFactory ) {
        add_action( "load_{$this->sPageSlug}_{$this->sTabSlug}", array( $this, 'replyToLoadTab' ) );
        add_filter( 'footer_left_' . $oFactory->oProp->sClassName, array( $this, 'replyToModifyFooterLeft' ) );
        $this->___enqueuePageResources( $oFactory );
    }
        /**
         * @since 4.6.6
         * @since 4.7.0 Moved from `AmazonAutoLinks_AdminPage`.
         */
        private function ___enqueuePageResources( $oFactory ) {
            $_sMin = $oFactory->oUtil->isDebugMode() ? '' : '.min';
            wp_enqueue_script(
                'aal-rating-prompt',
                $oFactory->oUtil->getResolvedSRC( AmazonAutoLinks_Opt_Out_Loader::$sDirPath . "/asset/js/rating-prompt{$_sMin}.js" ), array( 'jquery' ),
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
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback    add_action()      load_{page slug}_{tab slug}
     * @since       4.7.0
     */
    public function replyToLoadTab( $oFactory ) {
        new AmazonAutoLinks_Opt_Out_Setting_Section_UserBase(
            $oFactory,
            $this->sPageSlug,   // page slug
            array( 'tab_slug' => $this->sTabSlug, )
        );
    }

    /**
     * @param  string $sFooterHTML
     * @return string
     * @since  4.6.6
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