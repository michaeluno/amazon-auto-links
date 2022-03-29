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
 * Handles feed outputs.
 *
 * @since       4.6.0
 */
class AmazonAutoLinks_Event___Feed_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * @var string The feed type such as rss2 or json.
     * @since 4.6.4
     */
    public $sType = '';

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'replyToSetHooks' ) );
        add_action( 'init', array( $this, 'replyToLoadFeed' ), 999 );
        $this->_construct();
    }

    /**
     * @callback add_action init
     */
    public function replyToSetHooks() {
        do_action( 'aal_action_setup_feed_output_hooks', $this->sType );
    }

    /**
     * @callback add_action init
     */
    public function replyToLoadFeed() {
        $this->_load();
        exit();
    }

    /**
     * A user constructor.
     *
     * @remark Override this method in extended classes.
     * @since  4.6.0
     */
    protected function _construct() {}

    /**
     * Performs the actual task of rendering feeds.
     *
     * @remark Override this method in extended classes.
     * @since  4.6.0
     */
    protected function _load() {}

}