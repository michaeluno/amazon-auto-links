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
class AmazonAutoLinks_Output_Format_LinksStyle_4 extends AmazonAutoLinks_Output_Format_LinksStyle_Base {
    
    /**
     * Retruns a formatted url.
     * @return      string
     * @remark      http://www.amazon.[domain-suffix]/dp/ASIN/[asin]/ref=[...]?tag=[associate-id]
     */
    public function get( $sURL, $sASIN ) {

        $_aURLelem = parse_url( trim( $sURL ) );
        return add_query_arg( 
            array( 
                'tag' => $this->getAssociateID(),
            ),
            $_aURLelem[ 'scheme' ]
                . '://' 
                . $_aURLelem[ 'host' ]
                . '/dp/ASIN/' 
                . $sASIN 
                . '/' 
                . ( 
                    empty( $this->bRefNosim ) 
                        ? '' 
                        : 'ref=nosim' 
                )
        );
     
    }
             
}