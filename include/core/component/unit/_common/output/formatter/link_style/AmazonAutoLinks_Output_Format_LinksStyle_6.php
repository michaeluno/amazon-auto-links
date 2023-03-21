<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Formats amazon product link urls.
 * 
 * @since 5.3.0
 */
class AmazonAutoLinks_Output_Format_LinksStyle_6 extends AmazonAutoLinks_Output_Format_LinksStyle_Base {

    /**
     * Returns a formatted url.
     * @return string
     * @remark http://[yoursite]/[custom-path]/[asin]
     * @since  5.3.0
     */
    public function get( $sURL, $sASIN, $sLanguageCode='', $sCurrency='' ) {
        $_sCustomPath = $this->getElement( $this->aUnitOptions, array( 'link_style_custom_path' ), 'merchandise' );
        $_sURL        = $this->getSiteURL() . $this->getDoubleSlashesToSingle( '/' . $_sCustomPath . '/' ) . $sASIN;
        $_aQuery      = array(
            'locale'     => $this->sLocale,
            'language'   => $sLanguageCode,
            'currency'   => $sCurrency,
            'tag'        => $this->getAssociateID(),
        );
        return add_query_arg(
            array_filter( $_aQuery ), // drop empty elements
            $_sURL
        );
    }    
             
}