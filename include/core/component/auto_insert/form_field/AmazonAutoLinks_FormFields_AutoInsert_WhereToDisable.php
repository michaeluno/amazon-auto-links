<?php
/**
 * Provides the definitions of form fields.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_AutoInsert_WhereToDisable extends AmazonAutoLinks_FormFields_Base {

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
                'before_field'  => "<h3>" 
                        .  __( 'Where to Disable', 'amazon-auto-links' )
                    . "</h3>"
                    . "<p class='description'>"
                        . __( 'Define what kind of areas you do not want to insert units.', 'amazon-auto-links' )
                        . ' ' . __( 'You can exclude units appearance from certain areas of the site.', 'amazon-auto-links' )
                    . "</p>",
                'show_title_column'    => false,
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'enable_denied_area',
                'title'         => __( 'On / Off', 'amazon-auto-links' ),            
                'type'          => 'radio',
                'label'         => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'description'    => __( 'Applies the criteria set in this section.', 'amazon-auto-links' ),
                'after_fieldset' => '<hr />',
            ),        
            array(
                'field_id'      => $sFieldIDPrefix . 'diable_post_ids',
                'type'          => 'text',
                'title'         => __( 'Post IDs', 'amazon-auto-links' ),
                'attributes'    => array(
                    'size' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) 
                        ? 40 
                        : 60,
                ),
                'description'   => __( 'Enter post IDs separated by commas.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'      => $sFieldIDPrefix . 'disable_page_types',
                'type'          => 'checkbox',
                'title'         => __( 'Page Types', 'amazon-auto-links' ),
                'label'         => array(
                    'is_singular'   => __( 'Single Post', 'amazon-auto-links' ),
                    'is_home'       => __( 'Home / Front', 'amazon-auto-links' ),
                    'is_archive'    => __( 'Archive', 'amazon-auto-links' ),
                    'is_404'        => __( '404', 'amazon-auto-links' ),
                    'is_search'     => __( 'Search', 'amazon-auto-links' ),
                ),
                'description'   => __( 'This option does not take effect for static insertion.', 'amazon-auto-links' ),
            ),
            array(
                'field_id'        => $sFieldIDPrefix . 'disable_post_types',
                'type'            => 'posttype',
                'title'           => __( 'Post Types', 'amazon-auto-links' ),
                'slugs_to_remove' => array( 
                    'revision', 
                    'attachment', 
                    'nav_menu_item', 
                    'amazon_auto_links', 
                    'aal_auto_insert'
                ),
                'select_all_button'  => false,       
                'select_none_button' => false,                                                 
            ),
            array(
                'field_id'        => $sFieldIDPrefix . 'disable_taxonomy',
                'type'            => 'taxonomy',
                'title'           => __( 'Taxonomies', 'amazon-auto-links' ),
                'taxonomy_slugs'  => $this->getSiteTaxonomies(),
                'description'     => __( 'For static insertion, only Category for the default Post post type can take effect.', 'amazon-auto-links' ),
                'select_all_button'  => true,
                'select_none_button' => true,
                'save_unchecked'     => false,
            ),  
        );
    }   
   
  
}