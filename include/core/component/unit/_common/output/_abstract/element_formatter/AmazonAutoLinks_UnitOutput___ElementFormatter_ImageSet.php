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
 * A class that provides methods to format image set outputs.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_ImageSet extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    protected $_bIsSSL = false;

    protected $_aProduct = array();

    /**
     * Sets up properties.
     */
    public function __construct( $sASIN, $sLocale, $sAssociateID, array $aRow, $oUnitOption, $aProduct ) {
        parent::__construct( $sASIN, $sLocale, $sAssociateID, $aRow, $oUnitOption );
        $this->_aProduct = $aProduct;
    }

    protected function _construct() {
        $this->_bIsSSL = is_ssl();
    }

    /**
     * @return      string
     * @throws      Exception
     * @since       3.5.0
     */
    public function get() {
        
        $_snEncodedHTML = $this->_getCell( 'images' );
        if ( null === $_snEncodedHTML ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving an image set.', 'amazon-auto-links' )
            );
        }
        return $this->___getFormattedOutput(
            $_snEncodedHTML,
            $this->_aProduct[ 'product_url' ],
            $this->_aProduct[ 'formatted_title' ],
            $this->_oUnitOption->get( 'subimage_size' ),
            $this->_oUnitOption->get( 'subimage_max_count' )
        );

    }
        /**
         * @since   3.5.0
         * @return  string
         */
        private function ___getFormattedOutput( $_snEncodedHTML, $sProductURL, $sTitle, $iMaxImageSize, $iMaxNumberOfImages ) {
            if ( '' === $_snEncodedHTML ) {
                return '';
            }
            $sTitle    = strip_tags( $sTitle );

            // Extract image urls
            $_aImageURLs = $this->___getSubImageURLs(
                $_snEncodedHTML,
                $iMaxImageSize,
                $iMaxNumberOfImages
            );

            $_aSubImageTags = array();
            foreach( $_aImageURLs as $_sImageURL ) {
                $_sImageTag = $this->getHTMLTag(
                    'img',
                    array(
                        'src'   => esc_url( $_sImageURL ),
                        'class' => 'sub-image',
                        'alt'   => $sTitle,
                    )
                );
                $_sATag     = $this->getHTMLTag(
                    'a',
                    array(
                        'href'   => esc_url( $sProductURL ),
                        'target' => '_blank',
                        'title'  => $sTitle,
                    ),
                    $_sImageTag
                );
                $_aSubImageTags[] = $this->getHTMLTag(
                    'div',
                    array(
                        'class' => 'sub-image-container',
                    ),
                    $_sATag
                );
            }
            return "<div class='sub-images'>"
                    . implode( '', $_aSubImageTags )
                . "</div>";

        }

            /**
             * @return      array       An array holding image urls.
             */
            private function ___getSubImageURLs( array $aImages, $iMaxImageSize, $iMaxNumberOfImages ) {

                // If the size is set to 0, it means the user wants no image.
                if ( ! $iMaxImageSize ) {
                    return array();
                }

                // The 'main' element is embedded by the plugin.
                unset( $aImages[ 'main' ] );

                $_aURLs  = array();
                foreach( $aImages as $_iIndex => $_aImage ) {

                    // The user may set 0 to disable it.
                    if ( ( integer ) $_iIndex >= $iMaxNumberOfImages ) {
                        break;
                    }
                    $_aURLs[] = $this->___getImageURLFromResponseElement(
                        $_aImage,
                        $iMaxImageSize
                    );
                }
                // Drop empty items.
                return array_filter( $_aURLs );

            }
                /**
                 *
                 * @remark      available key names
                 * - SwatchImage
                 * - SmallImage
                 * - ThumbnailImage
                 * - TinyImage
                 * - MediumImage
                 * - LargeImage
                 * - HiResImage
                 */
                private function ___getImageURLFromResponseElement( array $aImage, $iImageSize ) {

                    $_sURL = '';
                    foreach( $aImage as $_sKey => $_aDetails ) {
                        $_sURL = $this->getElement(
                            $_aDetails, // subject array
                            array( 'URL' ), // dimensional key
                            ''  // default
                        );
                        if ( $_sURL ) {
                            break;
                        }
                    }
                    $_sURL = $this->getImageURLBySize(
                        $_sURL,
                        $iImageSize
                    );
                    return $this->_bIsSSL
                        ? $this->getAmazonSSLImageURL( $_sURL )
                        : $_sURL;
                }


}