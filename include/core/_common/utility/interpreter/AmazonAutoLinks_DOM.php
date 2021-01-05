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
 * Provides Dom related functions.
 * 
 * @package     Amazon Auto Links
 * @since       2.0.0
 * @since       3       Extends `AmazonAutoLinks_WPUtility`.
 */
class AmazonAutoLinks_DOM extends AmazonAutoLinks_WPUtility {

    /**
     * @var string
     */
    public $sCharEncoding;

    /**
     * @var AmazonAutoLinks_Encrypt
     */
    public $oEncrypt;

    /**
     * @var bool
     */
    public $bIsMBStringInstalled;

    /**
     * @var bool
     */
    public $bLoadHTMLFix;

    /**
     * Sets up properties.
     */
    function __construct() {
        
        $this->sCharEncoding    = get_bloginfo( 'charset' ); 
        $this->oEncrypt         = new AmazonAutoLinks_Encrypt;
        $this->bIsMBStringInstalled = function_exists( 'mb_language' );
        $this->bLoadHTMLFix     = defined( 'LIBXML_HTML_NOIMPLIED' ) && defined( 'LIBXML_HTML_NODEFDTD' ) 
            && ( version_compare( PHP_VERSION, '5.4.0' ) >= 0 );
        
    }

    /**
     * Creates a DOM object from a given HTML string.
     *
     * @remark      To output the modified HTML, perform
     * `
     * $_oDoc->saveXML( $_oDoc->documentElement, LIBXML_NOEMPTYTAG );
     * `
     * @param  string $sHTMLElements
     * @param  string $sMBLang
     * @param  string $sSourceCharSet
     * @return DOMDocument
     */
    public function loadDOMFromHTMLElement( $sHTMLElements, $sMBLang='uni', $sSourceCharSet='' ) {
        return $this->loadDOMFromHTML(
            '<div>' . $sHTMLElements . '</div>', // Enclosing in a div tag prevents from inserting the comment <!-- xml version .... --> when using saveXML() later.
            $sMBLang,
            $sSourceCharSet 
        );
    }

    /**
     * Creates a DOM object from a given url.
     * @param  string  $sURL
     * @param  string  $sMBLang
     * @param  boolean $bUseFileGetContents
     * @param  string  $sSourceCharSet
     * @param  integer $iCacheDuration
     * @return DOMDocument
     * @since  2.0.0
     * @since  3.2.0       Added the cache duration parameter.
     */
    public function loadDOMFromURL( $sURL, $sMBLang='uni', $bUseFileGetContents=false, $sSourceCharSet='', $iCacheDuration=86400 ) {
        return $this->loadDOMFromHTML( 
            $this->getHTML( 
                $sURL, 
                $bUseFileGetContents,
                $iCacheDuration
            ), 
            $sMBLang,
            $sSourceCharSet
        );
    }

    /**
     * 
     * @param       string          $sHTML     
     * @param       string          $sMBLang     
     * @param       string          $sSourceCharSet     If true, it auto-detects the character set. If a string is given, 
     * the HTML string will be converted to the given character set. If false, the HTML string is treated as it is.
     * @return      DOMDocument
     */
    public function loadDOMFromHTML( $sHTML, $sMBLang='uni', $sSourceCharSet='' ) {

        // without this, the characters get broken
        if ( ! empty( $sMBLang ) && $this->bIsMBStringInstalled ) {
            mb_language( $sMBLang ); 
        }

        if ( false !== $sSourceCharSet ) {
            $sHTML       = $this->convertCharacterEncoding(
                $sHTML, // subject
                $this->sCharEncoding, // to
                $sSourceCharSet, // from
                false   // no html entities conversion
            );           
        }

        // @todo    Examine whether the below line takes effect or not.
        // mb_internal_encoding( $this->sCharEncoding );                     
        
        $_bInternalErrors = libxml_use_internal_errors( true );
        
        $oDOM                     = new DOMDocument( 
            '1.0', 
            $this->sCharEncoding
        );
        $oDOM->recover            = true;    // @see http://stackoverflow.com/a/7386650, http://stackoverflow.com/a/9281963
        // $oDOM->sictErrorChecking = false; // @todo examine whether this is necessary or not. 
        $oDOM->preserveWhiteSpace = false;
        $oDOM->formatOutput       = true;
        $this->_loadHTML( $oDOM, $sHTML );
        
        libxml_use_internal_errors( $_bInternalErrors );
        
        return $oDOM;
        
    }

