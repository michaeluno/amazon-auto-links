<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * A base class of output argument formatter classes.
 * @since       3.5.0
 */
class AmazonAutoLinks_Output___ArgumentFormatter_Base extends AmazonAutoLinks_PluginUtility {

    protected $_aArguments = array();

    /**
     * Sets up properties.
     */
    public function __construct( $aArguments ) {
        $this->_aArguments = $this->getAsArray( $aArguments ) + $this->_aArguments;
    }

    public function get() {
        return $this->_aArguments;
    }

}