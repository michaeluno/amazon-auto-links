<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Adds an in-page tab to an admin page.
 * 
 * @since       3.10.0
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_HelpAdminPage_Help_About extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        return array(
            'tab_slug'  => 'about',
            'title'     => __( 'About', 'amazon-auto-links' ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     *
     * @param     AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @callback  add_action()      load_{page slug}_{tab slug}
     */
    protected function _loadTab( $oAdminPage ) {

        $_oReflection       = new ReflectionClass('AmazonAutoLinks_Registry');//
        $_oProductTable     = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_oHTTPRequestTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_oTaskTable        = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $oAdminPage->addSettingFields(
            '_default', // the target section id
            array(
                'title'     => __( 'Plugin Information', 'amazon-auto-links' ),
                'field_id'  => 'basic_info',
                'type'      => 'system',
                'data'      => array(
                    'Current Time' => '', 'Admin Page Framework' => '', 'WordPress' => '',
                    'PHP' => '', 'Server' => '', 'PHP Error Log' => '',
                    'MySQL' => '', 'MySQL Error Log' => '', 'Browser' => '',
                ) + $_oReflection->getConstants(),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'Database Tables', 'amazon-auto-links' ),
                'field_id'  => 'database_tables',
                'type'      => 'system',
                'data'      => array(
                    'Current Time' => '', 'Admin Page Framework' => '', 'WordPress'     => '',
                    'PHP'          => '', 'Server'               => '', 'PHP Error Log' => '',
                    'MySQL'        => '', 'MySQL Error Log'      => '', 'Browser'       => '',
                    'aal_products'      => $_oProductTable->getTableInformation(),
                    'aal_request_cache' => $_oHTTPRequestTable->getTableInformation(),
                    'aal_tasks'         => $_oTaskTable->getTableInformation(),
                ),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'General Options', 'amazon-auto-links' ),
                'field_id'  => 'plugin_options',
                'type'      => 'system',
                'data'      => array(
                                   'Current Time' => '', 'Admin Page Framework' => '', 'WordPress' => '',
                                   'PHP' => '', 'Server' => '', 'PHP Error Log' => '',
                                   'MySQL' => '', 'MySQL Error Log' => '', 'Browser' => '',
                ) + $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], array() ) ),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'Tools Options', 'amazon-auto-links' ),
                'field_id'  => 'tools_options',
                'type'      => 'system',
                'data'      => array(
                                   'Current Time' => '', 'Admin Page Framework' => '', 'WordPress' => '',
                                   'PHP' => '', 'Server' => '', 'PHP Error Log' => '',
                                   'MySQL' => '', 'MySQL Error Log' => '', 'Browser' => '',
                ) + $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ], array() ) ),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'Template Options', 'amazon-auto-links' ),
                'field_id'  => 'template_options',
                'type'      => 'system',
                'data'      => array(
                                   'Current Time' => '', 'Admin Page Framework' => '', 'WordPress' => '',
                                   'PHP' => '', 'Server' => '', 'PHP Error Log' => '',
                                   'MySQL' => '', 'MySQL Error Log' => '', 'Browser' => '',
                ) + $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ], array() ) ),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'File Permissions', 'amazon-auto-links' ),
                'field_id'  => 'file_permissins',
                'type'      => 'system',
                'data'      => array(
                    'Current Time' => '', 'Admin Page Framework' => '', 'WordPress'     => '',
                    'PHP'          => '', 'Server'               => '', 'PHP Error Log' => '',
                    'MySQL'        => '', 'MySQL Error Log'      => '', 'Browser'       => '',
                ) + $this->___getFilePermissionInformation(),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),            
            array(
                'title'     => __( 'Server Information', 'amazon-auto-links' ),
                'field_id'  => 'server_information',
                'type'      => 'system',
                'data'      => array(
//                                   'Current Time' => '', 'Admin Page Framework' => '', 'WordPress' => '',
//                                   'PHP' => '', 'Server' => '', 'PHP Error Log' => '',
//                                   'MySQL' => '', 'MySQL Error Log' => '', 'Browser' => '',
                ),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array()
        );
    }
        /**
         * @return array 
         */
        private function ___getFilePermissionInformation() {
            $_sSystemTempDirPath     = wp_normalize_path( sys_get_temp_dir() );
            $_sPluginSiteTempDirPath = AmazonAutoLinks_Registry::getPluginSiteTempDirPath();
            $this->getDirectoryCreated( $_sPluginSiteTempDirPath );
            return array(
                'System Temporary Directory'      =>  $this->___getDirectoryPermissionInformation( $_sSystemTempDirPath ),
                'Plugin Temporary Directory'      => $this->___getDirectoryPermissionInformation( dirname( $_sPluginSiteTempDirPath ) ),
                'Plugin Site Temporary Directory' => $this->___getDirectoryPermissionInformation( $_sPluginSiteTempDirPath ),
            );
        }
            /**
             * @param  string $sDirPath
             * @return array
             * @since  4.3.8
             */
            private function ___getDirectoryPermissionInformation( $sDirPath ) {
                return array(
                    'path'     => $sDirPath,
                    'exist'    => file_exists( $sDirPath ) ? 'Yes' : 'No',
                    'writable' => is_writable( $sDirPath ) ? 'Yes' : 'No',
                    'chmod'    => $this->getReadableCHMOD( $sDirPath ),
                );
            }
    /**
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action() do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {
        parent::_doTab( $oFactory );
    }
            
}