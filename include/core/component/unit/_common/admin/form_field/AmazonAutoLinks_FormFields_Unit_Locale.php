<?php
/**
 * Provides methods to retrieve field definitions.
 * 
 * @since           3.4.0
 */
class AmazonAutoLinks_FormFields_Unit_Locale extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'          => $sFieldIDPrefix . 'country',
                'type'              => 'select',
                'title'             => __( 'Country', 'amazon-auto-links' ),
                'type'              => 'select',
                'label'             => array(
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
                    'AU' => 'AU - ' . __( 'Australia', 'amazon-auto-links' ),   // 3.5.5+
                ),
                'default'       => 'US',
            ),          
        );
    }
  
}