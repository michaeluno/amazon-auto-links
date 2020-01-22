<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Provides methods to format output arguments.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_Output___ArgumentFormatter extends AmazonAutoLinks_Output___ArgumentFormatter_Base {

    protected $_aArguments = array(
        '_unit_ids' => array(),     // internal argument that stores the unit ids by parsing the arguments.
    );

    /**
     * @return      array
     */
    public function get() {

        $this->___setUnitIDArgument();
        $this->___setASINArguments();
        $this->___setSearchArguments();
        return $this->_aArguments;

    }
        /**
         * Sets the `id` argument.
         */
        private function ___setUnitIDArgument() {
            $this->_aArguments[ '_unit_ids' ] = $this->___getUnitIDs();
        }
            /**
             * @return      array
             */
            private function ___getUnitIDs() {
                $_oUnitIDArgumentFormatter = new AmazonAutoLinks_Output___ArgumentFormatter_UnitID( $this->_aArguments );
                return $_oUnitIDArgumentFormatter->get();
            }

        /**
         * Generates necessary arguments if the `asin` argument is set.
         * @return      void
         */
        private function ___setASINArguments() {
            if ( ! isset( $this->_aArguments[ 'asin' ] ) ) {
                return;
            }
            $_aASINs = $this->getStringIntoArray( $this->_aArguments[ 'asin' ], ',' );
            $this->_aArguments[ 'ItemId' ]         = implode( ',', $_aASINs );
            $this->_aArguments[ 'Operation' ]      = 'ItemLookup';
            $this->_aArguments[ '_allowed_ASINs' ] = $_aASINs;
            $this->_aArguments[ 'unit_type' ]      = 'item_lookup';
        }

        private function ___setSearchArguments() {
            if ( ! isset( $this->_aArguments[ 'search' ] ) ) {
                return;
            }
            $this->_aArguments[ 'Operation' ]     = 'SearchItems';
            $this->_aArguments[ 'Keywords' ]      = $this->_aArguments[ 'search' ];
            $this->_aArguments[ 'unit_type' ]     = 'search';
        }

}