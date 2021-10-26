<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A scratch class for task database.
 *  
 * @package     Auto Amazon Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_Database_aal_tasks extends AmazonAutoLinks_Scratch_Base {

    /**
     * Override this method.
     * @return mixed
     * @tags    task
     */
    public function scratch_setTask() {

        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_aRow       = array(
            'name'          => 'test',
            'action'        => 'aal_action_scratch_test',
            'arguments'     => array( 'FOO', 'BAR' ),
            'creation_time' => date( 'Y-m-d H:i:s', time() ),
            'next_run_time' => date( 'Y-m-d H:i:s', time() ),
        );
        $_mResult   = $_oTaskTable->setRow( $_aRow );
        return $this->_getDetails( $_mResult );

    }

    /**
     * Override this method.
     * @return mixed
     * @tags    task
     */
    public function scratch_insertRow() {

        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_aRow       = array(
            'name'          => 'test',
            'action'        => 'aal_action_scratch_test',
            'arguments'     => array( 'FOO', 'BAR' ),
            'creation_time' => date( 'Y-m-d H:i:s', time() ),
            'next_run_time' => date( 'Y-m-d H:i:s', time() ),
        );
        $_mResult   = $_oTaskTable->insertRow( $_aRow );
        return $this->_getDetails( $_mResult );

    }

    /**
     * Override this method.
     * @return mixed
     * @tags    task
     */
    public function scratch_insertRows() {

        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_aRows      = array(
            array(
                'name'          => 'test2',
                'action'        => 'aal_action_scratch_test',
                'arguments'     => array( 'Test 2' ),
                'creation_time' => date( 'Y-m-d H:i:s', time() ),
                'next_run_time' => date( 'Y-m-d H:i:s', time() ),
            ),
            array(
                'name'          => 'test3',
                'action'        => 'aal_action_scratch_test',
                'arguments'     => array( 'Test 3' ),
                'creation_time' => date( 'Y-m-d H:i:s', time() ),
                'next_run_time' => date( 'Y-m-d H:i:s', time() ),
            ),
            array(
                'name'          => 'test4',
                'action'        => 'aal_action_scratch_test',
                'arguments'     => array( 'Test 4' ),
                'creation_time' => date( 'Y-m-d H:i:s', time() ),
                'next_run_time' => date( 'Y-m-d H:i:s', time() ),
            ),
        );
        $_mResult   = $_oTaskTable->insertRows( $_aRows );
        return $this->_getDetails( $_mResult );

    }

    /**
     * @return mixed
     * @tags    task, insert_ignore
     */
    public function scratch_insertRowIgnore() {

        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_aRow       = array(
            'name'          => 'test',
            'action'        => 'aal_action_scratch_test',
            'arguments'     => array( 'OVERRIDDEN, BUT THIS SHuOLD NOT SEEN' ),
            'creation_time' => date( 'Y-m-d H:i:s', time() ),
            'next_run_time' => date( 'Y-m-d H:i:s', time() ),
        );
        $_mResult   = $_oTaskTable->insertRowIgnore( $_aRow );
        return $this->_getDetails( $_mResult );

    }

    /**
     * Override this method.
     * @return mixed
     * @tags    task
     */
    public function scratch_insertRowsIgnore() {

        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_aRows      = array(
            array(
                'name'          => 'test2',
                'action'        => 'aal_action_scratch_test',
                'arguments'     => array( 'Test 2' ),
                'creation_time' => date( 'Y-m-d H:i:s', time() ),
                'next_run_time' => date( 'Y-m-d H:i:s', time() ),
            ),
            array(
                'name'          => 'test3',
                'action'        => 'aal_action_scratch_test',
                'arguments'     => array( 'Test 3' ),
                'creation_time' => date( 'Y-m-d H:i:s', time() ),
                'next_run_time' => date( 'Y-m-d H:i:s', time() ),
            ),
            array(
                'name'          => 'test4',
                'action'        => 'aal_action_scratch_test',
                'arguments'     => array( 'Test 4' ),
                'creation_time' => date( 'Y-m-d H:i:s', time() ),
                'next_run_time' => date( 'Y-m-d H:i:s', time() ),
            ),
        );
        $_mResult   = $_oTaskTable->insertRowsIgnore( $_aRows );
        return $this->_getDetails( $_mResult );

    }

    /**
     * @return string|string[]|null
     * @tags    task
     */
    public function scratch_getDueItems() {

        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        return $this->_getDetails( $_oTaskTable->getDueItems() );

    }

    /**
     * @return mixed|string
     * @purpose The table engine must be `InnoDB`.
     * @tags engine
     */
    public function scratch_engineType() {
        $_oTable        = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_aRow          = $_oTable->getTableStatus();
        return $this->getElement( $_aRow, array( 'Engine' ) );
    }

}