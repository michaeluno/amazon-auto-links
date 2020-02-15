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
 * @since       2
 * @since       3       Changed the name from `AmazonAutoLinks_WPUtilities`.
 */
class AmazonAutoLinks_WPUtility extends AmazonAutoLinks_WPUtility_Post {

    /**
     * Schedules a WP Cron single event.
     * @since       3.5.0
     * @return      boolean     True if scheduled, false otherwise.
     */
    static public function scheduleSingleWPCronTask( $sActionName, array $aArguments, $iTime=0 ) {

        if ( wp_next_scheduled( $sActionName, $aArguments ) ) {
            return false;
        }
        $_bCancelled = wp_schedule_single_event(
            $iTime ? $iTime : time(), // now
            $sActionName,   // an action hook name which gets executed with WP Cron.
            $aArguments     // must be enclosed in an array.
        );
        return false !== $_bCancelled;

    }

    /**
     * Returns the readable date-time string.
     */
    static public function getSiteReadableDate( $iTimeStamp, $sDateTimeFormat=null, $bAdjustGMT=false ) {
                
        static $_iOffsetSeconds, $_sDateFormat, $_sTimeFormat;
        $_iOffsetSeconds = $_iOffsetSeconds 
            ? $_iOffsetSeconds 
            : get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
        $_sDateFormat = $_sDateFormat
            ? $_sDateFormat
            : get_option( 'date_format' );
        $_sTimeFormat = $_sTimeFormat
            ? $_sTimeFormat
            : get_option( 'time_format' );    
        $sDateTimeFormat = $sDateTimeFormat
            ? $sDateTimeFormat
            : $_sDateFormat . ' ' . $_sTimeFormat;
        
        if ( ! $iTimeStamp ) {
            return 'n/a';
        }
        $iTimeStamp = $bAdjustGMT ? $iTimeStamp + $_iOffsetSeconds : $iTimeStamp;
        return date_i18n( $sDateTimeFormat, $iTimeStamp );
            
    }       
    
    /**
     * Finds scheduled cron tasks by the given action name.
     *  
     * @since       3
     * @return      array
     */
    static public function getScheduledCronTasksByActionName( $sActionHookName ) {
        
        $_aTheTasks = array();        
        $_aTasks    = ( array ) _get_cron_array();
        foreach ( $_aTasks as $_iTimeStamp => $_aScheduledActionHooks ) {            
            foreach ( ( array ) $_aScheduledActionHooks as $_sScheduledActionHookName => $_aArgs ) {
                if ( ! in_array( $_sScheduledActionHookName, array( $sActionHookName ) ) ) {
                    continue;
                }
                $_aTheTasks[ $_iTimeStamp ][ $_sScheduledActionHookName ] = $_aArgs;
            }            
        }
        return $_aTheTasks;
                
    }    
    
    /**
     * Stores whether the server installs the mbstring extension or not.
     * @since       3
     */
    static protected $_bMBStringInstalled;
    
