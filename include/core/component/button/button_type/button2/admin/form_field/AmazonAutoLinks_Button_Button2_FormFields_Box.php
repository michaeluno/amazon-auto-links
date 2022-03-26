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
class AmazonAutoLinks_Button_Button2_FormFields_Box extends AmazonAutoLinks_Button_ButtonType_FormFields_Dimensions_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='' ) {
        $_aDimensions = parent::get( $sFieldIDPrefix, $sUnitType );
        $_aFieldsets  = array(
            // array(
            //     'field_id'      => '_dimensions',
            //     'title'         => __( 'Dimensions', 'amazon-auto-links' ),
            //     'content'       => array(
            //         array(
            //             'field_id'      => 'width_toggle',
            //             'type'          => 'revealer',
            //             'select_type'   => 'checkbox',
            //             'label'         => '<strong>' . __( 'Width', 'amazon-auto-links' ) . "<span class='title-colon'>:</span></strong>",
            //             'selectors'     => '.box-width',
            //             'class'         => array(
            //                 'field'     => 'dynamic-button-field',
            //             ),
            //             'attributes'    => array(
            //                 'data-property' => '_width_toggle',
            //             ),
            //             'default'       => 0,
            //         ),
            //         array(
            //             'field_id'          => 'width',
            //             'type'              => 'size',
            //             'units'             => $this->getCSSSizeUnits(),
            //             'default'           => array(
            //                 'size' => 148,
            //                 'unit' => 'px',
            //             ),
            //             'class'     => array(
            //                 'fieldset' => 'box-width',
            //                 'field'    => 'dynamic-button-field',
            //             ),
            //             'attributes' => array(
            //                 'size'  => array(
            //                     'data-property' => 'width',
            //                     'min'           => 0,
            //                 ),
            //                 'select' => array(
            //                     'data-property' => 'width',
            //                 ),
            //             ),
            //         ),
            //         array(
            //             'field_id'      => 'height_toggle',
            //             'type'          => 'revealer',
            //             'select_type'   => 'checkbox',
            //             'label'         => '<strong>' . __( 'Height', 'amazon-auto-links' ) . "<span class='title-colon'>:</span></strong>",
            //             'selectors'     => '.box-height',
            //             'class'         => array(
            //                 'field'     => 'dynamic-button-field',
            //             ),
            //             'attributes'    => array(
            //                 'data-property' => '_height_toggle',
            //             ),
            //             'default'       => 0,
            //         ),
            //         array(
            //             'field_id'          => 'height',
            //             'type'              => 'size',
            //             'units'             => $this->getCSSSizeUnits(),
            //             'default'           => array(
            //                 'size' => 60,
            //                 'unit' => 'px',
            //             ),
            //             'class'     => array(
            //                 'fieldset' => 'box-height',
            //                 'field'    => 'dynamic-button-field',
            //             ),
            //             'attributes' => array(
            //                 'size'  => array(
            //                     'data-property' => 'height',
            //                     'min'           => 0,
            //                 ),
            //                 'select' => array(
            //                     'data-property' => 'height',
            //                 ),
            //             ),
            //         ),
            //     ),
            // ),
            array(
                'field_id'      => '_padding_type',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Padding', 'amazon-auto-links' ),
                'label'         => array(
                    'all'   => __( 'All', 'amazon-auto-links' ),
                    'each'  => __( 'Each', 'amazon-auto-links'),
                ),
                'selectors'     => array(
                    'all'   => '.padding-all',
                    'each'  => '.padding-each',
                ),
                'class'         => array(
                    'field'     => 'dynamic-button-field',
                ),
                'attributes'    => array(
                    'data-property' => '_padding_type',
                ),
                'default'       => 'each',
            ),
            array(
                'field_id'      => '_padding',
                'class'         => array(
                    'field'     => 'dynamic-button-field',
                ),
                'content'       => array(
                    array(
                        'field_id'      => 'all',
                        'type'          => 'number',
                        'class'          => array(
                            'fieldset'  => 'padding-all',
                        ),
                        'attributes'    => array(
                            'data-property' => 'padding',
                            'data-suffix'   => 'px',
                            'min'           => 0,
                        ),
                        'default'       => 1,
                    ),
                    array(
                        'field_id'      => 'each',
                        'type'          => 'inline_mixed',
                        'class'          => array(
                            'fieldset'  => 'padding-each',
                        ),
                        'content'       => array(
                            array(
                                'field_id'      => 'top',
                                'type'          => 'number',
                                'title'         => __( 'Top', 'amazon-auto-links' ),
                                'attributes'    => array(
                                    'data-property' => 'padding-top',
                                    'data-suffix'   => 'px',
                                    'min'           => 0,
                                ),
                                'default'       => 8,
                            ),
                            array(
                                'field_id'      => 'right',
                                'type'          => 'number',
                                'title'         => __( 'Right', 'amazon-auto-links' ),
                                'attributes'    => array(
                                    'data-property' => 'padding-right',
                                    'data-suffix'   => 'px',
                                    'min'           => 0,
                                ),
                                'default'       => 16,
                            ),
                            array(
                                'field_id'      => 'bottom',
                                'type'          => 'number',
                                'title'         => __( 'Bottom', 'amazon-auto-links' ),
                                'attributes'    => array(
                                    'data-property' => 'padding-bottom',
                                    'data-suffix'   => 'px',
                                    'min'           => 0,
                                ),
                                'default'       => 8,
                            ),
                            array(
                                'field_id'      => 'left',
                                'type'          => 'number',
                                'title'         => __( 'Left', 'amazon-auto-links' ),
                                'attributes'    => array(
                                    'data-property' => 'padding-left',
                                    'data-suffix'   => 'px',
                                    'min'           => 0,
                                ),
                                'default'       => 16,
                            ),
                        ),
                    ),
                ),
            ),            
        );
        return array_merge( $_aDimensions, $_aFieldsets );
    }
      
}