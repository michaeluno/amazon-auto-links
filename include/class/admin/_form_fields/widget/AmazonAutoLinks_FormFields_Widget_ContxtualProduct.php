<?php
/**
 * Provides the form fields definitions.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_Widget_ContxtualProduct extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => $sFieldIDPrefix. 'title', 
                'type'          => 'text',
                'title'         => __( 'Title', 'amazon-auto-links' ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'show_title_on_no_result',
                'type'          => 'checkbox',
                'label'         => __( 'Show widget title on no result.', 'amazon-auto-links' ),
                'default'       => true,
            ),               
            array(
                'field_id'      => $sFieldIDPrefix . 'criteria',
                'title'         => __( 'Additional Criteria', 'amazon-auto-links' ),
                'type'          => 'checkbox',            
                'label'         => array(
                    'post_title'        => __( 'Post Title', 'amazon-auto-links' ),
                    'taxonomy_terms'    => __( 'Taxonomy Terms', 'amazon-auto-links' ),
                    'breadcrumb'        => __( 'Breadcrumb', 'amazon-auto-links' ),
                ),
                'default'       => array(
                    'post_title'        => true,
                    'taxonomy_terms'    => true,
                    'breadcrumb'        => false,
                ),
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'additional_keywords',
                'title'         => __( 'Additional Keywords', 'amazon-auto-links' ),
                'type'          => 'text',
                'attributes'    => array(
                    'style' => 'width: 80%',
                ),
                'description'   => __( 'Add additional search keywords.', 'amazon-auto-links' ),
            ),     
            
            array(
                'field_id'          => $sFieldIDPrefix . 'country',
                'type'              => 'select',
                'title'             => __( 'Country', 'amazon-auto-links' ),        
                'label'         => array(                        
                    'CA' => 'CA - ' . __( 'Canada', 'amazon-auto-links' ),
                    'CN' => 'CN - ' . __( 'China', 'amazon-auto-links' ),
                    'FR' => 'FR - ' . __( 'France', 'amazon-auto-links' ),
                    'DE' => 'DE - ' . __( 'Germany', 'amazon-auto-links' ),
                    'IT' => 'IT - ' . __( 'Italy', 'amazon-auto-links' ),
                    'JP' => 'JP - ' . __( 'Japan', 'amazon-auto-links' ),
                    'UK' => 'UK - ' . __( 'United Kingdom', 'amazon-auto-links' ),
                    'ES' => 'ES - ' . __( 'Spain', 'amazon-auto-links' ),
                    'US' => 'US - ' . __( 'United States', 'amazon-auto-links' ),
                    'IN' => 'IN - ' . __( 'India', 'amazon-auto-links' ),
                    'BR' => 'BR - ' . __( 'Brazil', 'amazon-auto-links' ),
                    'MX' => 'MX - ' . __( 'Mexico', 'amazon-auto-links' ),
                ),
                'default'       => 'US',
            ),     
            array(
                'field_id'          => $sFieldIDPrefix . 'associate_id',
                'type'              => 'text',
                'title'             => __( 'Associate ID', 'amazon-auto-links' ),
                'description'       => 'e.g. ' . '<code>miunosorft-20</code>'
            ),     
            array(
                'field_id'          => $sFieldIDPrefix . 'count',
                'type'              => 'number',
                'title'             => __( 'Number of Items', 'amazon-auto-links' ),
                'attributes'    => array(
                    'min' => 1,
                    'max' => $_oOption->getMaximumProductLinkCount() 
                        ? $_oOption->getMaximumProductLinkCount() 
                        : null,
                ),
                'default'           => 10,
            ),     
            array(
                'field_id'          => $sFieldIDPrefix . 'image_size',
                'type'              => 'number',
                'title'             => __( 'Image Size', 'amazon-auto-links' ),
                'attributes'        => array(
                    'min'   => 0,
                    'max'   => 500,
                ),
                'after_input'   => ' ' . __( 'pixel', 'amazon-auto-links' ),
                'description'   => __( 'The maximum width of the product image in pixel. Set <code>0</code> for no image.', 'amazon-auto-links' )
                    . ' ' . __( 'Max', 'amazon-auto-links' ) . ': <code>500</code> ' 
                    . __( 'Default', 'amazon-auto-links' ) . ': <code>160</code>',                                
                'default'           => 160,
            ),
            array(
                'field_id'      => $sFieldIDPrefix. 'ref_nosim',
                'title'         => __( 'Direct Link Bonus', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'description'   => sprintf( 
                    __( 'Inserts <code>ref=nosim</code> in the link url. For more information, visit <a href="%1$s">this page</a>.', 'amazon-auto-links' ),
                    'https://affiliate-program.amazon.co.uk/gp/associates/help/t5/a21'
                ),
                'default'       => 0,
            ),                  
            array(
                'field_id'      => $sFieldIDPrefix. 'title_length',
                'title'         => __( 'Title Length', 'amazon-auto-links' ),
                'type'          => 'number',
                'description'   => __( 'The allowed character length for the title.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Use it to prevent a broken layout caused by a very long product title. Set -1 for no limit.', 'amazon-auto-links' ) . '<br />'
                    . __( 'Default', 'amazon-auto-links' ) . ": <code>-1</code>",
                'default'       => -1,
            ),        
            array(
                'field_id'      => $sFieldIDPrefix. 'link_style',
                'title'         => __( 'Link Style', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(                        
                    1    => 'http://www.amazon.<code>[domain-suffix]</code>/<code>[product-name]</code>/dp/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>'
                        . "&nbsp;<span class='description'>(" . __( 'Default', 'amazon-auto-links' ) . ")</span>",
                    2    => 'http://www.amazon.<code>[domain-suffix]</code>/exec/obidos/ASIN/<code>[asin]</code>/<code>[associate-id]</code>/ref=<code>[...]</code>',
                    3    => 'http://www.amazon.<code>[domain-suffix]</code>/gp/product/<code>[asin]</code>/?tag=<code>[associate-id]</code>&ref=<code>[...]</code>',
                    4    => 'http://www.amazon.<code>[domain-suffix]</code>/dp/ASIN/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>',
                    5    => site_url() . '?' . $_oOption->get( 'query', 'cloak' ) . '=<code>[asin]</code>&locale=<code>[...]</code>&tag=<code>[associate-id]</code>'
                ),
                'before_label'  => "<span class='links-style-label'>",
                'after_label'   => "</span>",
                'default'       => 1,
            ),        
            array(
                'field_id'      => $sFieldIDPrefix. 'credit_link',
                'title'         => __( 'Credit Link', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(                        
                    1   => __( 'On', 'amazon-auto-links' ),
                    0   => __( 'Off', 'amazon-auto-links' ),
                ),
                'description'   => sprintf( 
                    __( 'Inserts the credit link at the end of the unit output.', 'amazon-auto-links' ), 
                    '' 
                ),
                'default'       => 1,
            ),                                    
            array()
        );
        return $_aFields;
        
    }
      
}