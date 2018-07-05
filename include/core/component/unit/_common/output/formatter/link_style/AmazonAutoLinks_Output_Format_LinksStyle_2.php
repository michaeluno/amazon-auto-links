<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Formats amazon product link urls.
 * 
 * @since       3
 */
class AmazonAutoLinks_Output_Format_LinksStyle_2 extends AmazonAutoLinks_Output_Format_LinksStyle_Base {
    
    /**
     * Retruns a formatted url.
     * @return      string
     * @remark      http://www.amazon.[domain-suffix]/exec/obidos/ASIN/[asin]/[associate-id]/ref=[...]
     */
    public function get( $sURL, $sASIN ) {
        
        $_aURLelem = parse_url( trim( $sURL ) );                
        return $_aURLelem[ 'scheme' ] . '://' . $_aURLelem[ 'host' ] 
            . '/exec/obidos/ASIN/' . $sASIN 
            . '/' . $this->getAssociateID() 
            . ( 
                empty( $this->bRefNosim ) 
                    ? '' 
                    : '/ref=nosim' 
            );    // ref=nosim
        
    }
             
}