<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A class for inserting custom URL query arguments into link URLs.
 *
 * @since       4.6.19
 */
class AmazonAutoLinks_UnitOutput__CustomQueryArguments extends AmazonAutoLinks_UnitOutput__DelegationBase {

    /**
     * @var   array
     * Structure is like
     * ```
     * array(
     *      array(
     *          'key'   => 'foo',
     *          'value' => 'bar',
     *      ),
     *      array(
     *          'key'   => 'another',
     *          'value' => 'one',
     *      ),
     * ),
     * ```
     * @since 4.6.19
     */
    public $aCustomLinkQuery = array();

    /**
     * @since 4.6.19
     */
    protected function _construct() {
        $this->aCustomLinkQuery = $this->getAsArray( $this->_oUnitOutput->oUnitOption->get( '_custom_url_query_string' ) );
    }

    /**
     * @return array
     */
    protected function _getFilterArguments() {
        return array(
            array(
                'aal_filter_product_link',
                array( $this, 'replyToModifyProductURLs' ),
                100, // priority
                1    //  parameters
            ),
        );
    }

    /**
     * @return boolean
     * @since  4.6.19
     */
    protected function _shouldProceed() {
        return $this->___hasCustomProductLinkURLQuery();
    }
        /**
         * @since   3.7.5
         * @since    4.6.19 Moved from `AmazonAutoLinks_UnitOutput_Base`.
         * @return  boolean
         */
        private function ___hasCustomProductLinkURLQuery() {
            foreach( $this->aCustomLinkQuery as $_aKeyValue ) {
                $_aQueryKeyValue = array_filter( $_aKeyValue );
                if ( empty( $_aQueryKeyValue ) ) {
                    continue;
                }
                return true;
            }
            return false;
        }

    /**
     * @param    string $sURL
     * @return   string
     * @since    3.7.5
     * @since    4.6.19 Moved from `AmazonAutoLinks_UnitOutput_Base`.
     * @callback add_filter()  aal_filter_product_link
     */
    public function replyToModifyProductURLs( $sURL ) {
        $_aQuery     = array();
        foreach( $this->aCustomLinkQuery as $_iIndex => $_aKeyValue ) {
            $_aQuery[ $_aKeyValue[ 'key' ] ] = $_aKeyValue[ 'value' ];
        }
        return add_query_arg( $_aQuery, $sURL );
    }

}