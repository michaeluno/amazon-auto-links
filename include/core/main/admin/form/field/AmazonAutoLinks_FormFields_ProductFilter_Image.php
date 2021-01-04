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
class AmazonAutoLinks_FormFields_ProductFilter_Image extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => 'skip_no_image',
                'type'          => 'checkbox',
                'title'         => __( 'Disallow No Thumbnail', 'amazon-auto-links' ),
                'label'   => __( 'Do not display products with no thumbnail.', 'amazon-auto-links' ),
            ),               
        );
        
    }
  
}