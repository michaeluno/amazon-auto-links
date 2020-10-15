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

    /**
     * @var string
     */
    protected $_sASIN           = '';
    /**
     * @var string
     */
    protected $_sLocale         = '';
    /**
     * @var string
     */
    protected $_sAssociateID    = '';
    /**
     * @var array
     */
    protected $_aRow            = array();

    /**
     * @var AmazonAutoLinks_UnitOption_Base
     */
    protected $_oUnitOption;

    /**
     * Sets up properties.
     * @param string $sASIN
     * @param string $sLocale
     * @param string $sAssociateID
     * @param array $aRow
     * @param AmazonAutoLinks_UnitOption_Base $oUnitOption
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
     * @param  mixed $mDefault
     *
     * @return integer|string|double|null
     * @since   3.5.0
     */
    public function getCell( $sColumnName, $mDefault=null ) {
        return $this->_getCell( $sColumnName, $mDefault );
    }

    /**
     * @param  string $sColumnName
     * @param  mixed $mDefault
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
         * @param   string $sColumnName
         * @param   array  $aRow
         * @param   mixed  $mDefault
         * @param   array  $aScheduleTask
         * @since   3
         * @sicne   3.5.0       Moved from `AmazonAutoLinks_UnitOutput__Base_CustomDBTable`.
         * @sicne   3.5.0       Changed the scope from protected.
         * @sicne   3.8.12      Added the `$aAPIRawItem` parameter.
         * @since   4.3.0       Deprecated the `$aAPIRawItem` parameter as it is not used.
         * @return  mixed
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

            $_sModifiedTime = $this->getElement(
                $aRow, // subject array
                array( 'modified_time' ), // dimensional keys
                '0000-00-00 00:00:00' // default
            );
            $_iCacheDuration  = ( int ) $this->_oUnitOption->get( 'cache_duration' );
            $_iExpirationTime = strtotime( $_sModifiedTime ) + $_iCacheDuration;
            $_bIsExpired      = $this->isExpired( $_iExpirationTime );
            $_bShouldSchedule = $_bIsExpired || ! $_bIsSet; // 3.8.5 When the value is not set, schedule retrieving extra product information

            $this->___scheduleBackgroundTask( $_bShouldSchedule, $aScheduleTask, $_iCacheDuration, $sColumnName );
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
             * @param integer $iCacheDuration
             * @param string  $sColumnName
             * @since 3.5.0
             * @since 4.3.0   Deprecated the `$aAPIRawItem` parameter.
             * @since 4.3.4   Added the `$sColumnName` parameter.
             */
            private function ___scheduleBackgroundTask( $bShouldSchedule, $aScheduleTask, $iCacheDuration, $sColumnName ) {

                if ( ! $bShouldSchedule ) {
                    return;
                }
                if ( $this->isEmpty( array_filter( $aScheduleTask ) ) ) {
                    return;
                }

                $iCacheDuration  = ( integer ) $iCacheDuration;
                $_bForceRenew    = ( boolean ) $this->_oUnitOption->get( '_force_cache_renewal' );
                $_sLocale        = strtoupper( $this->_oUnitOption->get( array( 'country' ), 'US' ) );
                $_sCurrency      = $this->_oUnitOption->get( array( 'preferred_currency' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultCurrencyByLocale( $_sLocale ) );
                $_sLanguage      = $this->_oUnitOption->get( array( 'language' ), AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $_sLocale ) );
                $_aRatingColumns = array( 'rating', 'rating_image_url', 'rating_html' );
                $_aReviewColumns = array( 'customer_reviews', 'customer_review_url', 'customer_review_charset' );
                $_aRatingReviewCommonColumns = array( 'number_of_reviews' );

                if ( ! in_array( $sColumnName, array_merge( $_aRatingColumns, $_aReviewColumns, $_aRatingReviewCommonColumns ), true ) ) {
                    AmazonAutoLinks_Event_Scheduler::scheduleProductInformation(
                        $aScheduleTask[ 'associate_id' ] . '|' . $_sLocale . '|' . $_sCurrency . '|' . $_sLanguage,
                        $aScheduleTask[ 'asin' ],
                        $iCacheDuration,
                        $_bForceRenew,
                        $this->_oUnitOption->get( 'item_format' )
                    );
                    return;
                }

                $_sProductID = $aScheduleTask[ 'asin' ] . '|' . $_sLocale . '|' . $_sCurrency . '|' . $_sLanguage;

                // Schedule a review retrieval background routine.
                if ( in_array( $sColumnName, $_aReviewColumns, true ) ) {
                    AmazonAutoLinks_Event_Scheduler::scheduleReviewIfNoProductInformationRoutines(
                        $_sProductID,
                        $iCacheDuration,
                        $_bForceRenew
                    );
                }

                // Schedule a rating retrieval routine.
                if ( in_array( $sColumnName, $_aRatingColumns, true ) ) {
                    AmazonAutoLinks_Event_Scheduler::scheduleRatingIfNoProductInformationRoutines(
                        $_sProductID,
                        $iCacheDuration,
                        $_bForceRenew
                    );
                    return;
                }

                // At this point, the column could be 'number_of_reviews'.
                // In this case, it can be retrieved by the rating or review routines.
                // So if the `%review%` Item Format tag is present and `%rating%` is not, schedule a review routine and vice versa.
                // Schedule a rating/review retrieval routine.
                if ( in_array( $sColumnName, $_aRatingReviewCommonColumns, true ) ) {
                    if ( $this->_oUnitOption->hasItemFormatTags( array( '%rating%' ) ) ) {
                        AmazonAutoLinks_Event_Scheduler::scheduleRatingIfNoProductInformationRoutines(
                            $_sProductID,
                            $iCacheDuration,
                            $_bForceRenew
                        );
                        return;
                    }
                    if ( $this->_oUnitOption->hasItemFormatTags( array( '%review%' ) ) ) {
                        AmazonAutoLinks_Event_Scheduler::scheduleReviewIfNoProductInformationRoutines(
                            $_sProductID,
                            $iCacheDuration,
                            $_bForceRenew
                        );
                        return;
                    }
                }

            }

            /**
             * Schedules debug information to be inserted at the bottom of the product output.
             * @param  boolean $_bIsSet
             * @param  string $sColumnName
             * @param  integer $_iCacheDuration
             * @param  integer $_iExpirationTime
             * @param  boolean $_bIsExpired
             * @param  array $aScheduleTask
             * @return void
             */
            private function ___addDebugInformation( $_bIsSet, $sColumnName, $_iCacheDuration, $_iExpirationTime, $_bIsExpired, $aScheduleTask ) {

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