<?php
/**
 * Creates Amazon Auto Links custom post type.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2015, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.0
 * 
 */

abstract class AmazonAutoLinks_PostType_ extends AmazonAutoLinks_AdminPageFramework_PostType {
    
    public function start_AmazonAutoLinks_PostType() {

        $this->setPostTypeArgs(
            array(            // argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels' => array(
                    'name'                  => AmazonAutoLinks_Commons::$strPluginName,
                    'singular_name'         => __( 'Amazon Auto Links Unit', 'amazon-auto-links' ),
                    'menu_name'             => AmazonAutoLinks_Commons::$strPluginName,    // this changes the root menu name so cannot simply put Manage Unit here
                    'add_new'               => __( 'Add New Unit by Category', 'amazon-auto-links' ),
                    'add_new_item'          => __( 'Add New Unit', 'amazon-auto-links' ),
                    'edit'                  => __( 'Edit', 'amazon-auto-links' ),
                    'edit_item'             => __( 'Edit Unit', 'amazon-auto-links' ),
                    'new_item'              => __( 'New Unit', 'amazon-auto-links' ),
                    'view'                  => __( 'View', 'amazon-auto-links' ),
                    'view_item'             => __( 'View Product Links', 'amazon-auto-links' ),
                    'search_items'          => __( 'Search Units', 'amazon-auto-links' ),
                    'not_found'             => __( 'No unit found for Amazon Auto Links', 'amazon-auto-links' ),
                    'not_found_in_trash'    => __( 'No Unit Found for Amazon Auto Links in Trash', 'amazon-auto-links' ),
                    'parent'                => 'Parent Unit'
                ),
                
                // If a custom preview post type is set, make it not public. 
                // However, other ui arguments should be enabled.
                'public'                => ! $GLOBALS['oAmazonAutoLinks_Option']->isCustomPreviewPostTypeSet(),
                'publicly_queryable'    => ! $GLOBALS['oAmazonAutoLinks_Option']->isCustomPreviewPostTypeSet()
                    && $GLOBALS['oAmazonAutoLinks_Option']->isPreviewVisible(),
                'has_archive'           => true,
                'show_ui'               => true,
                'show_in_nav_menus'     => true,
                'show_in_menu'          => true,

                'menu_position'         => 110,
                // 'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),    // 'custom-fields'
                'supports'              => array( 'title' ),
                'taxonomies'            => array( '' ),
                'menu_icon'             => AmazonAutoLinks_Commons::getPluginURL( 'asset/image/menu_icon_16x16.png' ),
                
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'can_export'            => $GLOBALS['oAmazonAutoLinks_Option']->canExport(),
                'exclude_from_search'   => ! $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['unit_preview']['searchable'],
            )        
        );
        $this->setAutoSave( false );
        $this->setAuthorTableFilter( false );
        
        $this->addTaxonomy( 
            AmazonAutoLinks_Commons::TagSlug, //'amazon_auto_links_tag', 
            array(
                'labels'                => array(
                    'name'          => __( 'Label', 'amazon-auto-links' ),
                    'add_new_item'  => __( 'Add New Label', 'amazon-auto-links' ),
                    'new_item_name' => __( 'New Label', 'amazon-auto-links' ),
                ),
                'show_ui'               => true,
                'show_tagcloud'         => false,
                'hierarchical'          => false,
                'show_admin_column'     => true,
                'show_in_nav_menus'     => false,
                'show_table_filter'     => true,        // framework specific key
                'show_in_sidebar_menus' => true,    // framework specific key
            )
        );
        
        $_sCurrentPostTypeInAdmin = isset( $GLOBALS['post_type'] ) 
            ? $GLOBALS['post_type']
            : isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
        
        // For admin
        if ( 
            is_admin() 
            && (
                ( $this->oProps->strPostType == $_sCurrentPostTypeInAdmin )
                || ( $GLOBALS['pagenow'] == 'post.php' && isset( $_GET['post'], $_GET['action'] ) && $_GET['action'] == 'edit' && get_post_type( $_GET['post'] ) == $this->oProps->strPostType )
            )
        ) {
            
            add_filter( 'enter_title_here', array( $this, 'changeTitleMetaBoxFieldLabel' ) );    // add_filter( 'gettext', array( $this, 'changeTitleMetaBoxFieldLabel' ) );
            add_action( 'edit_form_after_title', array( $this, 'addTextAfterTitle' ) );    
                
            // style    
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueueCustomStyle' ) );
        }
        
        
        add_filter( 'the_content', array( $this, '_replytToPrintPreviewProductLinks' ) );    

