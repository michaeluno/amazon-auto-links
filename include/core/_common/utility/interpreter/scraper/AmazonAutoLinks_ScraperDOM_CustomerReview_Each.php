<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Provides methods to extracts each customer review by using DOM objects.
 * 
 * @since       3
 * @since       3.9.0   Deprecated and now serves as a base class.
 */
class AmazonAutoLinks_ScraperDOM_CustomerReview_Each extends AmazonAutoLinks_ScraperDOM_Base {
        
    /**
     * Stores a DOM object.
     */
    public $oDoc;

    public $iMaxCount     = 0;   // integer
    public $bIncludeExtra = false;   // boolean

    public function __construct( $sURLOrFIlePathOrHTML, $sCharset='', $iMaxCount=5, $bIncludeExtra=false ) {
        
        parent::__construct( $sURLOrFIlePathOrHTML, $sCharset );
        $this->iMaxCount     = ( integer ) $iMaxCount;
        $this->bIncludeExtra = ( boolean ) $bIncludeExtra;

    }
    
    /**
     * 
     * @return  string
     */
    public function get() {

        if ( ! $this->bIncludeExtra ) {
            $this->_removeExtraElements();
        }

        $_oXpath = new DOMXPath( $this->oDoc );   
        $_oDIVs  = $_oXpath->query( "//td/div" );
        $_iCount = 0;
        foreach( $_oDIVs as $_oDIV ) {
            
            // Remove inline CSS rules in the each review container.
            $_oDIV->setAttribute( "style", "" );
            
            $_iCount++;
            if ( $_iCount > $this->iMaxCount  ) {
                $_oDIV->parentNode->removeChild( $_oDIV );
            }
            
        }
        
        // Convert image urls for SSL.
        if ( $this->bIsSSL ) {
            $this->setSSLImagesByDOM( $this->oDoc );
        }

        // Modify a tags.
        $this->oDOM->setAttributesByTagName( 
            $this->oDoc, // node
            'a', // tag name
            array( 
                'rel'    => 'nofollow noopener',
                'target' => '_blank',
            ) 
        );        

        // Output
        $_sHTML = $this->oDOM->getInnerHTML( 
            $this->oDoc->getElementsByTagName( 'td' )->item( 0 )
        );            
        $_sHTML = str_replace(
            ' 0             ',  // search
            '', // replace
            $_sHTML // subject
        );
        return $_sHTML;
        
    }
    
        protected function _removeExtraElements() {
            
            $_oXpath = new DOMXPath( $this->oDoc );   
            $_oBRs = $_oXpath->query( 
                "//table/tbody/tr/td//br" 
            );
            foreach( $_oBRs as $e ) {
                $e->parentNode->removeChild($e);
            }
            $_oExtras = $_oXpath->query(
                "//*[contains(*/@class,'reviews-voting-stripe') or contains(@class ,'crIFrameCreateReview')]"
            );
            foreach( $_oExtras as $e ) {
                $e->parentNode->removeChild( $e );
            }
                        
        }    
    

    
} 