    /**
     * Converts a given string into a specified character set.
     * @since       3
     * @return      string      The converted string.
     * @see         http://php.net/manual/en/mbstring.supported-encodings.php
     * @param       string          $sText                      The subject text string.
     * @param       string          $sCharSetTo                 The character set to convert to.
     * @param       string|boolean  $bsCharSetFrom              The character set to convert from. If a character set is not specified, it will be auto-detected.
     * @param       boolean         $bConvertToHTMLEntities     Whether or not the string should be converted to HTML entities.
     */
    static public function convertCharacterEncoding( $sText, $sCharSetTo='', $bsCharSetFrom=true, $bConvertToHTMLEntities=false ) {
        
        if ( ! function_exists( 'mb_detect_encoding' ) ) {
            return $sText;
        }
        if ( ! is_string( $sText ) ) {
            return $sText;
        }
        
        $sCharSetTo = $sCharSetTo
            ? $sCharSetTo
            : get_bloginfo( 'charset' );
        
        $_bsDetectedEncoding = $bsCharSetFrom && is_string( $bsCharSetFrom )
            ? $bsCharSetFrom
            : self::getDetectedCharacterSet(
                $sText,
                $bsCharSetFrom              
            );
        $sText = false !== $_bsDetectedEncoding
            ? mb_convert_encoding( 
                $sText, 
                $sCharSetTo, // encode to
                $_bsDetectedEncoding // from
            )
            : mb_convert_encoding( 
                $sText, 
                $sCharSetTo // encode to      
                // auto-detect
            );
        
        if ( $bConvertToHTMLEntities ) {            
            $sText  = mb_convert_encoding( 
                $sText, 
                'HTML-ENTITIES', // to
                $sCharSetTo  // from
            );
        }
        
        return $sText;
        
    }
    /**
     * 
     * @return      boolean|string      False when not found. Otherwise, the found encoding character set.
     */
    static public function getDetectedCharacterSet( $sText, $sCandidateCharSet='' ) {
        
        $_aEncodingDetectOrder = array(
            get_bloginfo( 'charset' ),
            "auto",
        );
        if ( is_string( $sCandidateCharSet ) && $sCandidateCharSet ) {
            array_unshift( $_aEncodingDetectOrder, $sCandidateCharSet );
        }        

        // Returns false or the found encoding character set
        return mb_detect_encoding( 
            $sText, // subject string
            $_aEncodingDetectOrder, // candidates
            true // strict detection - true/false
        );
        
    }
    /**
     * Redirect the user to a post definition edit page.
     * @sine        3
     * @return      void
     */
    static public function goToPostDefinitionPage( $iPostID, $sPostType, array $aGET=array() ) {
        exit( 
            wp_redirect( 
                self::getPostDefinitionEditPageURL( 
                    $iPostID, 
                    $sPostType, 
                    $aGET 
                )
            ) 
        );        
    }
    
    /**
     * Returns a url of a post definition edit page.
     * @return      string
     */
    static public function getPostDefinitionEditPageURL( $iPostID, $sPostType, array $aGET=array() ) {
         // e.g. http://.../wp-admin/post.php?post=196&action=edit&post_type=amazon_auto_links
        return add_query_arg( 
            array( 
                'action'    => 'edit',
                'post'      => $iPostID,
            ) + $aGET, 
            admin_url( 'post.php' ) 
        );
    }

    /**
     * Used by auto-insert form field definitions.
     * @return      array
     */
    static public function getPredefinedFilters() {            
        return array(                        
            'the_content'       => __( 'Post / Page Content', 'amazon-auto-links' ),
            'the_excerpt'       => __( 'Excerpt', 'amazon-auto-links' ),
            'comment_text'      => __( 'Comment', 'amazon-auto-links' ),
            'the_content_feed'  => __( 'Feed', 'amazon-auto-links' ),
            'the_excerpt_rss'   => __( 'Feed Excerpt', 'amazon-auto-links' ),
        );
    }
    /**
     * Used by auto-insert form field definitions.
     * @return      array
     */
    static public function getPredefinedFiltersForStatic( $bDescription=true ) {
        return array(
            'wp_insert_post_data' => __( 'Post / Page Content on Publish', 'amazon-auto-links' ) 
                . ( 
                    $bDescription 
                        ? "&nbsp;&nbsp;<span class='description'>(" . __( 'inserts product links into the database so they will be static.', 'amazon-auto-links' ) . ")</span>" 
                        : '' 
                ),
        );        
    }
    
    
    /**
     * Checks multiple file existence.
     * 
     * @return      boolean
     */
    static public function doFilesExist( $asFilePaths ) {        
        foreach( self::getAsArray( $asFilePaths ) as $_sFilePath ) {
            if ( ! file_exists( $_sFilePath ) ) {
                return false;
            }
        }                
        return true;
    }


