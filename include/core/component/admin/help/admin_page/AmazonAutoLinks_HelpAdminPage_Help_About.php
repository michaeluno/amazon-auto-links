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
     * @since   3.11.1
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
     * @callback        action      load_{page slug}_{tab slug}
     */
    protected function _loadTab( $oAdminPage ) {

        $_oReflection       = new ReflectionClass('AmazonAutoLinks_Registry');//
        $_oProductTable     = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_oHTTPRequestTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
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
                    'Current Time' => '', 'Admin Page Framework' => '', 'WordPress' => '',
                    'PHP' => '', 'Server' => '', 'PHP Error Log' => '',
                    'MySQL' => '', 'MySQL Error Log' => '', 'Browser' => '',
                    'aal_products' => array(
                        'name'    => $_oProductTable->getTableName(),
                        'version' => $_oProductTable->getVersion(),
                        'size'    => $_oProductTable->getTableSize(),
                        'columns' => $_oProductTable->getRows(
                            'DESCRIBE ' . $_oProductTable->aArguments[ 'table_name' ]
                        ),
                    ),
                    'aal_request_cache' => array(
                        'name'    => $_oHTTPRequestTable->getTableName(),
                        'version' => $_oHTTPRequestTable->getVersion(),
                        'size'    => $_oHTTPRequestTable->getTableSize(),
                        'columns' => $_oHTTPRequestTable->getRows(
                            'DESCRIBE ' . $_oHTTPRequestTable->aArguments[ 'table_name' ]
                        ),
                    ),
                ),
                'attributes' => array(
                    'style' => 'height: 300px;',
                ),
            ),
            array(
                'title'     => __( 'Plugin Options', 'amazon-auto-links' ),
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
     * 
     * @callback        action      do_{page slug}_{tab slug}
     */
    protected function _doTab( $oFactory ) {
        parent::_doTab( $oFactory );
    }
            
}