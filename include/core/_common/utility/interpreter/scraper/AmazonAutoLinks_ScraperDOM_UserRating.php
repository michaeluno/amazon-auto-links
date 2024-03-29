<?php
/**
 * Auto Amazon Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 * 
 */

/**
 * Provides methods to extracts each customer review by using DOM objects.
 * 
 * @since        3
 */
class AmazonAutoLinks_ScraperDOM_UserRating extends AmazonAutoLinks_ScraperDOM_Base {
            
    /**
     * 
     * @return  string
     */
    public function get() {
       
        // Convert image urls for SSL.
        if ( $this->bIsSSL ) {
            $this->_setSSLImagesByDOM( $this->oDoc );
        }

        // Modify tags.
        $this->oDOM->setAttributesByTagName( 
            $this->oDoc,  // DOM document
            'a', // tag name
            array( 
                'rel'    => 'nofollow noopener',
                'target' => '_blank',
            ) 
        );        

        // Output
        return $this->oDOM->getInnerHTML( 
            $this->oDoc
        );            
        
    }
    
    
} 