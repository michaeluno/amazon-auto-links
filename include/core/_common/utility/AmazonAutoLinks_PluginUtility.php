<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Provides plugin specific utility methods that uses WordPress built-in functions.
 *
 * @since       3
 */
class AmazonAutoLinks_PluginUtility extends AmazonAutoLinks_WPUtility {

    /**
     * Sets rows to the aal_products table.
     * @since 4.6.9
     */
    static public function setProductDatabaseRows( array $aRows ) {
        $_sTableVersion = get_option( 'aal_products_version', '0' );
        if ( ! $_sTableVersion ) {
            new AmazonAutoLinks_Error( 'UPDATE_PRODUCTS_FAILURE', 'The products cache table does not seem to be installed.', array(), true );
            return;
        }
        do_action( 'aal_action_debug_log', 'UPDATE_PRODUCTS', "Updating " . count( $aRows ) . " row(s): ", array_keys( $aRows ) , current_filter(), true );
        if ( version_compare( $_sTableVersion, '1.4.0b01', '<' ) ) {
            foreach( $aRows as $_sKey => $_aRow ) {
                // {$_sASIN}|{$sLocale}|{$sCurrency}|{$sLanguage}
                $_aKey          = explode( '|', $_sKey ) + array( null, null, null, null );
                $_sASIN         = $_aKey[ 0 ];
                $_sLocale       = $_aKey[ 1 ];
                $_sCurrency     = $_aKey[ 2 ];
                $_sLanguage     = $_aKey[ 3 ];
                $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
                $_oProductTable->setRowByASINLocale( $_sASIN . '_' . strtoupper( $_sLocale ), $_aRow, $_sCurrency, $_sLanguage );
            }
            return;
        }
        $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_oProductTable->setRows( $aRows );
    }
    /**
     * @param  string $sHTML
     * @param  string $sDefaultString
     * @param  array  $aRemoveTags
     * @return string
     */
    static public function getHTMLBody( $sHTML, $sDefaultString='', array $aRemoveTags=array( 'script', 'style', 'head' ) ) {
        $_oDOM       = new AmazonAutoLinks_DOM;
        $_oDoc       = $_oDOM->loadDOMFromHTML( $sHTML );
        $_oDOM->removeTags( $_oDoc, $aRemoveTags );
        $_oXPath     = new DOMXPath( $_oDoc );
        $_noBodyNode = $_oXPath->query( "/html/body" )->item( 0 );
        return $_noBodyNode
            ? $_oDoc->saveXml( $_noBodyNode, LIBXML_NOEMPTYTAG )
            : $sDefaultString;
    }

    static private $___aTasks = array();

    /**
     * @param  string  $sActionName     The action hook name bound to the event.
     * @param  array   $aArguments      A numerically indexed array representing function parameters.
     * @param  integer $iTime           A Unix timestamp that the action should run.
     * @return boolean true if scheduled; otherwise, false.
     * @since  4.3.4
     */
    static public function scheduleTask( $sActionName, array $aArguments=array(), $iTime=0 ) {

        $iTime     = $iTime ? $iTime : time();
        if ( version_compare( get_option( 'aal_tasks_version', '0' ), '1.0.0b01', '<' ) ) {
            return self::scheduleSingleWPCronTask( $sActionName, $aArguments, $iTime );
        }
        $_sName    = md5( $sActionName . serialize( $aArguments ) );
        $_aTaskRow = array(
            'name'          => $_sName,  // (unique column)
            'action'        => $sActionName,
            'arguments'     => $aArguments,
            'creation_time' => date( 'Y-m-d H:i:s', time() ),
            'next_run_time' => date( 'Y-m-d H:i:s', $iTime ),
        );
        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_biResult   = $_oTaskTable->insertRowIgnore( $_aTaskRow );
        self::$___aTasks[ $_sName ] = func_get_args();
        if ( $_biResult && time() >= $iTime ) {
            self::scheduleSingleWPCronTask( 'aal_action_check_tasks' );
            AmazonAutoLinks_Shadow::see();  // Loads the site in the background.
        }
        return ( boolean ) $_biResult;

    }

