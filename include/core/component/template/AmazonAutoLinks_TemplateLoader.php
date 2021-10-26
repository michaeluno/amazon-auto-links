<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the template component.
 *  
 * @package     Auto Amazon Links
 * @since       3.1.0
*/
class AmazonAutoLinks_TemplateLoader {

    /**
     * @var   string
     * @since 4.6.17
     */
    static $sDirPath;

    /**
     * Loads necessary components.
     */
    public function __construct( $sScriptPath ) {

        self::$sDirPath = dirname( __FILE__ );

        new AmazonAutoLinks_Template_Event_Action_ActivationStatus;
        new AmazonAutoLinks_Template_Event_Filter_TemplateListWarning;  // [4.6.17+]
        new AmazonAutoLinks_Template_Event_Ajax_NewTemplatesLoader;     // [4.6.18+]
        new AmazonAutoLinks_TemplateResourceLoader;
        
        if ( is_admin() ) {
            add_action( 'set_up_' . 'AmazonAutoLinks_AdminPage', array( $this, 'replyToSetUpAdminPage' ) );
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