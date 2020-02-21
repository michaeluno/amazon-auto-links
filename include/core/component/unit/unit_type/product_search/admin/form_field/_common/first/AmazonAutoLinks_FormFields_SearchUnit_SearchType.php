<?php
/**
 * Provides the definitions of form fields for the 'tag' unit type.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_SearchUnit_SearchType extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
            
        $_oOption      = $this->oOption;
        return array(
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'type'          => 'text',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>My Search Unit</code>',
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
                    'AU' => 'AU - ' . __( 'Australia', 'amazon-auto-links' ),
                ),
                'default' => 'US',
            ),                
            array(
                'field_id'      => $sFieldIDPrefix . 'associate_id',
                'type'          => 'text',
                'title'         => __( 'Associate ID', 'amazon-auto-links' ),
                'description'   => 'e.g. <code>miunosoft-20</code>',
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'Operation',
                'type'          => 'radio',
                'title'         => __( 'Types', 'amazon-auto-links' ),
                'label_min_width' => '100%',
                'label'         => array(                        
                    'SearchItems'     => '<strong>' . __( 'Products', 'amazon-auto-links' ) . '</strong> - ' . __( 'returns items that satisfy the search criteria in the title and descriptions.', 'amazon-auto-links' ),
                    'GetItems'        => '<span class=""><strong>' . __( 'Item Look-up', 'amazon-auto-links' ) . '</strong> - ' . __( 'returns some or all of the item attributes with the given item identifier.', 'amazon-auto-links' ) . '</span>',
//                    'SimilarityLookup'    => '<span class=""><strong>' . __( 'Similar Products', 'amazon-auto-links' ) . '</strong> - ' . __( 'returns products that are similar to one or more items specified.', 'amazon-auto-links' ) . '</span>', // @deprecated 3.9.0 PA-API 5 does not support this
                ),
                'default'       => 'SearchItems', // array( 'ItemSearch' => true ),

            ),
        );
    }
  
}