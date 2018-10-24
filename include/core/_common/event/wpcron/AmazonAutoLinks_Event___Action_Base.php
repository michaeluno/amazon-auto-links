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
 * Provides base methods for plugin event actions.

 * @package      Amazon Auto Links
 * @since        3.5.0
 */
abstract class AmazonAutoLinks_Event___Action_Base extends AmazonAutoLinks_PluginUtility {

    protected $_sActionHookName     = '';
    protected $_iCallbackParameters = 1;
    protected $_iHookPriority       = 10;

    /**
     * Sets up hooks.
     * @since       3.5.0
     */
    public function __construct() {

        add_action(
            $this->_sActionHookName,
            array( $this, 'replyToDoAction' ),
            $this->_iHookPriority, // priority
            $this->_iCallbackParameters
        );

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
        call_user_func_array( array( $this, '_doAction' ), $_aParameters );
    }

    protected function _doAction() {}

}