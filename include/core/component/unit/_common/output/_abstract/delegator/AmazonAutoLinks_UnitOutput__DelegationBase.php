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
 * A base class for unit output delegation classes.
 *
 * @since       3.5.0
 */
abstract class AmazonAutoLinks_UnitOutput__DelegationBase extends AmazonAutoLinks_UnitOutput_Utility {

    protected $_oUnitOutput;

    /**
     * Stores WP filter hooks arguments.
     * @var array
     */
    protected $_aFilterArguments = array();

    /**
     * Stores WP action hooks arguments.
     * @var array
     */
    protected $_aActionArguments = array();

    /**
     * Sets up hooks.
     *
     * @param $oUnitOutput
     */
    public function __construct( $oUnitOutput ) {

        $this->_oUnitOutput = $oUnitOutput;

        if ( ! $this->_shouldProceed() ) {
            return;
        }

        $this->_aFilterArguments = $this->_getFilterArguments();
        $this->_aActionArguments = $this->_getActionArguments();
        $this->___addHooks( $this->_aFilterArguments, 'add_filter' );
        $this->___addHooks( $this->_aActionArguments, 'add_action' );

        $this->_construct();

    }
        /**
         * @param array    $aHooksArguments
         * @param callable $aosCallable        Must be either `add_action` or `add_filter`
         */
        private function ___addHooks( array $aHooksArguments, $aosCallable ) {
            foreach( $aHooksArguments as $_aArguments ) {
                $this->___setHook( $_aArguments, $aosCallable );
            }
        }
        /**
         * @param array    $aHooksArguments
         * @param callable $aosCallable        Must be either `remove_action` or `remove_filter`
         */
        private function ___removeHooks( array $aHooksArguments, $aosCallable ) {
            foreach( $aHooksArguments as $_aArguments ) {
                unset( $aHooksArguments[ 3 ] );   // remove the 4th parameter - the number of parameters
                $this->___setHook( $_aArguments, $aosCallable );
            }        
        }
            /**
             * Sets an individual hook (singular).
             * @param array     $aArguments
             * @param callable  $aosCallable
             */
            private function ___setHook( array $aArguments, $aosCallable ) {
                if (
                    ! isset(
                        $aArguments[ 0 ],
                        $aArguments[ 1 ]
                    )
                ) {
                    return;
                }
                call_user_func_array( $aosCallable, $aArguments );
            }

    /**
     * This method must be called after instantiating this class in order to remove the hook(s) used by per-unit basis.
     */
    public function __destruct() {
        $this->___removeHooks( $this->_aFilterArguments, 'remove_filter' );
        $this->___removeHooks( $this->_aActionArguments, 'remove_action' );
    }

    /* Extended classes should override the following methods. */

    /**
     * @return array
     */
    protected function _getFilterArguments() {
        return array();
    }

    /**
     * @return  array
     */
    protected function _getActionArguments() {
        return array();
    }

    protected function _construct() {}

    protected function _shouldProceed() {
        return true;
    }

}