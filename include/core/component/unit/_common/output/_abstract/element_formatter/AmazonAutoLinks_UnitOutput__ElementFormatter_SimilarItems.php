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
 * A class that provides methods to format similar product outputs.
 *
 * @remark      The class name uses double underscores instead of triple underscores to indicate that this is a delegation class.
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput__ElementFormatter_SimilarItems extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    protected $oUnitOutput;

    /**
     * Sets up properties.
     */
    public function __construct( $oUnitOutput, $sASIN, $sLocale, $sAssociateID, array $aRow ) {

        parent::__construct( $sASIN, $sLocale, $sAssociateID, $aRow, $oUnitOutput->oUnitOption );
        $this->_oUnitOutput = $oUnitOutput;

    }

    /**
     * @return      string
     * @throws      Exception
     * @since       3.5.0
     * @deprecated  3.9.0
     */
    public function get() {

        if ( ! $this->___hasItemFormatVariable( array( '%similar%' ) ) ) {
            return '<!-- Similar products are not enabled -->';
        }

        // 3.9. this feature is deprecated
        return '';

        $_snEncodedHTML = $this->_getCell( 'similar_products' );
        if ( null === $_snEncodedHTML && $this->_oUnitOption->get( '_search_similar_products' ) ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving similar products.', 'amazon-auto-links' ),
                $this->_sLocale
            );
        }
        return $this->___getFormattedOutput( $_snEncodedHTML );

    }
        /**
         * @param array $aVariables
         *
         * @return boolean
         */
        private function ___hasItemFormatVariable( array $aVariables ) {
            return ( boolean ) $this->hasCustomVariable(
                $this->_oUnitOption->get( 'item_format' ),
                $aVariables
            );
        }

        /**
         * @since   3.5.0
         * @return  string
         */
        private function ___getFormattedOutput( $_snEncodedHTML ) {
            $_aOutputs = $this->___getSimilarProductOutputs( $_snEncodedHTML );
            if ( empty( $_aOutputs ) ) {
                return '<!-- No similar products for '  . $this->_sASIN . ' -->';
            }
            return "<div class='amazon-similar-products'>"
                    . implode( '', $_aOutputs )
                . "</div>";

        }

        /**
         * @return      array
         * @since       3.3.0
         */
        private function ___getSimilarProductOutputs( $_aSimilarProducts ) {

            $_iImageSize        = $this->_oUnitOption->get( 'similar_product_image_size' );
            $_iMaxCount         = $this->_oUnitOption->get( 'similar_product_max_count' );
        
            // By setting 0 or below to the image size, the user can disable the similar products.
            if ( 0 >= $_iImageSize ) {
                return array();
            }
        
            $_aOutputs = array();
            foreach( $this->getAsArray( $_aSimilarProducts ) as $_iIndex => $_aProduct ) {
                
                if ( $_iIndex >= $_iMaxCount ) {
                    break;
                }
                
                $_sOutput = $this->___getSimilarProductEach( $_aProduct, $_iImageSize );
                if ( ! $_sOutput ) {
                    continue;
                }
                $_aOutputs[] = $_sOutput;
                
            }               
            return $_aOutputs;
            
        }
            /**
             * @since       3.3.0
             * @return      string
             */
            private function ___getSimilarProductEach( $_aProduct, $_iImageSize ) {
                
                $_sThumbnailURL     = $this->_oUnitOutput->getProductImageURLFormatted(
                    $this->getElement(
                        $_aProduct,
                        array( 'MediumImage', 'URL' ),
                        strtoupper( $this->_oUnitOption->get( 'country' ) )
                    ), 
                    $_iImageSize 
                );
                if ( ! $_sThumbnailURL ) {
                    return '';
                }
                
                if ( ! $this->_oUnitOutput->isImageAllowed( $_sThumbnailURL ) ) {
                    return '';
                }
                
                $_sTitle        = $this->getElement(
                    $_aProduct,
                    array( 'ItemAttributes', 'Title' )
                );
                if ( $this->_oUnitOutput->isTitleBlocked( $_sTitle ) ) {
                    return '';
                }
                
                $_sASIN         = $this->getElement(
                    $_aProduct,
                    array( 'ASIN' )
                );
                if ( $this->_oUnitOutput->isASINBlocked( $_sASIN ) ) {
                    return '';
                }
                
                $_sProductURL   = $this->_oUnitOutput->getProductLinkURLFormatted(
                    rawurldecode( $this->getElement( $_aProduct, 'DetailPageURL' ) ),
                    $_sASIN,
                    $this->_oUnitOption->get( 'language' ),
                    $this->_oUnitOption->get( 'preferred_currency' )
                );

                $_sProductURL   = esc_url( $_sProductURL );
                $_sThumbnailURL = esc_url( $_sThumbnailURL );
                $_sTitle        = esc_attr( $_sTitle );
                return "<div class='amazon-similar-product' style='max-height: {$_iImageSize}px; max-width: {$_iImageSize}px;'>"
                        . "<a href='{$_sProductURL}' target='blank'>"
                            . "<img class='amazon-similar-product-thumbnail' src='{$_sThumbnailURL}' title='{$_sTitle}' alt='{$_sTitle}' style='max-height: {$_iImageSize}px;' />"
                        . "</a>"
                    . "</div>";

            }
        
}