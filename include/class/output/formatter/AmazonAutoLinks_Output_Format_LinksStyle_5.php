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
class AmazonAutoLinks_Output_Format_LinksStyle_5 extends AmazonAutoLinks_Output_Format_LinksStyle_Base {
    
    /**
     * Retruns a formatted url.
     * @return      string
     * @remark      http://[yoursite]?[costom_query_key]=[ASIN]
     */
    public function get( $sURL, $sASIN ) {
        
        $sQueryKey = $this->oOption->get( 
            'query', 
            'cloak' 
        );
        $_aQueries = array( 
            $sQueryKey   => $sASIN,
            'locale'     => $this->sLocale,
            'ref'        => 'nosim',
            'tag'        => $this->getAssociateID(),
        );
        if ( ! $this->bRefNosim ) {
            unset( $_aQueries[ 'ref' ] );
        }
        return add_query_arg( 
            $_aQueries, 
            site_url()
        );
                
    }    
             
}