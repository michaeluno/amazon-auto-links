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
 * Loads the info-box component.
 *  
 * @package     Amazon Auto Links
 * @since       3.1.0
*/
class AmazonAutoLinks_InfoBoxLoader {
    
    /**
     * Loads necessary components.
     */
    public function __construct() {
        
        $_aTargetPageSlugs = array(
            AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
            AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ],
            AmazonAutoLinks_Registry::$aAdminPages[ 'help' ],
            AmazonAutoLinks_Registry::$aAdminPages[ 'report' ],
        );
                
        if ( ! $this->_shouldProceed( $_aTargetPageSlugs ) ) {
            return;
        }
            
        new AmazonAutoLinks_AdminPageMetaBox_Information(
        null,                                // meta box id - passing null will make it auto generate
            __( 'Information', 'amazon-auto-links' ),  // title
            $_aTargetPageSlugs,
            'side',                            // context
            'low'                               // priority
        );
    
    }    
        /**
         * @param   array   $aTargetPageSlugs
         * @return  boolean
         */
        private function _shouldProceed( $aTargetPageSlugs ) {
            if ( ! is_admin() ) {
                return false;
            }
            if ( ! isset( $_GET[ 'page' ] ) ) {
                return false;
            }
            return in_array( $_GET[ 'page' ], $aTargetPageSlugs );
        }
    
}