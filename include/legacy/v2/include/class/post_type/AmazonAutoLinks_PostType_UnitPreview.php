<?php
/**
 * Creates Amazon Auto Links custom preview post type.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2015, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2.0
 * 
 */
class AmazonAutoLinks_PostType_UnitPreview {
    
    /**
     * 
     * @since       2.2.0
     */ 
    protected $sDefaultPreviewSlug = '';
    /**
     * @since       2.2.0
     */
    protected $sPreviewPostTypeSlug = '';
      
    
    /**
     * Sets up hooks and properties.
     * @since       2.2.0
     */
    public function __construct() {
        
        // If a custom preview post type is not set, do nothing.
        $_oOption = $GLOBALS['oAmazonAutoLinks_Option'];
        if ( ! $_oOption->isCustomPreviewPostTypeSet() ) {
            return;
        }
        
        // Properties
        $this->sDefaultPreviewSlug  = AmazonAutoLinks_Commons::PostTypeSlug;
        $this->sPreviewPostTypeSlug = $_oOption->arrOptions['aal_settings']['unit_preview']['preview_post_type_slug'];
        
        // Hooks
        /// Post Type
        add_action( 'init', array( $this, '_replyToRegisterCustomPreviewPostType' ) );
        
        /// Modify the links
        add_filter( 'post_row_actions', array( $this, '_replyToAddViewActionLink' ), 10, 2 );
        add_filter( 'post_link', array( $this, '_replyToModifyPermalinks' ), 10, 3 );                
        add_filter( "previous_post_link", array( $this, '_replyToModifyPostLink' ), 10, 4 );
        add_filter( "next_post_link", array( $this, '_replyToModifyPostLink' ), 10, 4 );    
        
        /// Modify database queries
        add_filter( 'request', array( $this, '_replyToModifyDatabaseQuery' ) );
        
    }
    
    /**
     * Modifies the action link of the post listing table.
     * 
     * @callback    filter      post_row_actions
     * @return      array       The action link definition array.
     */
    public function _replyToAddViewActionLink( $aArctions, $oPost ) {
        $_sLink = $this->_replaceWithUserSetPostTypeSlug( get_permalink( $oPost->ID ) );
        $aArctions[ 'view' ] = '<a href="' 
            . $_sLink . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $oPost->post_title ) ) . '" rel="permalink">' 
                . __( 'View' ) 
            . '</a>';
        return $aArctions;
    }
      
    /**
	 * @param string  $output The adjacent post link.
	 * @param string  $format Link anchor format.
	 * @param string  $link   Link permalink format.
	 * @param WP_Post $post   The adjacent post.
     * @callback        filter      previous_post_link
     * @callback        filter      next_post_link
	 */
    public function _replyToModifyPostLink( $sOutput, $format, $link, $oPost ) {
        
        if ( ! isset( $oPost->post_type ) ) {
            return $sOutput;
        }
        if ( $this->sDefaultPreviewSlug !== $oPost->post_type ) {
            return $sOutput;
        }
        return $this->_replaceWithUserSetPostTypeSlug( $sOutput );
    }

    /**
     * Creates a custom post type for preview pages.
     * 
     * If this is not created, the default plugin unit post type will be used.
     * And if this is created, the default one will be publicly disabled except some admin ui functionality.
     * 
     * @callback        action      init
     * @since           2.2.0
     * @return          void
     */
    public function _replyToRegisterCustomPreviewPostType() {

        register_post_type(
            $this->sPreviewPostTypeSlug,
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
                'public'                => true,
                'show_ui'               => false,
                'publicly_queryable'    => $GLOBALS['oAmazonAutoLinks_Option']->isPreviewVisible(),
            )        
        );
            
    }    
        /**
         * 
         * @since       2.2.0
         * @callback    filter      post_link
         * @param       string      $sPermalink     The post's permalink.
         * @param       WP_Post     $oPost          The post in question.
         * @param       bool        $bLeaveName     Whether to keep the post name.
         * @return      string
         */
        public function _replyToModifyPermalinks( $sPermalink, $oPost, $bLeaveName ) {
            
            if ( $this->sDefaultPreviewSlug !== $oPost->post_type ) {
                return $sPermalink;
            }
            return $this->_replaceWithUserSetPostTypeSlug( $sPermalink );
            
        }    
    
        /**
         * 
         * @since       2.2.0
         * @return      string
         */
        private function _replaceWithUserSetPostTypeSlug( $sSubject ) {
            return str_replace(
                array( 
                    '/' . $this->sDefaultPreviewSlug . '/',
                    'post_type=' . $this->sDefaultPreviewSlug,
                    $this->sDefaultPreviewSlug . '='
                ), // search
                array(
                    '/' . $this->sPreviewPostTypeSlug . '/',
                    'post_type=' . $this->sPreviewPostTypeSlug,
                    $this->sPreviewPostTypeSlug . '='
                ), // replace
                $sSubject     // subject
            );            
        }
         
    
    /**
     * 
     * @since       2.2.0
     * @callback    filter      request
     * @return      array       The database query request array.
     */
    public function _replyToModifyDatabaseQuery( $aRequest ) {

        if ( ! isset( $aRequest[ 'post_type' ], $aRequest[ 'name' ] ) ) {
            return $aRequest;
        }
        if ( $this->sPreviewPostTypeSlug !== $aRequest[ 'post_type' ] ) {
            return $aRequest;
        }
        if ( ! $GLOBALS['oAmazonAutoLinks_Option']->isPreviewVisible() ) {
            return $aRequest;
        }
        

        $aRequest[ 'post_type' ] = $this->sDefaultPreviewSlug;
        $aRequest[ $this->sDefaultPreviewSlug ] = $aRequest[ 'name' ];
        return $aRequest;
    
    }

}