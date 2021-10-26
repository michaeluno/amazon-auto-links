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
 * Adds the 'Associates' admin tab.
 * 
 * @since       4.5.0
 */
class AmazonAutoLinks_Main_AdminPage_Tab_Associates extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since  4.5.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'associates',
            'title'     => __( 'Associates', 'amazon-auto-links' ),
            'order'     => 4,   // should be the default
            'style'     => AmazonAutoLinks_SettingsAdminPageLoader::$sDirPath . '/asset/css/associates.css',
        );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _loadTab( $oAdminPage ) {

        new AmazonAutoLinks_Select2CustomFieldType( $oAdminPage->oProp->sClassName );

        // Sections
        new AmazonAutoLinks_Main_AdminPage_Section_Associates(
            $oAdminPage,
            $this->sPageSlug,
            array(
                'tab_slug'      => $this->sTabSlug,
            )
        );
        
        $this->___loadResources( $oAdminPage );
        
    }

        private function ___loadResources( $oAdminPage ) {

            wp_enqueue_script( 'jquery' );
            $_sPageSlug = $oAdminPage->oProp->getCurrentPageSlug();
            $oAdminPage->enqueueScript(
                $oAdminPage->oUtil->isDebugMode()
                    ? AmazonAutoLinks_SettingsAdminPageLoader::$sDirPath . '/asset/js/paapi-check.js'
                    : AmazonAutoLinks_SettingsAdminPageLoader::$sDirPath . '/asset/js/paapi-check.min.js',
                $_sPageSlug,
                $oAdminPage->oProp->getCurrentTabSlug( $_sPageSlug ),
                array(
                    'handle_id'     => 'aalPAAPICheck',
                    'dependencies'  => array( 'jquery' ),
                    'translation'   => array(
                        'ajaxURL'           => admin_url( 'admin-ajax.php' ),
                        'actionHookSuffix'  => 'aal_action_ajax_paapi_check',
                        'nonce'             => wp_create_nonce( 'aal_action_ajax_paapi_check' ),
                        'spinnerURL'        => admin_url( 'images/loading.gif' ),
                        'pluginName'        => AmazonAutoLinks_Registry::NAME,
                        'scriptName'        => 'PA-API Check',
                        'debugMode'         => AmazonAutoLinks_Option::getInstance()->isDebug( 'js' ),
                        'label'             => array(
                            'keyLengthAccessKey'  => __( 'The Access key length should be 20.', 'amazon-auto-links' ),
                            'keyLengthSecretKey'  => __( 'The Secret key length should be 40.', 'amazon-auto-links' ),
                            'required'          => __( 'required', 'amazon-auto-links' ),
                            'optionNotSet'      => __( 'The required options are not set.', 'amazon-auto-links' ),
                            'associateID'       => __( 'Associate ID', 'amazon-auto-links' ),
                            'accessKey'         => __( 'Access Key', 'amazon-auto-links' ),
                            'secretKey'         => __( 'Secret Key', 'amazon-auto-links' ),
                            'attr'              => array(
                                'checking' => esc_attr( __( 'Checking...', 'amazon-auto-links' ) ),
                            ),
                        ),
                    ),
                    'in_footer' => true,
                )
            );

        }    
}