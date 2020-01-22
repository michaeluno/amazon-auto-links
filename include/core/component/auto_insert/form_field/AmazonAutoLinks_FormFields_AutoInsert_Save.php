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
class AmazonAutoLinks_FormFields_AutoInsert_Save extends AmazonAutoLinks_FormFields_Base {

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
                'field_id'          => $sFieldIDPrefix . 'submit_save',
                'type'              => 'submit',
                'value'             => __( 'Save', 'amazon-auto-links' ),
                'label_min_width'   => 0,
                'attributes'        => array(
                    'field' => array(
                        'style' => 'float:right; clear:none; display: inline;',
                    ),
                ),         
                // 'redirect_url'      => add_query_arg(
                    // array(
                        // 'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ],
                    // ),
                    // admin_url( 'edit.php' )
                // ),
            )    
        );
    }
  
}