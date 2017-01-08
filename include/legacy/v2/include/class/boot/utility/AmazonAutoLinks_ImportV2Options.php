<?php
/**
 * Imports v1 options.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl   http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3
 */

final class AmazonAutoLinks_ImportV2Options {

    /**
     * Converts v2 options to v3.
     * 
     * @since       3
     */
    public function __construct( $abOptions ) {
        
        $this->aOptions = empty( $abOptions )
            ? array()
            : ( array ) $abOptions;
            
        $this->_set( $this->aOptions );
        
    }
        
        /**
         * Sets the converted options to the options table.
         * @since       3
         * 
            v2 General Option array structure
            array(
                'arrTemplates' => array(
                    ...
                ),
                'aal_settings' => array(
                    'capabilities' => array(
                        'setting_page_capability' => 'manage_options',
                    ),        
                    'product_filters' => array(
                        'black_list' => array(
                            'asin' => '',
                            'title' => '',
                            'description' => '',
                        ),
                    ),
                    'support' => array(
                        'rate' => 0,            // asked for the first load of the plugin admin page
                    ),
                    'query' => array(
                        'cloak' => 'productlink'
                    ),
                    ...
                )
            ),        
            
            v3 Option Structure - no page dimension
            array(
                'capabilities' => array(
                    'setting_page_capability' => 'manage_options',
                ),        
                'product_filters' => array(
                    'black_list' => array(
                        'asin' => '',
                        'title' => '',
                        'description' => '',
                    ),
                ),
                'support' => array(
                    'rate' => 0,            // asked for the first load of the plugin admin page
                ),
                'query' => array(
                    'cloak' => 'productlink'
                ),
                ...
            ),                    
        
         */
        private function _set( array $aOptions ) {
            
            // Extract the template options
            $_aTemplates = isset( $aOptions[ 'arrTemplates' ] )
                ? $aOptions[ 'arrTemplates' ]
                : array();
            unset( $aOptions[ 'arrTemplates' ] );
            
            // Drop the page dimension.
            $_aNewOptions = array();
            foreach( $aOptions as $_sPageSlug => $_aPageOptions ) {
                $_aNewOptions = $_aPageOptions + $_aNewOptions;
            }
            
            // Set the API connection status.
            $_aNewOptions[ 'authentication_keys' ][ 'api_authentication_status' ] = $this->_isAPIConnected( $_aNewOptions );
            
            // Save the options
            update_option(
                AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ],  // key 
                $_aNewOptions     // data
            );
            update_option(
                AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ],  // key 
                $_aTemplates
            );            
            
        }
            /**
             * @return      boolean
             */
            private function _isAPIConnected( $_aNewOptions ) {
                if ( 
                    ! isset( 
                        $_aNewOptions[ 'authentication_keys' ][ 'access_key' ],
                        $_aNewOptions[ 'authentication_keys' ][ 'access_key_secret' ]
                    )
                ) {
                    return false;
                }                
                return $_aNewOptions[ 'authentication_keys' ][ 'access_key' ]
                    && $_aNewOptions[ 'authentication_keys' ][ 'access_key_secret' ];
            }

}