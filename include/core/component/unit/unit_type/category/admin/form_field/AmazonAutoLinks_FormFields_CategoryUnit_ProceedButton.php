<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since           3  
 * @remark          The admin page and meta box access it.
 */
class AmazonAutoLinks_FormFields_CategoryUnit_ProceedButton extends AmazonAutoLinks_FormFields_Base {

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
                // Set the name attribute manually to have no dimension.
                'attributes'    => array(
                    'name'  => 'transient_id'
                ),
                'value' => $GLOBALS[ 'aal_transient_id' ],
            ),
            array(
                'field_id'  => 'mode',
                'value'     => 1, // new, 0 for edit
                'type'      => 'hidden',
                'hidden'    => true,
            ),                 
            array(
                'field_id'      => $sFieldIDPrefix . 'submit_proceed_category_select',
                'type'          => 'submit',
                'value'             => __( 'Proceed', 'amazon-auto-links' ),
                'label_min_width'   => 0,
                'attributes'        => array(
                    'class' => 'button-secondary',
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),    
                'redirect_url'  => add_query_arg(
                    array(
                        'transient_id'  => $GLOBALS[ 'aal_transient_id' ],
                        'aal_action'    => 'select_category',
                        'tab'           => 'second',
                    )
                    + $_GET,
                    admin_url( $GLOBALS[ 'pagenow' ] )
                ),
            )
        );
    }
  
}