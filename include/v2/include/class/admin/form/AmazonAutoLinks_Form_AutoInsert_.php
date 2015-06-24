<?php
/**
 * Provides the definitions of form fields for the category type unit.
 * 
 * @since            2.0.0
 * @remark            The admin page and meta box access it.
 */
abstract class AmazonAutoLinks_Form_AutoInsert_ extends AmazonAutoLinks_Form {
    
    protected $strPageSlug = 'aal_define_auto_insert';
    
    public function getSections( $strPageSlug='' ) {
        
        $strPageSlug = $strPageSlug ? $strPageSlug : $this->strPageSlug;
        return array(
            array(
                'strSectionID'        => 'autoinsert_status',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Status', 'amazon-auto-links' ),
                'strDescription'    => __( 'Switch On / Off', 'amazon-auto-links' ),
            ),                
            array(
                'strSectionID'        => 'autoinsert_area',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Where to Insert', 'amazon-auto-links' ),
                'strDescription'    => __( 'Define where auto insert should be performed.', 'amazon-auto-links' ),
            ),        
            array(
                'strSectionID'        => 'autoinsert_static_insertion',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Static Insertion', 'amazon-auto-links' ),
                'strDescription'    => __( 'Use this to insert unit outputs in the database when a post gets published. This means product links stay after plugin gets deactivated.', 'amazon-auto-links' ),
            ),                    
            array(
                'strSectionID'        => 'autoinsert_enable',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Where to Enable', 'amazon-auto-links' ),
                'strDescription'    => __( 'Only insert in the areas with certain page types, post types, taxonomies, and post IDs.', 'amazon-auto-links' ),
            ),        
            array(
                'strSectionID'        => 'autoinsert_disable',
                'strPageSlug'        => $strPageSlug,
                'strTitle'            => __( 'Where to Disable', 'amazon-auto-links' ),
                'strDescription'    => __( 'Do not insert in the areas with certain page types, post types, taxonomies, and post IDs.', 'amazon-auto-links' ),
            ),                    
        );
    
    }
    
    /**
     * Represents the structure and the default value of the auto insert options.
     * 
     * It is used for the Auto Insert custom post type post meta data.
     * 
     * @access            public
     * @remark            
     */
    public static $arrStructure_AutoInsertOptions = array(
        'status' => true,        // let toggle on and off
        'unit_ids' => null,    // will be array, e.g. array( 123, 234 )
        'built_in_areas' => array( 'the_content' => true ),
        'filter_hooks' => null,
        'position' => 'below',
        'static_areas' => array( 'wp_insert_post_data' => false ),
        'static_position' => 'below',
        'action_hooks' => null,
        'enable_allowed_area' => 0,
        'enable_post_ids' => null,
        'enable_page_types' => array( 'is_single' => true, 'is_singular' => true, 'is_home' => false, 'is_404' => false, 'is_archive' => false, 'is_search' => false ),    // the is_single key is deprecated
        'enable_post_types' => true,
        'enable_taxonomy' => true,
        'enable_denied_area' => 0,
        'diable_post_ids' => null,
        'disable_page_types' => array( 'is_single' => true, 'is_singular' => false, 'is_home' => false, 'is_404' => false, 'is_archive' => false, 'is_search' => false ), // the is_single key is deprecated
        'disable_post_types' => array(),
        'disable_taxonomy' => array(),    
    );
    
    /**
     * Returns the field array with the given section ID.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     */    
    public function getFields( $strSectionID='', $strPrefix='autoinsert_' ) {
        
        $this->arrAutoInsertOptions = ( $this->intPostID = $this->isEdit( $_GET ) )
            ? AmazonAutoLinks_Option::getUnitOptionsByPostID( $this->intPostID ) + self::$arrStructure_AutoInsertOptions
            : self::$arrStructure_AutoInsertOptions;
        $this->strMode = empty( $this->intPostID ) ? 'new' : 'edit';
        
        switch( $strSectionID ) {
            case 'autoinsert_status':
                return $this->getFieldsOfStatus( $strSectionID, $strPrefix );
            case 'autoinsert_area':
                return $this->getFieldsOfArea( $strSectionID, $strPrefix );
            case 'autoinsert_static_insertion':
                return $this->getFieldsOfStaticInsertion( $strSectionID, $strPrefix );
            case 'autoinsert_enable':
                return $this->getFieldsOfEnable( $strSectionID, $strPrefix );
            case 'autoinsert_disable':
                return $this->getFieldsOfDisable( $strSectionID, $strPrefix );
            default:
                return array();
        }

    }
        protected function isEdit( $arrGET ) {

            // post_type=amazon_auto_links&page=aal_define_auto_insert&mode=edit&post=235
            $arrGET = $arrGET + array(
                'post_type' => null,
                'page' => null,
                'mode' => null,
                'post' => null,
            );
            if ( $GLOBALS['pagenow'] != 'edit.php' ) return;
            if ( ! isset( $arrGET['post_type'], $arrGET['page'], $arrGET['mode'], $arrGET['post'] ) ) return;
            if ( $arrGET['post_type'] != AmazonAutoLinks_Commons::PostTypeSlug ) return;
            if ( $arrGET['page'] != 'aal_define_auto_insert' ) return;
            if ( $arrGET['mode'] != 'edit' ) return;
            if ( $arrGET['post'] ) 
                return $arrGET['post'];
            
        }
        
