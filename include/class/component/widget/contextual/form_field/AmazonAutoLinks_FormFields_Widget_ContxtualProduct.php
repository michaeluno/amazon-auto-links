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
                'tip'           => array(
                    __( 'Add additional search keywords, separated by commas.', 'amazon-auto-links' ),
                    ' e.g. <code>' . __( 'laptop, desktop', 'amazon-auto-links' ) . '</code>',
                ),                   
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
        );
        
        return $_aFields;
           
    }
      
}