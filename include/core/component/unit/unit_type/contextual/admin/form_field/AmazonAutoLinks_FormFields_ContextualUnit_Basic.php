<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno; Licensed GPLv2
 *
 */

/**
 * Provides the definitions of basic form fields of the 'contextual' unit type.
 *
 * @since           3.5.0
 */
class AmazonAutoLinks_FormFields_ContextualUnit_Basic extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     *
     * Pass an empty string to the parameter for meta box options.
     *
     * @return      array
     */
    public function get( $sFieldIDPrefix='' ) {

        $_aFields = array(
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_type',
                'type'          => 'hidden',
                'hidden'        => true,
                'value'         => 'contextual',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_title',
                'title'         => __( 'Unit Name', 'amazon-auto-links' ),
                'type'          => 'text',
                'description'   => 'e.g. <code>' . __( 'My Contextual Unit', 'amazon-auto-links' ) . '</code>',
                'value'         => '',    // a previous value should not appear
            ),
        );
        return $_aFields;

    }

}