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
 * A base class for classes that inject debug information in unit outputs.
 *
 * @since       3.5.0
 */
abstract class AmazonAutoLinks_UnitOutput__DebugInformation_Base extends AmazonAutoLinks_UnitOutput__DelegationBase {

    /**
     * @return boolean
     */
    protected function _shouldProceed() {
        if ( $this->_oUnitOutput->oUnitOption->get( 'is_preview' ) ) {
            return false;
        }
        return ( boolean ) $this->_oUnitOutput->oOption->isDebug();
    }

}