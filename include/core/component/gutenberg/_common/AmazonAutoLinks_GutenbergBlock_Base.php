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
 * The base class for Gutenberg Block classes.
 * @since 5.1.0
 */
abstract class AmazonAutoLinks_GutenbergBlock_Base extends AmazonAutoLinks_GutenbergBlock_Utility {

    /**
     * @var  string|WP_Block_Type Block type name including namespace, or alternatively
     * a path to the JSON file with metadata definition for the block,
     * or a path to the folder where the `block.json` file is located,
     * or a complete WP_Block_Type instance.
     * @sine 5.1.0
     * @see  register_block_type()
     */
    public $osBlockNameOrPath;

    /**
     * @var   string The object name passed to the Javascript script.
     * @since 5.1.0
     */
    public $sCustomDataVariableName = '';

    /**
     * Sets up properties and hooks.
     */
    public function __construct( $osBlockNameOrPath='' ) {
        if ( $osBlockNameOrPath ) {
            $this->osBlockNameOrPath = $osBlockNameOrPath;
        }
        add_action( 'init', array( $this, 'replyToRegisterBlock' ) );
    }

    /**
     * Registers all block assets so that they can be enqueued through Gutenberg in
     * the corresponding context.
     *
     * Passes translations to JavaScript.
     * @since    5.1.0
     * @callback add_action() init
     */
    public function replyToRegisterBlock() {

        if ( ! $this->_shouldLoad() ) {
            return;
        }

        $_oWPBlockType = $this->_getBlockRegistered( $this->_getArguments() );
        if ( ! ( $_oWPBlockType instanceof WP_Block_Type ) ) {
            return;
        }

        // Localization
        if ( function_exists( 'wp_set_script_translations' ) ) {
            /**
             * May be extended to wp_set_script_translations( 'my-handle', 'my-domain',
             * plugin_dir_path( MY_PLUGIN ) . 'languages' ) ). For details see
             * https://make.wordpress.org/core/2018/11/09/new-javascript-i18n-support-in-wordpress/
             */
            wp_set_script_translations(
                $_oWPBlockType->editor_script,
                AmazonAutoLinks_Registry::TEXT_DOMAIN,
                dirname( AmazonAutoLinks_Registry::$sBaseName ) . AmazonAutoLinks_Registry::TEXT_DOMAIN_PATH
            );
        }

        // Custom data
        $_aCustomData = $this->_getCustomData();
        if ( ! empty( $_aCustomData ) ) {
            wp_localize_script( $_oWPBlockType->editor_script, $this->sCustomDataVariableName, $_aCustomData );
        }

    }

    /**
     * Determines whether the block can/should load.
     * @return boolean
     * @since  5.1.0
     */
    protected function _shouldLoad() {
        return function_exists( 'register_block_type' ); // Gutenberg is not active.
    }

    /**
     * Registers the block.
     * @since  5.1.0
     * @param  array $aArguments
     * @see    WP_Block_Type::__construct()
     * @return WP_Block_Type|false
     */
    protected function _getBlockRegistered( array $aArguments ) {
        return register_block_type( $this->osBlockNameOrPath, $aArguments );
    }

    /**
     * @since  5.1.0
     * @return array
     */
    protected function _getArguments() {
        return array();
    }

    /**
     * @since  5.1.0
     * @return array    Custom data to pass to the JavaScript block script.
     */
    protected function _getCustomData() {
        return array();
    }

}