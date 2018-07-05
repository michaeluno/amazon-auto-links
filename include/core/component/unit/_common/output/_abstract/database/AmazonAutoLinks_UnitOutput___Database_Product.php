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
 * A class that provides method to retrieve products from a plugin custom database table.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput___Database_Product extends AmazonAutoLinks_UnitOutput_Utility {

    protected $_sASIN = '';
    protected $_sLocale = '';
    protected $_sAssociateID = '';
    protected $_aRow = array();
    protected $_oUnitOption;

    /**
     * Sets up properties.
     */
    public function __construct( $sASIN, $sLocale, $sAssociateID, array $aRow, $oUnitOption ) {
        $this->_sASIN           = $sASIN;
        $this->_sLocale         = $sLocale;
        $this->_sAssociateID    = $sAssociateID;
        $this->_aRow            = $aRow;
        $this->_oUnitOption     = $oUnitOption;
    }

    /**
     * @param  string $sColumnName
     *
     * @return integer|string|double|null
     * @since   3.5.0
     */
    public function getCell( $sColumnName, $mDefault=null ) {
        return $this->_getCell( $sColumnName, $mDefault );
    }

    /**
     * @param  string $sColumnName
     *
     * @return integer|string|double|null
     * @since   3.5.0
     */
    protected function _getCell( $sColumnName, $mDefault=null ) {
        return $this->___getValueFromRow(
            $sColumnName, // column name
            $this->_aRow, // row
            $mDefault,
            array( // schedule background task
                'asin'         => $this->_sASIN,
                'locale'       => $this->_sLocale,
                'associate_id' => $this->_sAssociateID,
            )
        );
    }

        /**
         *
         * @since       3
         * @sicne       3.5.0       Moved from `AmazonAutoLinks_UnitOutput__Base_CustomDBTable`.
         * @sicne       3.5.0       Changed the scope from protected.
         */
        private function ___getValueFromRow( $sColumnName, array $aRow, $mDefault=null, $aScheduleTask=array( 'locale' => '', 'asin' => '', 'associate_id' => '' ) ) {

            $_mValue        = $this->getElement(
                $aRow, // subject array
                array( $sColumnName ), // dimensional keys
                null // default - important to set null to evaluate whether the value exists in the table
            );
            $_bIsSet        = ! is_null( $_mValue );
            $_mValue        = $_bIsSet
                ? maybe_unserialize( $_mValue )
                : $mDefault; // default

            // Now schedule a background task to retrieve product info.
    //        $_bScheduleTask = ! $this->isEmpty( array_filter( $aScheduleTask ) );

            $_sModifiedTime = $this->getElement(
                $aRow, // subject array
                array( 'modified_time' ), // dimensional keys
                '0000-00-00 00:00:00' // default
            );
            $_iCacheDuration  = ( int ) $this->_oUnitOption->get( 'cache_duration' );
            $_iExpirationTime = strtotime( $_sModifiedTime ) + $_iCacheDuration;
            $_bIsExpired      = $this->isExpired( $_iExpirationTime );
    //        if (
    //            $_bScheduleTask && $_bIsExpired
    //        ) {
    //            AmazonAutoLinks_Event_Scheduler::scheduleProductInformation(
    //                $aScheduleTask[ 'associate_id' ],
    //                $aScheduleTask[ 'asin' ],
    //                $aScheduleTask[ 'locale' ],
    //                ( integer ) $_iCacheDuration,
    //                ( boolean ) $this->_oUnitOption->get( '_force_cache_renewal' )
    //            );
    //        }
            $this->___scheduleBackgroundTask(
                $_bIsExpired,
                $aScheduleTask,
                $_iCacheDuration
            );

            $this->___addDebugInformation(
                $_bIsSet,
                $sColumnName,
                $_iCacheDuration,
                $_iExpirationTime,
                $_bIsExpired,
                $aScheduleTask
            );

            return $_mValue;

        }
            /**
             * Schedules a background task to retrieve product info.
             * @param $_bIsExpired
             * @param $aScheduleTask
             * @param $_iCacheDuration
             */
            private function ___scheduleBackgroundTask(
                $_bIsExpired,
                $aScheduleTask,
                $_iCacheDuration
            ) {
                if ( ! $_bIsExpired ) {
                    return;
                }
                if ( $this->isEmpty( array_filter( $aScheduleTask ) ) ) {
                    return;
                }
                AmazonAutoLinks_Event_Scheduler::scheduleProductInformation(
                    $aScheduleTask[ 'associate_id' ],
                    $aScheduleTask[ 'asin' ],
                    $aScheduleTask[ 'locale' ],
                    ( integer ) $_iCacheDuration,
                    ( boolean ) $this->_oUnitOption->get( '_force_cache_renewal' )
                );

            }

            /**
             * Schedules debug information to be inserted at the bottom of the product output.
             */
            private function ___addDebugInformation(
                $_bIsSet,
                $sColumnName,
                $_iCacheDuration,
                $_iExpirationTime,
                $_bIsExpired,
                $aScheduleTask
            ) {

                if ( $_bIsSet ) {
                    return;
                }

                $_oOption = AmazonAutoLinks_Option::getInstance();
                if ( ! $_oOption->isDebug() ) {
                    return;
                }

                AmazonAutoLinks_UnitOutput__DebugInformation_Product::add(
                    $sColumnName,
                    $aScheduleTask[ 'asin' ] . '_' . $aScheduleTask[ 'locale' ],
                    array(
                        'column_name'    => $sColumnName,
                        'value'          => 'NULL',
                        'cache_duration' => $_iCacheDuration,
                        'expiry_time'    => $_iExpirationTime,
                        'now'            => time(),
                        'is_expired'     => $_bIsExpired,
                    ) + $aScheduleTask
                );

            }

}
