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
 * A base class for formatting element outputs.
 *
 * Elements here refer to unit output elements such as prices, customer reviews, ratings etc.
 *
 * @since       3.5.0
 */
abstract class AmazonAutoLinks_UnitOutput___ElementFormatter_Base extends AmazonAutoLinks_UnitOutput___Database_Product {

    /**
     * Sets up properties.
     */
    public function __construct( $sASIN, $sLocale, $sAssociateID, array $aRow, $oUnitOption ) {
        parent::__construct( $sASIN, $sLocale, $sAssociateID, $aRow, $oUnitOption );
        $this->_construct();
    }

    /**
     * User constructor.
     */
    protected function _construct() {}

    /**
     * @return      string
     * @since       3.5.0
     */
    public function get() {
        return '';
    }

    /**
     * @return      string
     * @since       3.5.0
     * @throws      Exception
     */
    protected function _getPendingMessage( $sMessage ) {
        // The scheduling should be handled automatically in the `___getValueFromRow()` method.
//            AmazonAutoLinks_Event_Scheduler::scheduleProductInformation(
//                $this->_sAssociateID,
//                $this->_sASIN,
//                $this->_sLocale,
//                ( integer ) $this->_oUnitOption->get( 'cache_duration' ),
//                ( boolean ) $this->_oUnitOption->get( '_force_cache_renewal' )
//            );
        if ( $this->_oUnitOption->get( '_no_pending_items' ) ) {
            throw new Exception( 'A product with a pending element is not allowed.' );
        }
        return $this->_oUnitOption->get( 'show_now_retrieving_message' )
            ? '<p>' . $sMessage . '</p>'
            : '';
    }

}