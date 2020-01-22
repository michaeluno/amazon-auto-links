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
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_AutoInsert_Area extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='' ) {
        return array(  
            array(
                'field_id'      => $sFieldIDPrefix . strtolower( get_class( $this ) ),
                'type'          => 'hidden',
                'attributes'    => array(
                    'name' => '', // disables the value to be sent to the form
                ),
                'show_title_column'    => false,
                'before_field'  => "<h3>" 
                        .  __( 'Where to Insert', 'amazon-auto-links' )
                    . "</h3>"
                    . "<p class='description'>"
                        . __( 'Define where auto insert should be performed.', 'amazon-auto-links' )
                    . "</p>",
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'unit_ids',
                'title'         => __( 'Select Units', 'amazon-auto-links' ),        
                'type'          => 'select',
                'is_multiple'   => true,
                'attributes'    => array(
                    'select' => array(
                        'size'  => 10,
                        'style' => 'height: 100%;',
                    ),
                ),
                // The below method is expensive so the `label` argument will be assigned in the `field_definition_{instantiated class name}_{field ID}` hook.
                // 'label'         => $this->getPostsLabelsByPostType(
                    // AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
                // ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'built_in_areas',
                'title'         => __( 'Areas', 'amazon-auto-links' ),
                'type'          => 'checkbox',
                'label'         => $this->getPredefinedFilters(),   // defined in the utility class.
                'description'   => __( 'Check where product links should appear.', 'amazon-auto-links' ),
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'filter_hooks',
                'title'         => __( 'Filter Hooks', 'amazon-auto-links' ) . " <span class='description'>(" . __( 'advanced', 'amazon-auto-links' ) . ")</span>",
                'type'          => 'text',
                'attribuets'    => array(
                    'size'      => version_compare( $GLOBALS['wp_version'], '3.8', '>=' )
                        ? 40 
                        : 60,
                ),
                'description'   => array(
                    sprintf( 
                        __( 'Enter the WordPress <a href="%1$s" target="_blank">filter hooks</a> with which the auto-insertion is performed, separated by commas.', 'amazon-auto-links' ),
                        'https://codex.wordpress.org/Plugin_API/Filter_Reference'
                    ),
                    "e.g. <code>"
                        . "my_custom_filter, other_plugin_filter"
                    . "</code>",    
                ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'position',
                'title'         => __( 'Positions ', 'amazon-auto-links' ),
                'type'          => 'radio',
                'label'         => array(
                    'above'    => __( 'Above', 'amazon-auto-links' ),
                    'below'    => __( 'Below', 'amazon-auto-links' ),
                    'both'     => __( 'Both', 'amazon-auto-links' ),
                ),
                'description'   => __( 'Determines whether the items are placed before or after (above or below) the area. This does not take effect for action hooks.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'action_hooks',
                'title'         => __( 'Action Hooks', 'amazon-auto-links' ) . " <span class='description'>(" . __( 'advanced', 'amazon-auto-links' ) . ")</span>",
                'type'          => 'text',
                'attributes'    => array(
                    'size' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'description'   => array(
                    sprintf( 
                        __( 'Enter the WordPress <a href="%1$s" target="_blank">action hooks</a> with which the auto-insertion is performed, separated by commas.', 'amazon-auto-links' ),
                        'http://codex.wordpress.org/Plugin_API/Action_Reference'
                    ),
                    "e.g. <code>"
                        . "login_footer, comment_form_before, comment_form_after, my_custom_action, other_plugin_action"
                    . "</code>",
                ),
            )
        );
    }
    
     
}