    /**
     * @param  string  $sActionName
     * @param  array   $aArguments
     * @param  string  $sRowName     The row name of the aal_tasks table. If not specified, auto-generated name will be used.
     * @return boolean
     */
    static public function isTaskScheduled( $sActionName, array $aArguments=array(), $sRowName='' ) {
        if ( version_compare( get_option( 'aal_tasks_version', '0' ), '1.0.0b01', '<' ) ) {
            return  ( boolean ) wp_next_scheduled( $sActionName, $aArguments );
        }
        $sRowName    = strlen( $sRowName ) ? $sRowName : md5( $sActionName . serialize( $aArguments ) );
        if ( isset( self::$___aTasks[ $sRowName ] ) ) {
            return true;
        }
        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        return $_oTaskTable->doesRowExist( array( 'name' => $sRowName ) );
    }

    /**
     * @param  string  $sActionName
     * @param  array   $aArguments
     * @param  string  $sRowName     The row name of the aal_tasks table. If not specified, auto-generated name will be used.
     * @return boolean true if successfully unscheduled. Otherwise, false.
     * @since  4.3.4
     */
    static public function unscheduleTask( $sActionName, array $aArguments=array(), $sRowName='' ) {
        $sRowName    = strlen( $sRowName ) ? $sRowName : md5( $sActionName . serialize( $aArguments ) );
        $_oTaskTable = new AmazonAutoLinks_DatabaseTable_aal_tasks;
        $_oTaskTable->deleteRows( $sRowName );
        unset( self::$___aTasks[ $sRowName ] );
        return ! ( boolean ) self::isTaskScheduled( $sActionName, $aArguments, $sRowName );
    }

    /**
     * @param  WP_Error|array $aoResponse
     * @param  string $sURL
     * @return array Consists of an error code as a key and a message as the value.
     * @since  4.5.0
     */
    static public function getHTTPResponseError( $aoResponse, $sURL ) {
        $_aError = self::___getWPErrorAsArray( $aoResponse );
        if ( ! empty( $_aError ) ) {
            return $_aError;
        }
        $_aError = self::___getHTTPStatusErrorAsArray( $aoResponse );
        if ( ! empty( $_aError ) ) {
            return $_aError;
        }
        return self::___getCaptchaErrorAsArray( $aoResponse, $sURL );
    }
        /**
         * @param  WP_Error|array $aoResponse
         * @return array
         * @since  4.3.5
         * @sinee  4.5.0 Moved from `AmazonAutoLinks_Main_Event_Filter_HTTPRequestError`.
         */
        static private function ___getWPErrorAsArray( $aoResponse ) {
            if ( is_wp_error( $aoResponse ) ) {
                return array(
                    '(WP_ERROR) ' . $aoResponse->get_error_code() => $aoResponse->get_error_message(),
                );
            }
            return array();
        }
        /**
         * @param  array $aResponse
         * @return array
         * @sinee  4.5.0 Moved from `AmazonAutoLinks_Main_Event_Filter_HTTPRequestError`.
         */
        static private function ___getHTTPStatusErrorAsArray( array $aResponse ) {
            $_sCode    = self::getElement( $aResponse, array( 'response', 'code' ) );
            $_s1stChar = substr( $_sCode, 0, 1 );
            if ( in_array( $_s1stChar, array( 2, 3 ) ) ) {
                return array();
            }
            return array(
                '(HTTP_STATUS_ERROR) ' . $_sCode => self::getElement( $aResponse, array( 'response', 'message' ) )
            );
        }

        /**
         *
         * Since v4.3.4, the timing of creating captcha error WP_Error object has changed
         * and therefore, the error needs to be captured here.
         * @param  array  $aResponse
         * @param  string $sURL
         * @return array
         * @since  4.3.4
         * @sinee  4.5.0 Moved from `AmazonAutoLinks_Main_Event_Filter_HTTPRequestError`.
         */
        static private function ___getCaptchaErrorAsArray( array $aResponse, $sURL ) {
            if ( self::isBlockedByAmazonCaptcha( wp_remote_retrieve_body( $aResponse ), $sURL ) ) {
                return array(
                    'CAPTCHA' => 'Blocked by Captcha',
                );
            }
            return array();
        }

    /**
     * @param   string  string $sHTML
     * @param   string  string $sURL
     * @since   4.2.2
     * @return  boolean
     */
    static public function isBlockedByAmazonCaptcha( $sHTML, $sURL ) {

        if ( empty ( $sHTML ) ) {
            return false;
        }
        if ( ! self::isAmazonURL( $sURL ) ) {
            return false;
        }
        $_oDOM      = new AmazonAutoLinks_DOM;
        $_oDoc      = $_oDOM->loadDOMFromHTML( $sHTML );
        $_oXPath    = new DOMXPath( $_oDoc );
        $_noNode    = $_oXPath->query( './/form[@action="/errors/validateCaptcha"]' )->item( 0 );
        return null !== $_noNode;

    }

