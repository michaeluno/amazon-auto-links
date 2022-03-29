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
 * Provides utility methods regarding WordPress KSES functionality.
 *
 * @since   4.6.19
 */
class AmazonAutoLinks_WPUtility_KSES extends AmazonAutoLinks_WPUtility_HTTP {

    /**
     * @param  string $sString  Comma separated attribute names such as `style, width, height`.
     * @return array
     * @sinec  4.6.19
     */
    static public function getKSESHTMLAttributes( $sString ) {
        $_aAttributes = self::getStringIntoArray( $sString, ',' );
        $_aAttributes = array_fill_keys( $_aAttributes, true );
        return array_change_key_case( $_aAttributes, CASE_LOWER );
    }

    /**
     * Provides the plugin required KSES allowed HTML tags.
     * @since  4.6.19
     * @remark For WordPress KSES functions.
     * @return array
     * Structure:
     * ```
     * array(
     *      // tag name => attributes
     *      'div' => array(
     *          // attribute name in lowercase => true/false
     *          'style'  => true,
     *          'script' => false,
     *          'data-*' => true,
     *      ),
     *      'a' => array(
     *          'rel' => true,
     *      ),
     *      ...
     * )
     * ```
     */
    static public function getRequiredKSESHTMLTags() {
        $_aRequiredHTMLTags   = array(
            'div', 'span', 'ol', 'ul', 'li', 'a', 'img',
            'svg', 'use', 'image', 'g', 'defs',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        );
        $_aGlobalAttributes   = array(
            'id'    => true,
            'class' => true,
            'name'  => true,
            'style' => true,
            'title' => true,
        );        
        $_aSVGCoreAttributes = array(
            // Core
            'id'       => true,
            'tabindex' => true,
            // Styling
            'style'    => true,
            'class'    => true,
            // XLink
            'xlink:href' => true,
            'xlink:title' => true,
        ) + $_aGlobalAttributes;

        $_aAllowedPostTags    = array(
            // 'a'              => array(
            //     'style' => true,
            // ) + $_aGlobalAttributes,
            'svg'   => array(
                'version'               => true,
                'class'                 => true,
                'fill'                  => true,
                'aria-hidden'           => true,
                'aria-labelledby'       => true,
                'role'                  => true,
                'xmlns'                 => true,
                'xmlns:svg'             => true,
                'xmlns:xlink'           => true,
                'width'                 => true,
                'height'                => true,
                'viewbox'               => true,   // viewBox
                'preserveaspectratio'   => true,   // preserveAspectRatio
                'enable-background'     => true,
            ) + $_aSVGCoreAttributes,
            'use'            => array(
                'href'              => true,
                'xlink:href'        => true,
                'fill'              => true,
                'x'                 => true,
                'y'                 => true,
                'width'             => true,
                'height'            => true,
            ) + $_aSVGCoreAttributes,
            'image'          => array(
                'src'        => true,
            ) + $_aSVGCoreAttributes,
            'g'     => array(
                'fill'      => true,
                'transform' => true,
            ) + $_aSVGCoreAttributes,
            'stop'           => array(
                 'offset'       => true,
                 'stop-color'   => true,
                 'stop-opacity' => true,
            ) + $_aSVGCoreAttributes,
            'lineargradient' => array() + $_aSVGCoreAttributes, // linearGradient
            'defs' => array() + $_aSVGCoreAttributes,
            'title' => array( 
                'title' => true 
            ) + $_aSVGCoreAttributes,
            'path'  => array(
                'd'            => true,
                'fill'         => true,
                'stroke'       => true,
                'stroke-width' => true,
            ) + $_aSVGCoreAttributes,
        );
        return $_aAllowedPostTags;
    }

