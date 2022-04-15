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
 * Creates default buttons
 * @since 5.2.0
 */
class AmazonAutoLinks_Button_DefaultButtons extends AmazonAutoLinks_Button_Utility {

    /**
     * @since 5.2.0
     * @var   array
     */
    public $aDefaultButtons = array();

    /**
     * Sets up properties and hooks.
     * @since 5.2.0
     */
    public function __construct() {
        $this->aDefaultButtons = array(
            // GUID => array( data )
            'https://aal-default-button/flat-default'                    => array(
                'post_columns' => array(
                    'post_title'  => __( 'Flat', 'amazon-auto-links' ),
                    'guid'        => 'https://aal-default-button/flat-default',
                    'post_status' => 'publish',
                    'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                ),
                'post_metas'   => array(
                    '_text'              => array(
                        'label'       => __( 'Buy Now', 'amazon-auto-links' ),
                        'font_size'   => 13,
                        'color'       => '#ffffff',
                        'weight'      => 400,
                        'margin_type' => 'each',
                        'margin'      => array(
                            'all'  => 0,
                            'each' => array(
                                'top'    => 0,
                                'right'  => 8,
                                'bottom' => 0,
                                'left'   => 8,
                            ),
                        ),
                    ),
                    'button_label'       => __( 'Buy Now', 'amazon-auto-links' ),

                    '_icon_left'         => array(
                        'enable'          => 1,
                        'image_type'      => 'svg_file',
                        'svg_file'        => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/cart.svg', true ),
                        'svg_mask'        => 1,
                        'svg_color'       => '#ffffff',
                        'image_file'      => '',
                        'background'      => array(
                            'color' => 'transparent',
                        ),
                        'border'          => array(
                            'width'  => '',
                            'color'  => 'transparent',
                            'radius' => '',
                        ),
                        'size_constraint' => 1,
                        // 'size_type'       => 'all',  // omitted
                        // 'size'            => array() // omitted
                        // 'padding_type'    => 'all',  // omitted
                        // 'padding'         => array() // omitted
                        // 'margin_type'     => 'all    // omitted
                        // 'margin'          => array() // omitted
                    ),
                    '_icon_right'        => array(
                        'enable'          => 0,
                        // omitted the rest
                    ),
                    // '_dimensions'        => array(), // omitted
                    '_padding_type'      => 'each',
                    '_padding'           => array(
                        'all'       => 1,
                        'each'      => array(
                            'top'    => 8,
                            'right'  => 16,
                            'bottom' => 8,
                            'left'   => 16,
                        ),
                    ),
                    '_border_radius'     => 4,
                    '_border_style'      => 'none',
                    '_border_color'      => '#1f628d',
                    '_border_width'      => 1,
                    '_background_type'   => 'solid',
                    '_background_color'  => '#4997e5',
                    '_hover_scale'       => 1,
                    '_hover_brightness'  => 1,
                    'button_css'         => '',
                    '_button_type'       => 'flat',
                ),
                'css'          => ".amazon-auto-links-button-___button_id___ { margin-right: auto; margin-left: auto; white-space: nowrap; text-align: center; display: inline-flex; justify-content: space-around; font-size: 13px; color: #ffffff; font-weight: 400; padding-top: 8px; padding-right: 16px; padding-bottom: 8px; padding-left: 16px; border-radius: 4px; border-color: #1f628d; border-width: 1px; background-color: #4997e5; transform: scale(0.98); border-style: none; background-solid: solid; } .amazon-auto-links-button-___button_id___ * { box-sizing: border-box; } .amazon-auto-links-button-___button_id___ .button-icon { margin-right: auto; margin-left: auto; display: none; height: auto; border: solid 0; } .amazon-auto-links-button-___button_id___ .button-icon > i { display: inline-block; width: 100%; height: 100%; } .amazon-auto-links-button-___button_id___ .button-icon-left { display: inline-flex; background-color: transparent; border-color: transparent; padding: 0px; margin: 0px; min-height: 17px; min-width: 17px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; } .amazon-auto-links-button-___button_id___ .button-icon-left > i { background-color: #ffffff; background-size: contain; background-position: center; background-repeat: no-repeat; -webkit-mask-image: url('___cart.svg___'); mask-image: url('___cart.svg___'); -webkit-mask-position: center center; mask-position: center center; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; } .amazon-auto-links-button-___button_id___ .button-label { margin-top: 0px; margin-right: 8px; margin-bottom: 0px; margin-left: 8px; } .amazon-auto-links-button-___button_id___ > * { align-items: center; display: inline-flex; vertical-align: middle; } .amazon-auto-links-button-___button_id___:hover { transform: scale(1.0); filter: alpha(opacity=70); opacity: 0.7; }",
            ),
            'https://aal-default-button/flat-two-icons'                  => array(
                'post_columns' => array(
                    'post_title'  => __( 'Flat - Two Icons', 'amazon-auto-links' ),
                    'guid'        => 'https://aal-default-button/flat-two-icons',
                    'post_status' => 'publish',
                    'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                ),
                'post_metas'   => array(
                    '_text'              => array(
                        'label'       => __( 'Get Now', 'amazon-auto-links' ),
                        'font_size'   => 13,
                        'color'       => '#ffffff',
                        'weight'      => 400,
                        'margin_type' => 'each',
                        'margin'      => array(
                            'all'  => 0,
                            'each' => array(
                                'top'    => 0,
                                'right'  => 16,
                                'bottom' => 0,
                                'left'   => 16,
                            ),
                        ),
                    ),
                    'button_label'       => __( 'Get Now', 'amazon-auto-links' ),
                    '_icon_left'         => array(
                        'enable'          => 1,
                        'image_type'      => 'svg_file',
                        'svg_file'        => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/cart.svg', true ),
                        'svg_mask'        => 1,
                        'svg_color'       => '#ffffff',
                        'image_file'      => '',
                        'background'      => array(
                            'color' => 'transparent',
                        ),
                        'border'          => array(
                            'width'  => '',
                            'color'  => 'transparent',
                            'radius' => '',
                        ),
                        'size_constraint' => 1,
                        // 'size_type'       => 'all',
                        // 'size'            => array() // omitted
                        // 'padding_type'    => 'all',  // omitted
                        // 'padding' => array()         // omitted
                        // 'margin_type' => 'all        // omitted
                        // 'margin' => array()          // omitted
                    ),
                    '_icon_right'        => array(
                        'enable'          => 1,
                        'image_type'      => 'svg_file',
                        'svg_file'        => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/controls-play.svg', true ),
                        'svg_mask'        => 1,
                        'svg_color'       => '#000000',
                        'image_file'      => '',
                        'background'      => array(
                            'color' => '#ffffff',
                        ),
                        'border'          => array(
                            'width'  => '',
                            'color'  => 'transparent',
                            'radius' => 10,
                        ),
                        'size_constraint' => 1,
                        // 'size_type'       => 'all',
                        // 'size'            => array(), // omitted
                        'padding_type'    => 'each',
                        'padding'         => array(
                            'all'   => 0,
                            'each'  => array(
                                'top'    => 0,
                                'right'  => 0,
                                'bottom' => 0,
                                'left'   => 2,
                            ),
                        ),
                        // 'margin_type' => 'all         // omitted
                        // 'margin'      => array(),         // omitted
                    ),
                    // '_dimensions'        => array(), // omitted
                    '_padding_type'      => 'each',
                    '_padding'           => array(
                        'all'       => 1,
                        'each'      => array(
                            'top'    => 8,
                            'right'  => 16,
                            'bottom' => 8,
                            'left'   => 16,
                        ),
                    ),
                    '_border_radius'     => 0,
                    '_border_style'      => 'none',
                    '_border_color'      => '#1f628d',
                    '_border_width'      => 1,
                    '_background_type'   => 'solid',
                    '_background_color'  => '#0a0101',
                    '_hover_scale'       => 1,
                    '_hover_brightness'  => 1,
                    'button_css'         => '',
                    '_button_type'       => 'flat',
                ),
                'css'   => ".amazon-auto-links-button-___button_id___ { margin-right: auto; margin-left: auto; white-space: nowrap; text-align: center; display: inline-flex; justify-content: space-around; font-size: 13px; color: #ffffff; font-weight: 400; padding-top: 8px; padding-right: 16px; padding-bottom: 8px; padding-left: 16px; border-radius: 0px; border-color: #1f628d; border-width: 1px; background-color: #0a0101; transform: scale(0.98); border-style: none; background-solid: solid; } .amazon-auto-links-button-___button_id___ * { box-sizing: border-box; } .amazon-auto-links-button-___button_id___ .button-icon { margin-right: auto; margin-left: auto; display: none; height: auto; border: solid 0; } .amazon-auto-links-button-___button_id___ .button-icon > i { display: inline-block; width: 100%; height: 100%; } .amazon-auto-links-button-___button_id___ .button-icon-left { display: inline-flex; background-color: transparent; border-color: transparent; padding: 0px; margin: 0px; min-height: 17px; min-width: 17px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 0px; } .amazon-auto-links-button-___button_id___ .button-icon-left > i { background-color: #ffffff; background-size: contain; background-position: center; background-repeat: no-repeat; -webkit-mask-image: url('___cart.svg___'); mask-image: url('___cart.svg___'); -webkit-mask-position: center center; mask-position: center center; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; } .amazon-auto-links-button-___button_id___ .button-icon-right { display: inline-flex; background-color: #ffffff; border-color: transparent; margin: 0px; min-height: 17px; min-width: 17px; border-radius: 10px; padding-top: 0px; padding-right: 0px; padding-bottom: 0px; padding-left: 2px; } .amazon-auto-links-button-___button_id___ .button-icon-right > i { background-color: #000000; background-size: contain; background-position: center; background-repeat: no-repeat; -webkit-mask-image: url('___controls-play.svg___'); mask-image: url('___controls-play.svg___'); -webkit-mask-position: center center; mask-position: center center; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; } .amazon-auto-links-button-___button_id___ .button-label { margin-top: 0px; margin-right: 16px; margin-bottom: 0px; margin-left: 16px; } .amazon-auto-links-button-___button_id___ > * { align-items: center; display: inline-flex; vertical-align: middle; } .amazon-auto-links-button-___button_id___:hover { transform: scale(1.0); filter: alpha(opacity=70); opacity: 0.7; }",
            ),
            'https://aal-default-button/flat-rounded'                    => array(
                'post_columns' => array(
                    'post_title'  => __( 'Flat - Rounded', 'amazon-auto-links' ),
                    'guid'        => 'https://aal-default-button/flat-rounded',
                    'post_status' => 'publish',
                    'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                ),
                'post_metas'   => array(
                    '_text'              => array(
                        'label'       => __( 'Available on Amazon', 'amazon-auto-links' ),
                        'font_size'   => 13,
                        'color'       => '#000000',
                        'weight'      => 500,
                        'margin_type' => 'each',
                        'margin'      => array(
                            'all'  => 0,
                            'each' => array(
                                'top'    => 0,
                                'right'  => 32,
                                'bottom' => 0,
                                'left'   => 32,
                            ),
                        ),
                    ),
                    'button_label'       => __( 'Available on Amazon', 'amazon-auto-links' ),
                    '_icon_left'         => array(
                        'enable'          => 0,
                        // omitted the rest
                    ),
                    '_icon_right'        => array(
                        'enable'          => 0,
                        // omitted the rest
                    ),
                    // '_dimensions'        => array(), // omitted
                    '_padding_type'      => 'each',
                    '_padding'           => array(
                        'all'       => 1,
                        'each'      => array(
                            'top'    => 8,
                            'right'  => 16,
                            'bottom' => 8,
                            'left'   => 16,
                        ),
                    ),
                    '_border_radius'     => 20,
                    '_border_style'      => 'solid',
                    '_border_color'      => '#e8b500',
                    '_border_width'      => 1,
                    '_background_type'   => 'solid',
                    '_background_color'  => '#ffd814',
                    '_hover_scale'       => 1,
                    '_hover_brightness'  => 1,
                    'button_css'         => '',
                    '_button_type'       => 'flat',
                ),
                'css'          => ".amazon-auto-links-button-___button_id___ { margin-right: auto; margin-left: auto; white-space: nowrap; text-align: center; display: inline-flex; justify-content: space-around; font-size: 13px; color: #000000; font-weight: 500; padding-top: 8px; padding-right: 16px; padding-bottom: 8px; padding-left: 16px; border-radius: 19px; border-color: #e8b500; border-width: 1px; background-color: #ffd814; transform: scale(0.98); border-style: solid; background-solid: solid; } .amazon-auto-links-button-___button_id___ * { box-sizing: border-box; } .amazon-auto-links-button-___button_id___ .button-icon { margin-right: auto; margin-left: auto; display: none; height: auto; border: solid 0; } .amazon-auto-links-button-___button_id___ .button-icon > i { display: inline-block; width: 100%; height: 100%; } .amazon-auto-links-button-___button_id___ .button-label { margin-top: 0px; margin-right: 32px; margin-bottom: 0px; margin-left: 32px; } .amazon-auto-links-button-___button_id___ > * { align-items: center; display: inline-flex; vertical-align: middle; } .amazon-auto-links-button-___button_id___:hover { transform: scale(1.0); filter: alpha(opacity=70); opacity: 0.7; }",
            ),
            'https://aal-default-button/flat-icon-with-background-color' => array(
                'post_columns' => array(
                    'post_title'  => __( 'Flat - Icon with Background Color', 'amazon-auto-links' ),
                    'guid'        => 'https://aal-default-button/flat-icon-with-background-color',
                    'post_status' => 'publish',
                    'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                ),
                'post_metas'   => array(
                    '_text'              => array(
                        'label'       => __( 'Add to Cart', 'amazon-auto-links' ),
                        'font_size'   => 13,
                        'color'       => '#000000',
                        'weight'      => 500,
                        'margin_type' => 'each',
                        'margin'      => array(
                            'all'  => 0,
                            'each' => array(
                                'top'    => 0,
                                'right'  => 32,
                                'bottom' => 0,
                                'left'   => 32,
                            ),
                        ),
                    ),
                    'button_label'       => __( 'Add to Cart', 'amazon-auto-links' ),
                    '_icon_left'         => array(
                        'enable'          => 1,
                        'image_type'      => 'svg_file',
                        'svg_file'        => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/cart.svg', true ),
                        'svg_mask'        => 1,
                        'svg_color'       => '#ffffff',
                        'image_file'      => '',
                        'background'      => array(
                            'color' => '#2d2d2d',
                        ),
                        'border'          => array(
                            'width'  => 1,
                            'color'  => '#0a0a0a',
                            'radius' => 2,
                        ),
                        'size_constraint' => 0,
                        'size_type'       => 'all',
                        'size'            => array(
                            'all'   => array(
                                'size' => 25,
                                'unit' => 'px'
                            ),
                            // omitted the rest
                        ),
                        'padding_type'    => 'each',
                        'padding'         => array(
                            'all'  => 3,
                            'each' => array(
                                'top'    => 2,
                                'right'  => 2,
                                'bottom' => 3,
                                'left'   => 2,
                            ),
                        ),
                        // 'margin_type'     => 'all    // omitted
                        // 'margin'          => array() // omitted
                    ),
                    '_icon_right'        => array(
                        'enable'          => 0,
                        // omitted the rest
                    ),
                    // '_dimensions'        => array(), // omitted
                    '_padding_type'      => 'all',
                    '_padding'           => array(
                        'all'       => 3,
                        // omitted the rest
                    ),
                    '_border_radius'     => 4,
                    '_border_style'      => 'solid',
                    '_border_color'      => '#c89411 #b0820f #99710d',
                    '_border_width'      => 1,
                    '_background_type'   => 'gradient',
                    '_background_color'  => '#ecb21f',
                    '_background_gradient' => array(
                        'direction' => 'to_button',
                        'colors'    => array(
                            '#f8e3ad', '#eeba37',
                        ),
                    ),
                    '_hover_scale'       => 1,
                    '_hover_brightness'  => 1,
                    'button_css'         => '',
                    '_button_type'       => 'flat',
                ),
                'css'          => ".amazon-auto-links-button-___button_id___ { margin-right: auto; margin-left: auto; white-space: nowrap; text-align: center; display: inline-flex; justify-content: space-around; font-size: 13px; color: #000000; font-weight: 500; padding: 3px; border-radius: 4px; border-color: #c89411 #b0820f #99710d; border-width: 1px; background-color: #ecb21f; transform: scale(0.98); border-style: solid; background-image: linear-gradient(to bottom,#f8e3ad,#eeba37); } .amazon-auto-links-button-___button_id___ * { box-sizing: border-box; } .amazon-auto-links-button-___button_id___ .button-icon { margin-right: auto; margin-left: auto; display: none; height: auto; border: solid 0; } .amazon-auto-links-button-___button_id___ .button-icon > i { display: inline-block; width: 100%; height: 100%; } .amazon-auto-links-button-___button_id___ .button-icon-left { display: inline-flex; background-color: #2d2d2d; border-width: 1px; border-color: #0a0a0a; border-radius: 2px; margin: 0px; padding-top: 2px; padding-right: 2px; padding-bottom: 3px; padding-left: 2px; min-width: 25px; min-height: 25px; } .amazon-auto-links-button-___button_id___ .button-icon-left > i { background-color: #ffffff; background-size: contain; background-position: center; background-repeat: no-repeat; -webkit-mask-image: url('___cart.svg___'); mask-image: url('___cart.svg___'); -webkit-mask-position: center center; mask-position: center center; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; } .amazon-auto-links-button-___button_id___ .button-label { margin-top: 0px; margin-right: 32px; margin-bottom: 0px; margin-left: 32px; } .amazon-auto-links-button-___button_id___ > * { align-items: center; display: inline-flex; vertical-align: middle; } .amazon-auto-links-button-___button_id___:hover { transform: scale(1.0); filter: alpha(opacity=70); opacity: 0.7; }",
            ),
            'https://aal-default-button/image-amazon-official'           => array(
                'post_columns' => array(
                    'post_title'  => __( 'Image - Amazon Official', 'amazon-auto-links' ),
                    'guid'        => 'https://aal-default-button/image-amazon-official',
                    'post_status' => 'publish',
                    'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                ),
                'post_metas'   => array(
                    '_image_url'         => 'https://images-na.ssl-images-amazon.com/images/G/01/associates/remote-buy-box/buy1.gif',
                    '_dimensions'        => array(
                        'width_toggle'  => 1,
                        'width'         => array(
                            'size' => 176,
                            'unit' => 'px',
                        ),
                        'height_toggle' => 1,
                        'height'        => array(
                            'size' => 28,
                            'unit' => 'px',
                        ),
                    ),
                    '_hover_scale'       => 0,
                    '_hover_brightness'  => 0,
                    'button_label'       => '',
                    'button_css'         => '',
                    '_button_type'       => 'image',
                ),
                'css'          => ".amazon-auto-links-button-___button_id___ { display: block; margin-right: auto; margin-left: auto; position: relative; width: 176px; height: 28px; } .amazon-auto-links-button-___button_id___ > img { height: unset; max-width: 100%; max-height: 100%; margin-right: auto; margin-left: auto; display: block; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); }",
            ),
            'https://aal-default-button/image-button' => array(
                'post_columns' => array(
                    'post_title'  => __( 'Image', 'amazon-auto-links' ),
                    'guid'        => 'https://aal-default-button/image-button',
                    'post_status' => 'publish',
                    'post_type'   => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                ),
                'post_metas'   => array(
                    '_image_url'         => AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/button/amazon-cart-rounded.png', true ),
                    '_dimensions'        => array(
                        'width_toggle'  => 1,
                        'width'         => array(
                            'size' => 148,
                            'unit' => 'px',
                        ),
                        'height_toggle' => 1,
                        'height'        => array(
                            'size' => 40,
                            'unit' => 'px',
                        ),
                    ),
                    '_hover_scale'       => 1,
                    '_hover_brightness'  => 1,
                    'button_label'       => '',
                    'button_css'         => '',
                    '_button_type'       => 'image',
                ),
                'css'          => ".amazon-auto-links-button-___button_id___ { display: block; margin-right: auto; margin-left: auto; position: relative; width: 148px; height: 79px; transform: scale(0.98); } .amazon-auto-links-button-___button_id___:hover { transform: scale(1.0); } .amazon-auto-links-button-___button_id___ > img { height: unset; max-width: 100%; max-height: 100%; margin-right: auto; margin-left: auto; display: block; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); } .amazon-auto-links-button-___button_id___ > img:hover { filter: alpha(opacity=70); opacity: 0.7; }",
            ),
        );
    }

    /**
     * @since  5.2.0
     * @return array Created button IDs
     */
    public function getButtonsCreated() {
        $_aMissingGUIDs   = $this->___getMissingButtons();
        $_aNewButtons     = array();
        $_aFailed         = array();
        foreach( $_aMissingGUIDs as $_sGUID ) {
            $_iNewPostID = $this->createPost(
                AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                $this->aDefaultButtons[ $_sGUID ][ 'post_columns' ],
                $this->aDefaultButtons[ $_sGUID ][ 'post_metas' ]
            );
            if ( empty( $_iNewPostID ) ) {
                $_aFailed[] = $_sGUID;
                continue;
            }
            update_post_meta(
                $_iNewPostID,
                'button_css',
                str_replace(
                    array( 
                        '___button_id___',
                        '___cart.svg___',
                        '___controls-play.svg___',
                    ),
                    array( 
                        $_iNewPostID,
                        AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/cart.svg', true ),
                        AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Button_Loader::$sDirPath . '/asset/image/icon/controls-play.svg', true ),
                    ),
                    $this->aDefaultButtons[ $_sGUID ][ 'css' ] )
            );
            $_aNewButtons[] = $_iNewPostID;
        }
        if ( ! empty( $_aFailed ) ) {
            new AmazonAutoLinks_Error( 'DEFAULT_BUTTONS', 'Failed to create default buttons.', $_aFailed );
        }
        // Update the button CSS option.
        if ( ! empty( $_aNewButtons ) ) {
            update_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ], $this->getCSSRulesOfActiveButtons() );
            update_option(
                AmazonAutoLinks_Registry::$aOptionKeys[ 'active_buttons' ],
                AmazonAutoLinks_PluginUtility::getActiveButtonIDsQueried(), // active button IDs
                true   // enable auto-load
            );
        }
        return $_aNewButtons;
    }
        /**
         * @since  5.2.0
         * @return array
         */
        private function ___getMissingButtons() {
            $_aGUIDs      = array_keys( $this->aDefaultButtons );
            $_aPosts      = $this->getPostByGUID( $_aGUIDs, 'guid' );
            $_aFoundGUIDs = isset( $_aPosts[ 0 ][ 'guid' ] )
                ? wp_list_pluck( $_aPosts, 'guid' )
                : array();
            return array_diff( $_aGUIDs, $_aFoundGUIDs );
        }

}