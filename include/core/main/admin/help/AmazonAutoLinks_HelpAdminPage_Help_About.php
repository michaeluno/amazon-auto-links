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
 * Adds an in-page tab of `About` to the `Help` an admin page.
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
            'capability' => 'manage_options',
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
                'field_id'  => 'file_permissions',
                'type'      => 'system',
                'data'      => array(
                    'Current Time' => '', 'Admin Page Framework' => '', 'WordPress'     => '',
                    'PHP'          => '', 'Server'               => '', 'PHP Error Log' => '',
                    'MySQL'        => '', 'MySQL Error Log'      => '', 'Browser'       => '',
                    'System' => array(
                        'umask'   => $this->getPaddedOctal( umask() ),
                        'user'    => get_current_user(),
                        'user_id' => $this->___getSystemCurrentUserID(),
                    ),
                ) + $this->___getFilePermissionInformation(),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'Paths', 'amazon-auto-links' ) . ' & ' . __( 'URLs', 'amazon-auto-links' ),
                'field_id'  => 'paths',
                'type'      => 'system',
                'data'      => array(
                    'Current Time'   => '', 'Admin Page Framework' => '', 'WordPress'     => '',
                    'PHP'            => '', 'Server'               => '', 'PHP Error Log' => '',
                    'MySQL'          => '', 'MySQL Error Log'      => '', 'Browser'       => '',
                    'Paths' => array(
                        'ABSPATH'               => ABSPATH,
                        'WP_CONTENT_DIR'        => WP_CONTENT_DIR,
                        'get_home_path()'       => get_home_path(),
                        'wp_upload_dir()'       => wp_upload_dir(),
                        'plugin_dir_path( __FILE__ )' => plugin_dir_path( AmazonAutoLinks_Registry::$sFilePath ),
                        'Exists'                => array(
                            "ABSPATH . 'wp-admin'" => file_exists( ABSPATH . 'wp-admin' ) ? 'Yes' : 'No',
                            "ABSPATH . 'wp-admin/includes/upgrade.php'"             => file_exists( ABSPATH . 'wp-admin/includes/upgrade.php' ) ? 'Yes' : 'No',
                            "ABSPATH . 'wp-admin/includes/plugin.php'"              => file_exists( ABSPATH . 'wp-admin/includes/plugin.php' ) ? 'Yes' : 'No',
                            "ABSPATH . 'wp-admin/includes/class-wp-list-table.php'" => file_exists( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ? 'Yes' : 'No',
                            "WP_CONTENT_DIR" => file_exists( WP_CONTENT_DIR ) ? 'Yes' : 'No',
                        ),
                    ),
                    'URLs' => array(
                        'WP_CONTENT_URL'  => WP_CONTENT_URL,
                        'home_url()'      => home_url(),
                        'content_url()'   => content_url(),
                        'admin_url()'     => admin_url(),
                        'site_url()'      => site_url(),
                        'includes_url()'  => includes_url(),
                        'plugins_url()'   => plugins_url(),
                        'WP_Style::base_url' => (new WP_Styles())->base_url,
                        "plugins_url( 'asset/image/menu_icon_16x16.png', __FILE__ )" => plugins_url( 'asset/image/menu_icon_16x16.png', AmazonAutoLinks_Registry::$sFilePath ),
                        'plugin_dir_url( __FILE__ )'  => plugin_dir_url( AmazonAutoLinks_Registry::$sFilePath ),
                        'plugin_basename( __FILE__ )' => plugin_basename( AmazonAutoLinks_Registry::$sFilePath ),
                        'Exists'          => array(
                            "plugins_url( 'asset/image/menu_icon_16x16.png', __FILE__ )" => $this->___doesURLExist( plugins_url( 'asset/image/menu_icon_16x16.png', AmazonAutoLinks_Registry::$sFilePath ) ) ? 'Yes' : 'No',
                            "getSRCFromPath( {plugin dir path} . '/template/category/screenshot.jpg' )" => $this->___doesURLExist( $this->getSRCFromPath( AmazonAutoLinks_Registry::$sDirPath . '/template/category/screenshot.jpg' ) ) ? 'Yes' : 'No',
                        ),
                    ),
                    'Default Template ID'   => AmazonAutoLinks_TemplateOption::getInstance()->getTemplateID( AmazonAutoLinks_Registry::$sDirPath . '/template/list' ),
                ),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            // array(
            //     'title'     => __( 'Server Information', 'amazon-auto-links' ),
            //     'field_id'  => 'server_information',
            //     'type'      => 'system',
            //     'data'      => array(
            //           // 'Current Time' => '', 'Admin Page Framework' => '', 'WordPress' => '',
            //           // 'PHP' => '', 'Server' => '', 'PHP Error Log' => '',
            //           // 'MySQL' => '', 'MySQL Error Log' => '', 'Browser' => '',
            //     ),
            //     'attributes' => array(
            //         'style' => 'height: 300px;',
            //     ),
            // ),
            array()
        );
    }
        /**
         * @param  string $sURL
         * @return boolean
         * @since  4.6.15
         */
        private function ___doesURLExist( $sURL ) {
            $file_headers = @get_headers( $sURL );
            if( ! $file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' ) {
                return false;
            }
            return true;
        }
        /**
         * @return int|string
         * @since  4.3.8
         */
        private function ___getSystemCurrentUserID() {
            $_sSystemTempDirPath = wp_normalize_path( untrailingslashit( sys_get_temp_dir() ) );
            if ( ! is_writable( $_sSystemTempDirPath ) ) {
                return 'n/a';
            }
            $_sFilePath = $_sSystemTempDirPath . '/' . uniqid() . '.txt';
            file_put_contents( $_sFilePath, time() );
            $_biUserID = fileowner($_sFilePath );
            unlink( $_sFilePath );
            return false === $_biUserID
                ? 'n/a'
                : $_biUserID;

        }
        /**
         * @return array
         * @since  4.3.8
         */
        private function ___getFilePermissionInformation() {
            $_sSystemTempDirPath     = wp_normalize_path( sys_get_temp_dir() );
            $_sPluginSiteTempDirPath = AmazonAutoLinks_Registry::getPluginSiteTempDirPath();
            $this->getDirectoryCreatedRecursive( $_sPluginSiteTempDirPath, $_sSystemTempDirPath, 0755, true );
            return array(
                'System Temporary Directory'      => $this->___getDirectoryPermissionInformation( $_sSystemTempDirPath ),
                'Plugin Site Temporary Directory' => $this->___getDirectoryPermissionInformation( $_sPluginSiteTempDirPath ),
                'Plugin Site Temporary Test Directory' => $this->___getTestDirectoryPermissionInformation( $_sPluginSiteTempDirPath ),
                'wp-content'                      => $this->___getDirectoryPermissionInformation( WP_CONTENT_DIR ),
                'wp-content Test Directory'       => $this->___getTestDirectoryPermissionInformation( WP_CONTENT_DIR ),
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
                    'owner_id' => fileowner( $sDirPath ),
                    'owner'    => function_exists( 'posix_getpwuid' )
                        ? posix_getpwuid( fileowner( $sDirPath ) )
                        : 'n/a',
                );
            }
            /**
             * Creates a test dir and remove it to see whether this is possible.
             * @param  string $sDirPath
             * @return array
             * @since  4.3.8
             */
            private function ___getTestDirectoryPermissionInformation( $sDirPath ) {
                $_sTestDirPath = $sDirPath . '/test';
                $this->getDirectoryCreated( $_sTestDirPath, 0755 );
                $_aInformation = $this->___getDirectoryPermissionInformation( $_sTestDirPath );
                $_aInformation[ 'deleted' ] = rmdir( $_sTestDirPath );
                return $_aInformation;
            }

    /**
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action() do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {
        parent::_doTab( $oFactory );
    }
            
}