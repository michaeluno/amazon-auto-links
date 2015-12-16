<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_SearchUnit_ProceedButton extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => $sFieldIDPrefix . 'transient_id',
                'type'          => 'hidden',
                'hidden'        => true,
                'attributes'    => array(
                    'name'  => 'transient_id',
                ),
                'value'         => $GLOBALS[ 'aal_transient_id' ],
            ),
            array(
                'field_id'  => 'bounce_url',
                'type'      => 'hidden',
                'hidden'    => true,
                'value'     => add_query_arg( 
                    array( 
                        'transient_id' => $GLOBALS[ 'aal_transient_id' ],
                    ) + $_GET, 
                    admin_url( $GLOBALS['pagenow'] ) 
                ),
            ),                
            array(
                'field_id'      => $sFieldIDPrefix . 'submit_proceed',
                'type'          => 'submit',
                'value'             => __( 'Proceed', 'amazon-auto-links' ),
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