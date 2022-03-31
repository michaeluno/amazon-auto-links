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
class AmazonAutoLinks_Button_Flat_FormFields_Background extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => '_background_type',
                'type'          => 'revealer',
                'select_type'   => 'radio',
                'title'         => __( 'Type', 'amazon-auto-links' ),
                'label'         => array(
                    'none'       => __( 'None', 'amazon-auto-links' ),
                    'solid'      => __( 'Solid', 'amazon-auto-links' ),
                    'gradient'   => __( 'Gradient', 'amazon-auto-links' ),   // Not implemented yet due to too much complexity in coding
                ),
                'selectors'     => array(
                    'none'       => '.none',
                    'solid'      => '.revealer-background-solid',
                    'gradient'   => '.revealer-background-gradient',
                ),
                'class'         => array(
                    'field'      => 'dynamic-button-field',
                ),
                'attributes'    => array(
                    'data-property' => '_background_type',
                ),
                'default'       => 'solid',
            ),
            array(
                'field_id'      => '_background_color',
                'type'          => 'color',
                'title'         => __( 'Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow'      => 'revealer-background-solid',
                    'field'         => 'dynamic-button-field',
                ),
                'attributes'    => array(
                    'data-property' => '_background_color',
                ),
                'default'       => '#4997e5', // '#3498db',
            ),
            array(
                'field_id'      => '_background_gradient',
                'class'         => array(
                    'fieldrow' => 'revealer-background-gradient',
                    'field'    => 'dynamic-button-field '
                        . 'background-gradient',    // used in JavaScript
                ),
                'content'       => array(
                    array(
                        'field_id'          => 'direction',
                        'type'              => 'select2',
                        'title'             => __( 'Direction', 'amazon-auto-links' ),
                        'label'             => array(
                            'to_bottom'        => __( 'To Bottom', 'amazon-auto-links' ),
                            'to_bottom_left'   => __( 'To Bottom Left', 'amazon-auto-links' ),
                            'to_left'          => __( 'To Left', 'amazon-auto-links' ),
                            'to_top_left'      => __( 'To Top Left', 'amazon-auto-links' ),
                            'to_top'           => __( 'To Top', 'amazon-auto-links' ),
                            'to_top_right'     => __( 'To Top Right', 'amazon-auto-links' ),
                            'to_right'         => __( 'To Right', 'amazon-auto-links' ),
                            'to_bottom_right'  => __( 'To Bottom Right', 'amazon-auto-links' ),
                        ),
                        'icon'      => array(
                            'to_bottom'        => '<span class="dashicons dashicons-arrow-down-alt"></span>',
                            'to_bottom_left'   => '<span class="dashicons dashicons-arrow-down-alt rotate-45"></span>',
                            'to_left'          => '<span class="dashicons dashicons-arrow-left-alt"></span>',
                            'to_top_left'      => '<span class="dashicons dashicons-arrow-left-alt rotate-45"></span>',
                            'to_top'           => '<span class="dashicons dashicons-arrow-up-alt"></span>',
                            'to_top_right'     => '<span class="dashicons dashicons-arrow-up-alt rotate-45"></span>',
                            'to_right'         => '<span class="dashicons dashicons-arrow-right-alt"></span>',
                            'to_bottom_right'  => '<span class="dashicons dashicons-arrow-right-alt rotate-45"></span>',
                        ),
                        'attributes'    => array(
                            'select' => array(
                                'data-property' => '_background_image_gradient_direction',
                            )
                        ),
                    ),
                    array(
                        'field_id'          => 'colors',
                        'title'             => __( 'Colors', 'amazon-auto-links' ),
                        'repeatable'        => true,
                        'type'              => 'color',
                        'attributes'    => array(
                            'data-property' => '_background_image_gradient_colors',
                        ),
                    ),
                ),
            ),
        );
    }

}