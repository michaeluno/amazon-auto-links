<?php
/**
 * Provides the form fields definitions.
 * 
 * @since           3.1.0
 */
class AmazonAutoLinks_FormFields_Unit_Common extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        $_oOption       = $this->oOption;
        $_aFields       = array(
            array(
                'field_id'      => $sFieldIDPrefix . 'associate_id',
                'type'          => 'text',
                'title'         => __( 'Associate ID', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>miunosoft-20</code>',
                'attributes'    => array(
                    'required'  => 'required',
                ),
            ),        
            array(
                'field_id'      => 'count',
                'title'         => __( 'Number of Items', 'amazon-auto-links' ),
                'type'          => 'number',
                'tip'           => __( 'The number of product links to display.', 'amazon-auto-links' ),
                'default'       => 10,
                'attributes'    => array(
                    'min' => 1,
                    'max' => $_oOption->getMaximumProductLinkCount() 
                        ? $_oOption->getMaximumProductLinkCount() 
                        : null,
                ),                
            ),             
            array(
                'field_id'      => $sFieldIDPrefix . 'image_size',
                'type'          => 'number',
                'title'         => __( 'Image Size', 'amazon-auto-links' ),
                'after_input'   => ' ' . __( 'pixel', 'amazon-auto-links' ),
                'delimiter'     => '',
                'tip'           => __( 'The maximum width of the product image in pixel. Set <code>0</code> for no image.', 'amazon-auto-links' ),
                'description'   => __( 'Max', 'amazon-auto-links' ) . ': <code>500</code> '
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>160</code>',
                'attributes'    => array(
                    'max' => 500,
                    'min' => 0,
                ),
                'default'       => 160,
            ),    
            array(
                'field_id'      => $sFieldIDPrefix . 'ref_nosim',
                'type'          => 'radio',
                'title'         => __( 'Direct Link Bonus', 'amazon-auto-links' ),
                'label'         => array(                        
                    1   => __( 'On', 'amazon-auto-links' ),
                    0   => __( 'Off', 'amazon-auto-links' ),
                ),
                'description'   => sprintf( 
                    __( 'Inserts <code>ref=nosim</code> in the link url. For more information, visit <a href="%1$s">this page</a>.', 'amazon-auto-links' ), 
                    'https://affiliate-program.amazon.co.uk/gp/associates/help/t5/a21' 
                ),
                'default'       => 0,
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'title_length',
                'type'          => 'number',
                'title'         => __( 'Title Length', 'amazon-auto-links' ),
                'tip'           => __( 'The allowed character length for the title.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Use it to prevent a broken layout caused by a very long product title. Set -1 for no limit.', 'amazon-auto-links' ),
                'description'   => __( 'Default', 'amazon-auto-links' ) . ": <code>-1</code>",
                'default'       => -1,
            ),   
            array(
                'field_id'      => $sFieldIDPrefix . 'description_length',
                'type'          => 'number',
                'title'         => __( 'Description Length', 'amazon-auto-links' ),
                'tip'           => __( 'The allowed character length for the description.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Set -1 for no limit.', 'amazon-auto-links' ),
                'description'   => __( 'Default', 'amazon-auto-links' ) . ": <code>250</code>",
                'default'       => 250,
            ),                
            array(
                'field_id'          => $sFieldIDPrefix . 'link_style',
                'type'              => 'radio',
                'title'             => __( 'Link Style', 'amazon-auto-links' ),
                'label'         => array(                        
                    1    => 'http://www.amazon.<code>[domain-suffix]</code>/<code>[product-name]</code>/dp/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>...'
                        . "&nbsp;<span class='description'>(" . __( 'Default', 'amazon-auto-links' ) . ")</span>",
                    2    => 'http://www.amazon.<code>[domain-suffix]</code>/exec/obidos/ASIN/<code>[asin]</code>/<code>[associate-id]</code>/ref=<code>[...]</code>...',
                    3    => 'http://www.amazon.<code>[domain-suffix]</code>/gp/product/<code>[asin]</code>/?tag=<code>[associate-id]</code>&ref=<code>[...]</code>...',
                    4    => 'http://www.amazon.<code>[domain-suffix]</code>/dp/ASIN/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>...',
                    5    => site_url() . '?' . $_oOption->get( 'query', 'cloak' ) . '=<code>[asin]</code>&locale=<code>[...]</code>&tag=<code>[associate-id]</code>...'
                ),
                'before_label'  => "<span class='links-style-label'>",
                'after_label'   => "</span>",
                'default'       => 1,
            )
        );
        
        $_oCreditFields = new AmazonAutoLinks_FormFields_Unit_Credit;
        $_aCreditFields = $_oCreditFields->get( $sFieldIDPrefix );             
        
        return array_merge(
            $_aFields,          // comes first
            $_aCreditFields    // appended
        );    
        
    }
      
}