    /**
     * Returns an array of the installed taxonomies on the site.
     * 
     */
    public static function getSiteTaxonomies() {
        
        $_aTaxonomies = get_taxonomies( '', 'names' );
        unset( 
            $_aTaxonomies[ 'nav_menu' ],
            $_aTaxonomies[ 'link_category' ],
            $_aTaxonomies[ 'post_format' ]
        );
        return $_aTaxonomies;
        
    }

    /**
     * Returns an array of associated taxonomies of the given post.
     * 
     * @param            string|integer|object            $isoPost            Either the post ID or the post object.
     */
    public static function getPostTaxonomies( $isoPost ) {
        
        if ( is_integer( $isoPost ) || is_string( $isoPost ) ) {
            $_oPost = get_post( $isoPost );
        } else if ( is_object( $isoPost ) ) {
            $_oPost = $isoPost;
        }
        return ( array ) get_object_taxonomies( 
            $_oPost, 
            'objects'
        );

    }    
    
    /**
     * Returns the current url of admin page.
     * @return      string
     */
    public static function getCurrentAdminURL() {
        return add_query_arg( 
            $_GET, 
            admin_url( $GLOBALS['pagenow'] ) 
        );
    }
        
    /**
     * Escapes the given string for the KSES filter with the criteria of allowing/disallowing tags and the protocol.
     * 
     * @remark      Attributes are not supported at this moment.
     * @param       array       $aAllowedTags               e.g. array( 'noscript', 'style', )
     * @param       array       $aDisallowedTags            e.g. array( 'table', 'tbody', 'thoot', 'thead', 'th', 'tr' )
     * @param       array       $aAllowedAttributes         e.g. array( 'rel', 'itemtype', 'style' )
     * @since       2.0.0
     * @since       3.1.0       Added the $aAllowedAttributes parameter.
     */
    static public function escapeKSESFilter( $sString, $aAllowedTags=array(), $aDisallowedTags=array(), $aAllowedProtocols=array(), $aAllowedAttributes=array() ) {

        foreach( $aAllowedTags as $sTag ) {
            $aFormatAllowedTags[ $sTag ] = array();    // activate the inline style attribute.
        }
        $aAllowedHTMLTags = AmazonAutoLinks_Utility::uniteArrays( $aFormatAllowedTags, $GLOBALS['allowedposttags'] );    // the first parameter takes over the second.
        
        foreach ( $aDisallowedTags as $sTag ) {
            if ( isset( $aAllowedHTMLTags[ $sTag ] ) ) {
                unset( $aAllowedHTMLTags[ $sTag ] );
            }
        }
        
        // Set allowed attributes.
        $_aFormattedAllowedAttributes = array_fill_keys( $aAllowedAttributes, 1 );
        foreach( $aAllowedHTMLTags as $_sTagName => $_aAttributes ) {
            $aAllowedHTMLTags[ $_sTagName ] = $_aAttributes + $_aFormattedAllowedAttributes;
        }
        
        if ( empty( $aAllowedProtocols ) ) {
            $aAllowedProtocols = wp_allowed_protocols();            
        }
            
        $sString = addslashes( $sString );                    // the original function call was doing this - could be redundant but haven't fully tested it
        $sString = stripslashes( $sString );                    // wp_filter_post_kses()
        $sString = wp_kses_no_null( $sString );                // wp_kses()
        $sString = wp_kses_normalize_entities( $sString );    // wp_kses()
        $sString = wp_kses_hook( $sString, $aAllowedHTMLTags, $aAllowedProtocols ); // WP changed the order of these funcs and added args to wp_kses_hook
        $sString = wp_kses_split( $sString, $aAllowedHTMLTags, $aAllowedProtocols );        
        $sString = addslashes( $sString );                // wp_filter_post_kses()
        $sString = stripslashes( $sString );                // the original function call was doing this - could be redundant but haven't fully tested it
        return $sString;
        
    }        

}