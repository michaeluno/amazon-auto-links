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
 * Checks response errors.
 *
 * Sets a post meta '_error' when an error is found, includes when no item is found.
 * This class only handles calling a callback method so each extended unit output class should have an overridden version of the `replyToCheckErrors()` method.
 *
 * @since       3.7.0
 */
class AmazonAutoLinks_UnitOutput__ErrorChecker extends AmazonAutoLinks_UnitOutput__DelegationBase {

    /**
     * Indicates whether the cache of the unit HTTP request is set or not.
     * @var bool
     */
    private $___bIsCacheSet = false;

    /**
     * @return array
     */
    protected function _getActionArguments() {
        return array(
            array(
                'aal_action_set_http_request_cache',
                array( $this, '_replyToCacheSetAction' ),   // callback
                10,  // priority - set low as it should be inserted last
                5    // number of parameters
            ),
        );
    }

    /**
     * @return array
     */
    protected function _getFilterArguments() {
        return array(
            array(
                'aal_filter_products',
                array( $this, '_replyToCheckErrors' ),
                10,  // priority - set low as it should be inserted last
                3    // 3 parameters
            ),
        );
    }

    public function _replyToCheckErrors( $aProducts, $aURLs, $oUnitOutput ) {
        if ( ! $this->___bIsCacheSet ) {
            return $aProducts;
        }
        $_iUnitID = $this->_oUnitOutput->oUnitOption->get( 'id' );
        if ( ! $_iUnitID ) {
            return $aProducts;
        }
        call_user_func_array(
            array( $this->_oUnitOutput, 'replyToCheckErrors' ),
            array( $aProducts, $_iUnitID )
        );
        return $aProducts;
    }

    /**
     * Called only when the unit HTTP request is cached.
     *
     * When a cache content is returned, this action is not triggered.
     *
     * @remark      Since the response data is raw and the format is different between API request results and RSS feeds,
     * this method just flags whether the cache is set or not. Then the actual handling is done in another callback.
     * @callback    action  aal_action_set_http_request_cache
     * @return      void
     * @since       3.7.0
     */
    public function _replyToCacheSetAction( $sCacheKey, $sURL, $aResponse, $iCacheDuration, $sCharacterset ) {
        $this->___bIsCacheSet = true;
    }

}