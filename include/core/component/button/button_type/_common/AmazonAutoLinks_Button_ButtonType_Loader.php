<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

abstract class AmazonAutoLinks_Button_ButtonType_Loader {

    static public $sDirPath;

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {

        $this->_construct();

        if ( is_admin() ) {
            $this->_loadPostMetaBoxes();
            add_filter( 'aal_filter_custom_meta_keys', array( $this, 'replyToAddProtectedMetaKeys' ) );
        }

    }

    /**
     * @since 5.2.0
     */
    protected function _construct() {}

    /**
     * Loads post meta boxes.
     * @since  5.2.0
     */
    protected function _loadPostMetaBoxes() {}

    /**
     * @since    3.3.0
     * @since    5.2.0        Moved from `AmazonAutoLinks_Button_Loader`.
     * @param    array        $aMetaKeys
     * @callback add_filter() aal_filter_custom_meta_keys
     * @return   array
     */
    public function replyToAddProtectedMetaKeys( $aMetaKeys ) {
        return $aMetaKeys;
    }

    /**
     * @since  5.2.0
     * @param  array $aMetaKeys
     * @return array
     */
    protected function _getProtectedMetaKeys( $aMetaKeys ) {
        return $aMetaKeys;
    }

}