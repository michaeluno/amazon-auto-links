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
class AmazonAutoLinks_FormFields_AutoInsert_WhereToEnable extends AmazonAutoLinks_FormFields_Base {

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
                        .  __( 'Where to Enable', 'amazon-auto-links' )
                    . "</h3>"
                    . "<p class='description'>"
                        . __( 'Define the areas where units will be inserted.', 'amazon-auto-links' )
                        . ' ' . __( 'You can limit units appearance to certain areas of the site.', 'amazon-auto-links' )
                    . "</p>",
            ),        
            array(
                'field_id'       => $sFieldIDPrefix . 'enable_allowed_area',
                'type'           => 'radio',
                'title'          => __( 'On / Off', 'amazon-auto-links' ),            
                'label'          => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'description'    => __( 'Applies the criteria set in this section.', 'amazon-auto-links' ),
                'after_fieldset' => '<hr />',
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'enable_post_ids',
                'type'          => 'text',
                'title'         => __( 'Post IDs', 'amazon-auto-links' ),
                'attributes'    => array(
                    'size'  => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'description'   => __( 'Enter post IDs separated by commas.', 'amazon-auto-links' )
                    . ' ' . __( 'Leave this empty to disable this option.', 'amazon-auto-links' ),
            ),            
            array(
                'field_id'      => $sFieldIDPrefix . 'enable_page_types',
                'type'          => 'checkbox',
                'title'         => __( 'Page Types', 'amazon-auto-links' ),
                'label'         => array(
                    'is_singular'    => __( 'Single Post', 'amazon-auto-links' ),
                    'is_home'        => __( 'Home / Front', 'amazon-auto-links' ),
                    'is_archive'     => __( 'Archive', 'amazon-auto-links' ),
                    'is_404'         => __( '404', 'amazon-auto-links' ),
                    'is_search'      => __( 'Search', 'amazon-auto-links' ),
                ),
                'description'   => __( 'This option does not take effect for static insertion.', 'amazon-auto-links' )
                    . ' ' .  __( 'If no item is checked, this option will not take effect.', 'amazon-auto-links' ), 
            ),        
            array(
                'field_id'              => $sFieldIDPrefix . 'enable_post_types',
                'type'                  => 'posttype',
                'title'                 => __( 'Post Types', 'amazon-auto-links' ),
                'select_all_button'     => false,
                'select_none_button'    => false,                 
                'default'               => true,
                'slugs_to_remove'       => array( 
                    'revision', 
                    'attachment', 
                    'nav_menu_item', 
                    'amazon_auto_links', 
                    'aal_auto_insert'
                ),
                'description'           => __( 'If no item is checked, this option will not take effect.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'           => $sFieldIDPrefix . 'enable_taxonomy',
                'title'              => __( 'Taxonomies', 'amazon-auto-links' ),
                'type'               => 'taxonomy',
                'save_unchecked'     => false,
                'select_all_button'  => true,       
                'select_none_button' => true,                                 
                'taxonomy_slugs'     => $this->getSiteTaxonomies(),
                'description'        => __( 'For static insertion, only Category for the default Post post type can take effect.', 'amazon-auto-links' )
                    . ' ' . __( 'The checked terms which do not belong to the post type of the currently loading page will not take effect.', 'amazon-auto-links' )
                    . ' ' . __( 'Leave all unchecked not to enable this option.', 'amazon-auto-links' ),
            ),   
        );
    }   
   
  
}