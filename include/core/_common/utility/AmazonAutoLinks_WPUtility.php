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
 * @since 2
 * @since 3 Changed the name from `AmazonAutoLinks_WPUtilities`.
 */
class AmazonAutoLinks_WPUtility extends AmazonAutoLinks_WPUtility_KSES {

    /**
     * Wraps the WordPress core translation functions so that third-party translation parser such as POEdit don't catch these.
     * This is used for translation items defined with the core default text-domain so there is no need to translate by plugin.
     * @since  5.1.6
     * @return string
     */
    static public function ___( $sString ) {
        return __( $sString );
    }
    /**
     * Wraps the WordPress core translation functions so that third-party translation parser such as POEdit don't catch these.
     * This is used for translation items defined with the core default text-domain so there is no need to translate by plugin.
     * @since 5.1.6
     */
    static public function __e( $sString ) {
        _e( $sString );
    }
    /**
     * Wraps the WordPress core translation functions so that third-party translation parser such as POEdit don't catch these.
     * This is used for translation items defined with the core default text-domain so there is no need to translate by plugin.
     * @since 5.1.6
     */
    static public function __x( $sString, $sContext ) {
        _x( $sString, $sContext );
    }
    /**
     * Wraps the WordPress core translation functions so that third-party translation parser such as POEdit don't catch these.
     * This is used for translation items defined with the core default text-domain so there is no need to translate by plugin.
     * @since  5.1.6
     */
    static public function _esc_html_e( $sString ) {
        esc_html_e( $sString );
    }
    
    /**
     * Simulates a blog header output.
     * @since  5.1.0
     * @since  5.1.2 Moved from `AmazonAutoLinks_Main_Event_UnitPreview`.
     * @see    ABSPATH . WPINC . '/theme-compat/header.php'
     * @remark This is an alternative for `get_header()`, which produces the warning, "theme without header.php is deprecated”
     */
    static public function printSiteHTMLHeader() {
        ?>
<link rel="profile" href="https://gmpg.org/xfn/11" />
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />

<?php if ( file_exists( get_stylesheet_directory() . '/images/kubrickbgwide.jpg' ) ) { ?>
<style type="text/css" media="screen">
#page { background: url("<?php bloginfo( 'stylesheet_directory' ); ?>/images/kubrickbgwide.jpg") repeat-y top; border: none; }
</style>
<?php } ?>
        <?php
            wp_head();
    }

    /**
     * @var    array
     * @since  4.7.5
     */
    static private $___aScriptDataBase;

    /**
     * @return array
     * @since  4.7.5
     */
    static public function getScriptDataBase() {
        if ( isset( self::$___aScriptDataBase ) ) {
            return self::$___aScriptDataBase;
        }
        self::$___aScriptDataBase = array(
            'ajaxURL'               => admin_url( 'admin-ajax.php' ),
            'spinnerURL'            => admin_url( 'images/loading.gif' ),
        );
        return self::$___aScriptDataBase;
    }

