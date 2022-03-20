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
class AmazonAutoLinks_Button_Button2_FormFields_Main extends AmazonAutoLinks_FormFields_Base {

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
                'title'        => __( 'Text', 'amazon-auto-links' ),
                'type'         => 'inline_mixed',
                'content'      => array(
                    array(
                        'field_id'      => 'label',
                        'type'          => 'text',
                        'title'         => __( 'Label', 'amazon-auto-links' ),
                        'attributes'    => array(
                            'style'         => 'min-width: 220px;',
                        ),
                    ),
                    array(
                        'field_id'      => 'size',
                        'type'          => 'number',
                        'title'         => __( 'Size', 'amazon-auto-links' ),
                        'attributes'    => array(
                            'min'           => 0,
                            'data-property' => 'font-size',
                            'style'         => 'width: 80px;',
                        ),
                        'default'       => 13,
                    ),
                    array(
                        'field_id'      => 'color',
                        'type'          => 'color',
                        'title'         => __( 'Color', 'amazon-auto-links' ),
                        // 'default'       => '#ffffff',
                        'attributes'    => array(
                            'data-property' => 'color',
                        ),
                    ),
                    array(
                        'field_id'      => 'weight',
                        'type'          => 'select',
                        'title'         => __( 'Weight', 'amazon-auto-links' ),
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
                        'default'   => 400,
                    ),
                ),
            ),
            array(
                'field_id'          => '_icon_left',
                'content'           => $this->___getIconNestedFieldsets(
                    __( 'Left Icon', 'amazon-auto-links' ),
                    'left',
                    true,
                    AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/cart.svg', true ),
                ),
            ),
            array(
                'field_id'          => '_icon_right',
                'content'           => $this->___getIconNestedFieldsets(
                    __( 'Right Icon', 'amazon-auto-links' ),
                    'right',
                    false,
                    AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/controls-play.svg', true ),
                ),
            ),
        );
    }
        /**
         * @param  string  $sLabel
         * @param  string  $sSuffix
         * @param  boolean $bDefault         Whether it is enabled by default.
         * @param  string  $sDefaultImageSRC The default image URL.
         * @return array
         * @since  5.2.0
         */
        private function ___getIconNestedFieldsets( $sLabel, $sSuffix, $bDefault=false, $sDefaultImageSRC='' ) {
            return array(
                array(
                    'field_id'          => 'enable',
                    'type'              => 'revealer',
                    'select_type'       => 'checkbox',
                    'label'             => '<strong>' . $sLabel . "<span class='title-colon'>:</span></strong>",
                    'selectors'         => '.button-icon-' . $sSuffix,
                    'default'           => $bDefault,
                ),
                array(
                    'field_id'          => 'image',
                    'type'              => 'image',
                    'show_title_column' => true,
                    'class'             => array(
                        'fieldset' => 'button-icon-' . $sSuffix,
                        'field'    => 'dynamic-button-field',
                    ),
                    'show_preview'      => false,
                    'hidden'            => true,
                    'description'       => sprintf(
                        /* translators: 1: A plugin URL that supports SVG uploads */
                        __( 'By default, WordPress disallow SVG files to be uploaded for security reasons. To allow SVG files, use a third-party plugin such as <a href="%1$s" target="_blank">this one</a>.', 'amazon-auto-links' ),
                        esc_url( 'https://wordpress.org/plugins/safe-svg/' )
                    ),
                    'default'           => $sDefaultImageSRC,
                ),
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
                    'default'           => 'all',
                ),
                array(
                    'hidden'=> true,
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
                                'data-property' => 'margin',
                                'min'           => 0,
                            ),
                            'default'       => 1,
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
                                        'data-property' => 'margin-top',
                                        'min'           => 0,
                                    ),
                                    'default'       => 1,
                                ),
                                array(
                                    'field_id'      => 'right',
                                    'type'          => 'number',
                                    'title'         => __( 'Right', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property' => 'margin-right',
                                        'min'           => 0,
                                    ),
                                    'default'       => 1,
                                ),
                                array(
                                    'field_id'      => 'bottom',
                                    'type'          => 'number',
                                    'title'         => __( 'Bottom', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property' => 'margin-bottom',
                                        'min'           => 0,
                                    ),
                                    'default'       => 1,
                                ),
                                array(
                                    'field_id'      => 'left',
                                    'type'          => 'number',
                                    'title'         => __( 'Left', 'amazon-auto-links' ),
                                    'attributes'    => array(
                                        'data-property' => 'margin-left',
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