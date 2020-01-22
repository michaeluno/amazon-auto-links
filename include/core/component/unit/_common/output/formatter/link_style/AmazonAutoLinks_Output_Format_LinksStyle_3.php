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
    public function get( $sURL, $sASIN, $sLanguageCode='', $sCurrency='' ) {

        $_aURLParts = parse_url( trim( $sURL ) );
        parse_str( $_aURLParts[ 'query' ], $_aQuery );
        unset( $_aQuery[ 'tag' ] ); // prevent duplicates
        $_aQuery    = $_aQuery + array(
            'tag' => $this->getAssociateID(),
            'language' => $sLanguageCode,
            'currency' => $sCurrency,
        );
        $_aQuery = array_filter( $_aQuery ); // drop non-true values

        if ( $this->bRefNosim ) {
            $_aQuery[ 'ref' ] = 'nosim';
        }
        return add_query_arg( 
            $_aQuery,
            $_aURLParts[ 'scheme' ]
                . '://' 
                . $_aURLParts[ 'host' ]
                . '/gp/product/' 
                . $sASIN 
        );                             
        
    }
             
}