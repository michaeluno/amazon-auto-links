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

        new AmazonAutoLinks_TemplateActivator;
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
       
        new AmazonAutoLinks_AdminPage_Template( $oFactory );
        
        if ( 'plugins.php' === $oFactory->oProp->sPageNow ) {
            
            $_sTemplateURL = add_query_arg(
                array(
                    'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'template' ],
                ),
                admin_url( 'edit.php' )
            );
            $oFactory->addLinkToPluginTitle(
                "<a href='" . esc_url( $_sTemplateURL ) . "'>"
                    . __( 'Templates', 'amazon-auto-links' )
                . "</a>"
            );
            
        }
       
        
    }
    
  
    
}