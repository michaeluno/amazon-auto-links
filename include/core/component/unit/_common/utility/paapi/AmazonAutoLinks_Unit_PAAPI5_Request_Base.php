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
 * Performs PA-API 5 requests.
 *
 * @since   5.0.0
 */
class AmazonAutoLinks_Unit_PAAPI5_Request_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * @var   AmazonAutoLinks_UnitOption_search
     * @since 5.0.0
     */
    public $oUnitOption;
    /**
     * @var   string
     * @since 5.0.0
     */
    public $sPublicKey;
    public $sSecretKey;

    /**
     * @var   string
     * @since 5.0.0
     */
    protected $_sResponseItemsParentKey = '';

    /**
     * Sets up properties and hooks.
     * @since  5.0.0
     */
    public function __construct( AmazonAutoLinks_UnitOption_Base $oUnitOption, $sPublicKey, $sSecretKey ) {
        $this->oUnitOption = $oUnitOption;
        $this->sPublicKey  = $sPublicKey;
        $this->sSecretKey  = $sSecretKey;
    }

    /**
     * @param  integer $iCount
     * @return array
     * @since  5.0.0
     */
    public function getPAAPIResponse( $iCount ) {
        return array();
    }

}
