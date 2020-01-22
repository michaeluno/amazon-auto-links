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
 * Provides method to truncate HTML string while preserving tags.
 * 
 * @see         http://stackoverflow.com/questions/16583676/shorten-text-without-splitting-words-or-breaking-html-tags/16584383#16584383
 * @package     Amazon Auto Links
 * @since       3.3.0
 * @deprecated  Note used at the moment.
 */
class AmazonAutoLinks_HTMLStringTruncator extends AmazonAutoLInks_DOM {
        
    protected $_bReachedLimit = false;
    protected $_iTotalLen     = 0;  
    // protected $iMaxLength     = 25,
    protected $_aToRemove     = array();
    
    public function getTrimmed( $sHTML, $iMaxLength=250, $sReadMore='' ) {
    
        $oDOM = $this->loadDOMFromHTML( $sHTML );

        // $oDOM = new DomDocument();
        // $oDOM->loadHTML( $sHTML );
        
        $_aToRemove = $this->_walk( $oDOM, $iMaxLength );
                
        // Remove any nodes that passed our limit
        foreach( $_aToRemove as $_oChild ) {
            $_oChild->parentNode->removeChild( $_oChild );
        }
// return $this->getInnerHTML( $oDOM );        
        
        /**
         * Remove wrapper tags added by DD (doctype, html...)
         * @see     http://stackoverflow.com/a/6953808/1058140
         */
        if( version_compare( PHP_VERSION, '5.3.6' ) < 0 ){
            $oDOM->removeChild( $oDOM->firstChild );            
            $oDOM->replaceChild( $oDOM->firstChild->firstChild->firstChild, $oDOM->firstChild );
            $_sOutput = $oDOM->saveHTML();
        }
        
        // $_sOutput = $this->getTagOuterHTML( $oDOM, 'body' );
        // $_sOutput = $oDOM->saveHTML( $oDOM->getElementsByTagName( 'body' )->item( 0 ) );
        $_sOutput = $oDOM->saveHTML();
        
        if ( $this->_bReachedLimit ) {
            $_sOutput .= $sReadMore;
        }        
        
        // return force_balance_tags( $_sOutput );
        return $_sOutput;
    }
    
    /**
     * @return      array
     */
    private function _walk( DomNode $oNode, $iMaxLength ){
    
        if ( $this->_bReachedLimit ) {
            
            $this->_aToRemove[] = $oNode;
            return $this->_aToRemove;
            
        } 

        /**
         * Only text nodes should have text so do the splitting here.
         */
        if ( $oNode instanceof DomText ) {
            
            $this->_iTotalLen += $oNodeLen = strlen( $oNode->nodeValue );
        
            // use mb_strlen / mb_substr for UTF-8 support
            if( $this->_iTotalLen > $iMaxLength ){
                $oNode->nodeValue = substr( 
                    $oNode->nodeValue, 
                    0, 
                    $oNodeLen - ( $this->_iTotalLen - $iMaxLength ) 
                )
                . '...'
                ;
                $this->_bReachedLimit = true;
            }
    
        }
    
        // if node has children, walk its child elements 
        // if( isset( $oNode->childNodes ) ) {
        if ( $oNode->hasChildNodes() ) {
            foreach( $oNode->childNodes as $_oChild ) {                
                $this->_walk( $_oChild, $iMaxLength );
            }
        }
         
        return $this->_aToRemove;
        
    }  
    
}