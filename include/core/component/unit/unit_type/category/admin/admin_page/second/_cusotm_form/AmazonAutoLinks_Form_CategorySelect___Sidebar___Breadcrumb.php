<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Provides methods to generate breadcrumb of selected categories.
 *
 * @sicne       3.5.7
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb {

    private $___sLocale    = 'US';
    private $___oDoc;
    /**
     * @since       3.5.7
     */
    public function __construct( DOMDocument $oDoc, $sLocale ) {

        $this->___sLocale    = $sLocale;
        $this->___oDoc       = $oDoc;

    }

    /**
     * @since       3.5.7
     */
    public function get() {
        return $this->___getBreadcrumb( $this->___oDoc, $this->___sLocale );
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
         * @since           3.9.1   No longer uses PHP Simple DOM Parser.
         * @return          string  The generated category breadcrumb.
         */
        private function ___getBreadcrumb( DOMDocument $oDoc, $sLocale='US' ) {

            $aBreadcrumb    = array();

            $_oXpath        = new DOMXPath( $oDoc );
            $_nodeSelected  = $_oXpath->query(
                "//*[contains(@class, 'zg_selected')]"
            )->item( 0 );

            if ( ! $_nodeSelected ) {
                return __( 'Failed to generate the breadcrumb.', 'amazon-auto-links' );
            }

            // Current category
            $aBreadcrumb[]  = trim( $_nodeSelected->nodeValue );

            // Climb up the node
            $_nodeClimb     = $_nodeSelected->parentNode;
            Do {
                if ( 'ul' === $_nodeClimb->tagName ) {
                    $_nodeUpperUl  = $_nodeClimb->parentNode;
                    $_nodeLi       = $_nodeUpperUl->getElementsByTagName( 'li' )->item( 0 );
                    $_nodeA        = $_nodeLi->getElementsByTagName( 'a' )->item( 0 );
                    $aBreadcrumb[] = trim( $_nodeA->nodeValue );
                }
                $_nodeClimb = $_nodeClimb->parentNode;
            } While (
                is_object( $_nodeClimb )
                && method_exists( $_nodeClimb, 'getAttribute' ) // it can be the root DOMDocument object and in that case the method does not exist
                && 'zg_browseRoot' !== $_nodeClimb->getAttribute( 'id' )
            );

            array_pop( $aBreadcrumb );    // remove the last element
            $aBreadcrumb[] = strtoupper( $sLocale );    // set the last element to the country code
            $aBreadcrumb   = array_reverse( $aBreadcrumb );
            return implode( " > ", $aBreadcrumb );

        }

}