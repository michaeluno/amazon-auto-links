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
 * Provides methods to generate breadcrumb of selected categories.
 *
 * @since       3.5.7
 */
class AmazonAutoLinks_Form_CategorySelect___Sidebar___Breadcrumb {

    protected $_sLocale    = 'US';
    protected $_oDoc;

    /**
     * A part of class selector for the selected list element.
     * @var   string
     * @since 4.6.13
     */
    protected $_sClassSelectorSelected = 'zg_selected';

    /**
     * @since       3.5.7
     */
    public function __construct( DOMDocument $oDoc, $sLocale ) {
        $this->_sLocale    = $sLocale;
        $this->_oDoc       = $oDoc;
    }

    /**
     * @since       3.5.7
     */
    public function get() {
        return $this->_getBreadcrumb( $this->_oDoc, $this->_sLocale );
    }
        /**
         * Creates a breadcrumb of the Amazon page sidebar.
         *
         * This is specific to Amazon's store page so if the site page structure changes, it won't work.
         * Especially it uses the unique id and class names including zg_browseRoot, zg_selected, the sidebar element IDs.
         *
         * @since           2.0.0
         * @since           3.5.7   Changed the scope to private as this is only used in this class.
         * @since           3.5.7   Moved from `AmazonAutoLinks_Form_CategorySelect`.
         * @since           3.9.1   No longer uses PHP Simple DOM Parser.
         * @since           4.6.13  Change the scope from private to protected as an extended class is added.
         * @return          string  The generated category breadcrumb.
         */
        protected function _getBreadcrumb( DOMDocument $oDoc, $sLocale='US' ) {

            $aBreadcrumb    = array();

            $_oXpath        = new DOMXPath( $oDoc );
            $_nodeSelected  = $_oXpath->query(
                "//*[contains(@class, '{$this->_sClassSelectorSelected}')]"
            )->item( 0 );

            if ( ! $_nodeSelected ) {
                return '';
            }

            // Current category
            $aBreadcrumb[]  = trim( $_nodeSelected->nodeValue );

            // Climb up the node
            $_nodeClimb     = $_nodeSelected->parentNode;
            Do {
                if ( $this->_isParentNode( $_nodeClimb ) ) {
                    $aBreadcrumb[] = $this->_getListItemText( $_nodeClimb );
                }
                $_nodeClimb = $_nodeClimb->parentNode;
            } While (
                is_object( $_nodeClimb )
                && method_exists( $_nodeClimb, 'getAttribute' ) // it can be the root DOMDocument object and in that case the method does not exist
                && $this->_isNotListRoot( $_nodeClimb )
            );

            array_pop( $aBreadcrumb );    // remove the last element
            $aBreadcrumb[] = strtoupper( $sLocale );    // set the last element to the country code
            $aBreadcrumb   = array_reverse( $aBreadcrumb );
            return implode( " > ", $aBreadcrumb );

        }

    /**
     * @param  $oNode
     * @return boolean
     * @since  5.0.4
     */
    protected function _isParentNode( $oNode ) {
        return 'ul' === $oNode->tagName;
    }

    /**
     * @param  $oNode
     * @return string
     * @since  5.0.4
     */
    protected function _getListItemText( $oNode ) {
        $_nodeUpperUl  = $oNode->parentNode;
        $_nodeLi       = $_nodeUpperUl->getElementsByTagName( 'li' )->item( 0 );
        $_nodeA        = $_nodeLi->getElementsByTagName( 'a' )->item( 0 );
        return trim( $_nodeA->nodeValue );
    }

    /**
     * @param  $oNode
     * @since  4.6.13
     * @return boolean
     */
    protected function _isNotListRoot( $oNode ) {
        return 'zg_browseRoot' !== $oNode->getAttribute( 'id' );
    }

}