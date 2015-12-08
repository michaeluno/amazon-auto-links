<?php
/**
 * Provides the definitions of form fields for the 'url' unit type.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_URLUnit_Main extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
            
        $_oOption = $this->oOption;
        $_aFields = array(  
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'url',
            ),          
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                'type'          => 'text',
                'description'   => 'e.g. <code>My URL Unit</code>',
                'value'         => '',    // a previous value should not appear
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'type'          => 'select',
                'title'         => __( 'Country', 'amazon-auto-links' ),
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
                'default' => 'US',
            ),           
            array(
                'field_id'      => $sFieldIDPrefix . 'associate_id',
                'type'          => 'text',
                'title'         => __( 'Associate ID', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>miunosoft-20</code>',
                'default'       => '',
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'urls',
                'type'          => 'text',
                'title'         => __( 'URLs', 'amazon-auto-links' ),
                'attributes'    => array(
                    'style' => 'width: 720px; max-width: 86%; ',
                    'size'  => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'repeatable'    => array(
                    'max'   => $_oOption->isAdvancedAllowed()
                        ? 0
                        : 1,
                ),
                'description'   => __( 'Enter URLs of the page to be parsed.', 'amazon-auto-links' )
                    . ' e.g. <code>http://www.amazon.com/gp/feature.html?docId=1000677541</code>',
            ),    
            array(
                'field_id'      => $sFieldIDPrefix . 'search_per_keyword',
                'type'          => 'checkbox',
                'title'         => __( 'Query per Term', 'amazon-auto-links' ),
                'tip'           => __( 'Although Amazon API allows multiple search terms to be set per request, when one of them returns an error, the entire result becomes an error. To prevent it, check this option so that the rest will be returned.', 'amazon-auto-links' ),
                'label'         => __( 'Perform search per item.', 'amazon-auto-links' ),
                'default'       => false,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'count',
                'title'             => __( 'Number of Items', 'amazon-auto-links' ),
                'type'              => 'number',
                'tip'               => __( 'The number of product links to display.', 'amazon-auto-links' ),
                'attributes'        => array(
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
                'after_input'       => ' ' . __( 'pixel', 'amazon-auto-links' ),
                'delimiter'         => '',
                'tip'           => __( 'The maximum width of the product image in pixel. Set <code>0</code> for no image.', 'amazon-auto-links' ),
                'description'   => __( 'Max', 'amazon-auto-links' ) . ': <code>500</code> '
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>160</code>',
                'attributes'        => array(
                    'max' => 500,
                    'min' => 0,                
                ),
                'default'           => 160,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . '_sort',
                'type'              => 'select',
                'title'             => __( 'Sort Order', 'amazon-auto-links' ),
                'label'             => array(                        
                    'raw'               => __( 'Raw', 'amazon-auto-links' ),
                    'title'             => __( 'Title', 'amazon-auto-links' ),
                    'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
                    'random'            => __( 'Random', 'amazon-auto-links' ),
                ),
                'tip'               => __( 'In order to not to sort and leave it as the found order, choose <code>Raw</code>.', 'amazon-auto-links' ),
                'default'           => 'raw',
            ),                         
            array(
                'field_id'          => $sFieldIDPrefix . 'ref_nosim',
                'type'              => 'radio',
                'title'             => __( 'Direct Link Bonus', 'amazon-auto-links' ),
                'label'             => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'description'       => sprintf( 
                    __( 'Inserts <code>ref=nosim</code> in the product link url. For more information, visit <a href="%1$s">this page</a>.', 'amazon-auto-links' ), 
                    'https://affiliate-program.amazon.co.uk/gp/associates/help/t5/a21' 
                ),
                'default'           => 0,
            ),        
            array(
                'field_id'          => $sFieldIDPrefix . 'title_length',
                'type'              => 'number',
                'title'             => __( 'Title Length', 'amazon-auto-links' ),
                'tip'               => __( 'The allowed character length for the title.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Use it to prevent a broken layout caused by a very long product title. Set -1 for no limit.', 'amazon-auto-links' ),
                'description'       => __( 'Default', 'amazon-auto-links' ) . ": <code>-1</code>",
                'default'           => -1,
            ),        
            array(
                'field_id'          => $sFieldIDPrefix . 'link_style',
                'type'              => 'radio',
                'title'             => __( 'Link Style', 'amazon-auto-links' ),
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
                'default'           => 1,
            ),          
            array(
                'field_id'      => $sFieldIDPrefix . 'Operation',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'ItemLookup',
            ),            
        );
            
        $_oCreditFields = new AmazonAutoLinks_FormFields_Unit_Credit;
        $_aCreditFields = $_oCreditFields->get( $sFieldIDPrefix );
        
        return array_merge(
            $_aFields,         // comes first
            $_aCreditFields    // appendded
        );          
        
    }
  
}