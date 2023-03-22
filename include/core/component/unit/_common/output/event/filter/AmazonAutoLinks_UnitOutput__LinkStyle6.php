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
 * Converts Amazon customer review URLs to a custom one.
 *
 * @since 5.3.0
 */
class AmazonAutoLinks_UnitOutput__LinkStyle6 extends AmazonAutoLinks_UnitOutput__DelegationBase {

    public $sCustomPath  = '';
    public $sReviewPath  = '';
    public $sAssociateID = '';
    public $sLanguage    = '';
    public $sCurrency    = '';
    public $sLocale      = '';

    /**
     * Sets up hooks.
     *
     * @param $oUnitOutput
     */
    public function __construct( $oUnitOutput ) {
        if ( '6' !== ( string ) $oUnitOutput->oUnitOption->get( 'link_style' ) ) {
            return;
        }
        parent::__construct( $oUnitOutput );
        $this->sCustomPath  = $oUnitOutput->oUnitOption->get( 'link_style_custom_path' );
        $this->sReviewPath  = $oUnitOutput->oUnitOption->get( 'link_style_custom_path_review' );
        $this->sAssociateID = $oUnitOutput->oUnitOption->get( 'associate_id' );
        $this->sLanguage    = $oUnitOutput->oUnitOption->get( 'language' );
        $this->sCurrency    = $oUnitOutput->oUnitOption->get( 'preferred_currency' );
        $this->sLocale      = $oUnitOutput->oUnitOption->get( 'country' );
    }

    /**
     * @return array
     */
    protected function _getFilterArguments() {
        return array(
            array(
                'aal_filter_unit_item_format_tag_replacements',
                array( $this, 'replyToGetProductElementReplacements' ),
                10,  // priority
                1    // 1 parameters
            ),
        );
    }

    public function replyToGetProductElementReplacements( $aReplacements /* , $aProduct, $oUnitOutput */ ) {
        $aReplacements[ '%rating%' ] = $this->___getLinkConverted( $aReplacements[ '%rating%' ] );
        return $aReplacements;
    }
        /**
         * @param   string  $sHTML
         * @return  string
         */
        private function ___getLinkConverted( $sHTML ) {
            return preg_replace_callback( $this->getRegexPattern_URL( 'amazon' ), array( $this, '___getLinkReplaced' ), $sHTML );
        }
            private function ___getLinkReplaced( $aMatches ) {
                $_sURLReview = $this->getSiteURL() . $this->getDoubleSlashesToSingle( '/' . $this->sReviewPath . '/' )
                    . $this->getElement( self::getASINs( $aMatches[ 2 ] ), 0 ); // somehow getASINFromURL() doesn't detect an ASIN well
                return $aMatches[ 1 ]
                        . $_sURLReview
                        // @deprecated let the user set these through the Custom URL Query unit option. The option needs to be enhanced though.
                        // . add_query_arg(
                        //     array_filter( array(
                        //         'language' => $this->sLanguage,
                        //         'currency' => $this->sCurrency,
                        //         'locale'   => $this->sLocale,
                        //         'tag'      => $this->sAssociateID,
                        //     ) ), // drop empty elements
                        //     $_sURLReview
                        // )
                    . $aMatches[ 4 ];
            }

}