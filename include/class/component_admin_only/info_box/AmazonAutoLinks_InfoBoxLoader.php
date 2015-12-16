<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads the iofo-box component.
 *  
 * @package     Amazon Auto Links
 * @since       3.1.0
*/
class AmazonAutoLinks_InfoBoxLoader {
    
    /**
     * Loads necessary components.
     */
    public function __construct() {
        
        if ( ! is_admin() ) {
            return;
        }        
    
        new AmazonAutoLinks_AdminPageMetaBox_Information(
            null,                                           // meta box id - passing null will make it auto generate
            __( 'Information', 'amazon-auto-links' ), // title
            array( // page slugs
                AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ],
                AmazonAutoLinks_Registry::$aAdminPages[ 'help' ],
            ),
            'side',                                       // context
            'default'                                     // priority            
        );    
    
    }    
    
}