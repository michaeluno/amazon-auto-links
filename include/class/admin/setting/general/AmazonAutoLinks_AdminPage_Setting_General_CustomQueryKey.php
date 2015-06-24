<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Custom Query Key' form section to the 'General' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_General_CustomQueryKey extends AmazonAutoLinks_AdminPage_Section_Base {
    
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
    
        $_oOption = AmazonAutoLinks_Option::getInstance();    
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'        => 'cloak',
                'type'            => 'text',
                'title'          => __( 'Link Style Query Key', 'amazon-auto-links' ),
                'description'    => array(
                    __( 'Define the query parameter key for the cloaking link style.', 'amazon-auto-links' ),
                    __( 'Default', 'amazon-auto-links' ) 
                        . ': <code>' 
                            . $_oOption->aDefault[ 'query' ][ 'cloak' ]
                        . '</code>',
                ),
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
        
          
        // Sanitize the query key.
        $aInput[ 'cloak' ] = AmazonAutoLinks_Utility::sanitizeCharsForURLQueryKey( 
            $aInput[ 'cloak' ]
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