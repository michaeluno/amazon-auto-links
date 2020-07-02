<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Handles AALB Gutenberg block contents.
 * 
 * @package     Amazon Auto Links
 * @since       3.11.0
 */
class AmazonAutoLinks_AALBSupport_GutenbergBlock extends AmazonAutoLinks_WPUtility {
    
    public $aShortcodes = array(
        'amazon_link',
        'amazon_textlink',
    );

    /**
     * @see AmazonAssociatesLinkBuilder\constants\GB_Block_Constants
     */
    // const GB_SCRIPT_HANDLE = 'amazon-associates-link-builder-gb-block'; // @deprecated 4.0.4 Not used anywhere.
    const SHORTCODE_ATTR = 'shortCodeContent';
    const SHORTCODE_ATTR_TYPE = 'string';
    const SEARCH_KEYWORD = 'searchKeyword';
    const SEARCH_KEYWORD_TYPE = 'string';
    // const GB_SUPPORTED_IDENTIFIER_METHOD = 'register_block_type'; // @deprecated 4.0.4 Not used anywhere

    /**
     * @see AmazonAssociatesLinkBuilder\includes\GB_Block_Manager
     */
    const OPENING_SQUARE_BRACKET = '[';
    const CLOSING_SQUARE_BRACKET = ']';
    const TYPE_ARRAY = 'array';
    const ASIN = 'asin';
    const ASINS = 'asins';

    const BLOCK_ID          = 'amazon-associates-link-builder/aalb-gutenberg-block';
    const AALB_PLUGIN_SLUG  = 'amazon-associates-link-builder/amazon-associates-link-builder.php';
    
    /**
     * Registers the shortcode(s).
     */
    public function __construct() {
        
        if ( $this->___isPluginActive( self::AALB_PLUGIN_SLUG ) ) {
            AmazonAutoLinks_Registry::setAdminNotice(
                __( 'The Amazon Associates Link Builder plugin needs to be deactivated to property handle their block contents.', 'amazon-auto-links' )
            );
            return;
        }

        /**
         * There is a report that caused Fatal error: Uncaught Error: Call to undefined function register_block_type().
         * @since   4.0.4
         */
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        register_block_type(self::BLOCK_ID, array(
            'attributes' => array(
                self::SHORTCODE_ATTR => array(
                    'type' => self::SHORTCODE_ATTR_TYPE,
                ),
                self::SEARCH_KEYWORD => array(
                    'type' => self::SEARCH_KEYWORD_TYPE,
                )
            ),
            'editor_script' => 'amazon-associates-link-builder-gb-block',
            'render_callback' => array( $this, 'replyToBlockEditorCallback' ),
        ));

    }
        /**
         * Alternative to the `is_plugin_active()` WordPress core function that is often not loaded in some cases.
         * @param $sPluginSlug
         * @see is_plugin_active()
         * @return bool
         */
        private function ___isPluginActive( $sPluginSlug ) {
            if ( ! function_exists('is_plugin_active' ) ) {
                @include( ABSPATH . '/wp-admin/includes/plugin.php' );
            }
            return is_plugin_active( $sPluginSlug );
        }

    public function replyToBlockEditorCallback( $aAttributes ) {
        $_asShortcodeAttributes = $this->___get_shortcode_value_from_attributes( $aAttributes );
        if ( ! $this->___is_valid_shortcode( $_asShortcodeAttributes ) ) {
            return null;
        }
        $_aAttributes = $_asShortcodeAttributes;
        return apply_filters( 'aal_filter_shortcode_' . $_aAttributes[ 0 ], '', $_aAttributes );

    }

        /**
         * Check if a shortcode is valid or not.
         * It validate by checking the following :-
         *  * If type of $shortcode  is array.
         *  * If $shortcode contains 'asin' or 'asins' keys.
         *
         * This method is used to check if shortcode attributes are added or not.
         *
         * @param $shortcode - shortcode.
         * @return bool
         */
        private function ___is_valid_shortcode( $shortcode ) {
            return (
                gettype( $shortcode ) == self::TYPE_ARRAY )
                && ( isset( $shortcode[ self::ASIN ] )
                || isset( $shortcode[ self::ASINS ] )
            );
        }
        /**
         * @param $attributes
         * @return array|string List of attribute values.
         *                      Returns empty array if trim( $text ) == '""'.
         *                      Returns empty string if trim( $text ) == ''.
         *                      All other matches are checked for not empty().
         */
        private function ___get_shortcode_value_from_attributes( $attributes ) {
            return isset( $attributes[ self::SHORTCODE_ATTR ] )
                ? shortcode_parse_atts(
                    trim(
                        trim(
                            $attributes[ self::SHORTCODE_ATTR ], self::OPENING_SQUARE_BRACKET
                        ),
                        self::CLOSING_SQUARE_BRACKET
                    )
                )
                : $attributes;
        }
    
            
}