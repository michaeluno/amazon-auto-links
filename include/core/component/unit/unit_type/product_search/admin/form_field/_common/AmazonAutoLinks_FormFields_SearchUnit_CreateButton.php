<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since  3
 * @since  4.5.0    Changed the parent class from `AmazonAutoLinks_FormFields_Base` to `AmazonAutoLinks_FormFields_Unit_Base`.
 */
class AmazonAutoLinks_FormFields_SearchUnit_CreateButton extends AmazonAutoLinks_FormFields_Unit_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $aUnitOptions=array() ) {
        return array(
            array(
                'field_id'      => $sFieldIDPrefix . 'submit_create',
                'type'          => 'submit',
                'value'             => __( 'Create', 'amazon-auto-links' ),
                'label_min_width'   => 0,
                'attributes'        => array(
                    'class' => 'button-secondary',
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),         
            ),
        );
    }
  
}