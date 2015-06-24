<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 */

/**
 * Adds the 'Unit Preview' form section to the 'General' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_General_UnitPreview extends AmazonAutoLinks_AdminPage_Section_Base {
    
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
                'field_id'       => 'preview_post_type_slug',
                'title'          => __( 'Post Type Slug', 'amazon-auto-links' ),
                'description'    => __( 'Up to 20 characters with small-case alpha numeric characters.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' )
                    . ': <code>'
                        . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
                    . '</code>',
                'type'           => 'text',
                'default'        => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
            ),
            array(
                'type'           => 'checkbox',
                'field_id'       => 'visible_to_guests',
                'title'          => __( 'Visibility', 'amazon-auto-links' ),                
                'label'          => __( 'Visible to non-logged-in users.', 'amazon-auto-links' ),
                'default'        => true,
            ),
            array(
                'type'           => 'checkbox',
                'field_id'       => 'searchable',
                'title'          => __( 'Searchable', 'amazon-auto-links' ),                
                'label'          => __( 'Possible for the WordPress search form to find the plugin preview pages.', 'amazon-auto-links' ),
                'default'        => false,
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
        
        // Sanitize the custom preview slug.
        $aInput[ 'preview_post_type_slug' ] = AmazonAutoLinks_Utility::sanitizeCharsForURLQueryKey(
            AmazonAutoLinks_Utility::getTrancatedString(
                $aInput[ 'preview_post_type_slug' ],
                20, // character length
                ''  // suffix
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