<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since           3  
 * @remark          The admin page and meta box access it.
 */
class AmazonAutoLinks_FormFields_CategoryUnit_BasicInformation extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
            
        $_oOption = $this->oOption;
        return array(
            array(
                'field_id'      => 'unit_type',
                'type'          => 'hidden',
                'value'         => 'category',
                'hidden'        => true,            // hides the field row as well.
            ),                    
            array(
                'field_id'      => 'country',
                'title'         => __( 'Country', 'amazon-auto-links' ),
                'type'          => 'select',
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
                'field_id'      => 'associate_id',
                'title'         => __( 'Associate ID', 'amazon-auto-links' ),
                'type'          => 'text',
                'description'   => 'e.g. <code>miunosoft-20</code>',
                'default'       => '',
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
                'field_id'      => 'image_size',
                'title'         => __( 'Image Size', 'amazon-auto-links' ),
                'type'          => 'number',
                'after_input'   => ' ' . __( 'pixel', 'amazon-auto-links' ),
                'elimiter'      => '',
                'tip'           => __( 'The maximum width of the product image in pixel. Set <code>0</code> for no image.', 'amazon-auto-links' ),
                'description'   => __( 'Max', 'amazon-auto-links' ) . ': <code>500</code> '
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>160</code>',
                'attributes'    => array(
                    'max'   => 500,
                    'min'   => 0,
                ),
                'default'       => 160,
            ),        
            array(
                'field_id'      => 'sort',
                'title'         => __( 'Sort Order', 'amazon-auto-links' ),
                'type'          => 'select',
                'label'         => array(                        
                    'date'              => __( 'Date', 'amazon-auto-links' ),
                    'title'             => __( 'Title', 'amazon-auto-links' ),
                    'title_descending'  => __( 'Title Descending', 'amazon-auto-links' ),
                    'random'            => __( 'Random', 'amazon-auto-links' ),
                ),
                'default'       => 'random',
            ),        
            array(
                'field_id'      => 'keep_raw_title',
                'title'         => __( 'Sort Option', 'amazon-auto-links' ),
                'type'          => 'checkbox',
                'label'         => __( 'Keep raw titles.', 'amazon-auto-links' ),
                'description'   => __( 'If checked, unsanitized titles will be used. This is useful to sort by rank.', 'amazon-auto-links' ),
                'default'       => false,
            ),                
            array(
                'field_id'      => 'feed_type',
                'title'         => __( 'Types', 'amazon-auto-links' ),
                'type'          => 'checkbox',
                'label'         => array(    
                    // These keys will be used in a url when generating the feed url.
                    'bestsellers'           => __( 'Best Sellers', 'amazon-auto-links' ),
                    'new-releases'          => __( 'Hot New Releases', 'amazon-auto-links' ),
                    'movers-and-shakers'    => __( 'Mover and Shakers', 'amazon-auto-links' ),
                    'top-rated'             => __( 'Top Rated', 'amazon-auto-links' ),
                    'most-wished-for'       => __( 'Most Wished For', 'amazon-auto-links' ),
                    'most-gifted'           => __( 'Gift Ideas', 'amazon-auto-links' ),
                ),
                'description'   => __( 'It is recommended to check only a few for faster page loading.', 'amazon-auto-links' )
                    . '&nbsp;' . __( 'Some of the types other than Best Sellers are not supported in some locales.', 'amazon-auto-links' ),
                'default'       => array( 'bestsellers' => true ),
            ),        
            array(
                'field_id'      => 'ref_nosim',
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
                'field_id'      => 'title_length',
                'title'         => __( 'Title Length', 'amazon-auto-links' ),
                'type'          => 'number',
                'tip'           => __( 'The allowed character length for the title.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Use it to prevent a broken layout caused by a very long product title. Set -1 for no limit.', 'amazon-auto-links' ),
                'description'   => __( 'Default', 'amazon-auto-links' ) . ": <code>-1</code>",
                'default'       => -1,
            ),        
            array(
                'field_id'      => 'link_style',
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
                'field_id'      => 'credit_link',
                'title'         => __( 'Credit Link', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(                        
                    1   => __( 'On', 'amazon-auto-links' ),
                    0   => __( 'Off', 'amazon-auto-links' ),
                ),
                'tip'           => __( 'Inserts the credit link at the end of the unit output.', 'amazon-auto-links' ), 
                'default'       => 1,
            ),    
        );
    }
  
}