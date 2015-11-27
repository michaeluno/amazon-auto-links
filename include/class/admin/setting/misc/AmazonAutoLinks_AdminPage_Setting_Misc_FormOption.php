<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Form' form section to the 'Misc' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Misc_FormOption extends AmazonAutoLinks_AdminPage_Section_Base {
    
    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    public function addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'      => 'allowed_html_tags',
                'type'          => 'text',
                'title'         => __( 'Allowed HTML Tags', 'amazon-auto-links' ),
                'tip'           => __( 'Enter the allowed HTML tags for the form input, separated by commas. By default, WordPress applies a filter called KSES that strips out certain tags before the user input is saved in the database for security reasons.', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>noscript, style</code>',
                'attributes'    => array(
                    'size'         => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 60 
                        : 80,
                ),
                'capability' => 'manage_options',
            )
        );           
    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3
     */
    public function validate( $aInput, $aOldInput, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        
        // Sanitize text inputs
        $aInput[ 'allowed_html_tags' ] = trim( 
            AmazonAutoLinks_Utility::trimDelimitedElements( 
                $aInput['allowed_html_tags'], 
                ',' 
            )
        );        
          
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInput;
        }
                
        return $aInput;     
        
    }
   
}