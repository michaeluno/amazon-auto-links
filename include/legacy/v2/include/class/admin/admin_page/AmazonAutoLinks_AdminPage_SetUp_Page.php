<?php
/**
 * Deals with the plugin admin pages.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.5
 * 
 */
abstract class AmazonAutoLinks_AdminPage_SetUp_Page extends AmazonAutoLinks_AdminPage_Start {

    protected function _setUpPages() {
    
        // Set the root menu.
        $this->setRootMenuPageBySlug( 'edit.php?post_type=amazon_auto_links' );
        
        // Set sub menus.
        $this->addSubMenuItems(
            array(
                'strPageTitle'        => __( 'Add Unit by Category', 'amazon-auto-links' ),
                'strPageSlug'        => 'aal_add_category_unit',
                'strScreenIcon'    => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
                // 'fPageHeadingTab' => false,
            ),        
            array(
                'strPageTitle'        => __( 'Add Unit by Tag', 'amazon-auto-links' ),
                'strPageSlug'        => 'aal_add_tag_unit',
                'strScreenIcon'    => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            ),                
            array(
                'strPageTitle'        => __( 'Add Unit by Search', 'amazon-auto-links' ),
                'strPageSlug'        => 'aal_add_search_unit',
                'strScreenIcon'        => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            ),                
            array(
                'strMenuTitle'        => __( 'Manage Auto Insert', 'amazon-auto-links' ),
                'strURL'            => admin_url( 'edit.php?post_type=aal_auto_insert' ),
            ),                                
            array(
                'strPageTitle'        => $GLOBALS['pagenow'] == 'edit.php' && isset( $_GET['mode'], $_GET['page'], $_GET['post_type'], $_GET['post'] ) && $_GET['mode'] == 'edit' && $_GET['page'] == 'aal_define_auto_insert'
                    ? __( 'Edit Auto Insert', 'amazon-auto-links' )
                    : __( 'Add Auto Insert', 'amazon-auto-links' ),
                'strPageSlug'        => 'aal_define_auto_insert',
                'strScreenIcon'        => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            ),                    
            array(
                'strPageTitle'        => __( 'Settings', 'amazon-auto-links' ),
                'strPageSlug'        => 'aal_settings',
                'strScreenIcon'        => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            ),
            // array(
                // 'strPageTitle' => __( 'Extensions', 'amazon-auto-links' ),
                // 'strPageSlug' => 'aal_extensions',
                // 'strScreenIcon'    => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            // ),            
            array(
                'strPageTitle' => __( 'Templates', 'amazon-auto-links' ),
                'strPageSlug' => 'aal_templates',
                'strScreenIcon'    => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            ),
            array(
                'strPageTitle' => __( 'About', 'amazon-auto-links' ),
                'strPageSlug' => 'aal_about',
                'strScreenIcon'    => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            ),
            array(
                'strPageTitle' => __( 'Help', 'amazon-auto-links' ),
                'strPageSlug' => 'aal_help',
                'strScreenIcon'    => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
            )                    
        );
        if ( $this->oOption->isDebugMode() ) {
            $this->addSubMenuItems(
                array(
                    'strPageTitle'        => __( 'Debug', 'amazon-auto-links' ),
                    'strPageSlug'        => 'aal_debug',
                    'strScreenIcon'    => AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ),
                    // 'fPageHeadingTab' => false,
                )
            );
        }
            
