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
 * Provides methods to retrieve site information.
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_SiteInformation extends AmazonAutoLinks_Utility {

    /**
     * @since 4.7.0
     * @param boolean $bExtra Whether to include extra data.
     */
    static public function get( $bExtra=true ) {
        return array(
            'Site'            => self::getSite(),
            'Plugin'          => self::getPlugin(),
            'File Permission' => self::getFilePermissions(),
            'Database Tables' => self::getCustomDatabaseTables(),
            'Paths & URLs'    => self::getPathsAndURLs( $bExtra ),    // no extra as sending a mail fails on Sakura
        );
    }

    static public function getSite() {
        return array(
            'URL' => site_url(),
            'Multisite' => is_multisite() ? 'Yes' : 'No',
        );
    }

    /**
     * Retrieves information regarding file permissions.
     * @return array
     * @sincc  4.7.0
     */
    static public function getFilePermissions() {
        return array(
            'System' => array(
                'umask'   => self::getPaddedOctal( umask() ),
                'user'    => get_current_user(),
                'user_id' => self::___getSystemCurrentUserID(),
            ),
        ) + self::___getFilePermissionInformation();
    }
        /**
         * @return int|string
         * @since  4.3.8
         * @since  4.7.0   Moved from `AmazonAutoLinks_HelpAdminPage_Help_About`.
         */
        static private function ___getSystemCurrentUserID() {
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
         * @since  4.7.0   Moved from `AmazonAutoLinks_HelpAdminPage_Help_About`.
         */
        static private function ___getFilePermissionInformation() {
            $_sSystemTempDirPath     = wp_normalize_path( sys_get_temp_dir() );
            $_sPluginSiteTempDirPath = AmazonAutoLinks_Registry::getPluginSiteTempDirPath();
            self::getDirectoryCreatedRecursive( $_sPluginSiteTempDirPath, $_sSystemTempDirPath, 0755, true );
            return array(
                'System Temporary Directory'      => self::___getDirectoryPermissionInformation( $_sSystemTempDirPath ),
                'Plugin Site Temporary Directory' => self::___getDirectoryPermissionInformation( $_sPluginSiteTempDirPath ),
                'Plugin Site Temporary Test Directory' => self::___getTestDirectoryPermissionInformation( $_sPluginSiteTempDirPath ),
                'wp-content'                      => self::___getDirectoryPermissionInformation( WP_CONTENT_DIR ),
                'wp-content Test Directory'       => self::___getTestDirectoryPermissionInformation( WP_CONTENT_DIR ),
            );
        }
            /**
             * @param  string $sDirPath
             * @return array
             * @since  4.3.8
             * @since  4.7.0   Moved from `AmazonAutoLinks_HelpAdminPage_Help_About`.
             */
            static private function ___getDirectoryPermissionInformation( $sDirPath ) {
                return array(
                    'path'     => $sDirPath,
                    'exist'    => file_exists( $sDirPath ) ? 'Yes' : 'No',
                    'writable' => is_writable( $sDirPath ) ? 'Yes' : 'No',
                    'chmod'    => self::getReadableCHMOD( $sDirPath ),
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
             * @since  4.7.0   Moved from `AmazonAutoLinks_HelpAdminPage_Help_About`.
             */
            static private function ___getTestDirectoryPermissionInformation( $sDirPath ) {
                $_sTestDirPath = $sDirPath . '/test';
                self::getDirectoryCreated( $_sTestDirPath, 0755 );
                $_aInformation = self::___getDirectoryPermissionInformation( $_sTestDirPath );
                $_aInformation[ 'deleted' ] = rmdir( $_sTestDirPath );
                return $_aInformation;
            }
        
    /**
     * Retrieves information regarding paths.
     * @return array
     * @sinec  4.7.0
     */
    static public function getPathsAndURLs( $bExtra=true ) {
        return array(
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
                "plugins_url( 'include/core/main/asset/image/menu_icon_16x16.png', __FILE__ )" => plugins_url( 'include/core/main/asset/image/menu_icon_16x16.png', AmazonAutoLinks_Registry::$sFilePath ),
                'plugin_dir_url( __FILE__ )'  => $bExtra ? plugin_dir_url( AmazonAutoLinks_Registry::$sFilePath ) : '(unset)',   // somehow sending an email fails with this included
                'plugin_basename( __FILE__ )' => plugin_basename( AmazonAutoLinks_Registry::$sFilePath ),
                'Exists'          => $bExtra ? array(
                    "plugins_url( 'include/core/main/asset/image/menu_icon_16x16.png', __FILE__ )" => self::doesURLExist( plugins_url( 'include/core/main/asset/image/menu_icon_16x16.png', AmazonAutoLinks_Registry::$sFilePath ) ) ? 'Yes' : 'No',
                    "getSRCFromPath( {plugin dir path} . '/template/category/screenshot.jpg' )" => self::doesURLExist( self::getSRCFromPath( AmazonAutoLinks_Registry::$sDirPath . '/template/category/screenshot.jpg' ) ) ? 'Yes' : 'No',
                ) : '(unset)',
            ),
        );
    }
    
    /**
     * Retrieves information regarding the plugin specific custom database tables.
     * @return array
     * @sinec  4.7.0
     */
    static public function getCustomDatabaseTables() {
        $_oProductTable     = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_oHTTPRequestTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_oTaskTable        = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        return array(
            'aal_products'      => $_oProductTable->getTableInformation(),
            'aal_request_cache' => $_oHTTPRequestTable->getTableInformation(),
            'aal_tasks'         => $_oTaskTable->getTableInformation(),
        );
    }

    /**
     * Retrieves plugin information.
     * @return array
     * @sinec  4.7.0
     */
    static public function getPlugin() {
        $_oReflection = new ReflectionClass('AmazonAutoLinks_Registry' );
        return array(
            'Basic Information'   => $_oReflection->getConstants(),
            'Installed Directory' => AmazonAutoLinks_Registry::$sDirPath,
            'Plugin URL'          => plugins_url( '', AmazonAutoLinks_Registry::$sFilePath ),
        );
    }

}