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
 * Optimize database tables.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_Database_Repair extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Repair the `aal_request_cache` table.
     * @tags repair
     * @throws Exception
     */
    public function scratch_repair_aal_request_cache() {
        $_oTable  = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        return $this->___repairTable( $_oTable );
    }

    /**
     * @purpose Repair the `aal_tasks` table.
     * @tags repair
     * @throws Exception
     */
    public function scratch_repair_aal_tasks() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        return $this->___repairTable( $_oTable );
    }

    /**
     * @purpose Repair the `aal_products` table.
     * @tags repair
     * @throws Exception
     */
    public function scratch_repair_aal_products() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        return $this->___repairTable( $_oTable );
    }
        /**
         * @param $_oTable
         *
         * @return mixed
         * @throws Exception
         */
        private function ___repairTable( $_oTable ) {
            $_sEngine = $_oTable->getTableStatus( 'Engine' );
            if ( ! in_array( $_sEngine, array( 'MyISSAM', 'ARCHIVE', 'CSV' ) ) ) {
                throw new Exception( 'The table engine type does not support the repair method. ' . "engine type: {$_sEngine}" );
            }
            $this->_outputDetails( 'Table status before repair', $_oTable->getTableStatus() );
            $_iStart  = time();
            $_mResult = $_oTable->repair();
            $this->_outputDetails( 'Elapsed', time() - $_iStart );
            $this->_outputDetails( 'Table status after repair', $_oTable->getTableStatus() );
            return $_mResult;
        }
    
}