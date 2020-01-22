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
 *
 */
abstract class AmazonAutoLinks_Event___Filter_Base extends AmazonAutoLinks_PluginUtility {

    protected $_sFilterHookName = '';
    protected $_iPriority = 10;
    protected $_iParameters = 1;

    /**
     *
     */
    public function __construct() {
        add_filter(
            $this->_sFilterHookName,
            array( $this, 'replyToFilter' ),
            $this->_iPriority,
            $this->_iParameters
        );
        $this->_construct();
    }

    public function replyToFilter( /* ... arguments ... */ ) {
        $_aArguments = func_get_args() + array( null );
        return call_user_func_array( array( $this, '_getFiltered' ), $_aArguments );
    }

    protected function _construct() {}

    protected function _getFiltered() {
        $_aArguments = func_get_args() + array( null );
        return  $_aArguments[ 0 ];
    }

}