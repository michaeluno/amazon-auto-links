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
 * A base class that provides utility methods for unit output classes.
 *
 */
abstract class AmazonAutoLinks_UnitOutput_Utility extends AmazonAutoLinks_Unit_Utility {

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
        $_aKeysNeedle = array();
        foreach( $aKeys as $_sKey ) {
            $_aKeysNeedle[] = '\Q' . $_sKey . '\E';
        }
        return preg_match(
            '/(' . implode( '|', $_aKeysNeedle ) . ')/',  // '/(\Q%price%\E|\Q%rating%\E|\Q%review%\E|\Q%image_set%\E)/'
            $sSubject
        );
    }

    /**
     * Strips HTML tags and sanitizes the product title.
     * @return  string
     * @since   unknown 
     * @since   3.10.0  Renamed from `_getTitleSanitized()`
     * @since   3.10.0  Moved from `AmazonAutoLinks_UnitOutput_Base`.
     * @since   3.10.0  Added the `$iTitleLength` parameter.
     */
    static public function getTitleSanitized( $sTitle, $iTitleLength ) {

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
     * @return      string
     */
    static public function getProductTitleFormatted( array $aProduct, $sFormat ) {
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