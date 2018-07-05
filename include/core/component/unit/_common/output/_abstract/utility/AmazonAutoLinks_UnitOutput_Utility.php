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
 * A base class that provides utility methods for unit output classes.
 *
 */
abstract class AmazonAutoLinks_UnitOutput_Utility extends AmazonAutoLinks_PluginUtility {

    /**
     *
     * @since       unknown
     * @since       2.1.1       Changed the name from `formatImage()`. Changed the scope from protected to private.
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     */
    static public function getProductImageURLFormatted( $sImageURL, $isImageSize, $sLocale='US' ) {

        // If no product image is found
        if ( ! $sImageURL ) {
            $sImageURL = isset( AmazonAutoLinks_Property::$aNoImageAvailable[ $sLocale ] )
                ? AmazonAutoLinks_Property::$aNoImageAvailable[ $sLocale ]
                : AmazonAutoLinks_Property::$aNoImageAvailable[ 'US' ];
        }

        if ( is_ssl() ) {
            $sImageURL = self::getAmazonSSLImageURL( $sImageURL );
        }

        return self::getImageURLBySize( $sImageURL, $isImageSize );

    }

    /**
     * Checks if a given custom variable(s) exists in a subject string.
     * @return      boolean
     * @since       3
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base`.
     */
    static public function hasCustomVariable( $sSubject, array $aKeys = array( '%price%', '%rating%', '%review%', '%image_set%' ) ) {
        $_aKeys = array();
        foreach( $aKeys as $_sKey ) {
            $_aKeys[] = '\Q' . $_sKey . '\E';
        }
        return preg_match(
            '/(' . implode( '|', $aKeys ) . ')/',  // '/(\Q%price%\E|\Q%rating%\E|\Q%review%\E|\Q%image_set%\E)/'
            $sSubject
        );
    }

    /**
     * Extracts ASIN from the given url.
     *
     * ASIN is a product ID consisting of 10 characters.
     *
     * example regex patterns:
     *         /http:\/\/(?:www\.|)amazon\.com\/(?:gp\/product|[^\/]+\/dp|dp)\/([^\/]+)/
     *         "http://www.amazon.com/([\\w-]+/)?(dp|gp/product)/(\\w+/)?(\\w{10})"
     *
     * @return      string      The found ASIN, or an empty string when not found.
     * @since       unknown
     * @since       3.5.0       Renamed from `getASIN()`
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     */
    static public function getASINFromURL( $sURL ) {

        $sURL = remove_query_arg(
            array( 'smid', 'pf_rd_p', 'pf_rd_s', 'pf_rd_t', 'pf_rd_i', 'pf_rd_m', 'pf_rd_r' ),
            $sURL
        );

        $sURL = preg_replace(
            array(
                '/[A-Z0-9]{11,}/',  // Remove strings like an ASIN but with more than 10 characters.
            ),
            '',
            $sURL
        );

        preg_match(
            '/(dp|gp|e)\/(.+\/)?([A-Z0-9]{10})\W/', // needle - [A-Z0-9]{10} is the ASIN
            $sURL,  // subject
            $_aMatches // match container
        );
        return isset( $_aMatches[ 3 ] )
            ? $_aMatches[ 3 ]
            : '';

    }

    /**
     * Returns the resized image url.
     *
     * @rmark       Adjusts the image size. _SL160_ or _SS160_
     * @return      string
     * @param       $sImgURL        string
     * @param       $iImageSize     integer     0 to 500.
     * @since       3
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     */
    static public function getImageURLBySize( $sImgURL, $iImageSize ) {
        return preg_replace(
            '/(?<=_S)([LS])(\d{1,3})(?=_)/i',
            '${1}'. $iImageSize,
            $sImgURL
       );
    }

}