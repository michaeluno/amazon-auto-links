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
 * Provides shared utility methods for the plugin database table classes.
 *
 * @since       3.5.0
 */
abstract class AmazonAutoLinks_DatabaseTable_Utility extends AmazonAutoLinks_DatabaseTable_Base {

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
     * @since       3.5.0
     */
    public function deleteExpired( $sExpiryTime='' ) {

        $sExpiryTime = $sExpiryTime
            ? $sExpiryTime
            : "UTC_TIMESTAMP()";    // NOW() <-- GMT compatible
        $this->getVariable(
            "DELETE FROM `{$this->aArguments[ 'table_name' ]}` "
            . "WHERE expiration_time < {$sExpiryTime}"
        );
        $this->getVariable( "OPTIMIZE TABLE `{$this->aArguments[ 'table_name' ]}`;" );

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
     * @since       3.7.3
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

        $_iTotalRows    = $this->getTotalItemCount();
        $_iGoalSize     = $_iTableSize * 0.9;   // 90% of the actual size in order not to exceed the set size
        $_fSizePerRow   = $_iGoalSize / $_iTotalRows;    // float
        $_iExceededSize = $_iGoalSize - $_iSetSize;
        $_iNumToDelete  = ceil( $_iExceededSize / $_fSizePerRow );
        $this->getVariable(
            "DELETE FROM `{$this->aArguments[ 'table_name' ]}` "
            . "ORDER BY modified_time ASC LIMIT {$_iNumToDelete};"
        );
        $this->getVariable( "OPTIMIZE TABLE `{$this->aArguments[ 'table_name' ]}`;" );

    }

}