    /**
     * Detects whether a given URL is of an Amazon site.
     * @param  string  $sURL
     * @return boolean
     * @since  4.3.4
     */
    static public function isAmazonURL( $sURL ) {
        return self::hasPrefix( 'amazon.', self::getSubDomain( $sURL ) );
    }

    /**
     * @param integer|string $isProductTableSize    Size in megabytes
     * @param integer|string $isRequestTableSize    Size in megabytes
     * @since 3.8.12
     */
    static public function truncateCacheTablesBySize( $isProductTableSize, $isRequestTableSize ) {

        $_aTableSizes = array(
            'AmazonAutoLinks_DatabaseTable_aal_products'      => $isProductTableSize,
            'AmazonAutoLinks_DatabaseTable_aal_request_cache' => $isRequestTableSize,
        );
        foreach( $_aTableSizes as $_sClassName => $_isSizeMB ) {
            // An empty string is for unlimited (do not truncate).
            if ( '' === $_isSizeMB || null === $_isSizeMB ) {
                continue;
            }
            $_oTable = new $_sClassName;
            $_oTable->truncateBySize( ( integer ) $_isSizeMB );
        }

    }

    /**
     * @return      boolean
     * @since       3.8.0
     */
    static public function isPluginAdminPage() {

        if ( ! is_admin() ) {
            return false;
        }
        $_sPageNow = self::getPageNow();
        if ( in_array( $_sPageNow, AmazonAutoLinks_Registry::$aAdminPages, true ) ) {
            return true;
        }
        if ( 'edit.php' !== $_sPageNow ) {
            return false;
        }
        return in_array(
            self::getElement( $_GET, 'post_type' ), // sanitization unnecessary as just checking
            AmazonAutoLinks_Registry::$aPostTypes,
            true
        );

    }

    /**
     * @remark  Used for breadcrumbs.
     * @return  null|object
     * @since   3.6.0
     */
    static public function getCurrentQueriedObject() {
        if ( ! method_exists( $GLOBALS[ 'wp_query' ], 'get_queried_object' ) ) {
            return null;
        }
        return $GLOBALS[ 'wp_query' ]->get_queried_object();
    }

    /**
     * Finds and extracts ASINs from given text.
     * @since       3.4.0
     * @since       3.4.12      Changed it to return an empty string if no ASIN is found.
     * @return      string
     * @param       string      $sText
     * @param       string      $sDelimiter
     */
    static public function getASINsExtracted( $sText, $sDelimiter=PHP_EOL ) {
        $_aASINs = self::getASINs( $sText );
        return empty( $_aASINs )
            ? ''
            : implode( $sDelimiter, $_aASINs );
    }

    /**
     * @param  string $sText    The text to extract ASINs from.
     * @return array  An array holding found ASINs.
     * @since  4.0.0
     */
    static public function getASINs( $sText ) {
        $sText = preg_replace(
            array(
                '/[A-Z0-9]{11,}/',     // Remove strings like an ASIN but with more than 10 characters.
                '/qid\=[0-9]{10}/',    // Remove ones consisting of only numbers with heading `qid=`.
            ),
            '',
            $sText
        );
        $_biResult = preg_match_all(
            '/(?<![a-zA-Z0-9])[A-Z0-9]{10}(?=[^a-zA-Z0-9]|$)/', // needle - [A-Z0-9]{10} is the ASIN, (?=[^a-zA-Z0-9]|$) is non alphanumeric and without $, an ASIN at the end of the string
            $sText,           // subject
            $_aMatches        // match container
        );
        return $_biResult && isset( $_aMatches[ 0 ] ) && is_array( $_aMatches[ 0 ] )
            ? array_unique( $_aMatches[ 0 ] )
            : array();
    }
    
    /**
     * Removes expired items in the set plugin custom database tables.
     * @since       3.4.0
     * @return      void
     */
    static public function deleteExpiredTableItems() {
        
        $_oCacheTable   = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_oCacheTable->deleteExpired();
        $_oProductTable = new AmazonAutoLinks_DatabaseTable_aal_products;
        $_oProductTable->deleteExpired();        
        
    }

