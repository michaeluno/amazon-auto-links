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

        $_aDisabled = array(
            'Current Time' => '', 'Admin Page Framework' => '', 'WordPress'     => '',
            'PHP'          => '', 'Server'               => '', 'PHP Error Log' => '',
            'MySQL'        => '', 'MySQL Error Log'      => '', 'Browser'       => '', );
        $oAdminPage->addSettingFields(
            '_default', // the target section id
            array(
                'title'      => __( 'Plugin Information', 'amazon-auto-links' ),
                'field_id'   => 'basic_info',
                'type'       => 'system',
                'data'       => AmazonAutoLinks_SiteInformation::getPlugin() + $_aDisabled,
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'      => __( 'Database Tables', 'amazon-auto-links' ),
                'field_id'   => 'database_tables',
                'type'       => 'system',
                'data'       => AmazonAutoLinks_SiteInformation::getCustomDatabaseTables() + $_aDisabled,
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'      => __( 'General Options', 'amazon-auto-links' ),
                'field_id'   => 'plugin_options',
                'type'       => 'system',
                'data'       => $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ], array() ) ) + $_aDisabled,
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'      => __( 'Tools Options', 'amazon-auto-links' ),
                'field_id'   => 'tools_options',
                'type'       => 'system',
                'data'       => $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'tools' ], array() ) ) + $_aDisabled,
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'Template Options', 'amazon-auto-links' ),
                'field_id'  => 'template_options',
                'type'      => 'system',
                'data'      => $this->getAsArray( get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ], array() ) ) + $_aDisabled,
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'File Permissions', 'amazon-auto-links' ),
                'field_id'  => 'file_permissions',
                'type'      => 'system',
                'data'      => AmazonAutoLinks_SiteInformation::getFilePermissions() + $_aDisabled,
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'Paths', 'amazon-auto-links' ) . ' & ' . __( 'URLs', 'amazon-auto-links' ),
                'field_id'  => 'paths',
                'type'      => 'system',
                'data'      => AmazonAutoLinks_SiteInformation::getPathsAndURLs() + $_aDisabled,
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
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action() do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {
        parent::_doTab( $oFactory );
    }
            
}