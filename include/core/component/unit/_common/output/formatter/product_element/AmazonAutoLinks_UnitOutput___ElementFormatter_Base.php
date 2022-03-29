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
 * A base class for formatting element outputs.
 *
 * Elements here refer to unit output elements such as prices, customer reviews, ratings etc.
 *
 * @since       3.5.0
 */
abstract class AmazonAutoLinks_UnitOutput___ElementFormatter_Base extends AmazonAutoLinks_UnitOutput___Database_Product {

    protected $_aProduct    = array();

    /**
     * Sets up properties.
     * @param string $sASIN
     * @param string $sLocale
     * @param string  $sAssociateID
     * @param array $aRow
     * @param AmazonAutoLinks_UnitOption_Base $oUnitOption
     * @param array $aProduct
     * @since       3.5.0
     * @since       3.8.11      Added the `$aProduct` parameter so that some extended classes do not have to declare the their own constructor.
     */
    public function __construct( $sASIN, $sLocale, $sAssociateID, array $aRow, $oUnitOption, array $aProduct=array() ) {
        parent::__construct( $sASIN, $sLocale, $sAssociateID, $aRow, $oUnitOption );
        $this->_aProduct = $aProduct;
        $this->_construct();
    }

    /**
     * User constructor.
     */
    protected function _construct() {}

    /**
     * Returns the formatted element output.
     * @return      string
     * @since       3.5.0
     */
    public function get() {
        return '';
    }

    /**
     * @param       string  $sMessage
     * @param       string  $sLocale        If this is given, it checks whether the PA-API keys are set for that locale. Otherwise, the message will not be shown.
     * @param       string $sContext A hint for the JavaScript script to know what element this is for, to update an image-set, rating, or price etc.
     * @return      string
     * @since       3.5.0
     * @since       4.0.1       Added the `$sLocale` parameter.
     * @throws      Exception
     */
    protected function _getPendingMessage( $sMessage, $sLocale, $sContext ) {

        if ( $this->_oUnitOption->get( '_no_pending_items' ) ) {
            throw new Exception( 'A product with a pending element is not allowed.' );
        }
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isPAAPIKeySet( $sLocale ) ) {
            return '';
        }
        return $this->_oUnitOption->get( 'show_now_retrieving_message' )
            ? '<p ' . $this->___getAttributes( $sContext, $this->_oUnitOption->get( 'unit_type' ) ) . '">'
                    . $sMessage
                . '</p>'
            : '';
    }

        /**
         * @param  string $sContext A hint for the JavaScript script to know what element this is for, to update an image-set, rating, or price etc.
         * @param  string $sUnitType
         * @return string
         * @since  4.3.0
         */
        private function ___getAttributes( $sContext, $sUnitType ) {
            return $this->getAttributes(
                array(
                    'class'                 => "now-retrieving context-{$sContext}",
                    'data-locale'           => $this->_sLocale,
                    'data-type'             => $sUnitType,
                    'data-currency'         => $this->_oUnitOption->get( array( 'preferred_currency' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $this->_sLocale ) ),
                    'data-language'         => $this->_oUnitOption->get( array( 'language' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $this->_sLocale ) ),
                    'data-asin'             => $this->_sASIN,
                    'data-tag'              => $this->_sAssociateID,
                    'data-context'          => $sContext,
                    'data-id'               => $this->_oUnitOption->get( array( 'id' ), 0 ),
                    'data-attempt'          => 0, // indicates how many times that the Ajax request was made for this element
                    'data-cache_duration'   => $this->_oUnitOption->get( 'cache_duration' ),
                    'data-call_id'          => $this->_oUnitOption->sCallID,
                    'data-item_format_tags' => str_replace( '%', '&', implode( ',', $this->_oUnitOption->aItemFormatTags ) ), // by itself it breaks HTML markups so convert % to & and these will be reverted in the now-retrieving updater Ajax script.
                )
            );
        }

}