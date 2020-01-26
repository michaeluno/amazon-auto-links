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
 * Adds the 'Form' form section to the 'Misc' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Misc_FormOption extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'form_options',       // avoid hyphen(dash), dots, and white spaces
            'capability'    => 'manage_options',
            'title'         => __( 'Form', 'amazon-auto-links' ),
            'description'   => __( 'Set allowed HTML tags etc.', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        // The default values will be merged with the options object property.
        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'      => 'allowed_html_tags',
                'type'          => 'textarea',
                'title'         => __( 'Allowed HTML Tags', 'amazon-auto-links' ),
                'tip'           => __( 'Enter allowed HTML tags for the form input, per line or separated by commas. By default, WordPress applies a filter called KSES that strips out certain tags before the user input is saved in the database for security reasons.', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>noscript, style</code>',
                'attributes'    => array(
                    'style' => 'width: 92%;'
                ),
                'capability' => 'manage_options',
            ),
            array(
                'field_id'      => 'allowed_attributes',
                'type'          => 'textarea',
                'title'         => __( 'Allowed HTML Tag Attributes', 'amazon-auto-links' ),
                'tip'           => __( 'Enter allowed HTML tag attributes for options that enter HTML tags such as Item Format unit option.', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>itemscope, itemtype, itemprop, </code>',
                'attributes'    => array(
                    'style' => 'width: 92%;'
                ),
                'capability' => 'manage_options',
            ),
            array(
                'field_id'      => 'allowed_inline_css_properties',
                'type'          => 'textarea',
                'title'         => __( 'Allowed Inline CSS Properties', 'amazon-auto-links' ),
                'tip'           => __( 'Enter allowed inline CSS properties for options that enter HTML tags such as Item Format unit option.', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>min-height, max-height, max-height, min-height, display</code>',
                'attributes'    => array(
                    'style' => 'width: 92%;'
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
        $aInput[ 'allowed_html_tags' ] = str_replace(
            PHP_EOL,    // search
            ',',        // replace
            $aInput[ 'allowed_html_tags' ]  // subject
        );
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