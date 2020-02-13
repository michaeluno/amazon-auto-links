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
 * A class that provides method to retrieve product data from the given table row.
 *
 * @remark  This does not access the database but just extracts data from a given array.
 * And if an item is expired, it schedules a background task to renew it.
 * @since   3.5.0
 */
class AmazonAutoLinks_UnitOutput___Database_Product extends AmazonAutoLinks_UnitOutput_Utility {

    protected $_sASIN           = '';
    protected $_sLocale         = '';
    protected $_sAssociateID    = '';
    protected $_aRow            = array();
    protected $_oUnitOption;
    
    /**
     * An array of raw item array extracted from the PA API response.
     * Used to pass to the background routine to save an API request.  
     * @sicne   3.8.12
     * @var array 
     */
    protected $_aAPIRawItem    = array();
    
    /**
     * Sets up properties.
     */
    public function __construct( $sASIN, $sLocale, $sAssociateID, array $aRow, $oUnitOption, array $aAPIRawItem=array() ) {
        $this->_sASIN           = $sASIN;
        $this->_sLocale         = $sLocale;
        $this->_sAssociateID    = $sAssociateID;
        $this->_aRow            = $aRow;
        $this->_oUnitOption     = $oUnitOption;
        $this->_aAPIRawItem     = $aAPIRawItem;
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
            ),
            $this->_aAPIRawItem
        );
    }

        /**
         *
         * @since       3
         * @sicne       3.5.0       Moved from `AmazonAutoLinks_UnitOutput__Base_CustomDBTable`.
         * @sicne       3.5.0       Changed the scope from protected.
         * @sicne       3.8.12      Added the `$aAPIRawItem` parameter.
         */
        private function ___getValueFromRow( $sColumnName, array $aRow, $mDefault=null, $aScheduleTask=array( 'locale' => '', 'asin' => '', 'associate_id' => '' ), $aAPIRawItem=array() ) {

            $_mValue        = $this->getElement(
                $aRow, // subject array
                array( $sColumnName ), // dimensional keys
                null // default - important to set null to evaluate whether the value exists in the table
            );
            $_bIsSet        = ! is_null( $_mValue );
            $_mValue        = $_bIsSet
                ? maybe_unserialize( $_mValue )
                : $mDefault; // default

            $_sModifiedTime = $this->getElement(
                $aRow, // subject array
                array( 'modified_time' ), // dimensional keys
                '0000-00-00 00:00:00' // default
            );
            $_iCacheDuration  = ( int ) $this->_oUnitOption->get( 'cache_duration' );
            $_iExpirationTime = strtotime( $_sModifiedTime ) + $_iCacheDuration;
            $_bIsExpired      = $this->isExpired( $_iExpirationTime );
            $_bShouldSchedule = $_bIsExpired || ! $_bIsSet; // 3.8.5 When the value is not set, schedule retrieving extra product information

            $this->___scheduleBackgroundTask( $_bShouldSchedule, $aScheduleTask, $_iCacheDuration, $aAPIRawItem );
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
             * @param boolean $bShouldSchedule
             * @param array   $aScheduleTask
             * @param integer $_iCacheDuration
             * @param array   $aAPIRawItem
             */
            private function ___scheduleBackgroundTask( $bShouldSchedule, $aScheduleTask, $_iCacheDuration, $aAPIRawItem=array() ) {
                if ( ! $bShouldSchedule ) {
                    return;
                }
                if ( $this->isEmpty( array_filter( $aScheduleTask ) ) ) {
                    return;
                }
                $_sLocale   = strtoupper( $this->_oUnitOption->get( array( 'country' ), 'US' ) );
                $_sCurrency = $this->_oUnitOption->get( array( 'preferred_currency' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale ) );
                $_sLanguage = $this->_oUnitOption->get( array( 'language' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale ) );

                AmazonAutoLinks_Event_Scheduler::scheduleProductInformation(
                    $aScheduleTask[ 'associate_id' ] . '|' . $_sLocale . '|' . $_sCurrency . '|' . $_sLanguage,
                    $aScheduleTask[ 'asin' ],
                    ( integer ) $_iCacheDuration,
                    ( boolean ) $this->_oUnitOption->get( '_force_cache_renewal' ),
                    $this->_oUnitOption->get( 'item_format' ),
                    $aAPIRawItem    // 3.8.12
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
