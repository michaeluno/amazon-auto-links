<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 *
 */

/**
 * Diminishes HTTP request caches.
 *
 * Sometimes the sql size exceeds 1mb and some servers with a small value for the `max_allowed_packet` MySQL option
 * gets an error. As a result, the data gets not cached. TO avoid that the data should be diminished (compressed).
 *
 * This class helps to just remove unnecessary elements from retrieved HTML outputs.
 *
 * @since       3.7.5
 */
class AmazonAutoLinks_Unit_URL_Event_HTTPCacheDiminisher extends AmazonAutoLinks_PluginUtility {

    public function __construct() {

        add_filter( 'aal_filter_http_request_set_cache', array( $this, 'replyToDiminishCacheContent' ), 10, 5 );

    }

    /**
     * Called when an HTTP request cache is going to be saved.
     *
     * @param $mData
     * @param $sRequestType
     * @param $sCacheName
     * @param $sCharSet
     * @param $iCacheDuration
     *
     * @return mixed
     */
    public function replyToDiminishCacheContent( $mData, $sRequestType, $sCacheName, $sCharSet, $iCacheDuration ) {

        if ( 'url_unit_type' !== $sRequestType ) {
            return $mData;
        }

        if ( ! isset( $mData[ 'body' ] ) ) {
            return $mData;
        }

        $_sHTML = $mData[ 'body' ];
        $_sHTML = $this->___getHTMLDminishedWithDOMDocumnet( $_sHTML, $sCharSet );

        // Remove doubled white spaces.
        $_sHTML = preg_replace( '/\s{2,}/', ' ', $_sHTML );

        $mData[ 'body' ] = $_sHTML;
        return $mData;

    }
        private function ___loadLibraries() {
            // Include the library.
            if ( ! class_exists( 'simple_html_dom_node', false ) ) {
                include_once( AmazonAutoLinks_Registry::$sDirPath . '/include/library/simple_html_dom.php' );
            }
        }

        private function ___getHTMLDminishedWithDOMDocumnet( $sHTML, $sCharSet ) {
            $_oAALDOM = new AmazonAutoLinks_DOM;
            // @see https://stackoverflow.com/a/8218649
            $_sPrefix = '<' . '?' . 'xml encoding=' . '"' . $sCharSet . '" ' . '?' . '>';
            $dom = $_oAALDOM->loadDOMFromHTML( $_sPrefix . $sHTML, 'uni', $sCharSet );
            $_aTags = array( 'script', 'style', 'link', 'meta', 'br',  );
            foreach( $_aTags as $_sTagName ) {
                $this->___removeNodesByTagName( $dom, $_sTagName );
            }
            $this->___removeCommentsByXPath( $dom );
            $_aAttributes = array( 'onload', 'onclick', 'title', 'style', 'class', 'align', 'border', 'for', 'action', 'aria-label' );
            $this->___removeAttributesByXPath( $dom, $_aAttributes );
            return $dom->saveHTML();
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
                foreach ($xpath->query('//comment()') as $comment) {
                    $comment->parentNode->removeChild($comment);
                }
            }

    /**
     * @param $_sHTML
     *
     * @return string
     * @deprecated
     */
    private function ___getHTMLDminishedBySimpleHTMLDOMParser( $_sHTML ) {
        if ( ! $this->hasBeenCalled( __METHOD__ ) ) {
            $this->___loadLibraries();
        }
        $_oSimpleDOM = str_get_html( $_sHTML );
        $this->___removeElementsByTagNames( $_oSimpleDOM, array( 'script', 'style', 'link', 'meta', 'comment', 'br' ) );
        $this->___removeAttributes(
            $_oSimpleDOM,
            '*',
            array( 'onload', 'onclick', 'title', 'style', 'width', 'height', 'class', 'align', 'border', 'for', 'action', 'aria-label' )
        );

        $_sHTML = ( string ) $_oSimpleDOM; // dump the modified HTML
        return $_sHTML;
    }
        /**
         * @param simple_html_dom $oSimpleDOM
         * @param $sSelector
         * @param array $aAttrToRemove
         * @deprecated  apache gets hung
         */
        private function ___removeAttributes( simple_html_dom $oSimpleDOM, $sSelector, array $aAttrToRemove ) {
            foreach( $oSimpleDOM->find( $sSelector ) as $_oNode ) {
                foreach ( $_oNode->getAllAttributes() as $_sAttributeName => $_sValue ) {
                    if ( in_array( $_sAttributeName, $aAttrToRemove ) ) {
                        $_oNode->removeAttribute( $_sAttributeName );
                    }
                }
            }
        }
        private function ___removeElementsByTagNames( simple_html_dom $oSimpleDOM, array $aTagNames ) {
            foreach( $aTagNames as $_sTagName ) {
                foreach( $oSimpleDOM->find( $_sTagName ) as $_oNode ) {
                    $_oNode->outertext = '';
                }
            }
        }

}