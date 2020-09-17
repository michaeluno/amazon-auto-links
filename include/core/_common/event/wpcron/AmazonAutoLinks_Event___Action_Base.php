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
 * Provides base methods for plugin event actions.

 * @package      Amazon Auto Links
 * @since        3.5.0
 */
abstract class AmazonAutoLinks_Event___Action_Base extends AmazonAutoLinks_PluginUtility {

    protected $_sActionHookName     = '';
    protected $_iCallbackParameters = 1;
    protected $_iHookPriority       = 10;

    /**
     * Stores action hook names for multiple actions.
     * @var array
     * @since   4.3.0
     */
    protected $_aActionHookNames    = array();

    /**
     * Sets up hooks.
     * @since       3.5.0
     */
    public function __construct() {

        if ( $this->_sActionHookName ) {
            add_action(
                $this->_sActionHookName,
                array( $this, 'replyToDoAction' ),
                $this->_iHookPriority, // priority
                $this->_iCallbackParameters
            );
        }

        foreach( $this->_aActionHookNames as $_sActionHookName ) {
            add_action(
                $_sActionHookName,
                array( $this, 'replyToDoAction' ),
                $this->_iHookPriority, // priority
                $this->_iCallbackParameters
            );
        }

        $this->_construct();

    }

    /**
     * @since       3.5.0
     */
    protected function _construct() {}

    /**
     * @remark          Override this method in an extended class.
     * @callback        action
     */
    public function replyToDoAction( /* $aArguments */ ) {
        $_aParameters = func_get_args();
        if ( ! call_user_func_array( array( $this, '_shouldProceed' ), $_aParameters ) ) {
            return;
        }
        call_user_func_array( array( $this, '_doAction' ), $_aParameters );
    }

    /**
     * @return bool
     * @since 4.3.0
     */
    protected function _shouldProceed( /* $aArguments */ ) {
        return true;
    }

    protected function _doAction( /* $aArguments */  ) {}



    /**
     * Checks whether the action is locked.
     * By calling this method, a lock temporary file (which will be deleted at the end of script) will be created on a disk if it does not exist.
     * @return  bool
     * @since   3.7.7
     * @param   array       $aoData      The data to identify the call, usually the callback parameters.
     */
    protected function _isLocked( $aoData=array() ) {

        $_sIdentifier = get_class( $this )
            . $this->_sActionHookName
            . $this->_iCallbackParameters
            . $this->_iHookPriority
            . serialize( $aoData );
        $_oLock = new AmazonAutoLinks_VersatileFileManager_DeleteMode( $_sIdentifier );
        return $_oLock->isLocked();

    }

}