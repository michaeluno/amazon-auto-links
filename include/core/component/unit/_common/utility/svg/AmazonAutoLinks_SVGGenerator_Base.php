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
 * A base class for SVG generator classes.
 *
 * @since 4.6.0
 */
class AmazonAutoLinks_SVGGenerator_Base {

    /**
     * @var array
     */
    static public $aDefinitions = array();

    /**
     * @var string The SVG main content.
     */
    public $sSVGInnerHTML = '';

    /**
     * @var boolean whether to use existent SVG definitions
     */
    public $bUseCache = true;

    /**
     * @var string
     */
    public $sSRCFallbackImage = '';

    /**
     * @var string
     */

    public $sTitle    = '';

    /**
     * @var boolean Indicates whether a SVG is already rendered.
     */
    static public $bSVGShown = false;

    /**
     * @var   array
     * @since 4.6.1
     */
    static public $aDefinitionHooks = array();

    /**
     * Sets up properties and hooks.
     * @param boolean $bUseCache
     * @param string  $sTitle
     * @param string  $sSRCFallbackImage
     */
    public function __construct( $bUseCache=true, $sTitle='', $sSRCFallbackImage='' ) {

        $this->bUseCache         = $bUseCache;
        $this->sTitle            = $sTitle;
        $this->sSRCFallbackImage = $sSRCFallbackImage;

        $_sExtendedClassName     =  get_class( $this );
        if ( $bUseCache && ! isset( self::$aDefinitionHooks[ $_sExtendedClassName ] ) ) {
            add_action( 'wp_footer', array( $this, 'replyToRenderSVGDefinition' ), 100 );
            add_action( 'embed_footer', array( $this, 'replyToRenderSVGDefinition' ), 100 );
            add_action( 'admin_footer', array( $this, 'replyToRenderSVGDefinition' ), 100 );
            add_filter( 'aal_filter_svg_definitions', array( $this, 'replyToGetSVGDefinition' ) );
            self::$aDefinitionHooks[ $_sExtendedClassName ] = true;
        }

    }

    /**
     * @remark Override this method in extended classes.
     * @return string
     */
    public function get() {
        return "<svg>"
                . $this->sSVGInnerHTML
                . "<image src='" . esc_url( $this->sSRCFallbackImage ) . "' />"
            . "</svg>";
    }

    /**
     * @param  string $sSVGDefinitions
     * @return string
     * @since  4.6.2
     */
    public function replyToGetSVGDefinition( $sSVGDefinitions ) {
        return $sSVGDefinitions . $this->_getDefinition();
    }

    /**
     *
     * @since 4.6.1
     * @callback add_action wp_footer
     * @callback add_action embed_footer
     */
    public function replyToRenderSVGDefinition() {
        echo apply_filters( 'aal_filter_output_svg_definition', $this->_getDefinition() );
    }

    /**
     * @since  4.6.1
     * @remark Override this method.
     * @return string The SVG definition for reuse.
     * @param  boolean $bVisible Whether to make it visible or not
     */
    protected function _getDefinition( $bVisible=false ) {
        return '';
    }

    /**
     * @return string
     * @since  4.6.1
     * @remark The now-retrieving updater uses this method.
     */
    public function getDefinition() {
        return $this->_getDefinition();
    }

}