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
class AmazonAutoLinks_Button_Button2_FormFields_Box extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'          => '_width',
                'type'              => 'size',
                'title'             => __( 'Width', 'amazon-auto-links' ),
                'units'             => array(
                    'px' => 'px',
                    'em' => 'em',
                    '%'  => '%',
                ),
                'default'           => array(
                    'size' => 148,
                    'unit' => 'px',
                ),
                'class'     => array(
                    'field' => 'dynamic-button-field',
                ),
                'attributes' => array(
                    'size'  => array(
                        'data-property' => 'width',
                        'min'           => 0,
                    ),
                    'select' => array(
                        'data-property' => 'width',
                    ),
                ),
            ),
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
                    // 'fieldset' => 'button-icon-',
                ),
                'default'       => 'all',
            ),
            array(
                'field_id'      => '_padding',
                'class'         => array(
                    // 'fieldset' => 'button-icon-',
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
                                    'min'           => 0,
                                ),
                                'default'       => 1,
                            ),
                            array(
                                'field_id'      => 'right',
                                'type'          => 'number',
                                'title'         => __( 'Right', 'amazon-auto-links' ),
                                'attributes'    => array(
                                    'data-property' => 'padding-right',
                                    'min'           => 0,
                                ),
                                'default'       => 1,
                            ),
                            array(
                                'field_id'      => 'bottom',
                                'type'          => 'number',
                                'title'         => __( 'Bottom', 'amazon-auto-links' ),
                                'attributes'    => array(
                                    'data-property' => 'padding-bottom',
                                    'min'           => 0,
                                ),
                                'default'       => 1,
                            ),
                            array(
                                'field_id'      => 'left',
                                'type'          => 'number',
                                'title'         => __( 'Left', 'amazon-auto-links' ),
                                'attributes'    => array(
                                    'data-property' => 'padding-left',
                                    'min'           => 0,
                                ),
                                'default'       => 1,
                            ),
                        ),
                    ),
                ),
            ),            
        );
    }
      
}