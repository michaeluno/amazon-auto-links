<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * One of the base classes for unit classes.
 * 
 * Provides shared methods and properties for debugging.
 * @since       3
 */
abstract class AmazonAutoLinks_Unit_Base_CustomDBTable extends AmazonAutoLinks_Unit_Base_Debug {
    
    /**
     * Retrieves a row array from the given database rows.
     * 
     * In order to perform a background task scheduling when a row is not found,
     * pass the associate ID.
     * 
     * @remark      The keys of the database rows must be formatted to have {asin}_{locale}.
     * @return      array
     */
    protected function _getDBProductRow( $aDBRows, $sASIN, $sLocale, $sAssociateID='' ) {

        if ( ! $this->bDBTableAccess ) {
            return array();
        }
        $_aDBProductRow = $this->getElementAsArray(
            $aDBRows,
            $sASIN . '_' . $sLocale,
            array()
        );            
        
        // Schedule a background task to retrieve the product information.
        if ( empty( $_aDBProductRow ) && $sAssociateID ) {

            AmazonAutoLinks_Event_Scheduler::getProductInfo(
                $sAssociateID,
                $sASIN, 
                $sLocale,
                ( int ) $this->oUnitOption->get( 'cache_duration' )
            );
        }
        
        return $_aDBProductRow;
    }    
    
    /**
     * 
     * @since       3
     */
    protected function _getValueFromRow( $sColumnName, array $aRow, $mDefault=null, $aScheduleTask=array( 'locale' => '', 'asin' => '', 'associate_id' => '' ) ) {

        $_mValue        = $this->getElement(
            $aRow, // subject array
            array( $sColumnName ), // dimensional keys
            null // default - important to set null to evaluate whether the value exists in the table
        );  
        $_bIsSet        = ! is_null( $_mValue );
        $_mValue        = $_bIsSet
            ? maybe_unserialize( $_mValue )
            : $mDefault;
                        
        // Now schedule a background task to retrieve product info.
        $_bScheduleTask = ! $this->isEmpty( array_filter( $aScheduleTask ) );

        $_sModifiedTime = $this->getElement(
            $aRow, // subject array
            array( 'modified_time' ), // dimensional keys
            '0000-00-00 00:00:00' // default
        );        
        $_iCacheDuration  = ( int ) $this->oUnitOption->get( 'cache_duration' );
        $_iExpirationTime = strtotime( $_sModifiedTime ) + $_iCacheDuration;
        $_bIsExpired      = $this->isExpired( $_iExpirationTime );
        if ( 
            $_bScheduleTask && $_bIsExpired
        ) {
            AmazonAutoLinks_Event_Scheduler::getProductInfo(
                $aScheduleTask[ 'associate_id' ],
                $aScheduleTask[ 'asin' ], 
                $aScheduleTask[ 'locale' ],
                $_iCacheDuration
            );
        }           
        
        // Now schedule a debug info to be inserted at the bottom of the product output.    
        if ( ! $_bIsSet && $this->oOption->isDebug() ) {
            $this->_setColumnItemDebugInfo(
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
        
        return $_mValue;

    }  
    
    /**
     * 
     * @since       3
     * @return      array
     */
    protected function _getProductsFromCustomDBTable( array $aASINLocales ) {
        
        $_sASINLocales = "('" . implode( "','", $aASINLocales ) . "')";
        $_aResults     =  $this->oProductTable->getRows(
            "SELECT *
            FROM {$this->oProductTable->sTableName}
            WHERE asin_locale in {$_sASINLocales}"
        );     
        return is_array( $_aResults )
            ? $this->_formatRows( $_aResults )
            : array();
        
    }
        /**
         * Modifies the array keys to asin_locale from numeric index.
         * @return      array
         */
        private function _formatRows( array $aRows ) {
            $_aNewRows = array();
            foreach( $aRows as $_iIndex => &$_aRow ) {
                if ( ! isset( $_aRow[ 'asin_locale' ] ) ) {
                    continue;
                }
                $_aNewRows[ $_aRow[ 'asin_locale' ] ] = $_aRow;
            }
            return $_aNewRows;
        }
    
}