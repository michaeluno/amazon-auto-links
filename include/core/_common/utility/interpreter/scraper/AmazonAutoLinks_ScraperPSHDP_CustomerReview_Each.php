<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 * 
 */

/**
 * Provides methods to extracts each customer review.
 * 
 * @since        3
 */
class AmazonAutoLinks_ScraperPSHDP_CustomerReview_Each extends AmazonAutoLinks_ScraperPSHDP_CustomerReview {
        
    public function __construct( $sURLOrFIlePathOrHTML, $iMaxCount=5, $bIncludeExtra=false ) {
        
        parent::__construct( $sURLOrFIlePathOrHTML );
        $this->iMaxCount     = $iMaxCount;
        $this->bIncludeExtra = $bIncludeExtra;
        $this->bIsSSL        = is_ssl();
    }
    
    /**
     * 
     * @return  string
     */
    public function get() {

        if ( ! $this->bIncludeExtra ) {
            $this->_removeExtraElements();
        }

        $_oTD = $this->oSimpleDOM->find( 
            'table > tbody > tr > td',
            0
        );
        if ( ! $_oTD ) {
            return '';
        }
        
        $_iCount = 0;
        foreach( $_oTD->children() as $_oElement ) {
            
            if ( 'br' === $_oElement->tag ) {
                $_oElement->outertext = '';
                continue;
            }
          
            if ( 'div' !== $_oElement->tag ) {
                continue;
            }
            
            $_iCount++;
            if ( $_iCount > $this->iMaxCount  ) {
                $_oElement->outertext = '';
                continue;
            }            
                       
        }
        
        if ( $this->bIsSSL ) {
            $_oIMGs = $this->oSimpleDOM->find( 'img' );
            foreach( $_oIMGs as $_oIMG ) {
                $_oIMG->src = $this->getAmazonSSLImageURL( $_oIMG->src );
            }
        }
        
        $_sHTML = str_replace(
            ' 0             ',  // search
            '', // replace
            $this->oSimpleDOM->outertext // subject
        );
        return force_balance_tags( $_sHTML );        
        
    }
    
        private function _removeExtraElements() {
            
            $_sSelector = '.reviews-voting-stripe';
            foreach( $this->oSimpleDOM->find( $_sSelector ) as $_oNode ) {
                $_oNode->parent()->outertext = '';
            }
            foreach( $this->oSimpleDOM->find( '.crIFrameCreateReview' ) as $_oNode ) {
                $_oNode->outertext = '';
            }            
            $this->oSimpleDOM->load(
                $this->oSimpleDOM->save()
            );        
            
        }    
    
} 