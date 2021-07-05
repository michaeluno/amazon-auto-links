<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A base class of output argument formatter classes.
 * @since       3.5.0
 */
class AmazonAutoLinks_Output___ArgumentFormatter_Base extends AmazonAutoLinks_PluginUtility {

    protected $_aArguments = array();

    /**
     * Sets up properties.
     * @param array $aArguments
     */
    public function __construct( $aArguments ) {
        $this->_aArguments = $this->getAsArray( $aArguments ) + $this->_aArguments;
    }

    public function get() {
        return $this->_aArguments;
    }

}