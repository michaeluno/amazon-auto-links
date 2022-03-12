<?php
/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Image_FormFields_Dimension extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='' ) {
        $_aUnits = array(
            'px' => 'px',
            'em' => 'em',
            '%'  => '%',
        );
        return array(
            array(
                'field_id'          => $sFieldIDPrefix . '_width',
                'type'              => 'size',
                'title'             => __( 'Width', 'amazon-auto-links' ),
                'units'             => $_aUnits,
                'default'           => array(
                    'size' => 148,
                    'unit' => 'px',
                ),
                'class'     => array(
                    'field' => 'dynamic-button-field',
                    // 'input' => 'dynamic-button'  // causes the select and remove button broken
                ),
                'attributes' => array(
                    'size'  => array(
                        'data-property' => 'width',
                    ),
                    'select' => array(
                        'data-property' => 'width',
                    ),
                ),
            ),
            array(
                'field_id'          => $sFieldIDPrefix . '_height',
                'type'              => 'size',
                'title'             => __( 'Height', 'amazon-auto-links' ),
                'units'             => $_aUnits,
                'default'           => array(
                    'size' => 40,
                    'unit' => 'px',
                ),
                'class'     => array(
                    'field' => 'dynamic-button-field',
                    // 'input' => 'dynamic-button'  // causes the select and remove button broken
                ),
                'attributes' => array(
                    'size'  => array(
                        'data-property' => 'height',
                    ),
                    'select' => array(
                        'data-property' => 'height',
                    ),
                ),
            ),
            // array(
            //     'field_id'          => $sFieldIDPrefix . '_max_width',
            //     'type'              => 'size',
            //     'title'             => __( 'Maximum Width', 'amazon-auto-links' ),
            //     'units'             => $_aUnits,
            //     'default'           => array(
            //         'size' => 200,
            //         'unit' => 'px',
            //     ),
            //     'class'     => array(
            //         'field' => 'dynamic-button-field',
            //         // 'input' => 'dynamic-button'  // causes the select and remove button broken
            //     ),
            //     'attributes' => array(
            //         'size'  => array(
            //             'data-property' => 'max-width',
            //         ),
            //         'select' => array(
            //             'data-property' => 'max-width',
            //         ),
            //     ),
            // ),
            // array(
            //     'field_id'          => $sFieldIDPrefix . '_max_height',
            //     'type'              => 'size',
            //     'title'             => __( 'Maximum Height', 'amazon-auto-links' ),
            //     'units'             => $_aUnits,
            //     'default'           => array(
            //         'size' => 100,
            //         'unit' => 'px',
            //     ),
            //     'class'     => array(
            //         'field' => 'dynamic-button-field',
            //         // 'input' => 'dynamic-button'  // causes the select and remove button broken
            //     ),
            //     'attributes' => array(
            //         'size'  => array(
            //             'data-property' => 'max-height',
            //         ),
            //         'select' => array(
            //             'data-property' => 'max-height',
            //         ),
            //     ),
            // ),
        );
    }
      
}