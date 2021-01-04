<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 3
 * @since 4.5.0 Change the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_Unit_CommonAdvanced extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * @param   string $sFieldIDPrefix
     * @param   string $sUnitType
     * @return  array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        $_oOption       = $this->oOption;
        $_aFields       = array(
            array(                  // 4.1.0
                'field_id'          => $sFieldIDPrefix . 'show_errors',
                'type'              => 'radio',
                'title'             => __( 'Show Errors', 'amazon-auto-links' ),
                'label'             => array(
                    0 => __( 'Do not show errors.', 'amazon-auto-links' ),
                    1 => __( 'Show errors.', 'amazon-auto-links' ),
                    2 => __( 'Show errors as an HTML comment.', 'amazon-auto-links' ),
                ),
                'default'           => 2,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'load_with_javascript',
                'type'              => 'checkbox',
                'title'             => __( 'Load with JavaScript', 'amazon-auto-links' ),
                'label'             => __( 'Load the output with JavaScript.', 'amazon-auto-links' ),
                'default'           => false,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . '_now_loading_text',
                'type'              => 'text',
                'title'             => __( 'Now Loading Text', 'amazon-auto-links' ),
                'default'           => __( 'Now loading...', 'amazon-auto-links' ),
                'tip'               => __( 'The text that appears while loading the unit in the background.', 'amazon-auto-links' ),
                'attributes'        => array(
                    'class' => 'width-full',
                    'field'    => array(
                        'class' => 'width-full',
                    ),
                ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'subimage_size',
                'type'              => 'number',            
                'title'             => __( 'Max Image Size for Sub-images', 'amazon-auto-links' ),
                'tip'               => __( 'Set the maximum width or height for sub-images.', 'amazon-auto-links' )
                    . ' ' . __( 'Set 0 for no image.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>100</code>',                    
                'after_input'       => '  pixel(s)',
                'attributes'        => array(
                    'max'               => 500,
                ),                
                'default'           => 100,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'subimage_max_count',
                'title'             => __( 'Max Number of Sub-images', 'amazon-auto-links' ),
                'type'              => 'number',
                'default'            => 20,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'customer_review_max_count',
                'title'             => __( 'Max Number of Customer Reviews', 'amazon-auto-links' ),
                'type'              => 'number',
                'default'           => 5,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'description_suffix',
                'title'             => __( 'Description Suffix', 'amazon-auto-links' ),
                'tip'               => __( 'Set the text appended to the description element when truncated. To disable it set an empty value.', 'amazon-auto-links' ),
                'type'              => 'text',
                'default'           => __( 'read more', 'amazon-auto-links' ),
                'attributes'        => array(
                    'class' => 'width-full',
                    'field'    => array(
                        'class' => 'width-full',
                    ),
                ),
            ),            
            array(
                'field_id'          => $sFieldIDPrefix . 'customer_review_include_extra',
                'title'             => __( 'Include Extra', 'amazon-auto-links' ),
                'type'              => 'checkbox',
                'label'             => __( 'Include sub-elements such as voting buttons.', 'amazon-auto-links' ),
                'description'       => __( 'To keep it simple, uncheck it.', 'amazon-auto-links' ),
                'default'           => false,
            ),     
            array(
                'field_id'          => $sFieldIDPrefix . 'show_now_retrieving_message',
                'title'             => __( 'Now Retrieving Message', 'amazon-auto-links' ),
                'type'              => 'checkbox',
                'label'             => __( 'Show the <strong>Now Retrieving...</strong> message for the elements being fetched in the background.', 'amazon-auto-links' ),
                'default'           => true,
            ),     
            array(
                'field_id'          => $sFieldIDPrefix . 'highest_content_heading_tag_level',
                'title'             => __( 'Highest Heading Tag Level', 'amazon-auto-links' ),
                'tip'               => array(
                    __( 'Set the highest level of heading tags in the product content output inserted with the <code>%content%</code> variable in the Item Format option.', 'amazon-auto-links' ),
                    __( 'For example, setting <code>3</code> will degrade <code>h1</code> and <code>h2</code> to <code>h3</code> and <code>h4</code> in product content outputs.', 'amazon-auto-links' ),
                    
                ),
                'type'              => 'number',
                'attributes'        => array(
                    'min'   => 1,
                    'step'  => 1,
                    'max'   => 6,
                ),  
                'default'           => 5,
            )
        );

        $_aFieldURLQueryString = array(
            'field_id'          => $sFieldIDPrefix . '_custom_url_query_string',
            'title'             => __( 'Custom URL Query', 'amazon-auto-links' ),
            'description'       => __( 'Key-value pairs added to product links.', 'amazon-auto-links' )
                . ' e.g. ' . __( 'Key', 'amazon-auto-links' ) . ': <code>foo</code> ' . __( 'Value', 'amazon-auto-links' ) . ': <code>bar</code> -> '
                . __( 'URL', 'amazon-auto-links' ) . ': https://amazon.com/...?<code>foo=bar</code>',
            'type'              => 'text',
            'repeatable'        => true,
            'label'             => array(
                'key'   => __( 'Key', 'amazon-auto-links' ),
                'value' => __( 'Value', 'amazon-auto-links' ),
            ),
            'order'             => 99,
        );
        if ( ! $_oOption->canAddQueryStringToProductLinks() ) {
            $_aFieldURLQueryString[ 'attributes' ]     = array(
                'disabled' => 'disabled',
                'class'    => 'disabled read-only',
            );
            $_aFieldURLQueryString[ 'repeatable' ] = array(
                'disabled'  => array(
                    'message' => sprintf(
                        __( "Please upgrade to <a href='%1\$s' target='_blank'>Pro</a> to enable this feature.", 'amazon-auto-links' ),
                        esc_url( AmazonAutoLinks_Registry::STORE_URI_PRO )
                    ),
                    'caption' => AmazonAutoLinks_Registry::NAME,
                ),
            );
        }
        $_aFields[] = $_aFieldURLQueryString;

        return $_aFields;
        
    }
      
}