<?php
/**
 * Provides Dom related functions.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 */

final class AmazonAutoLinks_DOM {

    function __construct() {
        
        $this->strCharEncoding = get_bloginfo( 'charset' ); 
        $this->oEncrypt = new AmazonAutoLinks_Encrypt;
        $this->strHTMLCachePrefix = AmazonAutoLinks_Commons::TransientPrefix . "_HTML_";
    }
    
        
    public function loadDOMFromHTMLElement( $strHTMLElements, $strMBLang='uni' ) {
        
        // this prevents later when using saveXML() from inserting the comment <!-- xml version .... -->
        $strHTMLElements = '<div>' . $strHTMLElements . '</div>';        
        return $this->loadDOMFromHTML( $strHTMLElements, $strMBLang );
        
    }    
    public function loadDOMFromURL( $strURL, $strMBLang='uni', $fUseFileGetContents=false ) {
            
        $strHTML = $this->getHTML( $strURL, $fUseFileGetContents );
        return $this->loadDOMFromHTML( $strHTML, $strMBLang );

    }    
    public function loadDOMFromHTML( $strHTML, $strMBLang='uni', $fDetectEncode=true ) {
        
        if ( ! empty( $strMBLang ) ) mb_language( $strMBLang ); // without this, the characters get broken    
        
        if ( $fDetectEncode ) {
            $strEncoding = @mb_detect_encoding( $strHTML, 'AUTO' );
            $strHTML = @mb_convert_encoding( $strHTML, $this->strCharEncoding , $strEncoding );    
            $strHTML = @mb_convert_encoding( $strHTML, 'HTML-ENTITIES', $this->strCharEncoding );         
        }
        // mb_internal_encoding( $this->strCharEncoding ); // not sure if this is necessary

        $oDOM = new DOMDocument( '1.0', $this->strCharEncoding );
        $oDOM->recover = true;    // http://stackoverflow.com/a/7386650, http://stackoverflow.com/a/9281963
        // $oDOM->strictErrorChecking = false;     // not sure about this.
        $oDOM->preserveWhiteSpace = false;
        $oDOM->formatOutput = true;
        @$oDOM->loadHTML( $strHTML );    
        return $oDOM;
        
    }
    
    public function getInnerHTML( $oNode ) {
        $strInnerHTML = ""; 
        $oChildNodes = $oNode->childNodes; 
        foreach ( $oChildNodes as $oChildNode ) { 
            $oTempDom = new DOMDocument( '1.0', $this->strCharEncoding );
            $oTempDom->appendChild( $oTempDom->importNode( $oChildNode, true ) ); 
            $strInnerHTML .= trim( @$oTempDom->saveHTML() ); 
        } 
        return $strInnerHTML;     
        
    }

    /**
     * Fetches HTML body with the specified URL with caching functionality.
     * 
     */
    public function getHTML( $strURL, $fUseFileGetContents=false ) {

        // Check the transient first.
        $strTransient =  $this->strHTMLCachePrefix . md5( $strURL );
        $strHTMLBodyEncoded = AmazonAutoLinks_WPUtilities::getTransient( $strTransient );
        if ( false !== $strHTMLBodyEncoded )
            return $this->oEncrypt->decode( $strHTMLBodyEncoded );
    
        // Fetch HTML.
        if ( $fUseFileGetContents )
            $strHTMLBody = file_get_contents( $strURL );
        else {
            $arrResponse = wp_remote_get( $strURL,  array( 'timeout' => 20, 'httpversion' => '1.1' ) );
            $strHTMLBody = wp_remote_retrieve_body( $arrResponse );
        }
    
        // If failed or empty, return.
        if ( empty( $strHTMLBody ) ) return $strHTMLBody;
        
        // Encode the HTML string; otherwise, sometimes it gets broken.
        AmazonAutoLinks_WPUtilities::setTransient( $strTransient, $this->oEncrypt->encode( $strHTMLBody ), 60*60*48 );
        return $strHTMLBody;
    
    }
    
    /**
     * Deletes the cache of the provided URL.
     */
    public function deleteCache( $strURL ) {
        AmazonAutoLinks_WPUtilities::deleteTransient( $this->strHTMLCachePrefix . md5( $strURL ) );
    }
    
    /**
     * Modifies the attributes of the given node elements by specifying a tag name.
     * 
     * Example:
     * $oDom->setAttributesByTagName( $oNode, 'a', array( 'target' => '_blank', 'rel' => 'nofollow' ) );
     * 
     */
    public function setAttributesByTagName( $oNode, $strTagName, $arrAttributes=array() ) {
        
        $arrAttributes = ( array ) $arrAttributes;        
        foreach( $oNode->getElementsByTagName( $strTagName ) as $oSelectedNode ) 
            foreach( $arrAttributes as $strAttribute => $strProperty ) 
                @$oSelectedNode->setAttribute( $strAttribute, $strProperty );
            
    }

    /**
     *    Removes nodes by tag an class selector. 
     * 
     * Example:
     * $this->oDOM->removeNodeByTagAndClass( $nodeDiv, 'span', 'riRssTitle' );
     * 
     */
    public function removeNodeByTagAndClass( $oNode, $strTagName, $strClassName, $intIndex='' ) {
        
        $oNodes = $oNode->getElementsByTagName( $strTagName );
        
        // If the index is specified,
        if ( $intIndex === 0 || is_integer( $intIndex ) ) {
            $oTagNode = $oNodes->item( $intIndex );
            if ( $oTagNode )         
                if ( stripos( $oTagNode->getAttribute( 'class' ), $strClassName ) !== false )     
                    $oTagNode->parentNode->removeChild( $oTagNode );
        }
        
        // Otherwise, remove all - Dom is a live object so iterate backwards
        for ( $i = $oNodes->length - 1; $i >= 0; $i-- ) {
            $oTagNode = $oNodes->item( $i );
            if ( stripos( $oTagNode->getAttribute( 'class' ), $strClassName ) !== false ) 
                $oTagNode->parentNode->removeChild( $oTagNode );
        }
        
    }                
    
}