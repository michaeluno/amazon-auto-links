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
 * Optimize database tables.
 *  
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_Database_Optimize extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Optimize the `aal_request_cache` table.
     * @tags optimize
     */
    public function scratch_optimize_aal_request_cache() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        return $this->___optimizeTable( $_oTable );
    }

    /**
     * @purpose Optimize the `aal_tasks` table.
     * @tags optimize
     */
    public function scratch_optimize_aal_tasks() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        return $this->___optimizeTable( $_oTable );
    }

    /**
     * @purpose Optimize the `aal_products` table.
     * @tags optimize
     */
    public function scratch_optimize_aal_products() {
        $_oTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        return $this->___optimizeTable( $_oTable );
    }

        /**
         * @param $_oTable
         * @return mixed
         */
        private function ___optimizeTable( $_oTable ) {
            $_sEngine = $_oTable->getTableStatus( 'Engine' );
            $this->_outputDetails( 'Table status before optimize', $_oTable->getTableStatus() );
            $_iStart  = time();
            $_mResult = $_oTable->optimize();
            $this->_outputDetails( 'Elapsed', time() - $_iStart );
            $this->_outputDetails( 'Table status after optimize', $_oTable->getTableStatus() );
            return $_mResult;
        }    
    
}