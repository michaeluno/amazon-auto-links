<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A base class that provides utility methods for unit output classes.
 *
 */
abstract class AmazonAutoLinks_UnitOutput_Utility extends AmazonAutoLinks_Unit_Utility {

    /**
     * Parses the given HTML content and returns found ASINs.
     * @since       3.2.0
     * @since       3.8.1   Changed the visibility scope to protected from private as category unit accesses this method.
     * @since       4.4.0   Moved from `AmazonAutoLinks_UnitOutput_url`.
     * @param       array   $aHTMLs An array holding string HTML documents.
     * @return      array
     */
    static public function getASINsFromHTMLs( $aHTMLs ) {

        $_aURLs  = array();
        $_aTexts = array();
        $_oDOM   = new AmazonAutoLinks_DOM;
        foreach( $aHTMLs as $_sURL => $_sHTML ) {

            $_oDoc      = $_oDOM->loadDOMFromHTML( $_sHTML );
            $_oDOM->removeTags( $_oDoc, array( 'script', 'style', 'noscript' ) );
            $_oDOM->removeComments( $_oDoc );

            // HTML documents, extract a tag href attribute value.
            $_aURLs     = $_aURLs + self::___getLinksFromHTML( $_oDoc );

            // For plain text pages, sanitize HTML entities.
            $_sText     = $_oDOM->getTagOuterHTML( $_oDoc, 'body' );
            $_sText     = str_replace(
                array( '&#13;', '&#10;' ), // search
                PHP_EOL, // replacement
                $_sText // subject
            );
            $_aTexts[ $_sURL ] = $_sText;

        }

        $_aURLs = $_aURLs + self::___getURLsFromText( implode( PHP_EOL, $_aTexts ) );
        return self::___getASINsExtractedFromURLs( $_aURLs );

    }
        /**
         * @param       array       $aURLs
         * @return      array
         */
        static private function ___getASINsExtractedFromURLs( array $aURLs ) {
            $_aASINs = array();
            foreach( $aURLs as $_sURL ) {
                $_sASIN = self::getASINFromURL( $_sURL );
                if ( ! $_sASIN ) {
                    continue;
                }
                $_aASINs[ $_sASIN ] = $_sASIN;
            }
            return $_aASINs;
        }

        /**
         * @param       DOMDocument $oDOM
         * @return      array
         */
        static private function ___getLinksFromHTML( $oDOM ) {

            $_aLinks = array();
            foreach( $oDOM->getElementsByTagName( 'a' ) as $nodeA ) {
                $sHref = $nodeA->getAttribute( 'href' );
                $_aLinks[ $sHref ] = $sHref;
            }
            return $_aLinks;

        }

        /**
         * Finds and returns urls from a given string.
         * @param       string   $sText
         * @return      array    List of urls
         */
        static private function ___getURLsFromText( $sText ) {

            $_aURLs = array();
            preg_match_all(
                '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#s',
                $sText,
                $_aURLs
            );
            $_aURLs = array_merge( $_aURLs[ 0 ], $_aURLs[ 1 ] );

            // Make it associative so that duplicate items will be removed.
            return empty( $_aURLs )
                ? $_aURLs
                : array_combine( $_aURLs, $_aURLs );

        }

    /**
     * @param  string         $sImageURL
     * @param  integer|string $isImageSize
     * @param  string         $sLocale
     * @return string|null
     * @since  unknown
     * @since  2.1.1          Changed the name from `formatImage()`. Changed the scope from protected to private.
     * @since  3.5.0          Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     */
    static public function getProductImageURLFormatted( $sImageURL, $isImageSize, $sLocale='US' ) {

        // 4.2.8 If it is unset, let it be resumed by cache
        if ( is_null( $sImageURL ) ) {
            return null;
        }

        // If no product image is found
        if ( ! $sImageURL ) {
            $_oLocale  = new AmazonAutoLinks_Locale( $sLocale );
            $sImageURL = $_oLocale->getNoImageURL();
        }

        if ( is_ssl() ) {
            $sImageURL = self::getAmazonSSLImageURL( $sImageURL );
        }

        return self::getImageURLBySize( $sImageURL, $isImageSize );

    }

    /**
     * Checks if a given custom variable(s) exists in a subject string.
     *
     * If muliple tags are given, when at least one of them exists, true will be returned.
     * @param       string      $sSubject
     * @param       array       $aTags
     * @return      boolean
     * @since       3
     * @since       3.5.0       Moved from `AmazonAutoLinks_UnitOutput_Base`.
     */
    static public function hasItemFormatTagsIn( $sSubject, array $aTags = array( '%price%', '%rating%', '%review%', '%image_set%' ) ) {
        $_aKeysNeedle = array();
        foreach( $aTags as $_sTag ) {
            $_aKeysNeedle[] = '\Q' . $_sTag . '\E';
        }
        return ( boolean ) preg_match(
            '/(' . implode( '|', $_aKeysNeedle ) . ')/',  // '/(\Q%price%\E|\Q%rating%\E|\Q%review%\E|\Q%image_set%\E)/'
            $sSubject
        );
    }

    /**
     * Strips HTML tags and sanitizes the product title.
     * @param   string      $sTitle
     * @param   integer     $iTitleLength
     * @return  string|null
     * @since   unknown 
     * @since   3.10.0  Renamed from `_getTitleSanitized()`
     * @since   3.10.0  Moved from `AmazonAutoLinks_UnitOutput_Base`.
     * @since   3.10.0  Added the `$iTitleLength` parameter.
     */
    static public function getTitleSanitized( $sTitle, $iTitleLength ) {

        // 4.2.8 If the title is unset, let it be resumed with the cache data.
        if ( is_null( $sTitle ) ) {
            return null;
        }

        $sTitle = apply_filters( 'aal_filter_unit_product_raw_title', $sTitle );
        
        // Title character length
        if ( 0 == $iTitleLength ) {
            return '';
        }
        if (
            $iTitleLength > 0
            && self::getStringLength( $sTitle ) > $iTitleLength
        ) {
            $sTitle = self::getSubstring( $sTitle, 0, $iTitleLength ) . '...';
        }

        // @remark 3.10.0 Removed `esc_attr()` for this function to be used in different places. If any side effects are found by this, apply `esc_attr()` outside this function.
         return $sTitle;

    }

    /**
     * Returns the formatted product title HTML Block.
     * @since       2.1.1
     * @since       3.5.0       Renamed from `_formatProductTitle()`.
     * @since       3.10.0      Moved from `AmazonAutoLinks_UnitOutput_Base_ElementFormat`.
     * @param       array       $aProduct
     * @param       string      $sFormat
     * @return      string|null
     */
    static public function getProductTitleFormatted( array $aProduct, $sFormat ) {
        // 4.2.8 Allow it to be null so that it will be resumed with caches.
        if ( ! isset( $aProduct[ 'title' ] ) ) {
            return null;
        }
        return str_replace(
            array(
                "%href%",
                "%title_text%",
                "%description_text%"
            ),
            array(
                esc_url( $aProduct[ 'product_url' ] ),
                strip_tags( $aProduct[ 'title' ] ),
                $aProduct[ 'text_description' ]
            ),
            $sFormat
        );
    }

}