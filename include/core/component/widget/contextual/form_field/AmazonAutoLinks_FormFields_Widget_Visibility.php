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
 * Provides the form fields definitions.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_Widget_Visibility extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        $_aFields       = array(
            array(
                'field_id'      => $sFieldIDPrefix. 'width',
                'type'          => 'number',
                'title'         => __( 'Width', 'amazon-auto-links' ),
                'default'       => 100,
            ), 
            array(
                'field_id'      => $sFieldIDPrefix .'width_unit',
                'type'          => 'select',
                'show_title_column' => false,
                // 'title'         => __( 'Width Unit', 'amazon-auto-links' ),
                'label'         => array(
                    'px'    => 'px', 
                    '%'     => '%', 
                    'em'    => 'em'
                ),                
                'default'       => '%',
                'description'   => __( 'Set 0 for no limit.', 'amazon-auto-links' ),               
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'height',
                'type'          => 'number',
                'title'         => __( 'Height', 'amazon-auto-links' ),
                'default'       => 400,
            ),       
            array(
                'field_id'      => $sFieldIDPrefix . 'height_unit',
                'type'          => 'select',
                'show_title_column' => false,
                // 'title'         => __( 'Height Unit', 'amazon-auto-links' ),
                'label'         => array(
                    'px'    => 'px', 
                    '%'     => '%', 
                    'em'    => 'em'
                ),                
                'default'       => 'px',
                'description'   => __( 'Set 0 for no limit.', 'amazon-auto-links' ),                
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'available_page_types',
                'type'          => 'checkbox',
                'title'         => __( 'Available Page Types', 'amazon-auto-links' ),
                'label'         => array(
                    'home'              => __( 'The home page.', 'amazon-auto-links' ),
                    'front'             => __( 'The front page.', 'amazon-auto-links' ),
                    'singular'          => __( 'Single pages.', 'amazon-auto-links' ),
                    'post_type_archive' => __( 'Post type archive pages.', 'amazon-auto-links' ),
                    'taxonomy'          => __( 'Taxonomy archive pages.', 'amazon-auto-links' ),
                    'date'              => __( 'Date archive pages.', 'amazon-auto-links' ),
                    'author'            => __( 'Author pages.', 'amazon-auto-links' ),
                    'search'            => __( 'Search result pages.', 'amazon-auto-links' ),
                    '404'               => __( 'The 404 page.', 'amazon-auto-links' ),
                ),
                'default'       => array(
                    'home'              => false,
                    'front'             => false,
                    'singular'          => true,
                    'post_type_archive' => false,
                    'taxonomy'          => false,
                    'date'              => false,
                    'author'            => false,
                    'search'            => false,
                    '404'               => false,
                ),
            ),                                  
            array()
        );

       
        return $_aFields;
        
    }
      
}