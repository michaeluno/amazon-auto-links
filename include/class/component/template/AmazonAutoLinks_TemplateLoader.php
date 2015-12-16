<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Loads the template component.
 *  
 * @package     Amazon Auto Links
 * @since       3.1.0
*/
class AmazonAutoLinks_TemplateLoader {
    
    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {
        
        new AmazonAutoLinks_TemplateResourceLoader;
        
        if ( is_admin() ) {
            
            add_action( 
                'set_up_' . 'AmazonAutoLinks_AdminPage',
                array( $this, 'replyToSetUpAdminPage' )
            );
            
        }
        
    }    
    
    /**
     * 
     */
    public function replyToSetUpAdminPage( $oFactory ) {
       
        new AmazonAutoLinks_AdminPage_Template(
            $oFactory,
            array(
                'page_slug' => AmazonAutoLinks_Registry::$aAdminPages[ 'template' ],
                'title'     => __( 'Templates', 'amazon-auto-links' ),
                'order'     => 60,
            )                
        );
       
        
    }
    
  
    
}