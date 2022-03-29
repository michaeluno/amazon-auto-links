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
 * The base class for Gutenberg dynamic-rendering block classes.
 *
 * @since 5.1.0
 */
abstract class AmazonAutoLinks_GutenbergBlock_Dynamic_Base extends AmazonAutoLinks_GutenbergBlock_Base {

    /**
     * @var string
     * @since 5.1.0
     */
    public $sScriptHandle = '';

    /**
     * Determines whether the block can/should load.
     * @return boolean
     * @since  5.1.0
     */
    protected function _shouldLoad() {
        return function_exists( 'register_block_type_from_metadata' ); // WordPress 5.1.0 or above
    }

    /**
     * Registers all block assets so that they can be enqueued through Gutenberg in
     * the corresponding context.
     *
     * @since  5.1.0
     * @return false|WP_Block_Type
     */
    protected function _getBlockRegistered( array $aArguments ) {
        $aArguments[ 'render_callback' ] = array( $this, 'replyToRenderDynamicBlock' );
        return parent::_getBlockRegistered( $aArguments );
    }

    /**
     * @param    array  $aBlockAttributes
     * @param    string $sContent
     * @param    array  $aProperties
     * @return   string
     * @sinec    5.1.0
     * @callback register_block_type_from_metadata() render_callback
     */
    public function replyToRenderDynamicBlock( $aBlockAttributes, $sContent, $aProperties ) {
        return $this->_renderBlock( $aBlockAttributes, $sContent, $aProperties );
    }

    /**
     * @param  array   $aBlockAttributes
     * @param  string  $sContent
     * @param  array   $aProperties
     * @return string
     * @remark Override this method in an extended class.
     * @since  5.1.0
     */
    protected function _renderBlock( $aBlockAttributes, $sContent, $aProperties ) {
        return "<h2>Block Attributes</h2>"
            . "<pre>"
                . var_export( $aBlockAttributes, true )
            . "</pre>"
            . "<h2>Properties</h2>"
            . "<pre>"
                . var_export( $aProperties, true )
            . "</pre>"
            . "<hr />";
    }



}