        /**
         * Performs the `loadHTML()` DOMDocument method with some additional checks and sanitization.
         * @param  DOMDocument $oDOM
         * @param  string $sHTML
         * @return void
         * @since  3.4.1
         */
        private function _loadHTML( $oDOM, $sHTML ) {
            
            $sHTML = function_exists( 'mb_convert_encoding' )
                ? mb_convert_encoding( $sHTML, 'HTML-ENTITIES', $this->sCharEncoding )
                : $sHTML;
            
            if ( $this->bLoadHTMLFix ) {
                $oDOM->loadHTML( 
                    $sHTML,     // subject HTML contents to parse
                    LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD        // removes <html><body> tags
                );
                return;
            }
            $oDOM->loadHTML( 
                $sHTML     // subject HTML contents to parse
            );               
            
        }

    /**
     *
     * @remark Be careful that the output becomes HTML-entity-encoded so `html_entity_decode( $output )` must be applied after that.
     * @param  DOMNode $oNode
     * @return string
     */
    public function getInnerHTML( $oNode ) {
        $sInnerHTML  = ""; 
        if ( ! $oNode ) {
            return $sInnerHTML;
        }
        $oChildNodes = $oNode->childNodes; 
        foreach ( $oChildNodes as $_oChildNode ) { 
            $_oTempDom      = new DOMDocument( '1.0', $this->sCharEncoding );
            $_oImportedNode = $_oTempDom->importNode( $_oChildNode, true );
            if ( $_oImportedNode ) {
                $_oTempDom->appendChild( $_oImportedNode );
            } 

            // [4.3.4] Clean HTML.
            $_oTempDom->preserveWhiteSpace = false;
            $_oTempDom->formatOutput       = true;
            // [3.4.1] Sometimes <html><body> tags get inserted.
            $sInnerHTML .= $this->___getAutoInjectedWrapperTagsRemoved( @$_oTempDom->saveHTML() );
            
        } 
        return $sInnerHTML; // @todo might need html_entity_decode( $sInnerHTML )
        
    }
        /**
         * Removes wrapped `<html>` and `<body>`tags from a given string.
         *
         * Sometimes $oDOM->saveHTML() returns a string with <html><body> wrapped. Use this method to remove those.
         *
         * @param  string $sHTML
         * @return string
         * @since  2.4.1
         */
        private function ___getAutoInjectedWrapperTagsRemoved( $sHTML ) {
            
            $sHTML = trim( $sHTML );
            
            if ( $this->bLoadHTMLFix ) {
                return $sHTML;
            }

            return preg_replace(
                '~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', 
                '', 
                $sHTML
            );
            
        }
    
    /**
     * Fetches HTML body with the specified URL with caching functionality.
     * 
     * @return      string
     * @param       string      $sURL
     * @param       boolean     $bUseFileGetContents    This is deprecated
     * @param       integer     $iCacheDuration
     */
    public function getHTML( $sURL, $bUseFileGetContents=false, $iCacheDuration=86400 ) {

        // @deprecated 4.2.10
//        if ( $bUseFileGetContents ) {
//            $_oHTML = new AmazonAutoLinks_HTTPClient_FileGetContents( $sURL, $iCacheDuration );
//            return $_oHTML->get();
//        }
        $_oHTML = new AmazonAutoLinks_HTTPClient( $sURL, $iCacheDuration );
        return $_oHTML->get();    
    
    }

