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
 * Updates unit status via ajax calls.
 * @since   3.7.6
 *
 */
class AmazonAutoLinks_Unit_AjaxEvent_UnitStatusUpdater extends AmazonAutoLinks_AjaxEvent_Base {

    protected $_sActionHookName = 'wp_ajax_aal_action_update_unit_status';

    protected $_sNonceKey = 'aal_unit_listing_table';

    protected function _getResponse( array $aPost ) {
        $_aUnits = $this->getElementAsArray( $aPost, 'units' );
        $_aUnitStatus = array();
        foreach( $_aUnits as $_iUnitID ) {
            do_action( 'aal_action_unit_prefetch', array( 'id' => array( $_iUnitID ) ) );
            $_nsStatus = get_post_meta( $_iUnitID, '_error', true );
            $_aUnitStatus[ $_iUnitID ] = $_nsStatus;
        }
        return $_aUnitStatus;
    }

}