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
 * Provides utility methods that uses WordPerss built-in functions.
 *
 * @package     Amazon Auto Links
 * @since       3       
 */
class AmazonAutoLinks_WPUtility_Post extends AmazonAutoLinks_WPUtility_Transient {
    
    /**
     * @since       3.1.0
     * @return      boolean
     */
    static public function isInPostEditingPage() {
        return in_array( $GLOBALS[ 'pagenow' ], array( 'post.php', 'post-new.php' ) );
    }

    /**
     * Returns a form field label array listing post titles.
     * @return      array
     */
    static public function getPostsLabelsByPostType( $sPostTypeSlug ) {
        
        static $_aCache = array();
        
        if ( isset( $_aCache[ $sPostTypeSlug ] ) ) {
            return $_aCache[ $sPostTypeSlug ];
        }
        
        $_aLabels = array();
        $_oQuery  = new WP_Query(
            array(
                'post_status'    => 'publish',    
                'post_type'      => $sPostTypeSlug,
                'posts_per_page' => -1, // ALL posts
            )
        );            
        foreach( $_oQuery->posts as $_oPost ) {
            $_aLabels[ $_oPost->ID ] = $_oPost->post_title;
        }
        
        $_aCache[ $sPostTypeSlug ] = $_aLabels;
        return $_aLabels;
        
    }  

    /**
     * Attempts to find a current post iD.
     * 
     * @return      integer
     */
    static public function getCurrentPostID() {

        // When editing a post, usually this is available.
        if ( isset( $_GET[ 'post' ] ) ) {
            return $_GET[ 'post' ];
        }
    
        // It is set when the user send the form in post.php.
        if ( isset( $_POST[ 'post_ID' ] ) ) {
            return $_POST[ 'post_ID' ];
        }
        
        // This also shoudl be available.
        if ( isset( $GLOBALS[ 'post' ], $GLOBALS[ 'post' ]->ID ) ) {
            return $GLOBALS[ 'post' ]->ID;
        }
        
        return 0;
        
    }

    /**
     * Counts the posts of the specified post type.
     * 
     * This is another version of wp_count_posts() without a filter.
     * 
     * @since             2.0.0
     */
    static public function countPosts( $strPostType, $perm='' ) {
        
        global $wpdb;

        $oUser      = wp_get_current_user();
        $cache_key  = 'posts-' . $strPostType;
        $query      = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";
        if ( 'readable' == $perm && is_user_logged_in() ) {
            $post_type_object = get_post_type_object( $strPostType );
            if ( !current_user_can( $post_type_object->cap->read_private_posts ) ) {
                $cache_key .= '_' . $perm . '_' . $oUser->ID;
                $query .= " AND (post_status != 'private' OR ( post_author = '$oUser->ID' AND post_status = 'private' ))";
            }
        }
        $query .= ' GROUP BY post_status';

        $oCount = wp_cache_get( $cache_key, 'counts' );
        if ( false === $oCount ) {
            $results = (array) $wpdb->get_results( $wpdb->prepare( $query, $strPostType ), ARRAY_A );
            $oCount = array_fill_keys( get_post_stati(), 0 );

            foreach ( $results as $row ) {
                $oCount[ $row['post_status'] ] = $row['num_posts'];
            }

            $oCount = (object) $oCount;
            wp_cache_set( $cache_key, $oCount, 'counts' );
        }
    
        return $oCount;
        
    }

    /**
     * @return      array|string       If no key is specified, an associative array holding meta values of the specified post by post ID. 
     * If a meta key is specified, it returns the value of the meta.
     */
    static public function getPostMeta( $iPostID, $sKey='' ) {
        
        if ( $sKey ) {
            return get_post_meta( 
                $iPostID, 
                $sKey, 
                true 
            );       
        }

        $_aPostMeta = array();        
        
        // There are cases that post id is not set, called from the constructor of a unit option class
        // only to use the format method.
        if ( ! $iPostID ) {
            return $_aPostMeta;
        }

        $_aMetaKeys = get_post_custom_keys( $iPostID );
        $_aMetaKeys = empty( $_aMetaKeys )
            ? array()
            : ( array ) $_aMetaKeys;
        foreach( $_aMetaKeys  as $_sKey ) {
            $_aPostMeta[ $_sKey ] = get_post_meta( 
                $iPostID, 
                $_sKey, 
                true 
            );        
        }
        return $_aPostMeta;                
        
    }

    /**
     * Creates a post.
     * 
     * @remark  another version of the `insertPost()` method below 
     * as it mixes meta data array and post columns. This method separates them.
     * @return      integer     The created post ID.
     */
    static public function createPost( $sPostTypeSlug, array $aPostColumns=array(), array $aPostMeta=array(), array $aTaxonomy=array() ) {
        
        $_iPostID = wp_insert_post(
            $aPostColumns
            + array(
                'comment_status'    => 'closed',
                'ping_status'       => 'closed',
                'post_author'       =>  get_current_user_id(), // $GLOBALS[ 'user_ID' ],
                'post_title'        => '',
                'post_status'       => 'publish',
                'post_type'         => $sPostTypeSlug,
                'tax_input'         => $aTaxonomy 
            )        
        );
        
        if ( ! $_iPostID ) {
            return $_iPostID;
        }
        
        // Add meta fields.
        self::updatePostMeta( 
            $_iPostID, 
            $aPostMeta 
        );
        
        // Add terms because the 'tax_input' argument does not take effect for some reasons when multiple terms are set.
        // if ( ! empty( $aTaxonomy ) ) {
            // foreach( $aTaxonomy as $_sTaxonomySlug => $_aTerms ) {                
                // wp_set_object_terms( 
                    // $_iPostID, 
                    // $_aTerms, 
                    // $_sTaxonomySlug, 
                    // true 
                // );
            // }
        // }     
                
        return $_iPostID;        
        
    }
    
    /**
     * Creates a post of a specified custom post type with unit option meta fields.
     */
    static public function insertPost( $aUnitOptions, $sPostTypeSlug, $aTaxonomy=array(), $aIgnoreFields=array( 'unit_title' ) ) {
        
        // Create a custom post if it's a new unit.        
        $_iPostID = wp_insert_post(
            array(
                'comment_status'    => 'closed',
                'ping_status'       => 'closed',
                'post_author'       => $GLOBALS[ 'user_ID' ],
                // 'post_name'      => $slug,
                'post_title'        => isset( $aUnitOptions[ 'unit_title' ] ) 
                    ? $aUnitOptions[ 'unit_title' ] 
                    : '',
                'post_status'       => 'publish',
                'post_type'         => $sPostTypeSlug,
                    
                // 'post_content'         => null,
                // 'post_date' => date('Y-m-d H:i:s'),
                
                // support for custom taxonomies. This does not work if the user capability is not sufficient.
                'tax_input'         => $aTaxonomy 
            )
        );        
        
        // Remove the ignoring keys.
        foreach( $aIgnoreFields as $_sFieldKey ) {
            unset( $aUnitOptions[ $_sFieldKey ] );
        }
        
        // Add meta fields.
        self::updatePostMeta( $_iPostID, $aUnitOptions );
                
        return $_iPostID;
        
    }    
    public static function updatePostMeta( $iPostID, array $aPostData ) {
        foreach( $aPostData as $_sFieldID => $_mValue ) {
            update_post_meta( 
                $iPostID, 
                $_sFieldID, 
                $_mValue 
            );
        }
    }   
    
}