    /**

    /**
     * Returns the active auto-insert ids.
     * @sine        3.3.0
     * @return      array
     */
    static public function getActiveAutoInsertIDs() {
        
        static $_aCache;
        if ( isset( $_aCache ) ) {
            return $_aCache;
        }            
        
        $_abActiveAutoInsertIDs = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'active_auto_inserts' ] );
        if ( false !== $_abActiveAutoInsertIDs ) {
             $_aCache = self::getAsArray( $_abActiveAutoInsertIDs );
            return $_aCache;
        }
        
        // Backward compatibility - if the option is not set, query the database.
        $_aActiveAutoInsertIDs = self::getActiveAutoInsertIDsQueried();
        update_option( 
            AmazonAutoLinks_Registry::$aOptionKeys[ 'active_auto_inserts' ],
            $_aActiveAutoInsertIDs,
            true   // enable auto-load
        );           
        $_aCache = $_aActiveAutoInsertIDs;
        return $_aCache;
        
    }
    
    /**
     * Returns the active auto-insert ids.
     * 
     * @remark      Do not cache the result as this method can be called multiple times in one page-load.
     * When an auto-insert post is about to be published, the callback for the `transition_post_status` action is triggered and calls this method. However, it is too early 
     * so the plugin triggers another callback after the post is completely published. So in that case, the method gets called twice. 
     * The callback for the `transition_post_status` hook is needed for user's trashing/restoring posts.
     * @since       3.3.0
     * @return      array   An numerically indexed array holding active auto-insert IDs.
     */
    static public function getActiveAutoInsertIDsQueried() {
        $_oQuery = new WP_Query(
            array(
                'post_status'    => 'publish',     // optional
                'post_type'      => AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ], 
                'posts_per_page' => -1, // ALL posts
                'fields'         => 'ids',  // return an array of post IDs
                'meta_query'        => array(
                    array(
                        'key'       => 'status',
                        'value'     => true,
                    ),                            
                ),                    
            )
        );       
        return $_oQuery->posts;
    }    

    /**
     * Returns the HTML credit comment.
     * @since       3.1.0
     * @return      string
     */
    static public function getCommentCredit() {
        return "<!-- Rendered with " . AmazonAutoLinks_Registry::NAME . " by miunosoft -->";
    }
    
    /**
     * Returns a list of labels (unit taxonomy) associated with the given unit id.
     * @since       3.1.0
     * @return      string
     * @param       integer     $iUnitID
     */
    static public function getReadableLabelsByUnitID( $iUnitID ) {
        $_aboTerms = get_the_terms( 
            $iUnitID, 
            AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ]
        );
        if ( ! is_array( $_aboTerms ) ) {
            return '';
        }
        $_aTermLabels = array();
        foreach( $_aboTerms as $_oTerm ) {
            $_aTermLabels[] = $_oTerm->name;
        }
        return implode( ', ', $_aTermLabels );
    }
    
    /**
     * @since       3.1.0
     * @return      string       Comma-delimited readable unit labels.
     * @param       array|string $asTermIDs
     */
    static public function getReadableLabelsByLabelID( $asTermIDs ) {

        $_aTermIDs = is_array( $asTermIDs )
            ? $asTermIDs
            : self::getStringIntoArray( $asTermIDs, ',' );
    
        $_aTermLabels = array();
        foreach( $_aTermIDs as $_iTermID ) {
            $_oTerm = get_term_by( 
                'id', 
                absint( $_iTermID ), 
                AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ]
            );
            $_aTermLabels[] = $_oTerm->name;
        }
    
        return implode( ', ', $_aTermLabels );
        
    }
    
    
    /**
     * 
     * @since       3.1.0
     * @return      string       comma-delimited unit label ids.
     * @param       integer      $iUnitID
     */
    static public function getLabelIDsByUnitID( $iUnitID ) {
        
        // Get the genres for the post.
        $_abTerms = get_the_terms( 
            $iUnitID, 
            AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ] 
        );
        
        $_aTerms  = self::getAsArray( $_abTerms );

        // Iterate each term, linking to the 'edit posts' page for the specific term. 
        $_aOutput = array();
        foreach ( $_aTerms as $_oTerm ) {
            $_aOutput[] = $_oTerm->term_id;
        }

        // Join the terms, separating them with a comma.
        return implode( ',', $_aOutput );
        
    }

    /**
     * Returns an array holding unit type labels.
     * @since       3
     * @return      array
     */
    static public function getUnitTypeLabels() {
        return apply_filters( 'aal_filter_registered_unit_type_labels', array() );
    }

    /**
     * @param  string $sUnitTypeSlug
     * @since  4.4.2
     * @return string
     * @deprecated 4.6.5    Used in only one place.
     */
    static public function getUnitTypeLabel( $sUnitTypeSlug ) {
        $_aUnitTypeLabels     = self::getUnitTypeLabels();
        return self::getElement( $_aUnitTypeLabels, array( $sUnitTypeSlug ), __( 'Unknown', 'amazon-auto-links' ) );
    }

    /**
     * @param string $sDegreeType
     * @param array $aArguments
     *
     * @return  string
     * @since   3
     */
    static public function getDegree( $sDegreeType, $aArguments ) {
        $_iDegree = self::getElement(
            $aArguments,
            $sDegreeType,
            null
        );
        if ( in_array( $_iDegree, array( null, '', false ), true ) ) {
            return '';
        }
        $_sUnit = self::getElement(
            $aArguments,
            $sDegreeType . '_unit',            
            'width' === $sDegreeType
                ? '%'
                : 'px'
        );
        return $_iDegree . $_sUnit;
    }

    /**
     * 
     * @since       3
     */
    static private $_aFunctionNamesWithSlugs = array(
        // function name => page type slug
        'is_home'               => 'home',
        'is_front_page'         => 'front',    
        'is_singular'           => 'singular',
        'is_post_type_archive'  => 'post_type_archive',
        'is_tax'                => 'taxonomy',
        'is_tag'                => 'taxonomy',
        'is_category'           => 'taxonomy',
        'is_date'               => 'date',
        'is_author'             => 'author',
        'is_search'             => 'search',
        'is_404'                => '404',
//        'wp_doing_ajax'         => 'ajax',  // 3.6.0+ `wp_doing_ajax()` exists in WP 4.7+
    );
    /**
     * 
     * @since       3
     * @since       4.6.15      Added the check for wp_query existent. Otherwise, the 'doing it wrong' warning appears if the variable is not established.
     * @return      string      The type slug
     */
    static public function getCurrentPageType() {
	    if ( ! isset( $GLOBALS[ 'wp_query' ] ) ) {
	        return '';
        }
        foreach( self::$_aFunctionNamesWithSlugs as $_sFuncName => $_sTypeSlug ) {
            if ( call_user_func( $_sFuncName ) ) {
                return $_sTypeSlug;
            }
        }
        return '';
    }    

