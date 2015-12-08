<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_ItemLookupUnit_Main extends AmazonAutoLinks_FormFields_SearchUnit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $aUnitOptions=array() ) {
            
        $_oOption      = $this->oOption;
        $aUnitOptions  = $aUnitOptions + array( 'country' => null );
        $_bUPCAllowed  = 'CA' !== $aUnitOptions[ 'country' ];
        $_bISBNAllowed = 'US' === $aUnitOptions[ 'country' ];
         
        $_aFields       =  array(
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'item_lookup',
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'type'          => 'text',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'ItemId',
                'type'          => 'textarea',
                'title'         => __( 'Item ID', 'amazon-auto-links' ),
                'attributes'    => array(
                    'size' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'description'   => __( 'Enter the ID(s) of the product per line or use the <code>,</code> (comma) characters to delimit the items.', 'amazon-auto-links' ) 
                    . ' e.g. <code>B009ZVO3H6, B0043D2DZA</code>',
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
                'field_id'      => $sFieldIDPrefix . 'IdType',
                'type'          => 'radio',
                'title'         => __( 'ID Type', 'amazon-auto-links' ),
                'label'         => array(
                    'ASIN'  => 'ASIN',
                    'SKU'   => 'SKU',
                    'UPC'   => '<span class="' . ( $_bUPCAllowed ? "" : "disabled" ) . '">UPC <span class="description">(' . __( 'Not available in the CA locale.', 'amazon-auto-links' ) . ')</span></span>',
                    'EAN'   => 'EAN',
                    'ISBN'  => '<span class="' . ( $_bISBNAllowed ? "" : "disabled" ) . '">ISBN <span class="description">(' . __( 'The US locale only, when the search index is Books.', 'amaozn-auto-links' ) .')</span></span>',
                ),
                'attributes' => array(              
                    'UPC' => array(
                        'disabled' => $_bUPCAllowed 
                            ? null 
                            : 'disabled',
                    ),                
                    'ISBN' => array(
                        'disabled' => $_bISBNAllowed 
                            ? null 
                            : 'disabled',
                    ),                                    
                ),
                'default'       => 'ASIN',
            ),                                    
            array(
                'field_id'      => $sFieldIDPrefix . 'Operation',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'ItemLookup',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'country',
                'type'          => 'text',
                'title'         => __( 'Locale', 'amazon-auto-links' ),
                'attributes'    => array(
                    'readonly' => true,
                ),
            ),                    
            array(
                'field_id'      => $sFieldIDPrefix . 'associate_id',
                'type'          => 'text',
                'title'         => __( 'Associate ID', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>miunosoft-20</code>',
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'SearchIndex',
                'type'          => 'select',
                'title'         => __( 'Categories', 'amazon-auto-links' ),            
                'label'         => AmazonAutoLinks_Property::getSearchIndexByLocale( 
                    isset( $aUnitOptions['country'] ) 
                        ? $aUnitOptions['country'] 
                        : null 
                    ),
                'default'       => 'All',
                'tip'           => __( 'Select the category to limit the searching area.', 'amazon-auto-links' ),
                'description'   => __( 'If the above ID Type is ISBN, this will be automatically set to Books.', 'amazon-auto-links' )
                    . ' ' . __( 'If the ID Type is ASIN this option will not take effect.', 'amazon-auto-links' ),
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
            )          
        );

        $_oCreditFields = new AmazonAutoLinks_FormFields_Unit_Credit;
        $_aCreditFields = $_oCreditFields->get( $sFieldIDPrefix );             
        
        return array_merge(
            $_aFields,          // comes first
            $_aCreditFields    // appendded
        );        
        
    }
          
}