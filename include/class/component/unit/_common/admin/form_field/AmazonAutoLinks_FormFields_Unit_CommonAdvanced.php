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
        $_aItemFormat   = AmazonAutoLinks_UnitOption_Base::getDefaultItemFormat();
        $_aFields       = array(
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
                'title'             => __( 'Max number of sub-images', 'amazon-auto-links' ),
                'type'              => 'number',
                'default'            => 20,
            ),                
            array(
                'field_id'          => $sFieldIDPrefix . 'customer_review_max_count',
                'title'             => __( 'Max number of customer reviews', 'amazon-auto-links' ),
                'type'              => 'number',
                'default'           => 5,
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
            ),             
        );

       
        return $_aFields;
        
    }
      
}