/**
     * @since       3.4.0
     * @return      array
     */
    static public function getActiveButtonLabelsForJavaScript() {

        $_aButtonIDs    = self::getActiveButtonIDs();
        $_aLabels       = array();
        $_sDefaultLabel = AmazonAutoLinks_Option::getInstance()->get( array( 'unit_default', 'button_label' ), 'DEBUG' );
        $_sDefaultLabel = $_sDefaultLabel ? $_sDefaultLabel : __( 'Buy Now', 'amazon-auto-inks' );
        foreach( $_aButtonIDs as $_iButtonID ) {
            if ( ! $_iButtonID ) {
                $_aLabels[ $_iButtonID ] = $_sDefaultLabel;
                continue;
            }
            $_sButtonLabel = self::getPostMeta( $_iButtonID, 'button_label', '' );
            $_aLabels[ $_iButtonID ] = $_sButtonLabel ? $_sButtonLabel : $_sDefaultLabel;
        }
        return $_aLabels;

    }

    /**
     * @since   4.3.0
     * @return  array
     */
    static public function getActiveButtonLabelsForFields() {

        static $_aCached = array();
        if ( ! empty( $_aCached ) ) {
            return $_aCached;
        }
        $_aButtonIDs = AmazonAutoLinks_PluginUtility::getActiveButtonIDs();
        $_aLabels    = array();
        foreach( $_aButtonIDs as $_iButtonID ) {
            if ( 0 == $_iButtonID ) {
                $_aLabels[ $_iButtonID ] = __( 'Theme button', 'amazon-auto-links' );
                continue;
            }
            $_aLabels[ $_iButtonID ] = get_the_title( $_iButtonID );
        }
        $_aCached = $_aLabels;
        return $_aCached;

    }

    /**
     * 
     * @return      array       An array holding button IDs.
     */
    static public function getActiveButtonIDs() {

        static $_aCache;
        if ( isset( $_aCache ) ) {
            return $_aCache;
        }    
    
        $_abActiveIDs = get_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'active_buttons' ] );
        if ( false !== $_abActiveIDs ) {
            $_aCache   = self::getAsArray( $_abActiveIDs );
            $_aCache[] = 0; // the normal <button> tag
            return $_aCache;
        }
        
        // Backward compatibility - if the option is not set, query the database.
        $_aActiveIDs = self::getActiveButtonIDsQueried();
        update_option( 
            AmazonAutoLinks_Registry::$aOptionKeys[ 'active_buttons' ],
            $_aActiveIDs,
            true   // enable auto-load
        );      
        $_aCache   = $_aActiveIDs;
        $_aCache[] = 0; // the normal <button> tag
        return $_aCache;
        
    }

    /**
     * Queries active buttons without caches.
     * @since       3.3.0
     * @return      array
     */
    static public function getActiveButtonIDsQueried() {

        $_oQuery = new WP_Query(
            array(
                'post_status'    => 'publish',     // optional
                'post_type'      => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ], 
                'posts_per_page' => -1, // ALL posts
                'fields'         => 'ids',  // return an array of post IDs

                // Also searching for items that the '_status' meta key does not exist for backward compatibility with v4.2.x or below which do not have this meta key.
                'meta_query'     => array(
                    'relation'  => 'OR',
                    array(
                        'key'       => '_status',
                        'value'     => true,
                    ),
                    array(
                        'key'       => '_status',
                        'value'     => '',
                        'compare'   => 'NOT EXISTS',
                    ),
                ),
            )
        );
        return $_oQuery->posts;

    }

    /**
     * Returns a button output by a given button (custom post) ID.
     *
     * @param   integer|string $isButtonID
     * @param   string $sLabel
     * @param   bool $bVisible
     * @param   bool $bOuterContainer   Whether to display the outer container. When this is false, the visible parameter does not take effect.
     * @return  string
     * @since   3
     */
    static public function getButton( $isButtonID, $sLabel='', $bVisible=true, $bOuterContainer=true ) {
        return apply_filters( 'aal_filter_button', '', $isButtonID, $sLabel, $bVisible, $bOuterContainer );
    }

    /**
     * Returns the url using the Amazon SSL image server.
     * @since       3
     * @return      string      The converted url.
     * @param       string      $sImgURL
     */
    static public function getAmazonSSLImageURL( $sImgURL ) {
        return preg_replace(
            "/^http:\/\/.+?\//", 
            "https://images-na.ssl-images-amazon.com/",
            $sImgURL
        );
    }

    /**
     * Creates an auto-insert.
     * @since       3
     * @param       integer     $iNewPostID
     */
    static public function createAutoInsert( $iNewPostID ) {
                
        // Construct a meta array.
        $_aAutoInsertOptions = array( 
            'unit_ids' => array( $iNewPostID ),
        ) + AmazonAutoLinks_AutoInsertAdminPage::$aStructure_AutoInsertDefaultOptions;
        
        // Insert a post.
        AmazonAutoLinks_WPUtility::insertPost( 
            $_aAutoInsertOptions, 
            AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ]
        );            
        
    }    
    
    /**
     * Go to the Authentication tab of the Settings page.
     * @since       3
     * @return      void
     */
    static public function goToAPIAuthenticationPage()  {
        exit( wp_safe_redirect( self::getAPIAuthenticationPageURL() ) );
    }
    
    /**
     * @param       array       $aAdditionalQuery   Used to specify a locale.
     * @return      string
     * @since       unknown
     * @since       4.5.0       Changed the tab to Associates as the Authentication tab is deprecated. Added the `$aAdditionalQuery` parameter.
     */
    static public function getAPIAuthenticationPageURL( array $aAdditionalQuery=array() ) {
        return add_query_arg(
            array( 
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                'tab'       => 'associates',
            ) + $aAdditionalQuery,
            admin_url( 'edit.php' )
        );
    }

    /**
     * @return string
     * @since  4.6.17 Moved from `AmazonAutoLinks_UnitOutput_embed::___getGuideMessageForErrors()`.
     */
    static public function getEnableHTTPProxyOptionMessage() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return '';
        }
        $_sURLAdminProxyTab = self::getProxySettingScreenURL();
        return '* ' . __( 'Message for administrator', 'amazon-auto-links' ) . ': '
            . sprintf( __( 'Consider enabling the %1$s option.', 'amazon-auto-links' ), "<a href='" . esc_url( $_sURLAdminProxyTab ) . "' target='_blank'>" . __( 'HTTP Proxies', 'amazon-auto-links' ) . "</a>" );
    }

    /**
     * @return string
     * @since  4.5.0  Moved from `AmazonAutoLinks_Unit_Category_Event_Ajax_CategorySelection`.
     */
    static public function getProxySettingScreenURL() {
        return add_query_arg(
            array(
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'tool' ],
                'tab'       => 'proxy',
            ),
            admin_url( 'edit.php' )
        );
    }

    /**
     * Returns a warning message for when PA-API keys are not set.
     * @return  string
     * @param   string  $sLocale
     * @since   4.0.0
     * @since   4.5.0   Added the `$sLocale` parameter.
     */
    static public function getAPIKeyUnsetWarning( $sLocale='' ) {
        $_aAdditionalQueries = $sLocale ? array( 'locale' => $sLocale ) : array();
        return '<span class="warning">* '
            . sprintf(
                __( '<a href="%1$s">Amazon Product Advertising API keys</a> must be set to enable this option.', 'amazon-auto-links' ),
                self::getAPIAuthenticationPageURL( $_aAdditionalQueries )
            )
            . '</span>';
    }

    /**
     * @return string[]
     * @since  5.0.0
     */
    static public function getLocaleFieldLabels() {
        $_oOption  = AmazonAutoLinks_Option::getInstance();
        $_aLabels  = array();
        $_aLocales = AmazonAutoLinks_Locales::getLocaleObjects();
        foreach( $_aLocales as $_sLocale => $_oLocale ) {
            if ( ! $_oOption->getAssociateID( $_sLocale ) ) {
                continue;
            }
            $_sCountryName  = $_oLocale->getName();
            $_aLabels[ $_sLocale ] = $_sCountryName;
        }
        return $_aLabels;
    }

    /**
     * @return string[]
     * @since  5.0.0
     */
    static public function getAdWidgetAPILocaleFieldLabels() {
        $_aAdWidgetLocales = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport( true );
        $_aLocaleLabels    = array();
        $_oOption          = AmazonAutoLinks_Option::getInstance();
        foreach( $_aAdWidgetLocales as $_oLocale ) {
            if ( ! $_oOption->hasAssociateID( $_oLocale->sSlug ) ) {
                continue;
            }
            $_aLocaleLabels[ $_oLocale->sSlug ] = $_oLocale->getName();
        }
        return $_aLocaleLabels;
    }

    /**
     * @return string[]
     * @since  4.7.4
     */
    static public function getPAAPILocaleFieldLabels() {
        $_oOption  = AmazonAutoLinks_Option::getInstance();
        $_aLabels  = array();
        $_aLocales = AmazonAutoLinks_PAAPI50___Locales::getLocaleObjects();
        foreach( $_aLocales as $_sLocale => $_oLocale ) {
            if ( ! $_oOption->isPAAPIKeySet( $_sLocale ) ) {
                continue;
            }
            $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
            $_aLabels[ $_sLocale ] = $_oLocale->getName();
        }
        return $_aLabels;
    }

    /**
     * @param  array $aLocaleSlugs
     * @return array
     * @since  4.7.0
     * @since  4.7.4 Moved from `AmazonAutoLinks_Main_AdminPage_Section_Associates`.
     */
    static public function getLocaleIcons( array $aLocaleSlugs ) {
        $_aIcons = array();
        foreach( $aLocaleSlugs as $_sLocale ) {
            $_sFlagCode = 'UK' === $_sLocale
                ? 'gb'
                : strtolower( $_sLocale );
            $_sIconPath  = AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/country_flags/' . $_sFlagCode . '.svg';
            $_sIconURL   = AmazonAutoLinks_Registry::getPluginURL( $_sIconPath, true );
            $_aIcons[ $_sLocale ] = "<img style='min-width:32px;' src='" . esc_url( $_sIconURL ) . "' />";
        }
        return $_aIcons;
    }

}