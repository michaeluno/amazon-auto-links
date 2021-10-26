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
 * Adds the 'Security' form section.
 * 
 * @since       4.6.19
 */
class AmazonAutoLinks_AdminPage_Setting_Misc_Security extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'security',       // avoid hyphen(dash), dots, and white spaces
            'capability'    => 'manage_options',
            'title'         => __( 'Security', 'amazon-auto-links' ),
            'description'   => __( 'Set allowed HTML tags in setting forms and plugin outputs etc.', 'amazon-auto-links' ),
        );
    }

    /**
     * Adds form fields.
     * @since       4.6.19
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        // The default values will be merged with the options object property.
        $oFactory->addSettingFields(
            $sSectionID, // the target section id    
            array(
                'field_id'      => 'allowed_html',
                'title'         => __( 'Allowed HTML Tags & Attributes', 'amazon-auto-links' ),
                'tip'           => __( 'Enter allowed HTML tags and tag attributes.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style' => 'width: 92%;'
                ),
                'content'       => array(
                    array(
                        'field_id'      => 'tags',
                        'before_input' => "<span class='inline-block margin-right-1em'><strong>" . __( 'Tags', 'amazon-auto-links' ) . "</strong></span>",
                        'type'          => 'text',
                        'attributes'    => array(
                            'style' => 'width: 400px;',
                        ),
                        'after_input' => "<span class='italic margin-left-1em'>"
                                . __( 'For multiple tags, enter them separated by commas.', 'amazon-auto-links' )
                                . ' e.g. <code>noscript, svg</code>'
                            . "</span>",
                        'show_title_column' => false,
                    ),
                    array(
                        'field_id'      => 'attributes',
                        'before_input' => "<span class='inline-block margin-bottom-dot-4em'><strong>" . __( 'Attributes', 'amazon-auto-links' ) . "</strong></span>",
                        'type'          => 'textarea',
                        'description'   => __( 'Set attributes for the tag, separated by commas.', 'amazon-auto-links' )
                            . ' e.g. <code>style, width, height, data-*</code>',
                        'show_title_column' => false,
                        'class'         => array(
                            'field' => 'width-full',
                            'input' => 'width-full',
                        ),
                    ),
                ),
                'capability'    => 'manage_options',
                'repeatable'    => true,
            ),
            array(
                'field_id'      => 'allowed_inline_css_properties',
                'type'          => 'textarea',
                'title'         => __( 'Allowed Inline CSS Properties', 'amazon-auto-links' ),
                'tip'           => __( 'Enter allowed inline CSS properties for options that enter HTML tags such as Item Format unit option.', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>min-height, max-height, display</code>',
                'class'         => array(
                    'field' => 'width-full',
                    'input' => 'width-full',
                ),
                'capability' => 'manage_options',
            )            
        );           
    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since  4.6.19
     * @return array
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {

        $aInputs[ 'allowed_inline_css_properties' ] = _sanitize_text_fields( $aInputs[ 'allowed_inline_css_properties' ] );
        $aInputs[ 'allowed_inline_css_properties' ] = $this->getEachDelimitedElementTrimmed( $aInputs[ 'allowed_inline_css_properties' ], ',' );
        foreach( $aInputs[ 'allowed_html' ] as $_iIndex => &$_aAllowedHTML ) {
            $_aAllowedHTML[ 'tags' ]       = str_replace( PHP_EOL, ',', $_aAllowedHTML[ 'tags' ] );
            $_aAllowedHTML[ 'tags' ]       = _sanitize_text_fields( $_aAllowedHTML[ 'tags' ] );
            $_aAllowedHTML[ 'tags' ]       = $this->getEachDelimitedElementTrimmed( $_aAllowedHTML[ 'tags' ], ',' );
            $_aAllowedHTML[ 'attributes' ] = str_replace( PHP_EOL, ',', $_aAllowedHTML[ 'attributes' ] );
            $_aAllowedHTML[ 'attributes' ] = _sanitize_text_fields( $_aAllowedHTML[ 'attributes' ] );
            $_aAllowedHTML[ 'attributes' ] = $this->getEachDelimitedElementTrimmed( $_aAllowedHTML[ 'attributes' ], ',' );
            if ( empty( $_aAllowedHTML[ 'tags' ] ) && empty( $_aAllowedHTML[ 'attributes' ] ) ) {
                unset( $aInputs[ 'allowed_html' ][ $_iIndex ] );
            }
        }
        return $aInputs;     
        
    }
   
}