    public static function getPredefinedFilters() {            
        return array(                        
            'the_content'                => __( 'Post / Page Content', 'amazon-auto-links' ),
            'the_excerpt'                => __( 'Excerpt', 'amazon-auto-links' ),
            'comment_text'                => __( 'Comment', 'amazon-auto-links' ),
            'the_content_feed'            => __( 'Feed', 'amazon-auto-links' ),
            'the_excerpt_rss'            => __( 'Feed Excerpt', 'amazon-auto-links' ),
        );
    }
    
    public static function getPredefinedFiltersForStatic( $fDescription=true ) {
        return array(
            'wp_insert_post_data'        => __( 'Post / Page Content on Publish', 'amazon-auto-links' ) 
                . ( $fDescription ? "&nbsp;&nbsp;<span class='description'>(" . __( 'inserts product links into the database so they will be static.', 'amazon-auto-links' ) . ")</span>" : '' ),
        );        
    }
    
    protected function getFieldsOfStatus( $strSectionID, $strPrefix ) {
        
        return array(
            array(
                'strFieldID' => $strPrefix . 'status',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Toggle Status', 'amazon-auto-links' ),        
                'strType' => 'radio',
                'vLabel' => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),                    
                ),
                'vValue' => $this->arrAutoInsertOptions['status'],
                'strDescription' => __( 'Use this to temporarily disable this auto-insertion.', 'amazon-auto-links' ),
                'strBeforeField' => $this->strMode == 'edit'
                    ? "<div class='right-button' style='margin-top: 0; margin-bottom: 16px;'><a class='button button-secondary' href='" . admin_url( 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlugAutoInsert ) . "'>" . __( 'Go Back', 'amazon-auto-links' ) . "</a></div>" . $this->getSaveButton()
                    : $this->getSaveButton(),
                // These will be checked in the validation callback to determine whether the submit is for edit or new.
                'strAfterField' => "<input type='hidden' name='mode' value='{$this->strMode}' />"
                    . "<input type='hidden' name='post' value='{$this->intPostID}' />",            
            ),            
        );
        
    }
    
    protected function getFieldsOfArea( $strSectionID, $strPrefix ) {
        
        return array(        
            array(
                'strFieldID' => $strPrefix . 'unit_ids',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Select Units', 'amazon-auto-links' ),        
                'strType' => 'select',
                'vMultiple' => true,
                'vLabel' => $this->getPostsLabels(),
                'vValue' => $this->arrAutoInsertOptions['unit_ids'],    
            ),
            array(
                'strFieldID' => $strPrefix . 'built_in_areas',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Areas', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vLabel' => self::getPredefinedFilters(),
                'vDelimiter' => '<br />',
                'strDescription'        => __( 'Check where product links should appear.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['built_in_areas'],
            ),        
            array(
                'strFieldID' => $strPrefix . 'filter_hooks',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Filter Hooks', 'amazon-auto-links' ) . " <span class='description'>(" . __( 'advanced', 'amazon-auto-links' ) . ")</span>",
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 60 : 80,
                'strDescription' => sprintf( __( 'Enter the WordPress <a href="%1$s" target="_blank">filter hooks</a> with which the auto-insertion is performed, separated by commas.', 'amazon-auto-links' ) , 'http://codex.wordpress.org/Plugin_API/Filter_Reference' ) . "<br />"
                    . "e.g. <code>my_custom_filter, other_plugin_filter</code>",    
                'vValue' => $this->arrAutoInsertOptions['filter_hooks'],                    
            ),
            array(
                'strFieldID' => $strPrefix . 'position',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Positions ', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(
                    'above'    => __( 'Above', 'amazon-auto-links' ),
                    'below'    => __( 'Below', 'amazon-auto-links' ),
                    'both'    => __( 'Both', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'Determines whether the items are placed before or after (above or below) the area. This does not take effect for action hooks.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['position'],    
            ),
            array(
                'strFieldID' => $strPrefix . 'action_hooks',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Action Hooks', 'amazon-auto-links' ) . " <span class='description'>(" . __( 'advanced', 'amazon-auto-links' ) . ")</span>",
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 60 : 80,
                'strDescription' => sprintf( __( 'Enter the WordPress <a href="%1$s" target="_blank">action hooks</a> with which the auto-insertion is performed, separated by commas.', 'amazon-auto-links' ) , 'http://codex.wordpress.org/Plugin_API/Action_Reference' ) . "<br />"
                    . "e.g. <code>login_footer, comment_form_before, comment_form_after, my_custom_action, other_plugin_action</code>",
                'vValue' => $this->arrAutoInsertOptions['action_hooks'],
            ),

            
        );
                
    }
        protected function getSaveButton() {
            
            return "<div class='right-button' style='clear: right; margin-top: 0;'>" 
                    . "<div id='autoinsert_disable_autoinsert_submit'>"
                        . "<span style='display: inline-block; min-width:0px;'>"
                            . "<input id='autoinsert_disable_autoinsert_submit_0' class='button button-primary' type='submit' name='save_button' value='" . __( 'Save', 'amazon-auto-links' ) . "'>"
                        . "</span>"
                    . "</div>"
                . "</div>";
            
        }
        protected function getPostsLabels() {
            
            $arrLabels = array(  );
            $oQuery = new WP_Query(
                array(
                    'post_status' => 'publish',     // optional
                    'post_type' => AmazonAutoLinks_Commons::PostTypeSlug,// 'amazon_auto_links', //  post_type
                    'posts_per_page' => -1, // ALL posts
                )
            );            
            foreach( $oQuery->posts as $oPost ) 
                $arrLabels[ $oPost->ID ] = $oPost->post_title;
            
            return $arrLabels;
            
        }
        
    protected function getFieldsOfStaticInsertion( $strSectionID, $strPrefix ) {
    
        return array(
            array(
                'strFieldID' => $strPrefix . 'static_areas',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Areas', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vLabel' => self::getPredefinedFiltersForStatic(),
                'vDelimiter' => '<br />',
                'strDescription' => __( 'Make sure you pick appropriate post types in the below sections of Where to Enable/Disable.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['static_areas'],
            ),            
            array(
                'strFieldID' => $strPrefix . 'static_position',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Positions ', 'amazon-auto-links' ),
                'strType' => 'radio',
                'vLabel' => array(
                    'above'    => __( 'Above', 'amazon-auto-links' ),
                    'below'    => __( 'Below', 'amazon-auto-links' ),
                    'both'    => __( 'Both', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'Determines whether the items are placed before or after (above or below) the area. This does not take effect for action hooks.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['static_position'],    
            ),                
        );
    
    }

    protected function getFieldsOfEnable( $strSectionID, $strPrefix ) {
        return array(
            array(
                'strFieldID' => $strPrefix . 'enable_allowed_area',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'On / Off', 'amazon-auto-links' ),            
                'strType' => 'radio',
                'vLabel' => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'Applies the criteria set in this section.', 'amazon-auto-links' ),
                'strAfterField' => '<hr />',
                'vValue' => $this->arrAutoInsertOptions['enable_allowed_area'],
            ),
            array(
                'strFieldID' => $strPrefix . 'enable_post_ids',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Post IDs', 'amazon-auto-links' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 60 : 80,
                'strDescription' => __( 'Enter post IDs separated by commas.', 'amazon-auto-links' ) 
                    . ' ' . __( 'Leave this empty to disable this option.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['enable_post_ids'],
            ),            
            array(
                'strFieldID' => $strPrefix . 'enable_page_types',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Page Types', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vLabel' => array(
                    'is_singular'    => __( 'Single Post', 'amazon-auto-links' ),
                    'is_home'        => __( 'Home / Front', 'amazon-auto-links' ),
                    'is_archive'    => __( 'Archive', 'amazon-auto-links' ),
                    'is_404'        => __( '404', 'amazon-auto-links' ),
                    'is_search'        => __( 'Search', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'This option does not take effect for static insertion.', 'amazon-auto-links' )
                    . ' ' .  __( 'If no item is checked, this option will not take effect.', 'amazon-auto-links' ), 
                'vValue' => $this->arrAutoInsertOptions['enable_page_types'],
            ),        
            array(
                'strFieldID' => $strPrefix . 'enable_post_types',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Post Types', 'amazon-auto-links' ),
                'strType' => 'posttype',
                'vDefault' => true,
                'arrRemove' => array( 'revision', 'attachment', 'nav_menu_item', 'amazon_auto_links', 'aal_auto_insert' ),
                'strDescription' => __( 'If no item is checked, this option will not take effect.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['enable_post_types'],
            ),
            array(
                'strFieldID' => $strPrefix . 'enable_taxonomy',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Taxonomies', 'amazon-auto-links' ),
                'strType' => 'taxonomy',
                'vTaxonomySlug' => $this->getSiteTaxonomies(),
                'strDescription' => __( 'For static insertion, only Category for the default Post post type can take effect.', 'amazon-auto-links' )
                    . ' ' . __( 'The checked terms which do not belong to the post type of the currently loading page will not take effect.', 'amazon-auto-links' )
                    . ' ' . __( 'Leave all unchecked not to enable this option.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['enable_taxonomy'],
            ),
        );
    }
        protected function getSiteTaxonomies() {
            
            $arrTaxonomies = get_taxonomies( '', 'names' );
            unset( $arrTaxonomies['nav_menu'] );
            unset( $arrTaxonomies['link_category'] );
            unset( $arrTaxonomies['post_format'] );
            return $arrTaxonomies;
            
        }
    protected function getFieldsOfDisable( $strSectionID, $strPrefix ) {
        return array(
            array(
                'strFieldID' => $strPrefix . 'enable_denied_area',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'On / Off', 'amazon-auto-links' ),            
                'strType' => 'radio',
                'vLabel' => array(
                    1 => __( 'On', 'amazon-auto-links' ),
                    0 => __( 'Off', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'Applies the criteria set in this section.', 'amazon-auto-links' ),
                'strAfterField' => '<hr />',
                'vValue' => $this->arrAutoInsertOptions['enable_denied_area'],
            ),        
            array(
                'strFieldID' => $strPrefix . 'diable_post_ids',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Post IDs', 'amazon-auto-links' ),
                'strType' => 'text',
                'vSize' => version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ? 60 : 80,
                'strDescription' => __( 'Enter post IDs separated by commas.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['diable_post_ids'],
            ),
            array(
                'strFieldID' => $strPrefix . 'disable_page_types',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Page Types', 'amazon-auto-links' ),
                'strType' => 'checkbox',
                'vLabel' => array(
                    'is_singular'    => __( 'Single Post', 'amazon-auto-links' ),
                    'is_home'        => __( 'Home / Front', 'amazon-auto-links' ),
                    'is_archive'    => __( 'Archive', 'amazon-auto-links' ),
                    'is_404'        => __( '404', 'amazon-auto-links' ),
                    'is_search'        => __( 'Search', 'amazon-auto-links' ),
                ),
                'strDescription' => __( 'This option does not take effect for static insertion.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['disable_page_types'],
            ),
            array(
                'strFieldID' => $strPrefix . 'disable_post_types',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Post Types', 'amazon-auto-links' ),
                'strType' => 'posttype',
                'arrRemove' => array( 'revision', 'attachment', 'nav_menu_item', 'amazon_auto_links', 'aal_auto_insert' ),
                'vValue' => $this->arrAutoInsertOptions['disable_post_types'],
            ),
            array(
                'strFieldID' => $strPrefix . 'disable_taxonomy',
                'strSectionID' => $strSectionID,
                'strTitle' => __( 'Taxonomies', 'amazon-auto-links' ),
                'strType' => 'taxonomy',
                'vTaxonomySlug' => $this->getSiteTaxonomies(),
                'strDescription' => __( 'For static insertion, only Category for the default Post post type can take effect.', 'amazon-auto-links' ),
                'vValue' => $this->arrAutoInsertOptions['disable_taxonomy'],
            ),
            array(  // single button
                'strFieldID' => $strPrefix . 'submit',
                'strSectionID' => $strSectionID,
                'strType' => 'submit',
                'strBeforeField' => "<div style='display: inline-block;'>" . $this->oUserAds->getTextAd() . "</div>"
                    . "<div class='right-button'>",
                'strAfterField' => "</div>",
                'vLabelMinWidth' => 0,
                'vDelimiter' => '',
                'vLabel' =>  isset( $_GET['mode'] ) && $_GET['mode'] == 'edit' ? __( 'Save', 'amazon-auto-links' ) : __( 'Create', 'amazon-auto-links' ),
                'vClassAttribute' => 'button button-primary',
            ),            
            array(  // single button
                'strFieldID' => $strPrefix . 'cancel',
                'strSectionID' => $strSectionID,
                'strType' => 'submit',
                'strBeforeField' => "<div class='right-button' style='margin-top:0px;'>",
                'strAfterField' => "</div>",
                'vLabelMinWidth' => 0,
                'vDelimiter' => '',
                'fIf' => $this->strMode == 'edit',
                'vLabel' => __( 'Cancel', 'amazon-auto-links' ),
                'vLink' => admin_url( 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlugAutoInsert ),
                'vClassAttribute' => 'button button-secondary',
            ),
        );
    }
    
}