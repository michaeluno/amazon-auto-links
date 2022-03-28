<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Provides the form fields definitions.
 * 
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_Flat_FormFields_Text extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='' ) {
        return array(
            array(
                'field_id'     => '_text',
                // 'title'        => __( 'Text', 'amazon-auto-links' ),
                'type'         => 'inline_mixed',
                'class'        => array(
                    // 'fieldset'  => 'margin-left-1em',
                    'fieldset'  => 'margin-top-1point5em',
                    'field'     => 'dynamic-button-field',
                ),
                'content'      => array(
                    array(
                        'field_id'      => 'label',
                        'type'          => 'text',
                        'title'         => __( 'Label', 'amazon-auto-links' ),
                        'attributes'    => array(
                            'data-property' => '_button_label',
                            'style'         => 'min-width: 220px;',
                        ),
                        'default'       => __( 'Buy Now', 'amazon-auto-links' ),
                    ),
                    array(
                        'field_id'      => 'font_size',
                        'type'          => 'number',
                        'title'         => __( 'Font Size', 'amazon-auto-links' ),
                        'attributes'    => array(
                            'min'           => 0,
                            'data-property' => 'font-size',
                            'data-suffix'   => 'px',
                            'style'         => 'width: 80px;',
                        ),
                        'default'       => 13,
                    ),
                    array(
                        'field_id'      => 'color',
                        'type'          => 'color',
                        'title'         => __( 'Color', 'amazon-auto-links' ),
                        'default'       => '#ffffff',
                        'attributes'    => array(
                            'data-property' => 'color',
                        ),
                    ),
                    array(
                        'field_id'      => 'weight',
                        'type'          => 'select',
                        'title'         => __( 'Weight', 'amazon-auto-links' ),
                        'default'       => 400,
                        'attributes'    => array(
                            'select' => array(
                                'data-property' => 'font-weight',
                            ),
                        ),
                        'label'         => array(
                            // 'normal'    => __( 'Normal', 'amazon-auto-links' ),
                            // 'bold'      => __( 'Bold', 'amazon-auto-links' ),
                            // 'lighter'   => __( 'Lighter', 'amazon-auto-links' ),
                            // 'bolder'    => __( 'Bolder', 'amazon-auto-links' ),
                            '100'       => 100,
                            '200'       => 200,
                            '300'       => 300,
                            '400'       => 400,  // normal
                            '500'       => 500,
                            '600'       => 600,
                            '700'       => 700,  // bold
                            '800'       => 800,
                            '900'       => 900,
                            // 'inherit',
                            // 'initial',
                            // 'revert',
                            // 'unset',
                        ),
                    ),
                    array(
                        'field_id'          => 'margin_type',
                        'type'              => 'revealer',
                        'select_type'       => 'radio',
                        'title'             => __( 'Margin', 'amazon-auto-links' ),
                        'label_min_width' => '140',
                        'label'             => array(
                            'all'   => __( 'All', 'amazon-auto-links' ),
                            'each'  => __( 'Each', 'amazon-auto-links'),
                        ),
                        'selectors'     => array(
                            'all'   => '.text-margin-all',
                            'each'  => '.text-margin-each',
                        ),
                        'class'         => array(
                            'fieldset'  => 'margin-top-1point5em',
                            'field'     => 'dynamic-button-field',
                        ),
                        'attributes'    => array(
                            'data-property'         => '_text_margin_type',
                            'data-selector-suffix'  => '.button-label',
                        ),
                        'default'       => 'each',
                    ),
                    array(
                        'field_id'      => 'margin',
                        'content'       => array(
                            array(
                                'field_id'      => 'all',
                                'type'          => 'number',
                                'class'          => array(
                                    'fieldset'  => 'text-margin-all',
                                ),
                                'attributes'    => array(
                                    'data-property'        => 'margin',
                                    'data-selector-suffix' => '.button-label',
                                    'data-suffix'          => 'px',
                                ),
                                'default'       => 0,
                            ),
                            array(
                                'field_id'      => 'each',
                                'type'          => 'inline_mixed',
                                'class'          => array(
                                    'fieldset'  => 'text-margin-each',
                                ),
                                'content'       => array(
                                    array(
                                        'field_id'      => 'top',
                                        'type'          => 'number',
                                        'title'         => __( 'Top', 'amazon-auto-links' ),
                                        'attributes'    => array(
                                            'data-property'         => 'margin-top',
                                            'data-selector-suffix'  => '.button-label',
                                            'data-suffix'           => 'px',
                                        ),
                                        'default'       => 0,
                                    ),
                                    array(
                                        'field_id'      => 'right',
                                        'type'          => 'number',
                                        'title'         => __( 'Right', 'amazon-auto-links' ),
                                        'attributes'    => array(
                                            'data-property'         => 'margin-right',
                                            'data-selector-suffix'  => '.button-label',
                                            'data-suffix'           => 'px',
                                        ),
                                        'default'       => 8,
                                    ),
                                    array(
                                        'field_id'      => 'bottom',
                                        'type'          => 'number',
                                        'title'         => __( 'Bottom', 'amazon-auto-links' ),
                                        'attributes'    => array(
                                            'data-property'         => 'margin-bottom',
                                            'data-selector-suffix'  => '.button-label',
                                            'data-suffix'           => 'px',
                                        ),
                                        'default'       => 1,   // somehow flex's align-items: center is a bit off center
                                    ),
                                    array(
                                        'field_id'      => 'left',
                                        'type'          => 'number',
                                        'title'         => __( 'Left', 'amazon-auto-links' ),
                                        'attributes'    => array(
                                            'data-property'         => 'margin-left',
                                            'data-selector-suffix'  => '.button-label',
                                            'data-suffix'           => 'px',
                                        ),
                                        'default'       => 8,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
      
}