    /**
     * Escapes the given string for the KSES filter with the criteria of allowing tags, tag attributes, and the protocol.
     * @param  string $sString
     * @param  array  $aAllowedHTMLTags
     * Structure:
     * ```
     * array(
     *      'svg' => array(
     *          'viewport' => true,
     *           'fill' => true,
     *      ),
     *      'use' => array(
     *          'stroke' => true,
     *          'fill' => true,
     *      ),
     *      ...
     * )
     * ```
     * @param  array  $aAllowedProtocols
     * @return string
     * @since  4.6.19
     */
    static public function getEscapedWithKSES( $sString, $aAllowedHTMLTags=array(), $aAllowedProtocols=array() ) {
        if ( empty( $aAllowedProtocols ) ) {
            $aAllowedProtocols = wp_allowed_protocols();
        }
        if ( empty( $aAllowedHTMLTags ) ) {
            $aAllowedHTMLTags = wp_kses_allowed_html( 'post' );
        }
        $sString = addslashes( $sString );                                              // the original function call was doing this - could be redundant but haven't fully tested it
        $sString = stripslashes( $sString );                                            // wp_filter_post_kses()
        $sString = wp_kses_no_null( $sString );                                         // wp_kses()
        $sString = wp_kses_normalize_entities( $sString );                              // wp_kses()
        $sString = wp_kses_hook( $sString, $aAllowedHTMLTags, $aAllowedProtocols );     // WP changed the order of these funcs and added args to wp_kses_hook
        $sString = wp_kses_split( $sString, $aAllowedHTMLTags, $aAllowedProtocols );
        $sString = addslashes( $sString );                                              // wp_filter_post_kses()
        return stripslashes( $sString );                                                // the original function call was doing this - could be redundant but haven't fully tested it
    }

    /**
     * Escapes the given string for the KSES filter with the criteria of allowing/disallowing tags and the protocol.
     *
     * @remark      Attributes are not supported at this moment.
     * @param       string      $sString
     * @param       array       $aAllowedTags               e.g. array( 'noscript', 'style', )
     * @param       array       $aAllowedProtocols
     * @param       array       $aDisallowedTags            e.g. array( 'table', 'tbody', 'thoot', 'thead', 'th', 'tr' )
     * @param       array       $aAllowedAttributes         e.g. array( 'rel', 'itemtype', 'style' )
     * @since       2.0.0
     * @since       3.1.0       Added the $aAllowedAttributes parameter.
     * @since       4.6.19      Renamed from `escapeKSESFilter()`.
     * @return      string
     * @see         wp_kses()
     */
    static public function getEscapedWithKSESLegacy( $sString, $aAllowedTags=array(), $aDisallowedTags=array(), $aAllowedProtocols=array(), $aAllowedAttributes=array() ) {

        $aFormatAllowedTags = array();
        foreach( $aAllowedTags as $sTag ) {
            $aFormatAllowedTags[ $sTag ] = array();    // activate the inline style attribute.
        }
        $aAllowedHTMLTags = AmazonAutoLinks_Utility::uniteArrays( $aFormatAllowedTags, $GLOBALS[ 'allowedposttags' ] );    // the first parameter takes over the second.

        foreach( $aDisallowedTags as $sTag ) {
            if ( isset( $aAllowedHTMLTags[ $sTag ] ) ) {
                unset( $aAllowedHTMLTags[ $sTag ] );
            }
        }

        // Set allowed attributes.
        $_aFormattedAllowedAttributes = array_fill_keys( $aAllowedAttributes, 1 );
        foreach( $aAllowedHTMLTags as $_sTagName => $_aAttributes ) {
            $aAllowedHTMLTags[ $_sTagName ] = $_aAttributes + $_aFormattedAllowedAttributes;
        }
        return self::getEscapedWithKSES( $sString );

    }
        /**
         * @param string $sString
         * @param array  $aAllowedTags
         * @param array  $aDisallowedTags
         * @param array  $aAllowedProtocols
         * @param array  $aAllowedAttributes
         * @return       string
         * @deprecated   4.6.19 Use getEscapedWithKSESLegacy()
         */
        static public function escapeKSESFilter( $sString, $aAllowedTags=array(), $aDisallowedTags=array(), $aAllowedProtocols=array(), $aAllowedAttributes=array() ) {
            return self::getEscapedWithKSESLegacy( $sString, $aAllowedTags=array(), $aDisallowedTags=array(), $aAllowedProtocols=array(), $aAllowedAttributes=array() );
        }

}