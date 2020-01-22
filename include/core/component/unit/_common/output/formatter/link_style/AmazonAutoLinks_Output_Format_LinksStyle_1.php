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
class AmazonAutoLinks_Output_Format_LinksStyle_1 extends AmazonAutoLinks_Output_Format_LinksStyle_Base {
    
    /**
     * Retruns a formatted url.
     * @return      string
     * @remark      www.amazon.[domain-suffix]/[product-name]/dp/[asin]/ref=[...]?tag=[associate-id]
     */
    public function get( $sURL, $sASIN, $sLanguageCode='', $sCurrency='' ) {

        // ref=nosim
        if ( ! empty( $this->bRefNosim ) ) {
            $sURL = preg_replace( 
                '/ref\=(.+?)(\?|$)/i', 
                'ref=nosim$2', 
                trim( $sURL )
            );
        }
            
        // http://.../ref=pd_zg_rss_ts_bt_beauty_8?ie=UTF8&amp;tag=miunosoft-20 -> http://.../ref=pd_zg_rss_ts_bt_beauty_8?ie=UTF8&tag=miunosoft-20
        $_aQuery = array(
            'tag' => $this->getAssociateID(),
            'language' => $sLanguageCode,
            'currency' => $sCurrency,
        );
        return add_query_arg( 
            array_filter( $_aQuery ),
            htmlspecialchars_decode( $sURL )
        );       
        
    }
             
}