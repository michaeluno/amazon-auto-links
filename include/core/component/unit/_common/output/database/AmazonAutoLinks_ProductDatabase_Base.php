<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * An abstract base class for product database data getter wrapper classes.
 *
 * @since       3.4.13
 */
abstract class AmazonAutoLinks_ProductDatabase_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores ASIN_Locale items.
     * @var array
     */
    protected $_aArguments = array(
    );

    /**
     * Performs necessary set-ups.
     */
    public function __construct( array $aArguments ) {
        $this->_aArguments = $aArguments + $this->_aArguments;
    }

}