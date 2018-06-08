<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Provides methods to generate breadcrumb of selected categories.
 *
 * @sicne       3.5.7
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb {

    private $___oSimpleDOM = null;
    private $___sLocale    = 'US';
    private $___sRSSURL    = '';

    /**
     * @since       3.5.7
     */
    public function __construct( $oSimpleDOM, $sLocale, $sRSSURL ) {

        // Properties
        $this->___oSimpleDOM = $oSimpleDOM;
        $this->___sLocale    = $sLocale;
        $this->___sRSSURL    = $sRSSURL;

    }

    /**
     * @since       3.5.7
     */
    public function get() {
        return $this->___sRSSURL
            ? $this->___getBreadcrumb( $this->___oSimpleDOM, $this->___sLocale )
            : __( 'None', 'amazon-auto-links' );
    }
        /**
         * Creates a breadcrumb of the Amazon page sidebar.
         *
         * This is specific to Amazon's store page so if the site page sucture changes, it won't work.
         * Especially it uses the unique id and class names including zg_browseRoot, zg_selected, the sidebar element IDs.
         *
         * @since           2.0.0
         * @since           3.5.7   Changed the scope to private as this is only used in this class.
         * @since           3.5.7     Moved from `AmazonAutoLinks_Form_CategorySelect`.
         * @return          string  The generated category breadcrumb.
         */
        private function ___getBreadcrumb( $_oSimpleDOM, $sLocale='US' ) {

            $aBreadcrumb    = array();
            $nodeBrowseRoot = $_oSimpleDOM->getElementById( 'zg_browseRoot' );
            $nodeSelected   = $nodeBrowseRoot->find( '.zg_selected', 0 );
            if ( ! $nodeSelected ) {
                return __( 'Failed to generate the breadcrumb.', 'amazon-auto-links' );
            }

            // Current category
            $aBreadcrumb[]  = trim( $nodeSelected->plaintext );

            // Climb up the node
            $nodeClimb      = $nodeSelected->parentNode();
            Do {
                if ( $nodeClimb->nodeName() == 'ul' ) {
                    $nodeUpperUl   = $nodeClimb->parentNode();
                    $nodeLi        = $nodeUpperUl->getElementByTagName( 'li' );
                    $nodeA         = $nodeLi->getElementByTagName( 'a' );
                    $aBreadcrumb[] = trim( $nodeA->innertext );
                }
                $nodeClimb = $nodeClimb->parentNode();

            } While ( $nodeClimb && $nodeClimb->getAttribute( 'id' ) != 'zg_browseRoot' );

            array_pop( $aBreadcrumb );    // remove the last element
            $aBreadcrumb[] = strtoupper( $sLocale );    // set the last element to the country code
            $aBreadcrumb   = array_reverse( $aBreadcrumb );
            return implode( " > ", $aBreadcrumb );

        }
}