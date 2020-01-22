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
 * Provides methods to clean HTML source code.
 *
 * At the moment, applied to downloaded HTML documents.
 *
 * @package     AmazonAutoLinks/Utility
 * @since       3.7.7
 */
class AmazonAutoLinks_HTMLCleaner {

    private $___sHTML = '';
    private $___sCharSet = '';

    private $___aRemoveTags = array();
    private $___aRemoveAttributes = array();
    private $___bRemoveComments = true;
    private $___bRemoveExtraWhiteSpaces = true;

    /**
     * AmazonAutoLinks_HTMLCleaner constructor.
     *
     * @param $sHTML
     * @param string $sCharSet
     * @see     http://php.net/manual/en/mbstring.supported-encodings.php
     */
    public function __construct( $sHTML, $sCharSet='' ) {
        $this->___sHTML     = $sHTML;
        $this->___sCharSet  = $sCharSet;
    }

    public function setRemovingTags( array $aTags ) {
        $this->___aRemoveTags = $aTags;
    }
    public function setRemovingAttributes( array $aAttributes ) {
        $this->___aRemoveAttributes = $aAttributes;
    }
    public function setWhetherToRemoveComments( $bYesNo ) {
        $this->___bRemoveComments = $bYesNo;
    }
    public function setWhetherToRemoveWhiteSpaces( $bYesNo ) {
        $this->___bRemoveExtraWhiteSpaces = $bYesNo;
    }

    public function get() {
        $this->___sHTML = $this->___getWhiteSpacesRemoved( $this->___sHTML );
        $this->___sHTML = $this->___getHTMLDiminishedWithDOMDocument( $this->___sHTML, $this->___sCharSet );
        $this->___sHTML = $this->___getWhiteSpacesRemoved( $this->___sHTML );
        return $this->___sHTML;
    }
    private function ___getWhiteSpacesRemoved( $sHTML ) {
        if ( ! $this->___bRemoveExtraWhiteSpaces ) {
            return $sHTML;
        }
        // Remove doubled white spaces.
        return preg_replace( '/\s{2,}/', ' ', $sHTML );
    }
    private function ___getHTMLDiminishedWithDOMDocument( $sHTML, $sCharSet ) {

        $_oAALDOM = new AmazonAutoLinks_DOM;
        // @see https://stackoverflow.com/a/8218649
        $_sPrefix = $sCharSet
            ? '<' . '?' . 'xml encoding=' . '"' . $sCharSet . '" ' . '?' . '>'
            : '';
        $dom = $_oAALDOM->loadDOMFromHTML( $_sPrefix . $sHTML, 'uni', $sCharSet );

        foreach( $this->___aRemoveTags as $_sTagName ) {
            $this->___removeNodesByTagName( $dom, $_sTagName );
        }

        if ( $this->___bRemoveComments ) {
            $this->___removeCommentsByXPath( $dom );
        }

        $this->___removeAttributesByXPath( $dom, $this->___aRemoveAttributes );

        $_sHTML = $dom->saveHTML();
        if ( $_sPrefix ) {
            $_sHTML = str_replace( $_sPrefix, '', $_sHTML );
        }
        return $_sHTML;

    }
        private function ___removeAttributesByXPath( DOMDocument $dom, array $aAttributes ) {
            foreach( $aAttributes as $_sAttribute ) {
                $_oXpath = new DOMXPath($dom);            // create a new XPath
                $_oNodes = $_oXpath->query("//*[@{$_sAttribute}]");  // Find elements with a style attribute
                foreach ( $_oNodes as $_oNode ) {              // Iterate over found elements
                    foreach( $aAttributes as $_sAttribute ) {
                        $_oNode->removeAttribute( $_sAttribute );    // Remove style attribute
                    }
                }
            }
        }
        private function ___removeNodesByTagName( DOMDocument $doc, $sTagName ) {
            $_nodes = $doc->getElementsByTagName( $sTagName );
            for ($i = $_nodes->length; --$i >= 0; ) {
              $_node = $_nodes->item($i);
              $_node->parentNode->removeChild( $_node );
            }
        }
        /**
         * @see https://stackoverflow.com/questions/6305643/remove-comments-from-html-source-code
         */
        private function ___removeCommentsByXPath( DOMDocument $dom ) {
            $xpath = new DOMXPath($dom);
            foreach ( $xpath->query('//comment()' ) as $comment ) {
                $comment->parentNode->removeChild( $comment );
            }
        }

}