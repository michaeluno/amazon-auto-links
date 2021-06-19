<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
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
     * Sets up properties and hooks.
     * @param boolean $bUseCache
     * @param string  $sTitle
     * @param string  $sSRCFallbackImage
     */
    public function __construct( $bUseCache=true, $sTitle='', $sSRCFallbackImage='' ) {
        $this->bUseCache         = $bUseCache;
        $this->sTitle            = $sTitle;
        $this->sSRCFallbackImage = $sSRCFallbackImage;
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

}