        // if not in admin or the post type slug is not set, the oLink object won't be set.
        if ( isset( $this->oLink ) ) {
            $this->oLink->strSettingPageLinkTitle = __( 'Units', 'amazon-auto-links' );
        }
            
    }
    
    public function enqueueCustomStyle() {
        wp_enqueue_style( 'amazon-auto-links-post-type-admin-style', AmazonAutoLinks_Commons::getPluginURL( 'asset/css/admin.css' ) );
    }
    
    
    public function changeTitleMetaBoxFieldLabel( $strText ) {
        return __( 'Set the unit name here.', 'amazon-auto-links' );        
    }
    public function addTextAfterTitle() {
        
        $oUserAds = isset( $GLOBALS['oAmazonAutoLinksUserAds'] ) ? $GLOBALS['oAmazonAutoLinksUserAds'] : new AmazonAutoLinks_UserAds;
        echo $oUserAds->getTextAd();
        
        // Text links will be inserted here.
    }
    
    
    /**
     * Prints out the fetched product links.
     * 
     * @remark            Used for the post type single page that functions as preview the result.
     * */
    public function _replytToPrintPreviewProductLinks( $sContent ) {
    
        if ( ! is_singular() ) {
            return $sContent;
        }
    
        if ( ! isset( $GLOBALS['post']->post_type ) || $GLOBALS['post']->post_type != $this->oProps->strPostType ) { 
            return $sContent; 
        }

        if ( ! $GLOBALS['oAmazonAutoLinks_Option']->isPreviewVisible() ) {
            return $sContent;
        }
        
        $_aUnitOptions       = AmazonAutoLinks_Option::getUnitOptionsByPostID( $GLOBALS['post']->ID );
        $_aUnitOptions['id'] = $GLOBALS['post']->ID;
        return $sContent 
            . AmazonAutoLinks_Units::getInstance( $_aUnitOptions )->getOutput();

    }

    /*
     * Extensible methods
     */
    public function addSettingsLinkInPluginListingPage( $aLinks ) {
        
        // http://.../wp-admin/edit.php?post_type=[...]
        array_unshift(    
            $aLinks,
            "<a href='edit.php?post_type={$this->strPostTypeSlug}'>" . __( 'Units', 'admin-page-framework' ) . "</a>"
        ); 
        return $aLinks;        
        
    }
    
    
    public function setColumnHeader( $arrColumnHeader ) {
        // Set the table header.
        return array(
            'cb'                => '<input type="checkbox" />',    // Checkbox for bulk actions. 
            'title'                => __( 'Unit Name', 'amazon-auto-links' ),        // Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
            'unit_type'            => __( 'Unit Type', 'amazon-auto-links' ),
            'template'            => __( 'Template', 'amazon-auto-links' ),
            // 'author'            => __( 'Author', 'amazon-auto-links' ),        // Post author.
            'amazon_auto_links_tag'    => __( 'Labels', 'amazon-auto-links' ),    // Tags for the post. 
            'code'                => __( 'Shortcode / PHP Code', 'amazon-auto-links' ),
            // 'date'            => __( 'Date', 'amazon-auto-links' ),     // The date and publish status of the post. 
        );        
        // return array_merge( $arrColumnHeader, $this->arrColumnHeaders );
    }
    public function setSortableColumns( $arrColumns ) {
        return array_merge( $arrColumns, $this->oProps->arrColumnSortable );        
    }    
    
    /*
     * Callback methods
     */
    
    protected $arrPostmeta = array();
    
    public function cell_amazon_auto_links_amazon_auto_links_tag( $strCell, $intPostID ) {    // cell_ + post type slug + column name
        
        // Get the genres for the post.
        $arrTerms = get_the_terms( $intPostID, AmazonAutoLinks_Commons::TagSlug );
    
        // If no tag is assigned to the post,
        if ( empty( $arrTerms ) ) { 
            return '—'; 
        }
        
        // Variables
        global $post;
        $arrOutput = array();
    
        // Loop through each term, linking to the 'edit posts' page for the specific term. 
        foreach ( $arrTerms as $oTerm ) {
            $arrOutput[] = sprintf( '<a href="%s">%s</a>',
                esc_url( add_query_arg( array( 'post_type' => $post->post_type, AmazonAutoLinks_Commons::TagSlug => $oTerm->slug ), 'edit.php' ) ),
                esc_html( sanitize_term_field( 'name', $oTerm->name, $oTerm->term_id, AmazonAutoLinks_Commons::TagSlug, 'display' ) )
            );
        }

        // Join the terms, separating them with a comma.
        return join( ', ', $arrOutput );
        
    }
    public function cell_amazon_auto_links_unit_type( $strCell, $intPostID ) {
                
        switch ( get_post_meta( $intPostID, 'unit_type', true ) ) {
            case 'similarity_lookup':
                return __( 'Search - Similarity Look-up', 'amazon-auto-links' );
            case 'item_lookup':
                return __( 'Search - Item Look-up', 'amazon-auto-links' );
             case 'search':
                return __( 'Search - Products', 'amazon-auto-links' );
            case 'tag':
                return __( 'Tag', 'amazon-auto-links' );
            case 'category':
            default:
                return __( 'Category', 'amazon-auto-links' );

        }
        
    }
    public function cell_amazon_auto_links_template( $strCell, $intPostID ) {
        
        $strTemplateID = get_post_meta( $intPostID, 'template_id', true );
        
        return $GLOBALS['oAmazonAutoLinks_Templates']->getTemplateNameByID( $strTemplateID );
        
    }    
    public function cell_amazon_auto_links_code( $strCell, $intPostID ) {
        return '<p>'
            . '<span>[amazon_auto_links id="' . $intPostID . '"]</span>' . '<br />'
            . '<span>&lt;?php AmazonAutoLinks( array( ‘id’ =&gt; ' . $intPostID . ' ) ); ?&gt;</span>'
            . '</p>';
    }
    
    // Style for this custom post type pages
    public function style_AmazonAutoLinks_PostType() {
        $strNone = 'none';
        return "#post-body-content {
                margin-bottom: 10px;
            }
            #edit-slug-box {
                display: {$strNone};
            }
            #icon-edit.icon32.icon32-posts-" . AmazonAutoLinks_Commons::PostTypeSlug . " {
                background:url('" . AmazonAutoLinks_Commons::getPluginURL( "asset/image/screen_icon_32x32.png" ) . "') no-repeat;
            }            
        ";
    }
}

