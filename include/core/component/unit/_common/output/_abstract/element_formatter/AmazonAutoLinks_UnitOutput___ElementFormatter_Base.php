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
     * @return      string
     * @since       3.5.0
     * @since       4.0.1       Added the `$sLocale` parameter.
     * @throws      Exception
     */
    protected function _getPendingMessage( $sMessage, $sLocale='' ) {
        if ( $this->_oUnitOption->get( '_no_pending_items' ) ) {
            throw new Exception( 'A product with a pending element is not allowed.' );
        }
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( ! $_oOption->isAPIKeySet( $sLocale ) ) {
            return '';
        }
        return $this->_oUnitOption->get( 'show_now_retrieving_message' )
            ? '<p>' . $sMessage . '</p>'
            : '';
    }

}