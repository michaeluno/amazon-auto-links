<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Provides the definitions of form fields.
 *
 * @since           3.5.0
 */
class AmazonAutoLinks_FormFields_ProductFilterAdvanced extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'      => '_no_pending_items',
                'type'          => 'checkbox',
                'title'         => __( 'No Pending Items', 'amazon-auto-links' ),
                'label'         => __( 'Do not show products with pending elements.', 'amazon-auto-links' ),
                'tip'           => __( 'Some product data including rating and prices are retrieved in the background and will be pending when the product is first displayed.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'      => '_filter_by_rating',
                'type'          => 'inline_mixed',
                'title'         => __( 'Customer Rating', 'amazon-auto-links' ),
                'content'       => array(
                    array(
                        'field_id'      => 'enabled',
                        'type'          => 'checkbox',
                        'label'         => sprintf( __( 'Show products which has a customer rating %1$s', 'amazon-auto-links' ), '' ),
                    ),
                    array(
                        'field_id'  => 'case',
                        'type'      => 'select',
                        'label'     => array(
                            'above' => __( 'above or equal to', 'amazon-auto-links' ),
                            'below' => __( 'below or equal to', 'amazon-auto-links' ),
                        ),
                    ),
                    array(
                        'field_id'      => 'size',
                        'type'          => 'number',
                        'attributes'    => array(
                            'step'  => 0.1,
                            'min'   => 0,
                            'max'   => 5.0,
                        ),
                        'after_input'   => ' .',
                    ),
                ),
            ),
            array(
                'field_id'      => '_filter_by_discount_rate',
                'type'          => 'inline_mixed',
                'title'         => __( 'Discount Rate', 'amazon-auto-links' ),
                'content'       => array(
                    array(
                        'field_id'      => 'enabled',
                        'type'          => 'checkbox',
                        'label'         => sprintf( __( 'Show products which has a discount rate %1$s', 'amazon-auto-links' ), '' ),
                    ),
                    array(
                        'type'      => 'select',
                        'field_id'  => 'case',
                        'label'     => array(
                            'above' => __( 'above or equal to', 'amazon-auto-links' ),
                            'below' => __( 'below or equal to', 'amazon-auto-links' ),
                        ),
                    ),
                    array(
                        'type'          => 'number',
                        'field_id'      => 'size',
                        'after_input'   => ' %.',
                        'attributes'    => array(
                            'min'   => 0,
                            'max'   => 100,
                            'step'  => 1,
                        ),
                    ),
                ),
            ),
        );

        // Insert common field arguments.
        $_oOption      = AmazonAutoLinks_Option::getInstance();
        $_bIsDisabled  = ! $_oOption->isAdvancedAllowed();
        $_aDefaults    =  $_oOption->get( array( 'unit_default' ), array() );
        $_sOpeningTag  = $_bIsDisabled
            ? "<div class='upgrade-to-pro' style='margin:0; padding:0; display: inline-block;' title='" . __( 'Please consider upgrading to Pro to use this feature!', 'amazon-auto-links' ) . "'>"
            : "";
        $_sClosingTag  = $_bIsDisabled
            ? "</div>"
            : "";
        foreach( $_aFields as &$_aField ) {
            $_aField = array(
                    'before_field' => $_sOpeningTag,
                    'after_field'  => $_sClosingTag,
                )
                + $_aField
                + array( 'attributes' => array() )
            ;
            $_aField[ 'attributes' ] = array(
                'disabled' => $_bIsDisabled
                    ? 'disabled'
                    : null,
                'class' => $_bIsDisabled
                    ? 'disabled read-only'
                    : '',
            ) + $_aField[ 'attributes' ];

            // Set the default value
            $_sFieldID = isset( $_aField[ 'field_id' ] ) ? $_aField[ 'field_id' ] : '';
            if ( isset( $_aDefaults[ $_sFieldID ] ) ) {
                $_aField[ 'default' ] = $_aDefaults[ $_sFieldID ];
            }

        }
        return $_aFields;

    }

}