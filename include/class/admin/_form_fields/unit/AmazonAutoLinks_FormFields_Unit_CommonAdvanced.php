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
                'description'       => array(
                    __( 'Set the maximum width or height for sub-images.', 'amazon-auto-links' ),
                    __( 'Set 0 for no image.', 'amazon-auto-links' ),
                    __( 'Default', 'amazon-auto-links' ) . ': <code>100</code>',                    
                ),
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
        );

       
        return $_aFields;
        
    }
      
}