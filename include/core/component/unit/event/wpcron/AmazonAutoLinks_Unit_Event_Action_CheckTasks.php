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
 * Searches products by the given ASIN and locale.
 *
 * This is a plural version of `AmazonAutoLinks_Event___Action_APIRequestSearchProducts` which queries multiple products at a time.
 *
 * @since       4.3.0
 */
class AmazonAutoLinks_Unit_Event_Action_CheckTasks extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName = 'aal_action_check_tasks';

    /**
     * @return bool
     * @since 4.3.0
     */
    protected function _shouldProceed() {

        if ( $this->hasBeenCalled( __METHOD__ ) ) {
            return false;
        }
        if ( $this->_isLocked() ) {
            return false;
        }
        return true;

    }

    /**
     * Checks added plugin tasks that is on due.
     * @since 4.3.0
     * @return void
     */
    protected function _doAction() {

        $_aTasks       = array();
        $_aPseudoLocks = array();
        $_iNextRunTime = date( 'Y-m-d H:i:s', time() + $this->getAllowedMaxExecutionTime( 30, 300 ) - 1 );
        foreach( $this->___getDueTasks() as $_aTask ) {
            $_aPseudoLocks[] = array(
                'name'          => $_aTask[ 'name' ],
                'next_run_time' => $_iNextRunTime,
            );
            // Separate tasks by action
            $_aTasks[ $_aTask[ 'action' ] ][] = $_aTask;
        }
        if ( empty( $_aTasks ) ) {
            return;
        }

        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_oTaskTable->setRows( $_aPseudoLocks );  // expanding the `next_run_time` values so that they won't be checked

        foreach( $_aTasks as $_sActionName => $_aActionTasks ) {

            // Let components format action arguments.
            $_aActionTasks = apply_filters( "aal_filter_tasks_{$_sActionName}", $_aActionTasks );

            $this->___doTaskActions( $this->getAsArray( $_aActionTasks ), $_oTaskTable );

        }

    }
        /**
         * Do actions by action name.
         * @param array $aActionTasks
         * @param AmazonAutoLinks_DatabaseTable_aal_tasks $_oTaskTable
         * @since 4.3.0
         * @return void
         */
        private function ___doTaskActions( array $aActionTasks, AmazonAutoLinks_DatabaseTable_aal_tasks $_oTaskTable ) {
            foreach( $aActionTasks as $_aTask ) {
                $_aTask = $_aTask + array( 'action' => '', 'arguments' => array() );
                if ( empty( $_aTask[ 'action' ] ) || empty( $_aTask[ 'arguments' ] ) ) {
                    continue;
                }
                $_aParams = $_aTask[ 'arguments' ];
                array_unshift($_aParams, $_aTask[ 'action' ] );
                call_user_func_array( 'do_action', $_aParams );
                $_oTaskTable->deleteRows( $_aTask[ 'name' ] );
            }
        }

        /**
         *
         * @since 4.3.0
         * @return array
         * The structure of the returned array will look like:
         * ```
         *   Array(
         *      [0] => Array(
         *          [name] => (string) AINGEOIDBE|US|USD|en-US
         *          [action] => (string) aal_action_api_get_products_info
         *          [arguments] => Array(
         *              [0] => (string) EDFWEWAINGEOIDEWFWRJO
         *          )
         *          [creation_time] => (string) 2020-09-16 08:58:47
         *          [next_run_time] => (string) 2020-09-16 08:58:47
         *      )
         *      [1] => Array( ...
         *
         *  )
         * ```
         */
        private function ___getDueTasks() {
            $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
            return $this->getAsArray( $_oTaskTable->getDueItems() );
        }
}