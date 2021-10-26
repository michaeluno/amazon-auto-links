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
 * Generates product links.
 *  
 * @since       4.3.0
*/
class AmazonAutoLinks_Event_Filter_ProductLinks extends AmazonAutoLinks_PluginUtility {

    public function __construct() {

        /**
         * Setting a high priority as this filter existed before this class is implemented
         * so it is used by somewhere else.
         */
        add_filter( 'aal_filter_product_link', array( $this, 'replyToGetProductURL' ), 1, 6 );

    }

    /**
     *
     * @since   4.3.0
     * @return  string
     */
    public function replyToGetProductURL( $sURL, $sRawURL, $sASIN, $aUnitOptions, $sLanguageCode, $sCurrency ) {
        return $this->___getFormattedProductLinkByStyle(
            $sURL,
            $sASIN,
            $this->getElement( $aUnitOptions, array( 'link_style' ), 1 ),
            $this->getElement( $aUnitOptions, array( 'ref_nosim' ), false ),
            $this->getElement( $aUnitOptions, array( 'associate_id' ), '' ),
            $this->getElement( $aUnitOptions, array( 'country' ), '' ),
            $sLanguageCode,
            $sCurrency
        );
    }

        /**
         * A helper function for the above `getProductLinkURLFormatted()` method.
         *
         * @remark      $iStyle should be 1 to 5 indicating the url style of the link.
         * @param string $sURL
         * @param string $sASIN
         * @param integer $iStyle
         * @param boolean $bRefNosim
         * @param string $sAssociateID
         * @param string $sLocale
         * @param string $sLanguageCode
         * @param string $sCurrency
         *
         * @return      string
         * @since
         * @since       4.3.0       Moved from ``.
         */
        private function ___getFormattedProductLinkByStyle( $sURL, $sASIN, $iStyle=1, $bRefNosim=false, $sAssociateID='', $sLocale='US', $sLanguageCode='', $sCurrency='' ) {

            $iStyle      = $iStyle ? ( integer ) $iStyle : 1;
            $_sClassName = "AmazonAutoLinks_Output_Format_LinksStyle_{$iStyle}";
            /**
             * @var AmazonAutoLinks_Output_Format_LinksStyle_1|AmazonAutoLinks_Output_Format_LinksStyle_2|AmazonAutoLinks_Output_Format_LinksStyle_3|AmazonAutoLinks_Output_Format_LinksStyle_4|AmazonAutoLinks_Output_Format_LinksStyle_5 $_oLinkStyle
             */
            $_oLinkStyle = new $_sClassName(
                $bRefNosim,
                $sAssociateID,
                $sLocale
            );
            $_sURL = $_oLinkStyle->get( $sURL, $sASIN, $sLanguageCode, $sCurrency );
            return str_replace(
                'amazon-auto-links-20',  // dummy url used for a request
                $sAssociateID,
                $_sURL
            );

        }

}