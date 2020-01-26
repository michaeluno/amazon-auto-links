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
 * Adds the 'Feed' form section to the 'General' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_General_Feed extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'feed',
            'title'         => __( 'Feed', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3.3.0
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3.3.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {
    
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'       => 'use_description_tag_for_rss_product_content',
                'title'          => __( 'RSS Content Tag', 'amazon-auto-links' ),
                'tip'            => array(
                    __( 'By default, the plugin uses the <code>description</code> tag for excerpt and the <code>content</code> for the entire product output.', 'amazon-auto-links' ),
                    __( 'However, some RSS readers cannot retrieve contents of <code>content</code> tag. In that case, enable this option to replace the output of the <code>content</code> tag with the <code>description</code> tag.', 'amazon-auto-links' ),
                ),
                'label'          => __( 'Use the <code>content</code> tag instead of <code>description</code> tag for the complete product output.', 'amazon-auto-links' ),
                'type'           => 'checkbox',
                'default'        => false,
            )  
        );
    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3.3.0
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }
                
        return $aInputs;     
        
    }
        
}