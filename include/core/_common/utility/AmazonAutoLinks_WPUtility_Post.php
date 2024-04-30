<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Provides utility methods that uses WordPress built-in functions.
 *
 * @since 3
 */
class AmazonAutoLinks_WPUtility_Post extends AmazonAutoLinks_WPUtility_Path {
    
    /**
     * @since  3.1.0
     * @return boolean
     */
    static public function isInPostEditingPage() {
        return in_array( $GLOBALS[ 'pagenow' ], array( 'post.php', 'post-new.php' ), true );
    }

    /**
     * Returns a form field label array listing post titles.
     *
     * @param  string $sPostTypeSlug
     * @return array
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
     * @return integer
     */
    static public function getCurrentPostID() {

        // When editing a post, usually this is available.
        if ( isset( $_GET[ 'post' ] ) ) {           // sanitization unnecessary as just checking
            return absint( $_GET[ 'post' ] );       // sanitization done
        }
    
        // It is set when the user send the form in post.php.
        if ( isset( $_POST[ 'post_ID' ] ) ) {       // sanitization unnecessary as just checking
            return absint( $_POST[ 'post_ID' ] );   // sanitization done
        }
        
        // This also shoudl be available.
        if ( isset( $GLOBALS[ 'post' ], $GLOBALS[ 'post' ]->ID ) ) {
            return absint( $GLOBALS[ 'post' ]->ID );
        }
        
        return 0;
        
    }

    /**
     * @param      string $sPostType
     * @param      string $sPermission
     * @return     false|mixed|object
     * @deprecated 5.2.2  All method and function names which return something must start with "get".
     */
    static public function countPosts( $sPostType, $sPermission='' ) {
        return self::getPostCountObject( $sPostType, $sPermission );
    }
    /**
     * Counts the posts of the specified post type.
     *
     * This is another version of wp_count_posts() without a filter.
     *
     * @param  string  $sPostType
     * @param  string  $sPermission
     * @param  boolean $bUseCache   Whether to use the cache from previous results.
     * @return false|mixed|object
     * @see    wp_count_posts()
     * @since  2.0.0
     * @since  5.2.2   Renamed from `countPosts()`. Added the `$bUseCache` parameter
     */
    static public function getPostCountObject( $sPostType, $sPermission='', $bUseCache=true ) {
        
        global $wpdb;

        $_oUser      = wp_get_current_user();
        $_sCacheKey  = 'posts-' . $sPostType;
        $_sSQLQuery  = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";
        if ( 'readable' == $sPermission && is_user_logged_in() ) {
            $_oPostType = get_post_type_object( $sPostType );
            if ( !current_user_can( $_oPostType->cap->read_private_posts ) ) {
                $_sCacheKey .= '_' . $sPermission . '_' . $_oUser->ID;
                $_sSQLQuery .= " AND (post_status != 'private' OR ( post_author = '$_oUser->ID' AND post_status = 'private' ))";
            }
        }
        $_sSQLQuery .= ' GROUP BY post_status';

        $_oCount     = wp_cache_get( $_sCacheKey, 'counts' );
        if ( ! $bUseCache || false === $_oCount ) {
            $_aResults = ( array ) $wpdb->get_results( $wpdb->prepare( $_sSQLQuery, $sPostType ), ARRAY_A );
            $_oCount   = array_fill_keys( get_post_stati(), 0 );

            foreach ( $_aResults as $_aRow ) {
                $_oCount[ $_aRow[ 'post_status' ] ] = $_aRow[ 'num_posts' ];
            }

            $_oCount = ( object ) $_oCount;
            wp_cache_set( $_sCacheKey, $_oCount, 'counts' );
        }
        return $_oCount;
        
    }

