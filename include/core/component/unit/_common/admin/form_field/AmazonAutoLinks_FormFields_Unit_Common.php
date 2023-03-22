<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 3.1.0
 * @since 4.5.0 Change the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_Unit_Common extends AmazonAutoLinks_FormFields_Unit_Base {

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
                'tip'           => 'e.g. <code>miunosoft-20</code>',
                'attributes'    => array(
                    'required'  => 'required',
                ),
            ),        
            array(
                'field_id'      => 'count',
                'title'         => __( 'Maximum Number of Items', 'amazon-auto-links' ),
                'type'          => 'number',
                'tip'           => __( 'A maximum number of products to display.', 'amazon-auto-links' )
                    . ' ' . __( 'When the number of items fetched from the resource does not reach the set count or items are filtered out by filter options, the result count can be less than this set count.', 'amazon-auto-links' ),
                'default'       => 5,
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
                'tip'           => array(
                    __( 'The maximum width of the product image in pixel. Set <code>0</code> for no image.', 'amazon-auto-links' ),
                    __( 'Max', 'amazon-auto-links' ) . ': <code>500</code> '
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>160</code>',
                ),
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
                'tip'           => sprintf(
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
                    . __( 'Use it to prevent a broken layout caused by a very long product title. Set -1 for no limit.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ": <code>-1</code>",
                'default'       => -1,
            ),   
            array(
                'field_id'      => $sFieldIDPrefix . 'description_length',
                'type'          => 'number',
                'title'         => __( 'Description Length', 'amazon-auto-links' ),
                'tip'           => array(
                    __( 'The allowed character length for the description.', 'amazon-auto-links' ) . '&nbsp;'
                    . __( 'Set <code>-1</code> for no limit.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ": <code>250</code>",
                ),
                'default'       => 250,
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'link_style',
                'type'              => 'revealer',
                'select_type'       => 'radio',
                'title'             => __( 'Link Style', 'amazon-auto-links' ),
                'label'         => array(                        
                    1    => 'https://www.amazon.<code>[domain-suffix]</code>/<code>[product-name]</code>/dp/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>...'
                        . "&nbsp;<span class='description'>(" . __( 'Default', 'amazon-auto-links' ) . ")</span>",
                    2    => 'https://www.amazon.<code>[domain-suffix]</code>/exec/obidos/ASIN/<code>[asin]</code>/<code>[associate-id]</code>/ref=<code>[...]</code>...',
                    3    => 'https://www.amazon.<code>[domain-suffix]</code>/gp/product/<code>[asin]</code>/?tag=<code>[associate-id]</code>&ref=<code>[...]</code>...',
                    4    => 'https://www.amazon.<code>[domain-suffix]</code>/dp/ASIN/<code>[asin]</code>/ref=<code>[...]</code>?tag=<code>[associate-id]</code>...',
                    5    => site_url() . '?' . $_oOption->get( 'query', 'cloak' ) . '=<code>[asin]</code>&locale=<code>[...]</code>&tag=<code>[associate-id]</code>...',
                    6    => site_url() . '/<code>[custom-path]</code>/<code>[asin]</code>&tag=<code>[associate-id]</code>...'
                ),
                'before_label'  => "<span class='links-style-label'>",
                'after_label'   => "</span>",
                'default'       => 1,
                'selectors'         => array(
                    6   => '.fieldrow_link_style_custom_path,.fieldrow_link_style_custom_path_review',
                ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'link_style_custom_path',
                'type'          => 'text',
                'title'         => __( 'Custom URL Path', 'amazon-auto-links' ),
                'hidden'        => true,
                'class'             => array(
                    'fieldrow'  => 'fieldrow_link_style_custom_path',
                ),
                'default'       => 'merchandise',
                'tip'           => __( 'The custom URL path that follows after the site URL for the #6 links style option.', 'amazon-auto-links' )
                    . ' ' . __( 'You would need to set up those pages or redirects by your self.', 'amazon-auto-links' )
                    . ' ' . __( 'This is for those who know what they are doing.', 'amazon-auto-links' ),
                'description'   => sprintf( 'The <code>[custom-url]</code> part in the URL.', trailingslashit( site_url( null ) ) )
                                   . ' ' . __( 'Forward slashes (<code>/</code>) are accepted.', 'amazon-auto-links' )
                                   . ' e.g.<code>merchandise</code>',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'link_style_custom_path_review',
                'type'          => 'text',
                'title'         => __( 'Custom URL Path for Reviews', 'amazon-auto-links' ),
                'hidden'        => true,
                'class'             => array(
                    'fieldrow'  => 'fieldrow_link_style_custom_path_review',
                ),
                'default'       => 'merchandise-reviews',
                'tip'           => __( 'For the links for rating elements.', 'amazon-auto-links' ),
                'description'   => array(
                    __( 'The custom path set here will be applied to review links where <code>[custom-review-path]</code> is placed below.', 'amazon-auto-links' )
                        . ' ' . __( 'Forward slashes (<code>/</code>) are accepted.', 'amazon-auto-links' ),
                    site_url() . '/<code>[custom-review-path]</code>/<code>[asin]</code>'
                ),
            ),
        );
        
        $_oCreditFields = new AmazonAutoLinks_FormFields_Unit_Credit( $this->oFactory );
        $_aCreditFields = $_oCreditFields->get( $sFieldIDPrefix );             
        
        return array_merge(
            $_aFields,         // comes first
            $_aCreditFields    // appended
        );    
        
    }
      
}