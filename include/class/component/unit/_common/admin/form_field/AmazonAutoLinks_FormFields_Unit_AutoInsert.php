<?php
/**
 * Provides the definitions of auto-insert form fields for units.
 * 
 * @since           3  
 * @remark          The admin page and meta box access it.
 */
class AmazonAutoLinks_FormFields_Unit_AutoInsert extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get() {
                        
        return array(    
            array(
                'field_id'      => 'auto_insert',
                'title'         => __( 'Auto Insert', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(                        
                    1        => __( 'On', 'amazon-auto-links' ),
                    0        => __( 'Off', 'amazon-auto-links' ),
                ),
                'description'   => __( 'Set it On to insert product links into post and pages automatically. More advanced options can be configured later.', 'amazon-auto-links' ),
                'default'       => 1,
            ),        
        );
    }
  
}