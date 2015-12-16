<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Formats amazon product link urls.
 * 
 * @since       3
 */
class AmazonAutoLinks_Output_Format_LinksStyle_3 extends AmazonAutoLinks_Output_Format_LinksStyle_Base {
    
    /**
     * Retruns a formatted url.
     * @return      string
     * @remark      http://www.amazon.[domain-suffix]/gp/product/[asin]/?tag=[associate-id]&ref=[...]
     */
    public function get( $sURL, $sASIN ) {

        $_aURLelem = parse_url( trim( $sURL ) );
        $_aQueries = array( 
            'tag' => $this->getAssociateID(),
        );
        if ( $this->bRefNosim ) {
            $_aQueries[ 'ref' ] = 'nosim';
        }
        return add_query_arg( 
            $_aQueries,
            $_aURLelem['scheme'] 
                . '://' 
                . $_aURLelem['host'] 
                . '/gp/product/' 
                . $sASIN 
        );                             
        
    }
             
}