    /**
     * Modifies the attributes of the given node elements by specifying a tag name.
     *
     * Example:
     * `
     * $oDom->setAttributesByTagName( $oDoc, 'a', array( 'target' => '_blank', 'rel' => 'nofollow noopener' ) );
     * `
     * @param DOMDocument $oDoc
     * @param string      $sTagName
     * @param array       $aAttributes
     */
    public function setAttributesByTagName( $oDoc, $sTagName, $aAttributes=array() ) {
        
        foreach( $oDoc->getElementsByTagName( $sTagName ) as $_oSelectedNode ) {
            foreach( $this->getAsArray( $aAttributes ) as $_sAttribute => $_sProperty ) {
                if ( in_array( $_sAttribute, array( 'src', 'href' ) ) ) {
                    $_sProperty = esc_url( $_sProperty );
                }
                @$_oSelectedNode->setAttribute( 
                    $_sAttribute, 
                    esc_attr( $_sProperty )
                );
            }
        }
            
    }

    /**
     * Removes nodes by tag and class selector.
     *
     * Example:
     * `
     * $this->oDOM->removeNodeByTagAndClass( $nodeDiv, 'span', 'riRssTitle' );
     * `
     * @param DOMDocument $oDoc
     * @param string      $sTagName
     * @param string      $sClassName
     * @param string      $iIndex
     * @deprecated 4.3.4 Unused.
     */
/*    public function removeNodeByTagAndClass( $oDoc, $sTagName, $sClassName, $iIndex='' ) {
        
        $oNodes = $oDoc->getElementsByTagName( $sTagName );
        
        // If the index is specified,
        if ( 0 === $iIndex || is_integer( $iIndex ) ) {
            $oTagNode = $oNodes->item( $iIndex );
            if ( $oTagNode ) {
                if ( stripos( $oTagNode->getAttribute( 'class' ), $sClassName ) !== false ) {
                    $oTagNode->parentNode->removeChild( $oTagNode );
                }
            }
        }
        
        // Otherwise, remove all - Dom is a live object so iterate backwards
        for ( $i = $oNodes->length - 1; $i >= 0; $i-- ) {
            $oTagNode = $oNodes->item( $i );
            if ( stripos( $oTagNode->getAttribute( 'class' ), $sClassName ) !== false ) {
                $oTagNode->parentNode->removeChild( $oTagNode );
            }
        }
        
    } */

    /**
     * Removes specified tags from the given dom node.
     * @param DOMDocument $oDom
     * @param array $aTags
     */
    public function removeTags( DOMDocument $oDom, array $aTags ) {
        
        foreach( $aTags as $_sTag ) {
            
            $_oXpath = new DOMXPath( $oDom );
            $_oNode  = $_oXpath->query( 
                "//*/{$_sTag}" 
            );
            foreach( $_oNode as $e ) {
                $e->parentNode->removeChild( $e );
            }          
        
        }
    }

    /**
     * @param DOMDocument $oDoc
     * @since 4.3.4
     */
    public function removeComments( DOMDocument $oDoc ) {
        $_oXPath = new DOMXPath( $oDoc );
        foreach ( $_oXPath->query('//comment()' ) as $_oCommentNode ) {
            $_oCommentNode->parentNode->removeChild( $_oCommentNode );
        }
    }

    /**
     * @param  DOMDocument $oDoc
     * @param  string      $sTag
     * @param  integer     $iIndex
     * @return string      Returns an outer HTML output of a specified tag.
     * @since  3.2.0
     */
    public function getTagOuterHTML( DOMDocument $oDoc, $sTag, $iIndex=0 ) {
        $_oXPath           = new DOMXPath( $oDoc );
        $_oTags            = $_oXPath->query( "/html/{$sTag}" );
        $_oTag             = $_oTags->item( $iIndex );
        return $oDoc->saveXml( $_oTag, LIBXML_NOEMPTYTAG );                                    
    }    

}