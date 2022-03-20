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
class AmazonAutoLinks_Button_Button2_FormFields_Background extends AmazonAutoLinks_FormFields_Base {

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
                    'none'      => __( 'None', 'amazon-auto-links' ),
                    'solid'     => __( 'Solid', 'amazon-auto-links' ),
                    'gradient'  => __( 'Gradient', 'amazon-auto-links' ),
                ),
                'selectors'     => array(
                    'none'      => '.none',
                    'solid'     => '.solid',
                    'gradient'  => '.gradient',
                ),
                'class'         => array(
                    'fieldrow'  => 'background_on',
                ),
                'attributes'    => array(
                    '.gradient' => array(
                        'data-property'         => 'background-gradient',
                        'data-control-display'  => 'background-gradient',
                        'data-switch'           => '.solid',
                    ),
                    '.solid' => array(
                        'data-property'         => 'background-solid',
                        'data-control-display'  => 'background-solid',
                        'data-switch'           => '.gradient',
                    ),
                ),
                'default'       => 'solid',
            ),
            array(
                'field_id'      => '_background',
                'type'          => 'color',
                'title'         => __( 'Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'solid background_on',
                ),
                'attributes'    => array(
                    'data-property' => 'background',
                ),
                'default'       => '#4997e5', // '#3498db',
            ),
            array(
                'field_id'      => '_bg_gradient_colors',
                'type'          => 'inline_mixed',
                'title'         => __( 'Color', 'amazon-auto-links' ),
                'class'         => array(
                    'fieldrow' => 'gradient background_on',
                ),
                'content'       => array(
                    array(
                        'field_id'      => '_bg_start_gradient',
                        'type'          => 'color',
                        'title'         => __( 'Start', 'amazon-auto-links' ),
                        'attributes'    => array(
                            'data-property' => 'bg-start-gradient',
                        ),
                        'default'       => '#4997e5', // '#3498db',
                    ),
                    array(
                        'field_id'      => '_bg_end_gradient',
                        'type'          => 'color',
                        'title'         => __( 'End', 'amazon-auto-links' ),
                        'attributes'    => array(
                            'data-property' => 'bg-end-gradient',
                        ),
                        'default'       => '#3f89ba', // '#2980b9',
                    )
                ),
            ),
        );
    }

}