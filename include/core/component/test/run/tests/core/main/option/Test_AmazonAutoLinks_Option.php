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
 * Tests the class, `AmazonAutoLinks_Option`.
 *
 * @since   4.6.19
 * @see     AmazonAutoLinks_Option
 * @tags    option
*/
class Test_AmazonAutoLinks_Option extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags property
     */
    public function testProperties() {
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        $this->_assertNotEmpty( $_oOption->aOptions );
    }

    /**
     * @tags kses
     */
    public function test_getAllowedHTMLTags() {
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        $_aAllowedTags  = $_oOption->getAllowedHTMLTags();
        $this->_outputDetails( 'Allowed HTML Tags', $_aAllowedTags );
        $_aFiltered     = array_filter( $_aAllowedTags, array( $this, 'isNotEmpty' ) );    // drop non-true values
        $this->_assertEqual( count( $_aAllowedTags ), count( $_aFiltered ) );
    }    
    
    /**
     * @tags kses
     */
    public function test_getAllowedHTMLAttributesLegacy() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        $_aAllowedAttributes = $_oOption->getAllowedHTMLAttributesLegacy();
        // $this->_outputDetails( 'Allowed HTML Attributes', $_aAllowedAttributes );
        $_aFiltered          = array_filter( $_aAllowedAttributes );    // drop non-true values
        $this->_assertEqual( count( $_aAllowedAttributes ), count( $_aFiltered ) );
    }

    /**
     * @tags kses, private
     * @throws ReflectionException
     */
    public function test_getAllowedHTMLAttributes() {

        $_oClass = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_Option', array( AmazonAutoLinks_Registry::$aOptionKeys[ 'main' ] ) );
        $_aHTMLTags = $_oClass->call( '___getAllowedHTMLTags' );
        // $this->_outputDetails( 'html tags', $_aHTMLTags );
        $_aFiltered          = array_filter( $_aHTMLTags, array( $this, 'isNotEmpty' ) );    // drop non-true values
        $this->_assertEqual( count( $_aHTMLTags ), count( $_aFiltered ) );

    }

    /**
     * @tags kses
     */
    public function test_getAllowedHTMLInlineStyles() {

        $_oOption = AmazonAutoLinks_Option::getInstance();
        $_sStyles = $_oOption->get( 'security', 'allowed_inline_css_properties' );
        $_aStyles = $_oOption->getAllowedHTMLInlineStyles();
        if ( $_sStyles ) {
            $this->_assertNotEmpty( $_aStyles );
        } else {
            $this->_assertEmpty( $_aStyles );
        }

    }


}