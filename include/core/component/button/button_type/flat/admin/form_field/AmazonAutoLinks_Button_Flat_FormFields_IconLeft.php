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
class AmazonAutoLinks_Button_Flat_FormFields_IconLeft extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'          => '_icon_left',
                'class'             => array(
                    'fieldrow'  => 'fieldrow-button-icon-left',
                    'field'     => 'dynamic-button-field fields-button-icon-left',
                ),
                'content'           => $this->_getIconNestedFieldsets(
                    'left',
                    true,
                    AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/cart.svg', true )
                ),
            ),
        );
    }
        /**
         * @since  5.2.0
         * @param  string  $sSuffix
         * @param  boolean $bDefault         Whether it is enabled by default.
         * @param  string  $sDefaultImageSRC The default image URL.
         * @return array
         */
        protected function _getIconNestedFieldsets( $sSuffix, $bDefault=false, $sDefaultImageSRC='' ) {
            return array(
                array(
                    'field_id'          => 'enable',
                    'type'              => 'revealer',
                    'select_type'       => 'radio',
                    'title'             => __( 'Enable', 'amazon-auto-links' ),
                    'label'             => array(
                        1 => __( 'On', 'amazon-auto-links' ),
                        0 => __( 'Off', 'amazon-auto-links' ),
                    ),
                    'selectors'         => array(
                        1 => '.button-icon-' . $sSuffix,
                        // 0 => '.non-existent',
                    ),
                    'attributes'        => array(
                        'data-property' => '_icon_toggle_' . $sSuffix,
                    ),
                    'default'           => ( integer ) $bDefault,
                ),
                array(
                    'field_id'          => 'image_type',
                    'type'              => 'revealer',
                    'select_type'       => 'radio',
                    'title'             => __( 'Type', 'amazon-auto-links' ),
                    'label'             => array(
                        'svg_file'     => __( 'SVG File', 'amazon-auto-links' ),
                        'image_file'   => __( 'Image File', 'amazon-auto-links' ),
                        // maybe in the future, implement these. But these are complicated to implement
                        // 'svg_html'        => 'SVG HTML Mark-up', :this requires a complicated sanitization process, to store it in the database plus display it in front-end
                        // 'svg_splice'      => 'Specify an icon ID from an SVG file', : for this, the user must know the ID of the icon element in the SVG file. This requires at least two fields; one is for the icon ID and the other is for the SVG file path.
                        // 'font_icon_css'   => 'Set by a CSS class', : for this, it is assumed that the user knows a class name of an icon. And this requires to dynamically set the entered class name to the icon element class attribute.
                        // 'font_icon_glyph' => 'Pick up an icon from a font file that contains icon lists', : it's hard to know what glyph index are used
                    ),
                    'selectors'         => array(
                        'svg_file'      => '.svg-file-' . $sSuffix,
                        'image_file'    => '.image-file-' . $sSuffix,
                    ),
                    'class'             => array(
                        'fieldset' => 'button-icon-' . $sSuffix,
                        'field'    => 'dynamic-button-field',
                    ),
                    'attributes'        => array(
                        'data-property' => '_icon_image_type_' . $sSuffix,
                    ),
                    'default'           => 'svg_file',
                ),
                array(
                    'field_id'          => 'svg_file',
                    'type'              => 'image',
                    'show_title_column' => true,
                    'class'             => array(
                        'fieldset' => 'margin-left-1em button-icon-' . $sSuffix . ' ' . 'svg-file-' . $sSuffix,
                        'field'    => 'dynamic-button-field',
                    ),
                    'show_preview'      => false,
                    'hidden'            => true,
                    'description'       => sprintf(
                        /* translators: 1: A plugin URL that supports SVG uploads */
                        __( 'To allow SVG files to be uploaded, install and activate a third-party plugin such as <a href="%1$s" target="_blank">this one</a>.', 'amazon-auto-links' ),
                        esc_url( 'https://wordpress.org/plugins/safe-svg/' )
                    ),
                    'attributes'        => array(
                        'data-property'        => '_icon_svg_file_' . $sSuffix,
                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                        'input'                => array(
                            'style' => 'min-width: 600px; max-width: 100%;',
                        ),
                    ),
                    'default'           => $sDefaultImageSRC,
                ),
                array(
                    'field_id'          => 'svg_mask',
                    'type'              => 'revealer',
                    'select_type'       => 'checkbox',
                    'label'             => __( 'Set color.', 'amazon-auto-links' ),
                    'selectors'         => array(
                        0 => '.svg-color-' . $sSuffix,
                        1 => '',
                    ),
                    'class'             => array(
                        'fieldset' => 'margin-left-0point5em button-icon-' . $sSuffix // indent 0.5 as revealer fields are double-nested so it results in indent 1
                            . ' ' . 'svg-file-' . $sSuffix,
                        'field'    => 'dynamic-button-field',
                    ),
                    'attributes'        => array(
                        'data-property' => '_icon_svg_mask_toggle_' . $sSuffix,
                    ),
                    'default'           => true,
                ),
                array(
                    'field_id'          => 'svg_color',
                    'type'              => 'color',
                    'attributes'        => array(
                        'data-property' => '_icon_svg_mask_' . $sSuffix,
                    ),
                    'class'             => array(
                        'fieldset' => 'margin-left-2em button-icon-' . $sSuffix
                            . ' ' . 'svg-file-' . $sSuffix,
                        'fields'   => 'svg-color-' . $sSuffix,
                        'field'    => 'dynamic-button-field',
                    ),
                    'default'           => '#ffffff',
                ),

                array(
                    'field_id'          => 'image_file',
                    'type'              => 'image',
                    'show_title_column' => true,
                    'class'             => array(
                        'fieldset' => 'margin-left-1em button-icon-' . $sSuffix . ' ' . 'image-file-' . $sSuffix,
                        'field'    => 'dynamic-button-field',
                    ),
                    'show_preview'      => false,
                    'hidden'            => true,
                    'attributes'        => array(
                        'data-property'        => '_icon_image_file_' . $sSuffix,
                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                        'input'                => array(
                            'style' => 'min-width: 600px; max-width: 100%;',
                        ),
                    ),
                    'default'           => '',
                ),

                // @remark not implemented yet. this should be enabled with the `font_icon_css` image type
                // array(
                //     'field_id'          => 'override_class_attribute',
                //     'type'              => 'revealer',
                //     'select_type'       => 'checkbox',
                //     'label'             => __( 'Override the class attribute of the icon element.', 'amazon-auto-links' ),
                //     'class'             => array(
                //         'fieldset' => 'margin-left-0point5em button-icon-' . $sSuffix,
                //     ),
                //     'selectors'         => '.button-icon-class-attribute-' . $sSuffix,
                //     'default'           => 0,
                // ),
                // array(
                //     'field_id'          => 'class_attribute',
                //     'type'              => 'text',
                //     'hidden'            => true,
                //     'class'             => array(
                //         'fieldset' => 'margin-left-2em button-icon-' . $sSuffix . ' button-icon-class-attribute-' . $sSuffix,
                //         'field'    => 'button-icon-class-attribute-' . $sSuffix,
                //     ),
                //     'attributes'        => array(
                //         'data-property' => '_class_attribute_icon_' . $sSuffix,
                //     ),
                //     'default'           => 'button-icon-' . $sSuffix,
                // ),
                array(
                    'field_id'          => 'size_constraint',
                    'type'              => 'revealer',
                    'select_type'       => 'checkbox',
                    'title'             => __( 'Size', 'amazon-auto-links' ),
                    'label'             => __( 'Same as the button label.', 'amazon-auto-links' ),
                    'hidden'            => true,
                    'selectors'         => array(
                        0 => '.revealer-size-constraint-' . $sSuffix
                              // . ', '
                            // . '.icon-size-all-'  . $sSuffix . ', '
                            // . '.icon-size-each-' . $sSuffix
                        ,
                        // 1 => '.empty-revealer-element',
                    ),
                    'class'             => array(
                        'fieldset' => 'button-icon-' . $sSuffix,
                    ),
                    'attributes'        => array(
                        'data-property' => '_icon_size_constraint_' . $sSuffix
                    ),
                    'default'           => 1,
                    // 'after_field' => "<span class='empty-revealer-element'></span>"
                ),
                array(
                    'field_id'          => 'size_type',
                    'type'              => 'revealer',
                    'select_type'       => 'radio',
                    'hidden'            => true,
                    'label'             => array(
                        'all'   => __( 'All', 'amazon-auto-links' ),
                        'each'  => __( 'Each', 'amazon-auto-links'),
                    ),
                    'selectors'     => array(
                        'all'   => '.icon-size-all-'  . $sSuffix ,
                        'each'  => '.icon-size-each-' . $sSuffix ,
                    ),
                    'class'             => array(
                        'fieldset' => 'button-icon-' . $sSuffix,
                        'fields'   => 'revealer-size-constraint-' . $sSuffix,
                    ),
                    'attributes'        => array(
                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                        'data-property'        => '_icon_size_type_' . $sSuffix,
                    ),
                    'default'           => 'all',
                ),
                array(
                    'field_id'      => 'size',
                    'class'         => array(
                        'fieldset' => 'margin-left-1em '     // to indent
                            . 'fieldset-button-icon-size '  // for CSS styling
                            // . 'revealer-size-constraint-' . $sSuffix   // for the size constraint revealer field
                            . 'button-icon-' . $sSuffix,    // for the revealer script
                        'fields' => 'revealer-size-constraint-' . $sSuffix,   // for the size constraint revealer field
                    ),
                    'type'          => 'inline_mixed',
                    'content'       => array(
                        array(
                            'field_id'          => 'all',
                            'type'              => 'size',
                            'units'             => $this->getCSSSizeUnits(),
                            'hidden'            => true,
                            'default'           => array(
                                // 'size' => 148,
                                'unit' => 'px',
                            ),
                            'class'     => array(
                                'fieldset' => 'icon-size-all-' . $sSuffix
                            ),
                            'attributes' => array(
                                'size'  => array(
                                    'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                    'data-property'        => '_icon_size_all_' . $sSuffix,
                                    'min'                  => 0,
                                ),
                                'select' => array(
                                    'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                    'data-property'        => '_icon_size_all_' . $sSuffix,
                                ),
                            ),
                        ),
                        array(
                            'field_id'          => 'width',
                            'type'              => 'size',
                            'title'             => __( 'Width', 'amazon-auto-links' ),
                            'units'             => $this->getCSSSizeUnits(),
                            'hidden'            => true,
                            'default'           => array(
                                // 'size' => 148,
                                'unit' => 'px',
                            ),
                            'class'     => array(
                                'fieldset' => 'icon-size-each-' . $sSuffix,
                            ),
                            'attributes' => array(
                                'size'  => array(
                                    'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                    'data-property'        => '_icon_size_each_width_' . $sSuffix,
                                    'min'                  => 0,
                                ),
                                'select' => array(
                                    'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                    'data-property'        => '_icon_size_each_width_' . $sSuffix,
                                ),
                            ),
                        ),
                        array(
                            'field_id'          => 'height',
                            'type'              => 'size',
                            'title'             => __( 'Height', 'amazon-auto-links' ),
                            'units'             => $this->getCSSSizeUnits(),
                            'hidden'            => true,
                            'default'           => array(
                                // 'size' => 148,
                                'unit' => 'px',
                            ),
                            'class'     => array(
                                'fieldset' => 'icon-size-each-' . $sSuffix,
                            ),
                            'attributes' => array(
                                'size'  => array(
                                    'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                    'data-property'        => '_icon_size_each_height_' . $sSuffix,
                                    'min'                  => 0,
                                ),
                                'select' => array(
                                    'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                    'data-property'        => '_icon_size_each_height_' . $sSuffix,
                                ),
                            ),
                        ),
                    ),
                ),

                // Padding
                array(
                    'field_id'          => 'padding_type',
                    'type'              => 'revealer',
                    'select_type'       => 'radio',
                    'title'             => __( 'Padding', 'amazon-auto-links' ),
                    'hidden'            => true,
                    'label'             => array(
                        'all'   => __( 'All', 'amazon-auto-links' ),
                        'each'  => __( 'Each', 'amazon-auto-links'),
                    ),
                    'selectors'     => array(
                        'all'   => '.padding-all-' . $sSuffix ,
                        'each'  => '.padding-each-' . $sSuffix ,
                    ),
                    'class'             => array(
                        'fieldset' => 'button-icon-' . $sSuffix,
                    ),
                    'attributes'        => array(
                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                        'data-property'        => '_icon_padding_type_' . $sSuffix,
                    ),
                    'default'           => 'all',                    
                ),
                array(
                    'hidden'        => true,
                    'field_id'      => 'padding',
                    'class'         => array(
                        'fieldset' => 'button-icon-' . $sSuffix,
                    ),
                    'content'       => array(
                        array(
                            'field_id'      => 'all',
                            'type'          => 'number',
                            'class'          => array(
                                'fieldset'  => 'padding-all-' . $sSuffix ,
                            ),
                            'attributes'    => array(
                                'data-property'        => 'padding',
                                'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                'data-suffix'          => 'px',
                                'min'                  => 0,    // unlike margin, padding does not take negative values in CSS
                            ),
                            'default'       => 0,
                        ),
                        array(
                            'field_id'      => 'each',
                            'type'          => 'inline_mixed',
                            'class'          => array(
                                'fieldset'  => 'padding-each-' . $sSuffix ,
                            ),
                            'content'       => array(
                                array(
                                    'field_id'      => 'top',
                                    'type'          => 'number',
                                    'title'         => __( 'Top', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property'        => 'padding-top',
                                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                        'data-suffix'          => 'px',
                                        'min'                  => 0,
                                    ),
                                    'default'       => 0,
                                ),
                                array(
                                    'field_id'      => 'right',
                                    'type'          => 'number',
                                    'title'         => __( 'Right', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property'         => 'padding-right',
                                        'data-selector-suffix'  => '.button-icon-' . $sSuffix,
                                        'data-suffix'           => 'px',
                                        'min'                  => 0,
                                    ),
                                    'default'       => 0,
                                ),
                                array(
                                    'field_id'      => 'bottom',
                                    'type'          => 'number',
                                    'title'         => __( 'Bottom', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property'        => 'padding-bottom',
                                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                        'data-suffix'          => 'px',
                                        'min'                  => 0,
                                    ),
                                    'default'       => 0,
                                ),
                                array(
                                    'field_id'      => 'left',
                                    'type'          => 'number',
                                    'title'         => __( 'Left', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property'         => 'padding-left',
                                        'data-selector-suffix'  => '.button-icon-' . $sSuffix,
                                        'data-suffix'           => 'px',
                                        'min'                  => 0,
                                    ),
                                    'default'       => 0,
                                ),
                            ),
                        ),
                    ),
                ),                
                
                // Margin
                array(
                    'field_id'          => 'margin_type',
                    'type'              => 'revealer',
                    'select_type'       => 'radio',
                    'title'             => __( 'Margin', 'amazon-auto-links' ),
                    'hidden'            => true,
                    'label'             => array(
                        'all'   => __( 'All', 'amazon-auto-links' ),
                        'each'  => __( 'Each', 'amazon-auto-links'),
                    ),
                    'selectors'     => array(
                        'all'   => '.margin-all-' . $sSuffix ,
                        'each'  => '.margin-each-' . $sSuffix ,
                    ),
                    'class'             => array(
                        'fieldset' => 'button-icon-' . $sSuffix,
                    ),
                    'attributes'        => array(
                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                        'data-property'        => '_icon_margin_type_' . $sSuffix,
                    ),
                    'default'           => 'all',
                ),
                array(
                    'hidden'        => true,
                    'field_id'      => 'margin',
                    'class'         => array(
                        'fieldset' => 'button-icon-' . $sSuffix,
                    ),
                    'content'       => array(
                        array(
                            'field_id'      => 'all',
                            'type'          => 'number',
                            'class'          => array(
                                'fieldset'  => 'margin-all-' . $sSuffix ,
                            ),
                            'attributes'    => array(
                                'data-property'        => 'margin',
                                'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                'data-suffix'          => 'px',
                            ),
                            'default'       => 0,
                        ),
                        array(
                            'field_id'      => 'each',
                            'type'          => 'inline_mixed',
                            'class'          => array(
                                'fieldset'  => 'margin-each-' . $sSuffix ,
                            ),
                            'content'       => array(
                                array(
                                    'field_id'      => 'top',
                                    'type'          => 'number',
                                    'title'         => __( 'Top', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property'        => 'margin-top',
                                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                        'data-suffix'          => 'px',
                                    ),
                                    'default'       => 0,
                                ),
                                array(
                                    'field_id'      => 'right',
                                    'type'          => 'number',
                                    'title'         => __( 'Right', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property'         => 'margin-right',
                                        'data-selector-suffix'  => '.button-icon-' . $sSuffix,
                                        'data-suffix'           => 'px',
                                    ),
                                    'default'       => 0,
                                ),
                                array(
                                    'field_id'      => 'bottom',
                                    'type'          => 'number',
                                    'title'         => __( 'Bottom', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property'        => 'margin-bottom',
                                        'data-selector-suffix' => '.button-icon-' . $sSuffix,
                                        'data-suffix'          => 'px',
                                    ),
                                    'default'       => 0,
                                ),
                                array(
                                    'field_id'      => 'left',
                                    'type'          => 'number',
                                    'title'         => __( 'Left', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property'         => 'margin-left',
                                        'data-selector-suffix'  => '.button-icon-' . $sSuffix,
                                        'data-suffix'           => 'px',
                                    ),
                                    'default'       => 0,
                                ),
                            ),
                        ),
                    ),
                ),
            );
        }

}