    /**
     * @param  string $sGUID
     * @param  string $sColumns The column parameter passed to the SQL query.
     * @param  string $sOutput  ARRAY_A or OBJECT
     * @see    wpdb::get_row()
     * @return array|object
     * @since  4.7.0
     */
    static public function getPostByGUID( $sGUID, $sColumns='*', $sOutput=ARRAY_A ) {
        global $wpdb;
        $_aoResult = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT {$sColumns} FROM `{$wpdb->base_prefix}posts` WHERE guid=%s",
                $sGUID
            ),
            $sOutput
        );
        if ( is_object( $_aoResult ) ) {
            return $_aoResult;
        }
        return self::getAsArray( $_aoResult );

    }

    /**
     * @return boolean
     * @since  4.6.8
     */
    static public function isRESTRequest() {
        return defined( 'REST_REQUEST' ) && REST_REQUEST;
    }

    /**
     * @return string e.g. +09:00
     * @since  4.4.0
     */
    public static function getGMTOffsetString() {
        $_fGMTOffsetHours = ( self::getGMTOffset() / 3600 ); // * 100;
        return self::___getNumberedOffsetString( $_fGMTOffsetHours );
    }

    /**
     * Determine time zone from WordPress options and return as object.
     *
     * @return integer The timezone offset in seconds.
     * @see    https://wordpress.stackexchange.com/a/283094
     * @since  4.4.0
     */
    public static function getGMTOffset() {

        $_iCache = self::getObjectCache( __METHOD__ );
        if ( isset( $_iCache ) ) {
            return $_iCache;
        }
        try {
            $_sTimeZone     = self::___getSiteTimeZone();
            $_oDateTimeZone = new DateTimeZone( $_sTimeZone );
            $_oDateTime     = new DateTime("now", $_oDateTimeZone );
        } catch ( Exception $oException ) {
            self::setObjectCache( __METHOD__, 0 );
            return 0;
        }
        $_iOffset = $_oDateTimeZone->getOffset( $_oDateTime );
        self::setObjectCache( __METHOD__, $_iOffset );
        return $_iOffset;

    }
        /**
         * @return string Timezone string compatible with the DateTimeZone objects.
         * @since  4.4.0
         */
        static private function ___getSiteTimeZone() {
            $_sTimeZone = get_option( 'timezone_string' );
            if ( ! empty( $_sTimeZone ) ) {
                return $_sTimeZone;
            }
            $_fOffset   = get_option( 'gmt_offset', 0 ); // e.g. 5.5
            return self::___getNumberedOffsetString( $_fOffset );
        }

        /**
         * @param  float  $fOffset
         * @return string
         * @since  4.4.0
         */
        static private function ___getNumberedOffsetString( $fOffset ) {
            $_iHours    = ( integer ) $fOffset;
            $_fiMinutes = abs( ( $fOffset - ( integer ) $fOffset ) * 60 );
            return sprintf( '%+03d:%02d', $_iHours, $_fiMinutes );
        }

    /**
     * Schedules a WP Cron single event.
     * @param  string  $sActionName
     * @param  array   $aArguments
     * @param  integer $iTime
     * @return boolean True if scheduled, false otherwise.
     * @since  3.5.0
     */
    static public function scheduleSingleWPCronTask( $sActionName, array $aArguments=array(), $iTime=0 ) {

        if ( wp_next_scheduled( $sActionName, $aArguments ) ) {
            return false;
        }

        // In previous WordPress versions, this function did not return true when scheduled. So checking false here.
        $_iTime = $iTime ? $iTime : time();
        return false !== wp_schedule_single_event( $_iTime, $sActionName, $aArguments );

    }

    /**
     * Returns the readable date-time string.
     * @param integer     $iTimeStamp
     * @param null|string $sDateTimeFormat
     * @param boolean     $bAdjustGMT
     * @return string
     */
    static public function getSiteReadableDate( $iTimeStamp, $sDateTimeFormat=null, $bAdjustGMT=false ) {
                
        static $_iOffsetSeconds, $_sDateFormat, $_sTimeFormat;
        $_iOffsetSeconds = $_iOffsetSeconds 
            ? $_iOffsetSeconds 
            : self::getGMTOffset();
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
     * @param       string  $sActionHookName
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
     * @param       string          $sText
     * @param       string          $sCandidateCharSet
     * @return      boolean|string  False when not found. Otherwise, the found encoding character set.
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
     * @sine   3
     * @param  integer $iPostID
     * @param  string  $sPostType
     * @param  array   $aGET
     * @return void
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
     * @param  integer $iPostID
     * @param  string  $sPostType
     * @param  array   $aGET
     * @return string
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
     * @param       boolean $bDescription
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
     * @param       string|integer|object            $isoPost            Either the post ID or the post object.
     * @return      array
     */
    public static function getPostTaxonomies( $isoPost ) {
        $_oPost = get_post( $isoPost );
        return is_object( $_oPost )
            ? ( array ) get_object_taxonomies( $_oPost, 'objects' )
            : array();
    }    
    
    /**
     * Returns the current url of admin page.
     * @return      string
     */
    public static function getCurrentAdminURL() {
        return add_query_arg( 
            self::getHTTPQueryGET(),
            admin_url( $GLOBALS[ 'pagenow' ] )
        );
    }

}