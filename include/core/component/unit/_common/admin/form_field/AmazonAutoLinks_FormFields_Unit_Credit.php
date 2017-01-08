<?php
/**
 * Provides the definitions of auto-insert form fields for units.
 * 
 * @since           3.2.3
 */
class AmazonAutoLinks_FormFields_Unit_Credit extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'          => $sFieldIDPrefix . 'credit_link',
                'type'              => 'revealer',
                'select_type'       => 'radio',
                'label_min_width'   => '140px;',
                'title'             => __( 'Credit Link', 'amazon-auto-links' ),
                'label'             => array(                        
                    1   => __( 'On', 'amazon-auto-links' ),
                    0   => __( 'Off', 'amazon-auto-links' ),
                ),
                'tip'               => __( 'Inserts the credit link at the end of the unit output.', 'amazon-auto-links' ),
                'default'           => 1,
                'selectors'         => array(
                    1   => '.fieldrow_credit_link_type',                
                ),
            ),          
            array(
                'field_id'          => $sFieldIDPrefix . 'credit_link_type',
                'type'              => 'radio',
                'title'             => __( 'Credit Link Type', 'amazon-auto-links' ),
                'label'             => array(
                    0   => __( 'Normal', 'amazon-auto-links' ) 
                        . "<div style='width: 160px; height: 160px; margin-top: 1em;'>" 
                            . apply_filters( 'aal_filter_credit_link_0', '', $this->oOption )
                        . "</div>",
                    1   => __( 'Square Image', 'amazon-auto-links' ) 
                        . "<div style='width: 200px; height: 160px; margin-top: 1em;'>" 
                            . apply_filters( 'aal_filter_credit_link_1', '', $this->oOption )
                        . "</div>",
                    2   => __( 'Horizontal Image', 'amazon-auto-links' ) 
                        . "<div style='width: 320px; margin-top: 1em;'>" 
                            . apply_filters( 'aal_filter_credit_link_2', '', $this->oOption )
                        . "</div>",
                ),
                'label_min_width'   => '100px; vertical-align: top;',
                'default'           => 1,
                'hidden'            => true,
                'class'             => array(
                    'fieldrow'  => 'fieldrow_credit_link_type'
                ),
            )
        );
    }
  
}