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
 * Deals with the plugin admin pages of Auto-insert.
 * 
 * @since       3
 */
final class AmazonAutoLinks_AutoInsertAdminPage extends AmazonAutoLinks_SimpleWizardAdminPage {

    /**
     * User constructor.
     */
    public function start() {
        
        if ( ! $this->oProp->bIsAdmin ) {
            return;
        }     
                
        // $this->oEncode = new AmazonAutoLinks_Encrypt;

        // For the create new unit page. Disable the default one.
        if ( $this->isUserClickedAddNewLink( AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ] ) ) {
            
            // Go to the Auto-insert creation page.
            exit(
                wp_safe_redirect(
                    add_query_arg(
                        array( 
                            'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                            'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'auto_insert' ],
                            'tab'       => 'new',
                        ), 
                        admin_url( 'edit.php' )
                    )
                )
            );
        }
        
        parent::start();
           
    }

    /**
     * Represents the structure and the default value of the auto insert options.
     * 
     * It is used for the Auto Insert custom post type post meta data.
     * 
     * @access            public
     * @remark            
     */
    public static $aStructure_AutoInsertDefaultOptions = array(
        'status'                => true,    // let toggle on and off
        'unit_ids'              => null,    // will be array, e.g. array( 123, 234 )
        'built_in_areas'        => array( 
            'the_content' => true 
        ),
        'filter_hooks'          => null,
        'position'              => 'below',
        'static_areas'          => array( 
            'wp_insert_post_data' => false 
        ),
        'static_position'       => 'below',
        'action_hooks'          => null,
        'enable_allowed_area'   => 0,
        'enable_post_ids'       => null,
        'enable_page_types'     => array( 
            // the is_single key is deprecated
            // 'is_single' => true, 
            'is_singular'   => true, 
            'is_home'       => false, 
            'is_404'        => false, 
            'is_archive'    => false, 
            'is_search'     => false 
        ),    
        'enable_post_types'     => true,
        'enable_taxonomy'       => true,
        'enable_denied_area'    => 0,
        'diable_post_ids'       => null,
        'disable_page_types'    => array( 
            // the is_single key is deprecated
            // 'is_single' => true, 
            'is_singular'   => false, 
            'is_home'       => false, 
            'is_404'        => false, 
            'is_archive'    => false, 
            'is_search'     => false 
         ), 
        'disable_post_types' => array(),
        'disable_taxonomy'   => array(),    
    );        
        
    /**
     * Sets the default option values for the setting form.
     * @callback    filter      `options_{class name}`
     * @return      array       The options array.
     */
    public function setOptions( $aOptions ) {
        
        $_oUtil        = new AmazonAutoLinks_WPUtility;
        $_iPostID      = $_oUtil->getCurrentPostID();
        $_aUnitOptions = $_iPostID 
            ? $_oUtil->getPostMeta( $_GET[ 'post' ] )
            : array();
        return $aOptions + $_aUnitOptions + self::$aStructure_AutoInsertDefaultOptions;
     
    }

    /**
     * Sets up admin pages.
     */
    public function setUp() {
        
        // Page group root.
        $this->setRootMenuPageBySlug( 
            'edit.php?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
        );
                    
        // Add pages
        new AmazonAutoLinks_AutoInsertAdminPage_AutoInsert( $this );
        
    }
    /**
     * Registers custom filed types of Admin Page Framework.
     */
    public function registerFieldTypes() {}
    
    /**
     * Page styling
     * @since       3
     * @return      void
     */
    public function doPageSettings() {
                    
        $this->setPageTitleVisibility( false ); // disable the page title of a specific page.
        $this->setInPageTabTag( 'h2' );                
   
        $this->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'asset/css/admin.css' ) );
                    
    }
        
}