<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 3
 */
class AmazonAutoLinks_FormFields_Button_Selector extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        return array(
            array(
                'field_id'          => $sFieldIDPrefix . 'button_id',
                'type'              => 'select',            
                'title'             => __( 'Select Button', 'amazon-auto-links' ),
                'class'             => array(
                    'fieldrow'      => 'button-select-row',
                ),   
                'tip'               => sprintf(
                    __( 'Select the button for the <code>%1$s</code> parameter of the Item Format option.', 'amazon-auto-links' ),
                    '%button%'
                ),
                'description'       => array(
                    sprintf(
                        __( 'Buttons can be created from <a href="%1$s">this screen</a>.', 'amazon-auto-links' ),
                        add_query_arg(
                            array(
                                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                            ),
                            admin_url( 'edit.php' )
                        )
                    ),                    
                ),
                // The label argument will be set with the 'field_definition_{...}' filter as it performs a database query.
                'after_field'       => "<div style='margin: 1em 0; width:100%; display: inline-block;'>"
                    . "<div class='iframe-button-preview-container' style='margin-left: auto; margin-right: auto; '>" // text-align:center;
                    . "</div>"
                . "</div>"
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'override_button_label',
                'type'              => 'revealer',
                'select_type'       => 'checkbox',
                'show_title_column' => false,
                'label'             => __( 'Override the button label.', 'amazon-auto-links' ),
                'selectors'         => '.button-label-field-row',
                'attributes'        => array(
                    'class' => 'override-button-label',
                ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'button_label',
                'type'              => 'text',
                'show_title_column' => false,
                'class'             => array(
                    'fieldrow' => 'button-label-field-row',
                ),
                'attributes'        => array(
                    'class' => 'button-label',
                ),
                'default'           => '',
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'button_type',
                'type'              => 'radio',            
                'title'             => __( 'Button Link Type', 'amazon-auto-links' ),
                'label'             => array(
                    0   => __( 'Link to the product page.', 'amazon-auto-links' ),
                    1   => __( 'Add to cart.', 'amazon-auto-links' ),
                ),
                'default'           => 1,
            ), 
        );

    }

}