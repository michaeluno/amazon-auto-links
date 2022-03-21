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
abstract class AmazonAutoLinks_Button_ButtonType_FormFields_Dimensions_Base extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     *
     * @since  5.2.0
     * @return array
     */
    public function get( $sFieldIDPrefix='', $sUnitType='' ) {
        return array(
            array(
                'field_id'      => $sFieldIDPrefix . '_dimensions',
                'title'         => __( 'Dimensions', 'amazon-auto-links' ),
                'class'     => array(
                    'field' => 'dynamic-button-field',
                ),
                'content'       => array(
                    array(
                        'field_id'      => 'width_toggle',
                        'type'          => 'revealer',
                        'select_type'   => 'checkbox',
                        'label'         => '<strong>' . __( 'Width', 'amazon-auto-links' ) . "<span class='title-colon'>:</span></strong>",
                        'selectors'     => '.box-width',
                        'attributes'    => array(
                            'data-property' => '_width_toggle',
                        ),
                        'default'       => 0,
                    ),
                    array(
                        'field_id'          => 'width',
                        'type'              => 'size',
                        'units'             => $this->getCSSSizeUnits(),
                        'hidden'            => true,
                        'default'           => array(
                            'size' => 148,
                            'unit' => 'px',
                        ),
                        'class'     => array(
                            'fieldset' => 'box-width',
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
                        'field_id'      => 'height_toggle',
                        'type'          => 'revealer',
                        'select_type'   => 'checkbox',
                        'label'         => '<strong>' . __( 'Height', 'amazon-auto-links' ) . "<span class='title-colon'>:</span></strong>",
                        'selectors'     => '.box-height',
                        'attributes'    => array(
                            'data-property' => '_height_toggle',
                        ),
                        'default'       => 0,
                    ),
                    array(
                        'field_id'          => 'height',
                        'type'              => 'size',
                        'units'             => $this->getCSSSizeUnits(),
                        'hidden'            => true,
                        'default'           => array(
                            'size' => 60,
                            'unit' => 'px',
                        ),
                        'class'     => array(
                            'fieldset' => 'box-height',
                        ),
                        'attributes' => array(
                            'size'  => array(
                                'data-property' => 'height',
                                'min'           => 0,
                            ),
                            'select' => array(
                                'data-property' => 'height',
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
      
}