        // In-page tabs for the Add New Category Unit page.
        $this->addInPageTabs(
            array(
                'strPageSlug'    => 'aal_add_category_unit',
                'strTabSlug'    => 'set_category_unit_options',
                'strTitle'        => __( 'Select Categories', 'amazon-auto-links' ),
                'fHide'            => true,
            ),        
            array(
                'strPageSlug'    => 'aal_add_category_unit',
                'strTabSlug'    => 'select_categories',
                'strTitle'        => __( 'Select Categories', 'amazon-auto-links' ),
                'strParentTabSlug' => 'set_category_unit_options',
                'fHide'            => true,
            )
        );
        // In-page tabs for the Add Search Unit page.
        $this->addInPageTabs(
            array(
                'strPageSlug'    => 'aal_add_search_unit',
                'strTabSlug'    => 'initial_search_settings',
                'strTitle'        => __( 'Initial Options', 'amazon-auto-links' ),
                'fHide'            => true,
            ),        
            array(
                'strPageSlug'    => 'aal_add_search_unit',
                'strTabSlug'    => 'search_products',
                'strTitle'        => __( 'Product Search', 'amazon-auto-links' ),
                'strParentTabSlug' => 'initial_search_settings',
                'fHide'            => true,
            ),
            array(
                'strPageSlug'    => 'aal_add_search_unit',
                'strTabSlug'    => 'item_lookup',
                'strTitle'        => __( 'Item Lookup', 'amazon-auto-links' ),
                'strParentTabSlug' => 'initial_search_settings',
                'fHide'            => true,
            ),
            array(
                'strPageSlug'    => 'aal_add_search_unit',
                'strTabSlug'    => 'similarity_lookup',
                'strTitle'        => __( 'Similarity Lookup', 'amazon-auto-links' ),
                'strParentTabSlug' => 'initial_search_settings',
                'fHide'            => true,
            )
        );
        // The Settings page
        $this->addInPageTabs(
            array(
                'strPageSlug'    => 'aal_settings',
                'strTabSlug'    => 'authentication',
                'strTitle'        => __( 'Authentication', 'amazon-auto-links' ),
                'numOrder'        => 1,                
            ),                        
            array(
                'strPageSlug'    => 'aal_settings',
                'strTabSlug'    => 'general',
                'strTitle'        => __( 'General', 'amazon-auto-links' ),
                'numOrder'        => 2,                
            ),                
            array(
                'strPageSlug'    => 'aal_settings',
                'strTabSlug'    => 'misc',
                'strTitle'        => __( 'Misc', 'amazon-auto-links' ),
                'numOrder'        => 3,                
            ),            
            array(
                'strPageSlug'    => 'aal_settings',
                'strTabSlug'    => 'reset',
                'strTitle'        => __( 'Reset', 'amazon-auto-links' ),
                'numOrder'        => 4,                
            ),
            array(
                'strPageSlug'    => 'aal_settings',
                'strTabSlug'    => 'support',
                'strTitle'        => __( 'Support', 'amazon-auto-links' ),
                'fHide'            => true,
                // 'strParentTabSlug' => 'general',
            ),
            array(
                'strPageSlug'    => 'aal_settings',
                'strTabSlug'    => 'import_v1_options',
                'strTitle'        => __( 'Import v1 Options', 'amazon-auto-links' ),
                'fHide'            => true,
                // 'strParentTabSlug' => 'general',
            ),
            array(
                'strPageSlug'    => 'aal_settings',
                'strTabSlug'    => 'create_v3_options',
                'strTitle'        => __( 'Create v3 Options', 'amazon-auto-links' ),
                'fHide'            => true,
            )
        );
        // $this->addInPageTabs(
            // array(
                // 'strPageSlug'    => 'extensions',
                // 'strTabSlug'    => 'get_extensions',
                // 'strTitle'        => __( 'Get Extensions', 'amazon-auto-links' ),
                // 'numOrder'        => 10,                
            // )        
        // );
        $this->addInPageTabs(
            array(
                'strPageSlug'    => 'aal_templates',
                'strTabSlug'    => 'table',
                'strTitle'        => __( 'Installed Templates', 'amazon-auto-links' ),
                'numOrder'        => 1,                
            ),
            array(
                'strPageSlug'    => 'aal_templates',
                'strTabSlug'    => 'get',
                'strTitle'        => __( 'Get Templates', 'amazon-auto-links' ),
                'numOrder'        => 10,                
            )            
        );        
        $this->addInPageTabs(
            array(
                'strPageSlug'    => 'aal_about',
                'strTabSlug'    => 'features',
                'strTitle'        => __( 'Features', 'amazon-auto-links' ),            
            ),
            array(
                'strPageSlug'    => 'aal_about',
                'strTabSlug'    => 'get_pro',
                'strTitle'        => __( 'Get Pro!', 'amazon-auto-links' ),
            ),
            array(
                'strPageSlug'    => 'aal_about',
                'strTabSlug'    => 'contact',
                'strTitle'        => __( 'Contact', 'amazon-auto-links' ),            
            ),
            array(
                'strPageSlug'    => 'aal_about',
                'strTabSlug'    => 'change_log',
                'strTitle'        => __( 'Change Log', 'amazon-auto-links' ),            
            )
        );
        $this->addInPageTabs(
            array(
                'strPageSlug'    => 'aal_help',
                'strTabSlug'    => 'install',
                'strTitle'        => __( 'Installation', 'amazon-auto-links' ),
            ),
            array(
                'strPageSlug'    => 'aal_help',
                'strTabSlug'    => 'faq',
                'strTitle'        => __( 'FAQ', 'amazon-auto-links' ),            
            ), 
            array(
                'strPageSlug'    => 'aal_help',
                'strTabSlug'    => 'notes',
                'strTitle'        => __( 'Other Notes', 'amazon-auto-links' ),            
            )        
        );        
    
        /*
         * HTML elements and styling
         */
        $this->showPageHeadingTabs( false );        // disables the page heading tabs by passing false.
        $this->setInPageTabTag( 'h2' );                
        $this->setInPageTabTag( 'h3', 'aal_add_category_unit' );                
        $this->showInPageTabs( false, 'aal_add_category_unit' );
        $this->showInPageTabs( false, 'aal_add_search_unit' );
        $this->showInPageTabs( false, 'aal_define_auto_insert' );
                
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/admin.css' ) );
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/select_categories.css' ), 'aal_add_category_unit', 'select_categories' );
        // $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/aal_add_category_unit.css' ), 'aal_add_category_unit' );
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/aal_add_search_unit.css' ), 'aal_add_search_unit' );
        // $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/aal_add_tag_unit.css' ), 'aal_add_tag_unit' );
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/aal_settings.css' ), 'aal_settings' );
        // $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/aal_define_auto_insert.css' ), 'aal_define_auto_insert' );
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/aal_templates.css' ), 'aal_templates' );
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/readme.css' ), 'aal_about' );
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/readme.css' ), 'aal_help' );
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'asset/css/get_pro.css' ), 'aal_about', 'get_pro' );
        $this->enqueueStyle( AmazonAutoLinks_Commons::getPluginURL( 'template/preview/style-preview.css' ), 'aal_add_category_unit', 'select_categories' );

        $this->setDisallowedQueryKeys( array( 'aal-option-upgrade', 'bounce_url' ) );
        
    }
        
}