    /**
     * @remark  Be careful when the meta key does not exist, an empty string is returned instead of null, which can cause an unexpected behavior when merging a resulting array.
     * @param   integer      $iPostID
     * @param   string       $sKey
     * @param   array|string $asDefaults Default values for when the meta value does not exist.
     * @return  array|string If no key is specified, an associative array holding meta values of the specified post by post ID.
     * If a meta key is specified, it returns the value of the meta.
     * @since   3
     * @since   4.2.6        Added the $asDefaults parameter.
     * @sinee   4.5.0        Uses object caches to reduce SQL queries.
     */
    static public function getPostMeta( $iPostID, $sKey='', $asDefaults=array() ) {

        self::$___aDefaults_getPostMeta = self::getAsArray( $asDefaults );
        if ( $sKey ) {

            if ( ! empty( $_aPostMetaCached ) && isset( $_aPostMetaCached[ $sKey ] ) ) {
                return $_aPostMetaCached[ $sKey ];
            }
            $_mPostMetaSingleCached = self::getObjectCache( __METHOD__ . $iPostID . '_' . $sKey );
            if ( ! empty( $_mPostMetaSingleCached ) ) {
                return $_mPostMetaSingleCached;
            }

            self::$___aDefaults_getPostMeta = array( $sKey => $asDefaults );
            add_filter( 'default_post_metadata', array( __CLASS__, 'replyToSetMetaDefaultValue' ), 10, 5 );
            $_mValue = get_post_meta( $iPostID, $sKey, true );
            remove_filter( 'default_post_metadata', array( __CLASS__, 'replyToSetMetaDefaultValue' ), 10 );

            self::$___aDefaults_getPostMeta = array(); // 5.4.3 moved from `replyToSetMetaDefaultValue()`

            self::setObjectCache( __METHOD__ . $iPostID . '_' . $sKey, $_mValue );
            return $_mValue;
        }

        $_aPostMeta = array();        
        
        // There are cases that post id is not set, called from the constructor of a unit option class
        // only to use the format method.
        if ( ! $iPostID ) {
            self::$___aDefaults_getPostMeta = array();
            return $_aPostMeta;
        }

        $_aPostMetaCached = self::getObjectCache( __METHOD__ . $iPostID );
        if ( ! empty( $_aPostMetaCached ) ) {
            return $_aPostMetaCached;
        }

        $_aMetaKeys = get_post_custom_keys( $iPostID );
        $_aMetaKeys = empty( $_aMetaKeys )
            ? array()
            : ( array ) $_aMetaKeys;

        foreach( $_aMetaKeys  as $_sKey ) {
            add_filter( 'default_post_metadata', array( __CLASS__, 'replyToSetMetaDefaultValue' ), 10, 5 );
            $_aPostMeta[ $_sKey ] = get_post_meta( 
                $iPostID, 
                $_sKey, 
                true 
            );
            remove_filter( 'default_post_metadata', array( __CLASS__, 'replyToSetMetaDefaultValue' ), 10 );
        }
        self::$___aDefaults_getPostMeta = array();
        self::setObjectCache( __METHOD__ . $iPostID, $_aPostMeta );
        return $_aPostMeta;                
        
    }
        static private $___aDefaults_getPostMeta = array();
        /**
         * @param  mixed   $mValue
         * @param  integer $iObjectID
         * @param  string  $sMetaKey
         * @param  boolean $bSingle
         * @param  string  $sMetaType
         * @return mixed
         */
        static public function replyToSetMetaDefaultValue( $mValue, $iObjectID, $sMetaKey, $bSingle, $sMetaType ) {
            if ( isset( self::$___aDefaults_getPostMeta[ $sMetaKey ] ) ) {
                return self::$___aDefaults_getPostMeta[ $sMetaKey ];
            }
            // self::$___aDefaults_getPostMeta = array(); // @deprecated 5.4.3 Causes a bug
            return $mValue;
        }

    /**
     * Creates a post.
     *
     * @remark  another version of the `insertPost()` method below
     * as it mixes meta-data array and post columns. This method separates them.
     * @param  string  $sPostTypeSlug
     * @param  array   $aPostColumns
     * @param  array   $aPostMeta
     * @param  array   $aTaxonomy
     * @return integer The created post ID.
     */
    static public function createPost( $sPostTypeSlug, array $aPostColumns=array(), array $aPostMeta=array(), array $aTaxonomy=array() ) {
        
        $_iPostID = wp_insert_post(
            $aPostColumns
            + array(
                'comment_status'    => 'closed',
                'ping_status'       => 'closed',
                'post_author'       => get_current_user_id(),
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
     *
     * @param  array    $aUnitOptions
     * @param  string   $sPostTypeSlug
     * @param  array    $aTaxonomy
     * @param  string[] $aIgnoreFields
     * @return integer|WP_Error
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

    /**
     * @param integer $iPostID
     * @param array   $aPostData
     */
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