<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 * 
 */

/**
 * Provides methods to extracts each customer review by using DOM objects.
 * 
 * @since        3.9.0
 */
class AmazonAutoLinks_ScraperDOM_CustomerReview2_Each extends AmazonAutoLinks_ScraperDOM_CustomerReview_Each {

    /**
     *
     * @return  string
     */
    public function get( /* $sLocale, $sAssociateID */ ) {

        if ( ! $this->bIncludeExtra ) {
            $this->_removeExtraElements();
        }

        $_aParameters  = func_get_args() + array( '', '' );
        $_sLocale      = $_aParameters[ 0 ];
        $_sAssociateID = $_aParameters[ 1 ];

        $_oLocale      = new AmazonAutoLinks_PAAPI50___Locales;
        $_sDomain      = $_oLocale->aMarketPlaces[ $_sLocale ];

        $_oXpath = new DOMXPath( $this->oDoc );
        $_oDIVs  = $_oXpath->query( "//div[@data-hook='review']" );
        $_iCount = 0;
        foreach( $_oDIVs as $_oDIV ) {

            // Remove inline CSS rules in the each review container.
            $_oDIV->setAttribute( "style", "" );

            $_iCount++;
            if ( $_iCount > $this->iMaxCount  ) {
                $_oDIV->parentNode->removeChild( $_oDIV );
                continue;
            }

            // Fix relative links
            $_oAs  = $_oXpath->query( "//a", $_oDIV );
            foreach( $_oAs as $_oA ) {

                $_sHref = $_oA->getAttribute( 'href' );
                $_sURL  = 'http' === substr( $_sHref, 0, 4 )
                    ? $_sHref
                    : 'https://' . $_sDomain . $_sHref;
                $_sURL  = add_query_arg(
                    array(
                        'tag'   => $_sAssociateID,
                    ),
                    $_sURL
                );
                $_oA->setAttribute( 'href', $_sURL );

            }

            // Fix star ratings
            $_oIs  = $_oXpath->query( "//i[@data-hook='review-star-rating']", $_oDIV );
            foreach( $_oIs as $_oI ) {
                $_inRating = AmazonAutoLinks_Unit_Utility::getRatingExtracted( $_oI->nodeValue );
                if ( null === $_inRating ) {
                    continue;
                }
                $_sNewHTML = AmazonAutoLinks_Unit_Utility::getRatingOutput( $_inRating );
                $_oNodeReplacement = $this->oDoc->createDocumentFragment();
                $_oNodeReplacement->appendXML( $_sNewHTML );
                $_oI->parentNode->replaceChild( $_oNodeReplacement, $_oI );
            }

        }


        // Convert image urls for SSL.
        if ( $this->bIsSSL ) {
            $this->setSSLImagesByDOM( $this->oDoc );
        }

        // Modify a tags.
        $this->oDOM->setAttributesByTagName(
            $this->oDoc, // node
            'a', // tag name
            array(
                'rel'    => 'nofollow noopener',
                'target' => '_blank',
            )
        );

        // Output
        $_sHTML = $this->oDOM->getInnerHTML( $this->oDoc->getElementByID( 'cm_cr-review_list' ) );
        return $_sHTML;

    }

        protected function _removeExtraElements() {

            $_oXpath = new DOMXPath( $this->oDoc );
            $_oDIVs  = $_oXpath->query( "//*[contains(@class, 'a-form-actions')]" );
            foreach( $_oDIVs as $_e ) {
                $_e->parentNode->removeChild( $_e );
            }

            $_oActionBars = $_oXpath->query( "//*[contains(@class, 'cr-vote-action-bar')]" );
            foreach( $_oActionBars as $_oActionBarNode ) {
                $_oActionBarNode->parentNode->removeChild( $_oActionBarNode );
            }

        }


} 