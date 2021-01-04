<?php
/**
 * Provides methods to retrieve field definitions.
 * 
 * @since 3.4.0
 * @since 3.10.0 Changed the name from `AmazonAutoLinks_FormFields_Unit_Locale`.
 * @since 4.5.0  Change the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_Unit_Country extends AmazonAutoLinks_FormFields_Unit_Base {

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
                'label'             => AmazonAutoLinks_Locales::getLabels(),
                'default'           => 'US',
            ),          
        );
    }
  
}