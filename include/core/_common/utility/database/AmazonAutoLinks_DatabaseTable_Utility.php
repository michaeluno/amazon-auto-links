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
 * Provides shared utility methods for the plugin database table classes.
 *
 * @since       3.5.0
 */
abstract class AmazonAutoLinks_DatabaseTable_Utility extends AmazonAutoLinks_DatabaseTable_Base {

    /**
     * Checks whether the given cache exists or not by the given name.
     * @param  array $aColumnNameValuePairs e.g. array( 'asin' => '1234567890', 'locale' => 'US ) )
     * @return boolean
     * @since  4.3.4
     */
    public function doesRowExist( array $aColumnNameValuePairs=array() ) {
        $_aColumnNames     = array_keys( $aColumnNameValuePairs );
        $_aValues          = array_values( $aColumnNameValuePairs );
        $_aFirstColumnName = reset( $_aColumnNames );
        $_aFirstValue      = reset( $_aValues );
        $_sQuery           = "SELECT {$_aFirstColumnName} "
            . " FROM {$this->aArguments[ 'table_name' ]} "
            . " WHERE {$_aFirstColumnName} = '{$_aFirstValue}'";
        foreach( $aColumnNameValuePairs as $_sName => $_sValue ) {
            $_sQuery .= " AND {$_sName}='{$_sValue}'";
        }
        $_sQuery          .= " LIMIT 1;";
        return ( boolean ) $this->getVariable( $_sQuery );
    }

    /**
     * Retrieves a count of expired rows.
     * @sine        3.5.0
     * @return      integer
     */
    public function getExpiredItemCount() {
        return $this->getVariable(
            "SELECT COUNT(*) FROM `{$this->aArguments[ 'table_name' ]}` "
            . "WHERE expiration_time < UTC_TIMESTAMP()"     // not using NOW() as NOW() is GMT compatible
        );
    }

    /**
     * Removes expired items from the table.
     * @param string $sExpiryTime
     * @since 3.5.0
     */
    public function deleteExpired( $sExpiryTime='' ) {

        $sExpiryTime = $sExpiryTime
            ? $sExpiryTime
            : "UTC_TIMESTAMP()";    // NOW() <-- GMT compatible
        $this->getVariable(
            "DELETE FROM `{$this->aArguments[ 'table_name' ]}` "
            . "WHERE expiration_time < {$sExpiryTime}"
        );
        $this->optimize();

    }

    /**
     * @since       3.5.0
     * @deprecated  Not used at the moment.
     */
    public function deleteAll() {
        $this->getVariable(
            "Truncate table `{$this->aArguments[ 'table_name' ]}`"
        );
    }

    /**
     * Removes rows from older ones to limit the size.
     * @param integer $iMB  Megabytes in integer.
     * @since 3.7.3
     */
    public function truncateBySize( $iMB ) {

        if ( 0 === $iMB ) {
            $this->delete();    // delete all rows by passing nothing
            return;
        }

        $_iSetSize      = ( integer ) $iMB;
        $_iTableSize    = $this->getTableSize( true );  // mb
        if ( $_iSetSize > $_iTableSize ) {
            return;
        }

        $_iNumToDelete  = $this->___getNumberOfRowsToDelete( $_iTableSize, $_iSetSize );

        $this->getVariable(
            "DELETE FROM `{$this->aArguments[ 'table_name' ]}` "
            . "ORDER BY modified_time ASC LIMIT {$_iNumToDelete};"
        );
        $this->optimize();

    }
        /**
         * @param  integer $iTableSize
         * @param  integer $iSetSize
         * @return integer
         * @since  3.8.12
         */
        private function ___getNumberOfRowsToDelete( $iTableSize, $iSetSize ) {

            $_iTotalRows    = $this->getTotalItemCount();
            $_fSizePerRow   = $iTableSize / $_iTotalRows;    // float - approximate size per row
            $_iExceededSize = $iTableSize - $iSetSize;
            $_iNumToDelete  = ceil( $_iExceededSize / $_fSizePerRow );

            // Add extra number of rows
            $_f10PercentOfSetSize = $iSetSize * 0.1;
            $_iExtraRowsToDelete  = ceil( $_f10PercentOfSetSize / $_fSizePerRow );

            return $_iNumToDelete + $_iExtraRowsToDelete;

        }

    /**
     * @remark Used for unit tests to check the used table engine.
     * @since  4.3.0
     * @param  string $sColumnName The status table column name.
     * @return array|string|null   When the column name is specified, returns the value of the column (null when not found). Otherwise, the full status data as an array.
     */
    public function getTableStatus( $sColumnName='' ) {
        $_aTableStatus = $this->getRow( "SHOW TABLE STATUS FROM `{$GLOBALS[ 'wpdb' ]->dbname}` LIKE '{$this->aArguments[ 'table_name' ]}';" );
        return $sColumnName
            ? ( isset( $_aTableStatus[ $sColumnName ] ) ? $_aTableStatus[ $sColumnName ] : null )
            : $_aTableStatus;
    }

    /**
     * @param  string $sColumns
     * @return array
     * @since  4.3.5
     */
    public function getInformationSchema( $sColumns='*' ) {
        $_sQuery = "SELECT {$sColumns} FROM INFORMATION_SCHEMA.TABLES "
            . " WHERE table_schema = '{$GLOBALS[ 'wpdb' ]->dbname}'"
            . " AND table_name ='{$this->aArguments[ 'table_name' ]}';";
        return $this->getRow( $_sQuery );
    }

    /**
     * @return array
     * @since  4.3.5
     */
    public function getMySQLVersion() {
        return $this->getVariable( "SELECT VERSION();" );
    }

    /**
     * @return array
     * @since  4.3.5
     */
    public function getColumnInformation() {
        return $this->getRows( "DESCRIBE `{$this->aArguments[ 'table_name' ]}`;" );
    }

    /**
     * Reurns the table information.
     * @return array
     * @since  4.3.5
     */
    public function getTableInformation() {
        return array(
            'set_name'    => $this->getTableName(),
            'set_version' => $this->getVersion(),
            'exists'      => $this->tableExists() ? 'Yes' : 'No',
            'size'        => $this->getTableSize(),
            'rows'        => $this->getTotalItemCount(),
        )  + array_change_key_case( $this->getInformationSchema(), CASE_LOWER )
           + array_change_key_case( $this->getTableStatus(), CASE_LOWER )
           + array(
               'checks'  => $this->check(),
               'columns' => $this->getColumnInformation(),
           );
    }

    /**
     * @return array
     * @since  4.3.5
     */
    public function check() {
        return $this->getRow( "CHECK TABLE `{$this->aArguments[ 'table_name' ]}`;" );
    }

    /**
     * Performs optimization on the table.
     * @since  4.3.5
     * @remark The REPAIR TABLE method only effective on the MyISAM, ARCHIVE, or CSV table engine type.
     * @return mixed
     */
    public function repair() {
        return $this->getVariable( "REPAIR TABLE `{$this->aArguments[ 'table_name' ]}`;" );
    }

    /**
     * Performs optimization on the table.
     * @since  4.3.5
     * @return mixed
     */
    public function optimize() {
        return $this->getVariable( "OPTIMIZE TABLE `{$this->aArguments[ 'table_name' ]}`;" );
    }

}