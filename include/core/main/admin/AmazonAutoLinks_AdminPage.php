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

        // [4.6.7] Allows components to set setting notices
        add_action( 'aal_action_set_admin_setting_notice', array( $this, 'replyToSetSettingNotice' ), 10, 2 );

    }
        
    public function load() {

        $this->___doPageSettings();
        $this->___setAdminNoticePluginWarning();

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
     * @param  array $aInputs
     * @param  array $aOldInputs
     * @param  AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @param  array $aSubmitInfo
     * @return array
     * @since  4.6.6
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        if ( ! isset( $aInputs[ 'user_actions' ] ) ) {
            $aInputs[ 'user_actions' ] = array();
        }
        $aInputs[ 'user_actions' ][ 'last_saved' ] = time();
        if ( empty( $aInputs[ 'user_actions' ][ 'first_saved' ] ) ) {
            $aInputs[ 'user_actions' ][ 'first_saved' ] = time();
        }
        $_iUserID = get_current_user_id();
        $_iTime   = get_user_meta( $_iUserID, 'aal_first_saved', true );
        if ( ! $_iTime ) {
            update_user_meta( $_iUserID, 'aal_first_saved', time() );
        }
        return $aInputs;
    }

    /**
     * @param string $sMessage
     * @param string $sType
     * @since 4.6.7
     */
    public function replyToSetSettingNotice( $sMessage, $sType='error' ) {
        $this->setSettingNotice( $sMessage, $sType );
    }

}