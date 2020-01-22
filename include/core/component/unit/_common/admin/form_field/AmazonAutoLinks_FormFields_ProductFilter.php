<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_ProductFilter extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
            
        return array(
            array(
                'field_id'      => 'white_list',
                'type'          => 'textarea',
                'title'         => __( 'White List', 'amazon-auto-links' ),
                'tip'           => __( 'Enter characters separated by commas or in each line.', 'amazon-auto-links' )
                    . ' ' . __( 'Product links that do not contain the white list items will be omitted.', 'amazon-auto-links' ),
                'attributes'    => array(   
                    'style' => 'width: 96%;',               
                    'field' => array(
                        'style' => 'width: 100%;'
                    ),                         
                ), 
                'label' => array(    
                    'asin'          => 'ASIN',
                    'title'         => __( 'Title', 'amazon-auto-links' ),
                    'description'   => __( 'Description', 'amazon-auto-links' ),
                ),
                'after_input' => array(
                    'asin'        => '<p><span class="description">e.g. <code>120530902X, BO1000000X</code></span></p>',
                    'title'       => '<p><span class="description">e.g. <code>Laptop</code></span></p>',
                    'description' => '</p><span class="description">e.g. <code>$100</code></span></p>',
                ),
            ),
            array(
                'field_id'      => 'black_list',
                'title'         => __( 'Black List', 'amazon-auto-links' ),
                'type'          => 'textarea',
                'tip'           => __( 'Enter characters separated by commas or in each line.', 'amazon-auto-links' )
                    . ' ' . __( 'Product links that contain the black list items will be omitted.', 'amazon-auto-links' ),
                'attributes'    => array(   
                    'style' => 'width: 96%;',               
                    'field' => array(
                        'style' => 'width: 100%;'
                    ),                    
                ),            
                'label'         => array(    
                    'asin'          => 'ASIN',
                    'title'         => __( 'Title', 'amazon-auto-links' ),
                    'description'   => __( 'Description', 'amazon-auto-links' ),
                ),
                'after_input' => array(
                    'asin'          => '<p><span class="description">e.g. <code>020530902X, BO0000000X</code></span></p>',
                    'title'         => '<p><span class="description">e.g. <code>xxx, adult</code></span></p>',
                    'description'   => '<p><span class="description">e.g. <code>xxx, $0.</code></span></p>',
                ),
            ),
            array(
                'field_id'      => 'case_sensitive',
                'title'         => __( 'Case Sensitive', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'default'       => 0,
                'tip'           => __( 'If this is on, upper cases and lower cases of characters have to match to find the given string.', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>' . __( 'Off', 'amazon-auto-links' ) . '</code>',
            ),
            array(
                'field_id'      => 'no_duplicate',
                'title'         => __( 'Prevent Duplicates', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'default'       => 0,
                'tip'           => __( 'If this is on, the same products that are already loaded will not be displayed among different units', 'amazon-auto-links' )
                    . ' ' . __( 'Default', 'amazon-auto-links' ) . ': <code>' . __( 'On', 'amazon-auto-links' ) . '</code>',
            ),                
        );
    }
  
}