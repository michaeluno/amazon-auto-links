<?php
/**
 * Provides the form fields definitions.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_Unit_CommonAdvanced extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        $_oOption       = $this->oOption;
        $_bAPIConnected = $this->oOption->isAPIConnected();
        $_sDel          = $_bAPIConnected
            ? ''
            : "delete-line";
        $_iMaxCol       = $this->oOption->getMaxSupportedColumnNumber();
        $_aFields       = array(
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
// @depercated  3.9.0   PA-API 5 does not support similarity look-up
//            array(
//                'field_id'          => $sFieldIDPrefix . 'similar_product_image_size',
//                'type'              => 'number',
//                'title'             => __( 'Max Image Size for Similar Product Thumbnails', 'amazon-auto-links' ),
//                'tip'               => __( 'Set the maximum width or height for similar product thumbnails.', 'amazon-auto-links' )
//                    . ' ' . __( 'Set 0 for no image.', 'amazon-auto-links' )
//                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>100</code>',
//                'after_input'       => '  pixel(s)',
//                'attributes'        => array(
//                    'max'               => 500,
//                ),
//                'default'           => 120,
//            ),
//            array(
//                'field_id'          => $sFieldIDPrefix . 'similar_product_max_count',
//                'title'             => __( 'Max number of Similar Products', 'amazon-auto-links' ),
//                'type'              => 'number',
//                'attributes'        => array(
//                    'max'               => 10,
//                    'min'               => 0,
//                    'step'              => 1,
//                ),
//                'default'            => 10,
//            ),
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