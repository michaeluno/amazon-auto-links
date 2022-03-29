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
 * Adds an in-page tab to a setting page.
 * 
 * @since       3.9.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 * @since       4.3.0   Renamed from `AmazonAutoLinks_ToolAdminPage_Tool_ErrorLog`.
 */
class AmazonAutoLinks_Log_Error_AdminPage_Tab_ErrorLog extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return  array
     * @since   3.9.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'error_log',
            'title'     => __( 'Error Log', 'amazon-auto-links' ),
            'order'     => 5,
            'style'     => array(
                AmazonAutoLinks_Registry::$sDirPath . '/asset/js/utility.css',
                AmazonAutoLinks_Log_Loader::$sDirPath . '/asset/css/log.css',
            ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
     */
    protected function _loadTab( $oAdminPage ) {
        
        // Form sections
        new AmazonAutoLinks_Log_Error_AdminPage_Section_ErrorLog_Log( $oAdminPage, $this->sPageSlug, array( 'tab_slug' => $this->sTabSlug, ) );

        $this->_enqueueResources( $oAdminPage );

    }
        /**
         * @param AmazonAutoLinks_AdminPageFramework $oAdminPage
         * @since   4.3.0
         */
        protected function _enqueueResources( $oAdminPage ) {

            wp_enqueue_script( 'jquery' );

            $oAdminPage->enqueueScript(
                $oAdminPage->oUtil->isDebugMode()
                    ? AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/utility.js'
                    : AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/js/utility.min.js',
                $this->sPageSlug,
                $this->sTabSlug,
                array(
                    'handle_id'     => 'aalUtility',
                    'dependencies'  => array( 'jquery', ),
                    'in_footer'     => true,
                )
            );
            $oAdminPage->enqueueScript(
                $oAdminPage->oUtil->isDebugMode()
                    ? AmazonAutoLinks_Log_Loader::$sDirPath . '/asset/js/log-item-filters.js'
                    : AmazonAutoLinks_Log_Loader::$sDirPath . '/asset/js/log-item-filters.min.js',
                $this->sPageSlug,
                $this->sTabSlug,
                array(
                    'handle_id'     => 'aalLog',
                    'dependencies'  => array( 'jquery', 'jquery-ui-accordion', 'aalUtility' ),
                    'translation'   => array(
                        'debugMode' => $oAdminPage->oUtil->isDebugMode(),
                        'pluginName' => AmazonAutoLinks_Registry::NAME,
                        'labels'     => array(
                            'copied' => __( 'Copied the log to the clipboard.' ),
                            'not_copied' => __( 'Failed to copied the log to the clipboard.' ),
                        ),
                    ),
                    'in_footer'     => true,
                )
            );
        }

}
