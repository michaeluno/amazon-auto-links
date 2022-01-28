<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Loads the unit Gutenberg block.
 */
class AmazonAutoLinks_GutenbergBlock_UnitBlock extends AmazonAutoLinks_GutenbergBlock_Dynamic_Base {

    public $sCustomDataVariableName = 'aalGutenbergUnitBlock';

    public $bDebug = false;

    /**
     * Sets up properties and hooks.
     * @since 5.1.0
     */
    public function __construct() {

        if ( $this->bDebug ) {
            add_filter( 'block_type_metadata_settings', array( $this, 'debugLog' ), 10, 2 );
        }

        parent::__construct( dirname( __FILE__ ) );

    }

    public function debugLog( $aSettings, $aMetaData ) {
        $_sName = $this->getElement( $aSettings, array( 'name' ) );
        if ( 'auto-amazon-links/unit' !== $_sName ) {
            return $aSettings;
        }
        AmazonAutoLinks_Debug::log( func_get_args() );
        return $aSettings;
    }

    /**
     * @return array
     * @since  5.1.0
     */
    protected function _getArguments() {
        return array(
            '$schema'      => 'https://json.schemastore.org/block.json',
            'apiVersion'   => 2,
            'title'        => AmazonAutoLinks_Registry::NAME . ': ' . __( 'Unit', 'amazon-auto-links' ),
            'name'         => 'auto-amazon-links/unit',
            'category'     => 'embed',
            'icon'         => 'amazon',
            'textdomain'   => "amazon-auto-links",
            'example'      => array(),
	        'attributes'   => array(
                'id'    => array(
                    'type'    => 'integer',
                    'default' => 0
                ),
                'count' => array(
                    'type'    => 'integer',
                ),
            ),
            // Defined in block.json
	        // 'editorScript' => 'file:./build/index.js',
	        // 'editorStyle'  => 'file:./build/index.css',
	        // 'style'        => 'file:./build/style-index.css',
        );
    }

    /**
     * @return array
     * @since  5.1.0
     */
    protected function _getCustomData() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return array(
            'maxNumberOfItems' => $_oOption->getMaximumProductLinkCount(),
            'siteURL'          => get_site_url(),   // for iframe preview URLs
        );
    }

    /**
     * @param  array   $aBlockAttributes
     * @param  string  $sContent
     * @param  array   $aProperties
     * @return string
     * @remark Override this method in an extended class.
     */
    protected function _renderBlock( $aBlockAttributes, $sContent, $aProperties ) {
        return $this->isBackendRequest()
            ? $this->___getOutputBackend( $aBlockAttributes, $sContent, $aProperties )
            : $this->___getOutputFrontend( $aBlockAttributes, $sContent, $aProperties );
    }
        private function ___getOutputFrontend( $aBlockAttributes, $sContent, $aProperties ) {
            $_iUnitID = ( integer ) $this->getElement( $aBlockAttributes, array( 'id' ) );
            if ( ! $_iUnitID ) {
                return "<!-- A unit is not selected. -->";
            }
            return AmazonAutoLinks( $aBlockAttributes, false );
        }
        private function ___getOutputBackend( $aBlockAttributes, $sContent, $aProperties ) {
            $_iUnitID = ( integer ) $this->getElement( $aBlockAttributes, array( 'id' ) );
            if ( ! $_iUnitID ) {
                return '';  // returning an empty output will trigger a placeholder component in Gutenberg.
            }
            return "<div class='aal-gutenberg-unit-preview'>"
                    . $this->___getIframeUnitPreview( $aBlockAttributes )
                . "</div>";
        }
            private function ___getIframeUnitPreview( array $aArguments ) {
                $_sNonce      = wp_create_nonce( 'aal_unit_preview' );
                $_sSpinnerURL = esc_url( admin_url( 'images/spinner.gif' ) );
                $_aAttributes = array(
                    'class'       => 'aal-unit-preview-frame',
                    'src'         => $this->___getUnitPreviewURL( $aArguments ),
                    'frameborder' => '0',
                    'border'      => '0',
                    // 'width'       => '200',
                    'height'      => '400',
                    'scrolling'   => 'yes',
                    'data-secret' => $_sNonce,
                    // 'style'       => 'background-image: url("' . $_sSpinnerURL . '"); background-repeat: no-repeat; background-position: center;',
                );
                $_aContainerAttributes = array(
                    'class'       => 'aal-unit-preview-container',
                );
                return "<div " . $this->getAttributes( $_aContainerAttributes ) . ">"
                        . "<iframe " . $this->getAttributes( $_aAttributes ) . "></iframe>"
                    . "</div>";
            }
                /**
                 * @return string
                 * @since  5.1.0
                 */
                private function ___getUnitPreviewURL( array $aArguments ) {
                    $_aQuery = array(
                        'aal-unit-preview' => 1,
                    ) + $aArguments;
                    $_aQuery = array_filter( $_aQuery, array( $this, 'isNotNull' ) );
                    return add_query_arg( $_aQuery, get_site_url() );
                }

}