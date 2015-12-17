<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_AutoInsert_Static extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => $sFieldIDPrefix . strtolower( get_class( $this ) ),
                'type'          => 'hidden',
                'attributes'    => array(
                    'name' => '', // disables the value to be sent to the form
                ),
                'show_title_column'    => false,
                'before_field'  => "<h3>" 
                        .  __( 'Static Insertion', 'amazon-auto-links' )
                    . "</h3>"
                    . "<p class='description'>"
                        . __( 'Use this to insert unit outputs in the database when a post gets published.', 'amazon-auto-links' )
                        . ' ' . __( 'This means product links stay after plugin gets deactivated.', 'amazon-auto-links' )
                    . "</p>",
            ),       
            // @3.3.0 Deprecated. The same field id exists in another section.
            // array(
                // 'field_id'      => $sFieldIDPrefix . 'unit_ids',
                // 'title'         => __( 'Select Units', 'amazon-auto-links' ),        
                // 'type'          => 'select',
                // 'is_multiple'   => true,
                // 'label'         => $this->getPostsLabelsByPostType(
                    // AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
                // ),
            // ),
            array(
                'field_id'      => $sFieldIDPrefix . 'static_areas',
                'title'         => __( 'Areas', 'amazon-auto-links' ),
                'type'          => 'checkbox',
                'label'         => $this->getPredefinedFiltersForStatic(),
                'description'   => __( 'Make sure you pick appropriate post types in the below sections of Where to Enable/Disable.', 'amazon-auto-links' ),
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'static_position',
                'title'         => __( 'Positions ', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(
                    'above'   => __( 'Above', 'amazon-auto-links' ),
                    'below'   => __( 'Below', 'amazon-auto-links' ),
                    'both'    => __( 'Both', 'amazon-auto-links' ),
                ),
                'description'   => __( 'Determines whether the items are placed before or after (above or below) the area. This does not take effect for action hooks.', 'amazon-auto-links' ),
            ),       
        );
    }
    
   
  
}