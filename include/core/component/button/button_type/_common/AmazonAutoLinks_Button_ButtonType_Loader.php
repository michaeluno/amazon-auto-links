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
 * @since 5.2.0
 */
abstract class AmazonAutoLinks_Button_ButtonType_Loader {

    static public $sDirPath;

    /**
     * @since 5.2.0
     * @var   string
     */
    public $sButtonTypeSlug = '';

    /**
     * Sets up properties and hooks.
     * @since 5.2.0
     */
    public function __construct() {

        $this->_construct();

        if ( is_admin() ) {
            $this->_loadPostMetaBoxes();
            add_filter( 'aal_filter_custom_meta_keys', array( $this, 'replyToAddProtectedMetaKeys' ) );
        }

        add_filter( 'aal_button_type_label_' . $this->sButtonTypeSlug, array( $this, 'replyToGetButtonTypeLabel' ), 10, 2 );

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

    /**
     * @param  string  $sLabel    The button label. Default: `Classic`
     * @param  integer $iButtonID A post ID of the plugin button post type.
     * @return string
     */
    public function replyToGetButtonTypeLabel( $sLabel, $iButtonID ) {
        return $this->_getButtonLabel();
    }

    /**
     * @since  5.2.0
     * @return string
     */
    protected function _getButtonLabel() {
        return __( 'Classic', 'amazon-auto-links' );
    }

}