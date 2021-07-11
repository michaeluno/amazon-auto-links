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
 * Deals with the plugin admin pages.
 * 
 * @since 2.0.5
 */
class AmazonAutoLinks_AdminPage extends AmazonAutoLinks_AdminPageFramework {

    /**
     * User constructor.
     */
    public function start() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
        add_filter( 'options_' . $this->oProp->sClassName, array( $this, 'replyToSetOptions' ) );
        
    }
        /**
         * Sets the default option values for the setting form.
         * @param  array $aOptions
         * @return array       The options array.
         */
        public function replyToSetOptions( $aOptions ) {

            $_oOption    = AmazonAutoLinks_Option::getInstance();
            
            // [3.4.0] Merging recursively to cover newly added elements over new versions.
            return $this->oUtil->uniteArrays( $aOptions, $_oOption->aDefault );
            
        }
        
    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        $this->setRootMenuPageBySlug( 'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );

        if ( 'plugins.php' === $this->oProp->sPageNow ) {
            $this->addLinkToPluginDescription(
                "<a href='https://wordpress.org/support/plugin/amazon-auto-links' target='_blank'>" 
                        . __( 'Support', 'amazon-auto-links' ) 
                    . "</a>"
            );         
        }

        $_oOption = AmazonAutoLinks_Option::getInstance();
        $this->setCapability( $_oOption->get( array( 'capabilities', 'setting_page_capability' ), 'manage_options' ) );

        add_filter( 'footer_left_' . $this->oProp->sClassName, array( $this, 'replyToModifyFooterLeft' ) );


    }
        
    public function load() {

        $this->___doPageSettings();
        $this->___enqueuePageResources();
        $this->___setAdminNoticePluginWarning();

    }
        /**
         * @since 4.6.6
         */
        private function ___enqueuePageResources() {
            $_sMin = $this->oUtil->isDebugMode() ? '' : '.min';
            wp_enqueue_script( 'aal-rating-prompt', $this->oUtil->getResolvedSRC( AmazonAutoLinks_Main_Loader::$sDirPath . "/asset/js/rating-prompt{$_sMin}.js" ), array( 'jquery' ), false, true );
            $_sActionHookSuffix = 'aal_action_rating_prompt';
            $_aData = array(
                'actionHookSuffix' => $_sActionHookSuffix,
                'nonce'            => wp_create_nonce( $_sActionHookSuffix ),
                'ajaxURL'          => admin_url( 'admin-ajax.php' ),
            );
            wp_localize_script( 'aal-rating-prompt', 'aalRatingPrompt', $_aData );
        }
        /**
         * @since 4.6.6
         */
        private function ___setAdminNoticePluginWarning() {
            $_sPHPVersion = phpversion();
            if ( version_compare( $_sPHPVersion, '5.6.20', '>=' ) ) {
                return;
            }
            $_sMessage = sprintf( __( 'The plugin soon drops support for your PHP version %1$s. Please update PHP to the latest.' ), $_sPHPVersion );
            AmazonAutoLinks_Registry::setAdminNotice( $_sMessage );
        }

         /**
         * Do page styling.
         * @since    3
         * @since    4.4.0  Changed the visibility to private and renamed from `replyToDoPageSettings()`.
         * @return   void
         */
        private function ___doPageSettings() {

            $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
            $this->setInPageTabTag( 'h2' );                
            $this->enqueueStyle( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/css/admin.css' );
            $this->setDisallowedQueryKeys( array( 'aal-option-upgrade', 'bounce_url' ) );            
        
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
        $_oSVG   = new AmazonAutoLinks_SVGGenerator_RatingStar( true, __( 'Five stars', 'amazon-auto-links' ) );
        $_sStars = $_oSVG->get( 50 );
        return "<span class='aal-rating-prompt'>"
            . "<span class='aal-have-you-rated'>"
                . "<span class='icon-info dashicons dashicons-editor-help'></span>"
                . __( 'Is the plugin useful?', 'amazon-auto-links' )
            . "</span>"
            . "<span class='aal-have-you-rated-answer'>"
                . "<span>"
                    . sprintf(
                        __( 'Give the plugin <a href="%1$s" target="_blank">%2$s</a> to encourage the development!', 'amazon-auto-links' ),
                        'https://wordpress.org/support/plugin/amazon-auto-links/reviews/',
                        $_sStars
               )
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
                . sprintf( __( 'Thanks for <a href="%1$s" target="_blank">rating</a>', 'amazon-auto-links' ), 'https://wordpress.org/support/plugin/amazon-auto-links/reviews/' )
           .  "</span>"
           . $sFooterHTML
        . "</span>";

    }

    /**
     * @param  array $aInputs
     * @param  array $aOldInputs
     * @param  AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @param  array $aSubmitInfo
     * @return array
     * @since  4.6.6
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        if ( ! isset( $aInputs[ 'times' ] ) ) {
            $aInputs[ 'times' ] = array();
        }
        $aInputs[ 'times' ][ 'last_saved' ] = time();
        if ( empty( $aInputs[ 'times' ][ 'first_saved' ] ) ) {
            $aInputs[ 'times' ][ 'first_saved' ] = time();
        }
        $_iUserID = get_current_user_id();
        $_iTime   = get_user_meta( $_iUserID, 'aal_first_saved', true );
        if ( ! $_iTime ) {
            update_user_meta( $_iUserID, 'aal_first_saved', time() );
        }
        return $aInputs;
    }
          
}