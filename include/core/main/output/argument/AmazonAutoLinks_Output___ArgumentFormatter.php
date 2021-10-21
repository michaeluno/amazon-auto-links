<?php
/**
 * Amazon Auto Links
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
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
     * @return array
     */
    public function get() {
        $this->___setUnitIDArgument();
        return $this->_aArguments;
    }
        /**
         * Sets the `id` argument.
         */
        private function ___setUnitIDArgument() {
            $this->_aArguments[ '_unit_ids' ] = $this->___getUnitIDs();
        }
            /**
             * @return array
             */
            private function ___getUnitIDs() {
                $_oUnitIDArgumentFormatter = new AmazonAutoLinks_Output___ArgumentFormatter_UnitID( $this->_aArguments );
                return $_oUnitIDArgumentFormatter->get();
            }

}