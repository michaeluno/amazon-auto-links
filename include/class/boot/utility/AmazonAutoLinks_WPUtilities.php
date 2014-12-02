<?php
/**
 *    Provides utility methods that uses WordPerss built-in functions.
 *
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl    http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 * 
 */

class AmazonAutoLinks_WPUtilities extends AmazonAutoLinks_WPUtilities_Transient {

    /**
     * Returns an array of the installed taxonomies on the site.
     * 
     */
    public static function getSiteTaxonomies() {
        
        $arrTaxonomies = get_taxonomies( '', 'names' );
        unset( $arrTaxonomies['nav_menu'] );
        unset( $arrTaxonomies['link_category'] );
        unset( $arrTaxonomies['post_format'] );
        return $arrTaxonomies;
        
    }

    /**
     * Returns an array of associated taxonomies of the given post.
     * 
     * @param            string|integer|object            $vPost            Either the post ID or the post object.
     */
    public static function getPostTaxonomies( $vPost ) {
        
        if ( is_integer( $vPost ) || is_string( $vPost ) )
            $oPost = get_post( $vPost );
        else if ( is_object( $vPost ) )
            $oPost = $vPost;
                    
        return ( array ) get_object_taxonomies( $oPost, 'objects' );

    }    
    
    /**
     * Returns the current url of admin page.
     */
    public static function getCurrentAdminURL() {
        
        return add_query_arg( $_GET, admin_url( $GLOBALS['pagenow'] ) );
        
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

        if ( ! post_type_exists( $strPostType ) )
            return new stdClass;

        $oUser = wp_get_current_user();

        $cache_key = 'posts-' . $strPostType;

        $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";
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

            foreach ( $results as $row )
                $oCount[ $row['post_status'] ] = $row['num_posts'];

            $oCount = (object) $oCount;
            wp_cache_set( $cache_key, $oCount, 'counts' );
        }
    
        return $oCount;
        
    }
    
    /**
     * Escapes the given string for the KSES filter with the criteria of allowing/disallowing tags and the protocol.
     * 
     * @remark            Attributes are not supported at this moment.
     * @param            array            $arrAllowedTags                e.g. array( 'noscript', 'style', )
     * @param            array            $arrDisallowedTags            e.g. array( 'table', 'tbody', 'thoot', 'thead', 'th', 'tr' )
     * @since            2.0.0
     */
    static public function escapeKSESFilter( $strString, $arrAllowedTags = array(), $arrDisallowedTags=array(), $arrAllowedProtocols = array() ) {

        foreach( $arrAllowedTags as $strTag )
            $arrFormatAllowedTags[ $strTag ] = array();    // activate the inline style attribute.
        $arrAllowedHTMLTags = AmazonAutoLinks_Utilities::uniteArrays( $arrFormatAllowedTags, $GLOBALS['allowedposttags'] );    // the first parameter takes over the second.
        
        foreach ( $arrDisallowedTags as $strTag )         
            if ( isset( $arrAllowedHTMLTags[ $strTag ] ) ) 
                unset( $arrAllowedHTMLTags[ $strTag ] );
        
        if ( empty( $arrAllowedProtocols ) )
            $arrAllowedProtocols = wp_allowed_protocols();            
            
        $strString = addslashes( $strString );                    // the original function call was doing this - could be redundant but haven't fully tested it
        $strString = stripslashes( $strString );                    // wp_filter_post_kses()
        $strString = wp_kses_no_null( $strString );                // wp_kses()
        $strString = wp_kses_js_entities( $strString );            // wp_kses()
        $strString = wp_kses_normalize_entities( $strString );    // wp_kses()
        $strString = wp_kses_hook( $strString, $arrAllowedHTMLTags, $arrAllowedProtocols ); // WP changed the order of these funcs and added args to wp_kses_hook
        $strString = wp_kses_split( $strString, $arrAllowedHTMLTags, $arrAllowedProtocols );        
        $strString = addslashes( $strString );                // wp_filter_post_kses()
        $strString = stripslashes( $strString );                // the original function call was doing this - could be redundant but haven't fully tested it
        return $strString;
        
    }        
        
    /**
     * Calculates the URL from the given path.
     * 
     * 
     * 
     * @static
     * @access            public
     * @return            string            The source url
     * @since            2.0.1
     * @since            2.0.3.1            Prevented "/./" to be inserted in the url.
     */
    static public function getSRCFromPath( $strFilePath ) {
                        
        $oWPStyles = new WP_Styles();    // It doesn't matter whether the file is a style or not. Just use the built-in WordPress class to calculate the SRC URL.
        $strRelativePath = AmazonAutoLinks_Utilities::getRelativePath( ABSPATH, $strFilePath );        
        $strRelativePath = preg_replace( "/^\.[\/\\\]/", '', $strRelativePath, 1 );    // removes the heading ./ or .\ 
        $sHref = trailingslashit( $oWPStyles->base_url ) . $strRelativePath;
        return esc_url( $sHref );
        
    }
    
}