<?php 
/**
 * Admin Page Framework
 * 
 * Provides plugin and theme developers with simpler means of creating option pages, custom post types, ant meta boxes. 
 * The framework uses the built-in WordPress Settings API so it respects the WordPress standard form layout design.
 * 
 * @author				Michael Uno <michael@michaeluno.jp>
 * @copyright			Michael Uno
 * @license				GPLv2 or later
 * @see					http://wordpress.org/plugins/admin-page-framework/
 * @see					https://github.com/michaeluno/admin-page-framework
 * @link				http://en.michaeluno.jp/admin-page-framework
 * @package				Admin Page Framework
 * @remarks				To use the framework, 1. Extend the class 2. Override the setUp() method. 3. Use the hook functions.
 * @remarks				Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
 * @remarks				The documentation employs the <a href="http://en.wikipedia.org/wiki/PHPDoc">PHPDOc(DocBlock)</a> syntax.
 * @version				2.1.7.2
 */
/*
	Library Name: Admin Page Framework
	Library URI: http://wordpress.org/extend/plugins/admin-page-framework/
	Author:  Michael Uno
	Author URI: http://michaeluno.jp
	Version: 2.1.7.2
	Requirements: WordPress 3.3 or above, PHP 5.2.4 or above.
	Description: Provides simpler means of building administration pages for plugin and theme developers.
*/

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_WPUtilities' ) ) :
/**
 * Provides utility methods which use WordPress functions.
 *
 * @abstract
 * @since			2.0.0
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
abstract class AmazonAutoLinks_AdminPageFramework_WPUtilities {

	/**
	 * Triggers the do_action() function with the given action names and the arguments.
	 * 
	 * This is useful to perform do_action() on multiple action hooks with the same set of arguments.
	 * For example, if there are the following action hooks, <em>action_name</em>, <em>action_name1</em>, and <em>action_name2</em>, and to perform these, normally it takes the following lines.
	 * <code>do_action( 'action_name1', $var1, $var2 );
	 * do_action( 'action_name2', $var1, $var2 );
	 * do_action( 'action_name3', $var1, $var2 );</code>
	 * 
	 * This method saves these line this way:
	 * <code>$this->doActions( array( 'action_name1', 'action_name2', 'action_name3' ), $var1, $var2 );</code>
	 * 
	 * <h4>Example</h4>
	 * <code>$this->doActions( array( 'action_name1' ), $var1, $var2, $var3 );</code> 
	 * 
	 * @since			2.0.0
	 * @access			public
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to four.
	 * @param			array			$arrActionHooks			a numerically indexed array consisting of action hook names to execute.
	 * @param			mixed			$vArgs1					an argument to pass to the action callbacks.
	 * @param			mixed			$vArgs2					another argument to pass to the action callbacks.
	 * @param			mixed			$_and_more				add as many arguments as necessary to the next parameters.
	 * @return			void			does not return a value.
	 */		
	public function doActions( $arrActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
		
		$arrArgs = func_get_args();		
		$arrActionHooks = $arrArgs[ 0 ];
		foreach( ( array ) $arrActionHooks as $strActionHook  ) {
			$arrArgs[ 0 ] = $strActionHook;
			call_user_func_array( 'do_action' , $arrArgs );
		}

	}
	// protected function doAction() {		// Parameters: $strActionHook, $vArgs...
		
		// $arrArgs = func_get_args();	
		// call_user_func_array( 'do_action' , $arrArgs );
		
	// }
	
	/**
	 * Adds the method of the given action hook name(s) to the given action hook(s) with arguments.
	 * 
	 * In other words, this enables to register methods to the custom hooks with the same name and triggers the callbacks (not limited to the registered ones) assigned to the hooks. 
	 * Of course, the registered methods will be triggered right away. Thus, the magic overloading __call() should catch them and redirect the call to the appropriate methods.
	 * This enables, at the same time, publicly the added custom action hooks; therefore, third-party scripts can use the action hooks.
	 * 
	 * This is the reason the object instance must be passed to the first parameter. Regular functions as the callback are not supported for this method.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->oUtil->addAndDoActions( $this, array( 'my_action1', 'my_action2', 'my_action3' ), 'argument_a', 'argument_b' );</code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			Accepts variadic parameters.
	 * @param			object			$oCallerObject			the object that holds the callback method that matches the action hook name.
	 * @param			array			$arrActionHooks			a numerically index array consisting of action hook names that serve as the callback method names. 
	 * @param			mixed			$vArgs1					the argument to pass to the hook callback functions.
	 * @param			mixed			$vArgs2					another argument to pass to the hook callback functions.
	 * @param			mixed			$_and_more				add as many arguments as necessary to the next parameters.
	 * @return			void
	 */ 
	public function addAndDoActions( $oCallerObject, $arrActionHooks, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
	
		$arrArgs = func_get_args();	
		$oCallerObject = $arrArgs[ 0 ];
		$arrActionHooks = $arrArgs[ 1 ];
		foreach( ( array ) $arrActionHooks as $strActionHook ) {
			$arrArgs[ 1 ] = $strActionHook;
			call_user_func_array( array( $this, 'addAndDoAction' ) , $arrArgs );			
		}
		
	}
	
	/**
	 * Adds the methods of the given action hook name to the given action hook with arguments.
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			Accepts variadic parameters.
	 * @return			void
	 */ 
	public function addAndDoAction( $oCallerObject, $strActionHook, $vArgs1=null, $vArgs2=null, $_and_more=null ) {
		
		$intArgs = func_num_args();
		$arrArgs = func_get_args();
		$oCallerObject = $arrArgs[ 0 ];
		$strActionHook = $arrArgs[ 1 ];
		add_action( $strActionHook, array( $oCallerObject, $strActionHook ), 10, $intArgs - 2 );
		unset( $arrArgs[ 0 ] );	// remove the first element, the caller object
		call_user_func_array( 'do_action' , $arrArgs );
		
	}
	public function addAndApplyFilters() {	// Parameters: $oCallerObject, $arrFilters, $vInput, $vArgs...
			
		$arrArgs = func_get_args();	
		$oCallerObject = $arrArgs[ 0 ];
		$arrFilters = $arrArgs[ 1 ];
		$vInput = $arrArgs[ 2 ];

		foreach( ( array ) $arrFilters as $strFilter ) {
			$arrArgs[ 1 ] = $strFilter;
			$arrArgs[ 2 ] = $vInput;
			$vInput = call_user_func_array( array( $this, 'addAndApplyFilter' ) , $arrArgs );						
		}
		return $vInput;
		
	}
	public function addAndApplyFilter() {	// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...

		$intArgs = func_num_args();
		$arrArgs = func_get_args();
		$oCallerObject = $arrArgs[ 0 ];
		$strFilter = $arrArgs[ 1 ];
		add_filter( $strFilter, array( $oCallerObject, $strFilter ), 10, $intArgs - 2 );	// this enables to trigger the method named $strFilter and the magic method __call() will be called
		unset( $arrArgs[ 0 ] );	// remove the first element, the caller object	// array_shift( $arrArgs );							
		return call_user_func_array( 'apply_filters', $arrArgs );	// $arrArgs: $vInput, $vArgs...
		
	}		
	
	/**
	 * Provides an array consisting of filters for the addAndApplyFileters() method.
	 * 
	 * The order is, page + tab -> page -> class, by default but it can be reversed with the <var>$fReverse</var> parameter value.
	 * 
	 * @since			2.0.0
	 * @access			public
	 * @return				array			Returns an array consisting of the filters.
	 */ 
	public function getFilterArrayByPrefix( $strPrefix, $strClassName, $strPageSlug, $strTabSlug, $fReverse=false ) {
				
		$arrFilters = array();
		if ( $strTabSlug && $strPageSlug )
			$arrFilters[] = "{$strPrefix}{$strPageSlug}_{$strTabSlug}";
		if ( $strPageSlug )	
			$arrFilters[] = "{$strPrefix}{$strPageSlug}";			
		if ( $strClassName )
			$arrFilters[] = "{$strPrefix}{$strClassName}";
		
		return $fReverse ? array_reverse( $arrFilters ) : $arrFilters;	
		
	}
	
	/**
	 * Redirects to the given URL and exits. Saves one extra line, exit;.
	 * 
	 * @since			2.0.0
	 */ 
	public function goRedirect( $strURL ) {
		
		if ( ! function_exists('wp_redirect') ) include_once( ABSPATH . WPINC . '/pluggable.php' );
		die( wp_redirect( $strURL ) );
		
	}
	
	/**
	 * Returns an array of plugin data from the given path.		
	 * 
	 * An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
	 * 
	 * @since			2.0.0
	 */ 
	protected function getScriptData( $strPath, $strType='plugin' )	{
	
		$arrData = get_file_data( 
			$strPath, 
			array(
				'strName' => 'Name',
				'strURI' => 'URI',
				'strScriptName' => 'Script Name',
				'strLibraryName' => 'Library Name',
				'strLibraryURI' => 'Library URI',
				'strPluginName' => 'Plugin Name',
				'strPluginURI' => 'Plugin URI',
				'strThemeName' => 'Theme Name',
				'strThemeURI' => 'Theme URI',
				'strVersion' => 'Version',
				'strDescription' => 'Description',
				'strAuthor' => 'Author',
				'strAuthorURI' => 'Author URI',
				'strTextDomain' => 'Text Domain',
				'strDomainPath' => 'Domain Path',
				'strNetwork' => 'Network',
				// Site Wide Only is deprecated in favour of Network.
				'_sitewide' => 'Site Wide Only',
			),
			in_array( $strType, array( 'plugin', 'theme' ) ) ? $strType : 'plugin' 
		);			

		switch ( trim( $strType ) ) {
			case 'theme':	
				$arrData['strName'] = $arrData['strThemeName'];
				$arrData['strURI'] = $arrData['strThemeURI'];
				break;
			case 'library':	
				$arrData['strName'] = $arrData['strLibraryName'];
				$arrData['strURI'] = $arrData['strLibraryURI'];
				break;
			case 'script':	
				$arrData['strName'] = $arrData['strScriptName'];
				break;		
			case 'plugin':	
				$arrData['strName'] = $arrData['strPluginName'];
				$arrData['strURI'] = $arrData['strPluginURI'];
				break;
			default:	
				break;				
		}		

		return $arrData;
		
	}			
	
	/**
	 * Retrieves the current URL in the admin page.
	 * 
	 * @since			2.1.1
	 */
	public function getCurrentAdminURL() {
		
		$strRequestURI = $GLOBALS['is_IIS'] ? $_SERVER['PATH_INFO'] : $_SERVER["REQUEST_URI"];
		$strPageURL = ( @$_SERVER["HTTPS"] == "on" ) ? "https://" : "http://";
		
		if ( $_SERVER["SERVER_PORT"] != "80" ) 
			$strPageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $strRequestURI;
		else 
			$strPageURL .= $_SERVER["SERVER_NAME"] . $strRequestURI;
		
		return $strPageURL;
		
	}
	
	/**
	 * Returns a url with modified query stings.
	 * 
	 * Identical to the getQueryURL() method except that if the third parameter is omitted, it will use the currently browsed admin url.
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @param			array			$arrAddingQueries			The appending query key value pairs e.g. array( 'page' => 'my_page_slug', 'tab' => 'my_tab_slug' )
	 * @param			array			$arrRemovingQueryKeys		( optional ) The removing query keys. e.g. array( 'settings-updated', 'my-custom-admin-notice' )
	 * @param			string			$strSubjectURL				( optional ) The subject url to modify
	 * @return			string			The modified url.
	 */
	public function getQueryAdminURL( $arrAddingQueries, $arrRemovingQueryKeys=array(), $strSubjectURL='' ) {
		
		$strSubjectURL = $strSubjectURL ? $strSubjectURL : add_query_arg( $_GET, admin_url( $GLOBALS['pagenow'] ) );
		return $this->getQueryURL( $arrAddingQueries, $arrRemovingQueryKeys, $strSubjectURL );
		
	}
	/**
	 * Returns a url with modified query stings.
	 * 
	 * @since			2.1.2
	 * @param			array			$arrAddingQueries			The appending query key value pairs
	 * @param			array			$arrRemovingQueryKeys			The removing query key value pairs
	 * @param			string			$strSubjectURL				The subject url to modify
	 * @return			string			The modified url.
	 */
	public function getQueryURL( $arrAddingQueries, $arrRemovingQueryKeys, $strSubjectURL ) {
		
		// Remove Queries
		$strSubjectURL = empty( $arrRemovingQueryKeys ) 
			? $strSubjectURL 
			: remove_query_arg( ( array ) $arrRemovingQueryKeys, $strSubjectURL );
			
		// Add Queries
		$strSubjectURL = add_query_arg( $arrAddingQueries, $strSubjectURL );
		
		return $strSubjectURL;
		
	}	

	/**
	 * Calculates the URL from the given path.
	 * 
	 * @since			2.1.5
	 * @static
	 * @access			public
	 * @return			string			The source url
	 */
	static public function getSRCFromPath( $strFilePath ) {
				
		// It doesn't matter whether the file is a style or not. Just use the built-in WordPress class to calculate the SRC URL.
		$oWPStyles = new WP_Styles();	
		$strRelativePath = '/' . AmazonAutoLinks_AdminPageFramework_Utilities::getRelativePath( ABSPATH, $strFilePath );
		$strHref = $oWPStyles->_css_href( $strRelativePath, '', '' );
		unset( $oWPStyles );	// for PHP 5.2.x or below
		return $strHref;
		
	}	

	/**
	 * Resolves the given src.
	 * 
	 * Checks if the given string is a url, a relative path, or an absolute path and returns the url if it's not a relative path.
	 * 
	 * @since			2.1.5
	 * @since			2.1.6			Moved from the AmazonAutoLinks_AdminPageFramework_HeadTag_Base class. Added the $fReturnNullIfNotExist parameter.
	 */
	static public function resolveSRC( $strSRC, $fReturnNullIfNotExist=false ) {	
		
		if ( ! $strSRC )	
			return $fReturnNullIfNotExist ? null : $strSRC;	
		
		// It is a url
		if ( filter_var( $strSRC, FILTER_VALIDATE_URL ) )
			return $strSRC;

		// If the file exists, it means it is an absolute path. If so, calculate the URL from the path.
		if ( file_exists( realpath( $strSRC ) ) )
			return self::getSRCFromPath( $strSRC );
		
		if ( $fReturnNullIfNotExist )
			return null;
		
		// Otherwise, let's assume the string is a relative path 'to the WordPress installed absolute path'.
		return $strSRC;
		
	}	
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Utilities' ) ) :
/**
 * Provides utility methods which do not use WordPress functions.
 *
 * @since			2.0.0
 * @extends			AmazonAutoLinks_AdminPageFramework_WPUtilities
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AmazonAutoLinks_AdminPageFramework_Utilities extends AmazonAutoLinks_AdminPageFramework_WPUtilities {
	
	/**
	 * Converts non-alphabetic characters to underscore.
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			it must be public 
	 * @return			string			The sanitized string.
	 */ 
	public static function sanitizeSlug( $strSlug ) {
		return preg_replace( '/[^a-zA-Z0-9_\x7f-\xff]/', '_', trim( $strSlug ) );
	}	
	
	/**
	 * Converts non-alphabetic characters to underscore except hyphen(dash).
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @remark			it must be public 
	 * @return			string			The sanitized string.
	 */ 
	public static function sanitizeString( $strString ) {
		return preg_replace( '/[^a-zA-Z0-9_\x7f-\xff\-]/', '_', $strString );
	}	
	
	/**
	 * Retrieves a corresponding array value from the given array.
	 * 
	 * When there are multiple arrays and they have similar index structures but it's not certain if one has the key and the others,
	 * use this method to retrieve the corresponding key value. 
	 * 
	 * @remark			This is mainly used by the field array to insert user-defined key values.
	 * @return			string|array			If the key does not exist in the passed array, it will return the default. If the subject value is not an array, it will return the subject value itself.
	 * @since			2.0.0
	 * @since			2.1.3					Added the $fBlankToDefault parameter that sets the default value if the subject value is empty.
	 * @since			2.1.5					Changed the scope to public static from protected as converting all the utility methods to all public static.
	 */
	public static function getCorrespondingArrayValue( $vSubject, $strKey, $strDefault='', $fBlankToDefault=false ) {	
				
		// If $vSubject is null,
		if ( ! isset( $vSubject ) ) return $strDefault;	
			
		// If the $fBlankToDefault flag is set and the subject value is a blank string, return the default value.
		if ( $fBlankToDefault && $vSubject == '' ) return $strDefault;
			
		// If $vSubject is not an array, 
		if ( ! is_array( $vSubject ) ) return ( string ) $vSubject;	// consider it as string.
		
		// Consider $vSubject as array.
		if ( isset( $vSubject[ $strKey ] ) ) return $vSubject[ $strKey ];
		
		return $strDefault;
		
	}
	
	/**
	 * Finds the dimension depth of the given array.
	 * 
	 * @access			protected
	 * @since			2.0.0
	 * @remark			There is a limitation that this only checks the first element so if the second or other elements have deeper dimensions, it will not be caught.
	 * @param			array			$array			the subject array to check.
	 * @return			integer			returns the number of dimensions of the array.
	 */
	public static function getArrayDimension( $array ) {
		return ( is_array( reset( $array ) ) ) ? self::getArrayDimension( reset( $array ) ) + 1 : 1;
	}
	
	
	/**
	 * Merges multiple multi-dimensional array recursively.
	 * 
	 * The advantage of using this method over the array unite operator or array_merge() is that it merges recursively and the null values of the preceding array will be overridden.
	 * 
	 * @since			2.1.2
	 * @static
	 * @access			public
	 * @remark			The parameters are variadic and can add arrays as many as necessary.
	 * @return			array			the united array.
	 */
	public static function uniteArrays( $arrPrecedence, $arrDefault1 ) {
				
		$arrArgs = array_reverse( func_get_args() );
		$arrArray = array();
		foreach( $arrArgs as $arrArg ) 
			$arrArray = self::uniteArraysRecursive( $arrArg, $arrArray );
			
		return $arrArray;
		
	}
	
	/**
	 * Merges two multi-dimensional arrays recursively.
	 * 
	 * The first parameter array takes its precedence. This is useful to merge default option values. 
	 * An alternative to <em>array_replace_recursive()</em>; it is not supported PHP 5.2.x or below.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5				Changed the scope to static. 
	 * @access			public
	 * @remark			null values will be overwritten. 	
	 * @param			array			$arrPrecedence			the array that overrides the same keys.
	 * @param			array			$arrDefault				the array that is going to be overridden.
	 * @return			array			the united array.
	 */ 
	public static function uniteArraysRecursive( $arrPrecedence, $arrDefault ) {
				
		if ( is_null( $arrPrecedence ) ) $arrPrecedence = array();
		
		if ( ! is_array( $arrDefault ) || ! is_array( $arrPrecedence ) ) return $arrPrecedence;
			
		foreach( $arrDefault as $strKey => $v ) {
			
			// If the precedence does not have the key, assign the default's value.
			if ( ! array_key_exists( $strKey, $arrPrecedence ) || is_null( $arrPrecedence[ $strKey ] ) )
				$arrPrecedence[ $strKey ] = $v;
			else {
				
				// if the both are arrays, do the recursive process.
				if ( is_array( $arrPrecedence[ $strKey ] ) && is_array( $v ) ) 
					$arrPrecedence[ $strKey ] = self::uniteArraysRecursive( $arrPrecedence[ $strKey ], $v );			
			
			}
		}
		return $arrPrecedence;		
	}		
	
	/**
	 * Retrieves the query value from the given URL with a key.
	 * 
	 * @since			2.0.0
	 * @return			string|null
	 */ 
	static public function getQueryValueInURLByKey( $strURL, $strQueryKey ) {
		
		$arrURL = parse_url( $strURL );
		parse_str( $arrURL['query'], $arrQuery );		
		return isset( $arrQuery[ $strQueryKey ] ) ? $arrQuery[ $strQueryKey ] : null;
		
	}
	
	/**
	 * Checks if the passed value is a number and set it to the default if not.
	 * 
	 * This is useful for form data validation. If it is a number and exceeds the set maximum number, 
	 * it sets it to the maximum value. If it is a number and is below the minimum number, it sets to the minimum value.
	 * Set a blank value for no limit.
	 * 
	 * @since			2.0.0
	 * @return			string|integer			A numeric value will be returned. 
	 */ 
	static public function fixNumber( $numToFix, $numDefault, $numMin="", $numMax="" ) {

		if ( ! is_numeric( trim( $numToFix ) ) ) return $numDefault;
		if ( $numMin !== "" && $numToFix < $numMin ) return $numMin;
		if ( $numMax !== "" && $numToFix > $numMax ) return $numMax;
		return $numToFix;
		
	}		
	
	/**
	 * Calculates the relative path from the given path.
	 * 
	 * This function is used to generate a template path.
	 * 
	 * @since			2.1.5
	 * @author			Gordon
	 * @author			Michael Uno,			Modified variable names and spacing.
	 * @see				http://stackoverflow.com/questions/2637945/getting-relative-path-from-absolute-path-in-php/2638272#2638272
	 */
	static public function getRelativePath( $from, $to ) {
		
		// some compatibility fixes for Windows paths
		$from = is_dir( $from ) ? rtrim( $from, '\/') . '/' : $from;
		$to   = is_dir( $to )   ? rtrim( $to, '\/') . '/'   : $to;
		$from = str_replace( '\\', '/', $from );
		$to   = str_replace( '\\', '/', $to );

		$from     = explode( '/', $from );
		$to       = explode( '/', $to );
		$relPath  = $to;

		foreach( $from as $depth => $dir ) {
			// find first non-matching dir
			if( $dir === $to[ $depth ] ) {
				// ignore this directory
				array_shift( $relPath );
			} else {
				// get number of remaining dirs to $from
				$remaining = count( $from ) - $depth;
				if( $remaining > 1 ) {
					// add traversals up to first matching dir
					$padLength = ( count( $relPath ) + $remaining - 1 ) * -1;
					$relPath = array_pad( $relPath, $padLength, '..' );
					break;
				} else {
					$relPath[ 0 ] = './' . $relPath[ 0 ];
				}
			}
		}
		return implode( '/', $relPath );
		
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Help_Base' ) ) :
/**
 * Provides base methods and properties for manipulating the contextual help tabs.
 * 
 * @since			2.1.0
 */
abstract class AmazonAutoLinks_AdminPageFramework_Help_Base {
	
	/**
	 * Stores the screen object.
	 * @var				object
	 * @since			2.1.0
	 */ 
	protected $oScreen;
	
	/**
	 * Sets the contextual help tab.
	 * 
	 * On contrary to other methods relating to contextual help tabs that just modify the class properties, this finalizes the help tab contents.
	 * In other words, the set values here will take effect.
	 * 
	 * @access			protected
	 * @remark			The sidebar contents in the help pane can be set but if it's called from the meta box class and the page loads in regular post types, the sidebar text may be overridden by the default one.
	 * @since			2.1.0
	 */  
	protected function setHelpTab( $strID, $strTitle, $arrContents, $arrSideBarContents=array() ) {
		
		if ( empty( $arrContents ) ) return;
		
		$this->oScreen = isset( $this->oScreen ) ? $this->oScreen : get_current_screen();
		$this->oScreen->add_help_tab( 
			array(
				'id'	=> $strID,
				'title'	=> $strTitle,
				'content'	=> implode( PHP_EOL, $arrContents ),
			) 
		);						
		
		if ( ! empty( $arrSideBarContents ) )
			$this->oScreen->set_help_sidebar( implode( PHP_EOL, $arrSideBarContents ) );
			
	}
	
	/**
	 * Encloses the given string with the contextual help specific tag.
	 * @since			2.1.0
	 * @internal
	 */ 
	protected function formatHelpDescription( $strHelpDescription ) {
		return "<div class='contextual-help-description'>" . $strHelpDescription . "</div>";
	}
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_MetaBox_Help' ) ) :
/**
 * Provides methods to manipulate the contextual help tab .
 * 
 * @since			2.1.0
 * @extends			AmazonAutoLinks_AdminPageFramework_Help_Base
 */
abstract class AmazonAutoLinks_AdminPageFramework_MetaBox_Help extends AmazonAutoLinks_AdminPageFramework_Help_Base {
	
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addHelpText( 
	 *		__( 'This text will appear in the contextual help pane.', 'admin-page-framework-demo' ), 
	 *		__( 'This description goes to the sidebar of the help pane.', 'admin-page-framework-demo' )
	 *	);</code>
	 * 
	 * @since			2.1.0
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>registerHelpTabTextForMetaBox()</em> method.
	 * @remark			The user may use this method to add contextual help text.
	 */ 
	protected function addHelpText( $strHTMLContent, $strHTMLSidebarContent="" ) {
		$this->oProps->arrHelpTabText[] = "<div class='contextual-help-description'>" . $strHTMLContent . "</div>";
		$this->oProps->arrHelpTabTextSide[] = "<div class='contextual-help-description'>" . $strHTMLSidebarContent . "</div>";
	}
	
	/**
	 * Adds the given HTML text to the contextual help pane.
	 * 
	 * The help tab will be the meta box title and all the added text will be inserted into the content area within the tab.
	 * On contrary to the <em>addHelpTab()</em> method of the AmazonAutoLinks_AdminPageFramework_Help class, the help tab title is already determined and the meta box ID and the title will be used.
	 * 
	 * @since			2.1.0
	 * @uses			addHelpText()
	 * @remark			This method just adds the given text into the class property. The actual registration will be performed with the <em>registerHelpTabTextForMetaBox()</em> method.
	 */ 	
	protected function addHelpTextForFormFields( $strFieldTitle, $strHelpText, $strHelpTextSidebar="" ) {
		$this->addHelpText(
			"<span class='contextual-help-tab-title'>" . $strFieldTitle . "</span> - " . PHP_EOL
				. $strHelpText,		
			$strHelpTextSidebar
		);		
	}

	/**
	 * Registers the contextual help tab contents.
	 * 
	 * @internal
	 * @since			2.1.0
	 * @remark			A call back for the <em>load-{page hook}</em> action hook.
	 * @remark			The method name implies that this is for meta boxes. This does not mean this method is only for meta box form fields. Extra help text can be added with the <em>addHelpText()</em> method.
	 */ 
	public function registerHelpTabTextForMetaBox() {
	
		if ( ! in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) ) return;
		if ( isset( $_GET['post_type'] ) && ! in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) ) return;
		if ( ! isset( $_GET['post_type'] ) && ! in_array( 'post', $this->oProps->arrPostTypes ) ) return;
		if ( isset( $_GET['post'], $_GET['action'] ) && ! in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) ) return; // edit post page
		
		$this->setHelpTab( 	// this method is defined in the base class.
			$this->oProps->strMetaBoxID, 
			$this->oProps->strTitle, 
			$this->oProps->arrHelpTabText, 
			$this->oProps->arrHelpTabTextSide 
		);
		
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Help' ) ) :
/**
 * Provides methods to manipulate the help screen sections.
 * 
 * @abstract
 * @remark				Shared with the both AmazonAutoLinks_AdminPageFramework and AmazonAutoLinks_AdminPageFramework_PostType.
 * @since				2.1.0
 * @package				Admin Page Framework
 * @subpackage			Admin Page Framework - Page
 * @extends				AmazonAutoLinks_AdminPageFramework_Help_Base
 * @staticvar			array			$arrStructure_HelpTab			stores the array structure of the help tab array.
 */
abstract class AmazonAutoLinks_AdminPageFramework_Help extends AmazonAutoLinks_AdminPageFramework_Help_Base {
	
	/**
	 * Represents the structure of help tab array.
	 * 
	 * @since			2.1.0
	 * @internal
	 */ 
	public static $arrStructure_HelpTab = array(
		'strPageSlug'				=> null,	// ( mandatory )
		'strPageTabSlug'			=> null,	// ( optional )
		'strHelpTabTitle'			=> null,	// ( mandatory )
		'strHelpTabID'				=> null,	// ( mandatory )
		'strHelpTabContent'			=> null,	// ( optional )
		'strHelpTabSidebarContent'	=> null,	// ( optional )
	);

	/**
	 * Registers help tabs to the help toggle pane.
	 * 
	 * This adds a user-defined help information into the help screen placed just below the top admin bar.
	 * 
	 * @remark			The callback of the <em>admin_head</em> action hook.
	 * @see				http://codex.wordpress.org/Plugin_API/Action_Reference/load-%28page%29
	 * @remark			the screen object is supported in WordPress 3.3 or above.
	 * @since			2.1.0
	 * @internal
	 */	 
	public function registerHelpTabs() {
			
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$strCurrentPageTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : ( isset( $this->oProps->arrDefaultInPageTabs[ $strCurrentPageSlug ] ) ? $this->oProps->arrDefaultInPageTabs[ $strCurrentPageSlug ] : '' );
		
		if ( empty( $strCurrentPageSlug ) ) return;
		if ( ! $this->oProps->isPageAdded( $strCurrentPageSlug ) ) return;
		
		foreach( $this->oProps->arrHelpTabs as $arrHelpTab ) {
			
			if ( $strCurrentPageSlug != $arrHelpTab['strPageSlug'] ) continue;
			if ( isset( $arrHelpTab['strPageTabSlug'] ) && ! empty( $arrHelpTab['strPageTabSlug'] ) & $strCurrentPageTabSlug != $arrHelpTab['strPageTabSlug'] ) continue;
				
			$this->setHelpTab( 
				$arrHelpTab['strID'], 
				$arrHelpTab['strTitle'], 
				$arrHelpTab['arrContent'], 
				$arrHelpTab['arrSidebar']
			);
		}
		
	}
	
	/**
	 * Adds the given contextual help tab contents into the property.
	 * 
	 * <h4>Contextual Help Tab Array Structure</h4>
	 * <ul>
	 * 	<li><strong>strPageSlug</strong> - the page slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>strPageTabSlug</strong> - ( optional ) the tab slug of the page that the contextual help tab and its contents are displayed.</li>
	 * 	<li><strong>strHelpTabTitle</strong> - the title of the contextual help tab.</li>
	 * 	<li><strong>strHelpTabID</strong> - the id of the contextual help tab.</li>
	 * 	<li><strong>strHelpTabContent</strong> - the HTML string content of the the contextual help tab.</li>
	 * 	<li><strong>strHelpTabSidebarContent</strong> - ( optional ) the HTML string content of the sidebar of the contextual help tab.</li>
	 * </ul>
	 * 
	 * <h4>Example</h4>
	 * <code>	$this->addHelpTab( 
	 *		array(
	 *			'strPageSlug'				=> 'first_page',	// ( mandatory )
	 *			// 'strPageTabSlug'			=> null,	// ( optional )
	 *			'strHelpTabTitle'			=> 'Admin Page Framework',
	 *			'strHelpTabID'				=> 'admin_page_framework',	// ( mandatory )
	 *			'strHelpTabContent'			=> __( 'This contextual help text can be set with the <em>addHelpTab()</em> method.', 'admin-page-framework' ),
	 *			'strHelpTabSidebarContent'	=> __( 'This is placed in the sidebar of the help pane.', 'admin-page-framework' ),
	 *		)
	 *	);</code>
	 * 
	 * @since			2.1.0
	 * @remark			Called when registering setting sections and fields.
	 * @remark			The user may use this method.
	 * @param			array			$arrHelpTab				The help tab array. The key structure is explained in the description part.
	 * @return			void
	 */ 
	protected function addHelpTab( $arrHelpTab ) {
		
		// Avoid undefined index warnings.
		$arrHelpTab = ( array ) $arrHelpTab + AmazonAutoLinks_AdminPageFramework_Help::$arrStructure_HelpTab;
		
		// If the key is not set, that means the help tab array is not created yet. So create it and go back.
		if ( ! isset( $this->oProps->arrHelpTabs[ $arrHelpTab['strHelpTabID'] ] ) ) {
			$this->oProps->arrHelpTabs[ $arrHelpTab['strHelpTabID'] ] = array(
				'strID' => $arrHelpTab['strHelpTabID'],
				'strTitle' => $arrHelpTab['strHelpTabTitle'],
				'arrContent' => ! empty( $arrHelpTab['strHelpTabContent'] ) ? array( $this->formatHelpDescription( $arrHelpTab['strHelpTabContent'] ) ) : array(),
				'arrSidebar' => ! empty( $arrHelpTab['strHelpTabSidebarContent'] ) ? array( $this->formatHelpDescription( $arrHelpTab['strHelpTabSidebarContent'] ) ) : array(),
				'strPageSlug' => $arrHelpTab['strPageSlug'],
				'strPageTabSlug' => $arrHelpTab['strPageTabSlug'],
			);
			return;
		}

		// This line will be reached if the help tab array is already set. In this case, just append an array element into the keys.
		if ( ! empty( $arrHelpTab['strHelpTabContent'] ) )
			$this->oProps->arrHelpTabs[ $arrHelpTab['strHelpTabID']]['arrContent'][] = $this->formatHelpDescription( $arrHelpTab['strHelpTabContent'] );
		if ( ! empty( $arrHelpTab['strHelpTabSidebarContent'] ) )
			$this->oProps->arrHelpTabs[ $arrHelpTab['strHelpTabID'] ]['arrSidebar'][] = $this->formatHelpDescription( $arrHelpTab['strHelpTabSidebarContent'] );
		
	}
	
}
endif;


if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_HeadTag_Base' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag.
 * 
 * @since			2.1.5
 * 
 */
abstract class AmazonAutoLinks_AdminPageFramework_HeadTag_Base {
	
	function __construct( $oProps ) {
		
		$this->oProps = $oProps;
		$this->oUtil = new AmazonAutoLinks_AdminPageFramework_Utilities;
				
		// Hook the admin header to insert custom admin stylesheet.
		add_action( 'admin_head', array( $this, 'replyToAddStyle' ) );
		add_action( 'admin_head', array( $this, 'replyToAddScript' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'replyToEnqueueScripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'replyToEnqueueStyles' ) );
		
	}	
	
	/*
	 * Methods that should be overridden in extended classes.
	 */
	public function replyToAddStyle() {}
	public function replyToAddScript() {}
	protected function enqueueSRCByConditoin( $arrEnqueueItem ) {}
 	
	/*
	 * Shared methods
	 */
		
	/**
	 * Performs actual enqueuing items. 
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class.
	 * @internal
	 */
	protected function enqueueSRC( $arrEnqueueItem ) {
		
		// For styles
		if ( $arrEnqueueItem['strType'] == 'style' ) {
			wp_enqueue_style( $arrEnqueueItem['strHandleID'], $arrEnqueueItem['strSRC'], $arrEnqueueItem['arrDependencies'], $arrEnqueueItem['strVersion'], $arrEnqueueItem['strMedia'] );
			return;
		}
		
		// For scripts
		wp_enqueue_script( $arrEnqueueItem['strHandleID'], $arrEnqueueItem['strSRC'], $arrEnqueueItem['arrDependencies'], $arrEnqueueItem['strVersion'], $arrEnqueueItem['fInFooter'] );
		if ( $arrEnqueueItem['arrTranslation'] ) 
			wp_localize_script( $arrEnqueueItem['strHandleID'], $arrEnqueueItem['strHandleID'], $arrEnqueueItem['arrTranslation'] );
		
	}
	
	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 * 
	 * @remark			A callback for the admin_enqueue_scripts hook.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueStylesCalback to replyToEnqueueStyles().
	 * @internal
	 */	
	public function replyToEnqueueStyles() {	
		foreach( $this->oProps->arrEnqueuingStyles as $strKey => $arrEnqueuingStyle ) 
			$this->enqueueSRCByConditoin( $arrEnqueuingStyle );
	}
	
	/**
	 * Takes care of added enqueuing scripts by page slug and tab slug.
	 * 
	 * @remark			A callback for the admin_enqueue_scripts hook.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueScriptsCallback to callbackEnqueueScripts().
	 * @internal
	 */
	public function replyToEnqueueScripts() {							
		foreach( $this->oProps->arrEnqueuingScripts as $strKey => $arrEnqueuingScript ) 
			$this->enqueueSRCByConditoin( $arrEnqueuingScript );				
	}
	
}

endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_HeadTag_Pages' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the main framework class.
 * 
 * @since			2.1.5
 * @var			boolean		$fIsMediaUploaderScriptEnqueued		indicates whether the JavaScript script for media uploader is enqueued.
 * @var			boolean		$fIsTaxonomyChecklistScriptAdded	indicates whether the JavaScript script for taxonomy checklist is enqueued.
 * @var			boolean		$fIsImageFieldScriptEnqueued		indicates whether the JavaScript script for image selector is enqueued.
 * @var			boolean		$fIsMediaUploaderScriptAdded		indicates whether the JavaScript script for media uploader is enqueued.
 * @var			boolean		$fIsColorFieldScriptEnqueued		indicates whether the JavaScript script for color picker is enqueued.
 * @var			boolean		$fIsDateFieldScriptEnqueued			indicates whether the JavaScript script for date picker is enqueued.

 */
class AmazonAutoLinks_AdminPageFramework_HeadTag_Pages extends AmazonAutoLinks_AdminPageFramework_HeadTag_Base {

	/**
	 * A flag that indicates whether the JavaScript script for media uploader is enqueued.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_SettingsAPI.
	 * @internal
	 */ 
	protected $fIsMediaUploaderScriptEnqueued = false;

	/**
	 * A flag that indicates whether the JavaScript script for taxonomy checklist boxes.
	 * 
	 * @since			2.1.1
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_SettingsAPI.
	 * @internal
	 */
	protected $fIsTaxonomyChecklistScriptAdded = false;	
	
	/**
	 * A flag that indicates whether the JavaScript script for image selector is enqueued.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_SettingsAPI.
	 * @internal
	 */ 	
	protected $fIsImageFieldScriptEnqueued = false;	
	
	/**
	 * A flag that indicates whether the JavaScript script for media uploader is added.
	 * @since			2.1.3
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_SettingsAPI.
	 * @internal
	 */
	protected $fIsMediaUploaderScriptAdded = false;	
	
	/**
	 * A flag that indicates whether the JavaScript script for color picker is enqueued.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_SettingsAPI.
	 * @internal
	 */ 		
	protected $fIsColorFieldScriptEnqueued = false;	
	
	/**
	 * A flag that indicates whether the JavaScript script for date picker is enqueued.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_SettingsAPI.
	 * @internal
	 */ 			
	protected $fIsDateFieldScriptEnqueued = false;		
	
	/**
	 * Adds the stored CSS rules in the property into the head tag.
	 * 
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from the main class.
	 */		
	public function replyToAddStyle() {
		
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$strTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $strPageSlug );
		
		// If the loading page has not been registered nor the plugin page which uses this library, do nothing.
		if ( ! $this->oProps->isPageAdded( $strPageSlug ) ) return;
					
		$oCaller = $this->oProps->getParentObject();
		
		// Print out the filtered styles.
		$strStyle = AmazonAutoLinks_AdminPageFramework_Properties::$strDefaultStyle . PHP_EOL . $this->oProps->strStyle;
		$strStyle = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( AmazonAutoLinks_AdminPageFramework_Pages::$arrPrefixes['style_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $strStyle );
		$strStyleIE = AmazonAutoLinks_AdminPageFramework_Properties::$strDefaultStyleIE . PHP_EOL . $this->oProps->strStyleIE;
		$strStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( AmazonAutoLinks_AdminPageFramework_Pages::$arrPrefixes['style_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $strStyleIE );
		if ( ! empty( $strStyle ) )
			echo 
				"<style type='text/css' id='admin-page-framework-style'>" 
					. $strStyle
				. "</style>";
		if ( ! empty( $strStyleIE ) )
			echo 
				"<!--[if IE]><style type='text/css' id='admin-page-framework-style-for-IE'>" 
					. $strStyleIE
				. "</style><![endif]-->";
						
	}
	
	/**
	 * Adds the stored JavaScript scripts in the property into the head tag.
	 * 
	 * @remark			A callback for the <em>admin_head</em> hook.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from the main class.
	 */
	public function replyToAddScript() {
		
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$strTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $strPageSlug );
		
		// If the loading page has not been registered or not the plugin page which uses this library, do nothing.
		if ( ! $this->oProps->isPageAdded( $strPageSlug ) ) return;

		$oCaller = $this->oProps->getParentObject();
		
		// Print out the filtered scripts.
		echo "<script type='text/javascript' id='admin-page-framework-script'>"
				. $this->oUtil->addAndApplyFilters( $oCaller, $this->oUtil->getFilterArrayByPrefix( AmazonAutoLinks_AdminPageFramework_Pages::$arrPrefixes['script_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $this->oProps->strScript )
			. "</script>";		
		
	}

	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 */
	public function enqueueStyles( $arrSRCs, $strPageSlug='', $strTabSlug='', $arrCustomArgs=array() ) {
		
		$arrHandleIDs = array();
		foreach( ( array ) $arrSRCs as $strSRC )
			$arrHandleIDs[] = $this->enqueueStyle( $strSRC, $strPageSlug, $strTabSlug, $arrCustomArgs );
		return $arrHandleIDs;
		
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>strHandleID</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>arrDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>strVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>strMedia</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class.
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$strSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			$strPageSlug		(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$strTabSlug			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$arrCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $strSRC, $strPageSlug='', $strTabSlug='', $arrCustomArgs=array() ) {
		
		$strSRC = trim( $strSRC );
		if ( empty( $strSRC ) ) return '';
		if ( isset( $this->oProps->arrEnqueuingScripts[ md5( $strSRC ) ] ) ) return '';	// if already set
		
		$strSRC = $this->oUtil->resolveSRC( $strSRC );
		
		$strSRCHash = md5( $strSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->arrEnqueuingStyles[ $strSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $arrCustomArgs,
			array(		
				'strSRC' => $strSRC,
				'strPageSlug' => $strPageSlug,
				'strTabSlug' => $strTabSlug,
				'strType' => 'style',
				'strHandleID' => 'style_' . $this->oProps->strClassName . '_' .  ( ++$this->oProps->intEnqueuedStyleIndex ),
			),
			AmazonAutoLinks_AdminPageFramework_Properties::$arrStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->arrEnqueuingStyles[ $strSRCHash ][ 'strHandleID' ];
		
	}
	
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 */
	public function enqueueScripts( $arrSRCs, $strPageSlug='', $strTabSlug='', $arrCustomArgs=array() ) {
		
		$arrHandleIDs = array();
		foreach( ( array ) $arrSRCs as $strSRC )
			$arrHandleIDs[] = $this->enqueueScript( $strSRC, $strPageSlug, $strTabSlug, $arrCustomArgs );
		return $arrHandleIDs;
		
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>strHandleID</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>arrDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>strVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>arrTranslation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>fInFooter</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		'apf_read_me', 	// page slug
	 *		'', 	// tab slug
	 *		array(
	 *			'strHandleID' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'arrTranslation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $strStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class.
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$strSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$strPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$strTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$arrCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $strSRC, $strPageSlug='', $strTabSlug='', $arrCustomArgs=array() ) {
		
		$strSRC = trim( $strSRC );
		if ( empty( $strSRC ) ) return '';
		if ( isset( $this->oProps->arrEnqueuingScripts[ md5( $strSRC ) ] ) ) return '';	// if already set
		
		$strSRC = $this->oUtil->resolveSRC( $strSRC );
		
		$strSRCHash = md5( $strSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->arrEnqueuingScripts[ $strSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $arrCustomArgs,
			array(		
				'strPageSlug' => $strPageSlug,
				'strTabSlug' => $strTabSlug,
				'strSRC' => $strSRC,
				'strType' => 'script',
				'strHandleID' => 'script_' . $this->oProps->strClassName . '_' .  ( ++$this->oProps->intEnqueuedScriptIndex ),
			),
			AmazonAutoLinks_AdminPageFramework_Properties::$arrStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->arrEnqueuingScripts[ $strSRCHash ][ 'strHandleID' ];
	}
		
	/**
	 * A helper function for the above replyToEnqueueScripts() and replyToEnqueueStyle() methods.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved from the main class. Changed the name from enqueueSRCByPageConditoin.
	 */
	protected function enqueueSRCByConditoin( $arrEnqueueItem ) {
		
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$strCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $strCurrentPageSlug );
			
		$strPageSlug = $arrEnqueueItem['strPageSlug'];
		$strTabSlug = $arrEnqueueItem['strTabSlug'];
		
		// If the page slug is not specified and the currently loading page is one of the pages that is added by the framework,
		if ( ! $strPageSlug && $this->oProps->isPageAdded( $strCurrentPageSlug ) )  // means script-global(among pages added by the framework)
			return $this->enqueueSRC( $arrEnqueueItem );
				
		// If both tab and page slugs are specified,
		if ( 
			( $strPageSlug && $strCurrentPageSlug == $strPageSlug )
			&& ( $strTabSlug && $strCurrentTabSlug == $strTabSlug )
		) 
			return $this->enqueueSRC( $arrEnqueueItem );
		
		// If the tab slug is not specified and the page slug is specified, 
		// and if the current loading page slug and the specified one matches,
		if ( 
			( $strPageSlug && ! $strTabSlug )
			&& ( $strCurrentPageSlug == $strPageSlug )
		) 
			return $this->enqueueSRC( $arrEnqueueItem );

	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_HeadTag_MetaBox' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the post type class.
 * 
 * @since			2.1.5
 * 
 */
class AmazonAutoLinks_AdminPageFramework_HeadTag_MetaBox extends AmazonAutoLinks_AdminPageFramework_HeadTag_Base {
		
	/*
	 * Callback functions 
	 */
	
	/**
	 * Appends the CSS rules of the framework in the head tag. 
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_MetaBox. Changed the name from addAtyle() to replyToAddStyle().
	 * @remark			A callback for the <em>admin_head</em> hook.
	 */ 	
	public function replyToAddStyle() {
	
		// If it's not post (post edit) page nor the post type page,
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
				&& ( 
					( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) )
					|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) )		// edit post page
				) 
			)
		) return;	
	
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$strRootClassName}_StyleLoaded" ] ) return;
		$GLOBALS[ "{$strRootClassName}_StyleLoaded" ] = true;
				
		$oCaller = $this->oProps->getParentObject();		
				
		// Print out the filtered styles.
		$strStyle = AmazonAutoLinks_AdminPageFramework_Properties::$strDefaultStyle . PHP_EOL . $this->oProps->strStyle;
		$strStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProps->strClassName}", $strStyle );
		$strStyleIE = AmazonAutoLinks_AdminPageFramework_Properties::$strDefaultStyleIE . PHP_EOL . $this->oProps->strStyleIE;
		$strStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProps->strClassName}", $strStyleIE );
		if ( ! empty( $strStyle ) )
			echo 
				"<style type='text/css' id='admin-page-framework-style-meta-box'>" 
					. $strStyle
				. "</style>";
		if ( ! empty( $strStyleIE ) )
			echo 
				"<!--[if IE]><style type='text/css' id='admin-page-framework-style-meta-box'>" 
					. $strStyleIE
				. "</style><![endif]-->";
			
	}
	
	/**
	 * Appends the JavaScript script of the framework in the head tag. 
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_MetaBox. Changed the name from addScript() to replyToAddScript().
	 * @remark			A callback for the <em>admin_head</em> hook.
	 */ 
	public function replyToAddScript() {

		// If it's not post (post edit) page nor the post type page, do not add scripts for media uploader.
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
				&& ( 
					( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) )
					|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) )		// edit post page
				) 
			)
		) return;	
	
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] ) return;
		$GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] = true;
	
		$oCaller = $this->oProps->getParentObject();
		
		// Print out the filtered scripts.
		$strScript = $this->oUtil->addAndApplyFilters( $oCaller, "script_{$this->oProps->strClassName}", $this->oProps->strScript );
		if ( ! empty( $strScript ) )
			echo 
				"<script type='text/javascript' id='admin-page-framework-script-meta-box'>"
					. $strScript
				. "</script>";	
			
	}	
	
	
	/**
	 * Enqueues styles by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 */
	public function enqueueStyles( $arrSRCs, $arrPostTypes=array(), $arrCustomArgs=array() ) {
		
		$arrHandleIDs = array();
		foreach( ( array ) $arrSRCs as $strSRC )
			$arrHandleIDs[] = $this->enqueueStyle( $strSRC, $arrPostTypes, $arrCustomArgs );
		return $arrHandleIDs;
		
	}
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>strHandleID</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>arrDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>strVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>strMedia</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.5			
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$strSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			array			$arrPostTypes		(optional) The post type slugs that the stylesheet should be added to. If not set, it applies to all the pages of the post types.
	 * @param 			array			$arrCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $strSRC, $arrPostTypes=array(), $arrCustomArgs=array() ) {
		
		$strSRC = trim( $strSRC );
		if ( empty( $strSRC ) ) return '';
		if ( isset( $this->oProps->arrEnqueuingScripts[ md5( $strSRC ) ] ) ) return '';	// if already set
		
		$strSRC = $this->oUtil->resolveSRC( $strSRC );
		
		$strSRCHash = md5( $strSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->arrEnqueuingStyles[ $strSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $arrCustomArgs,
			array(		
				'strSRC' => $strSRC,
				'arrPostTypes' => empty( $arrPostTypes ) ? $this->oProps->arrPostTypes : $arrPostTypes,
				'strType' => 'style',
				'strHandleID' => 'style_' . $this->oProps->strClassName . '_' .  ( ++$this->oProps->intEnqueuedStyleIndex ),
			),
			AmazonAutoLinks_AdminPageFramework_Properties::$arrStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->arrEnqueuingStyles[ $strSRCHash ][ 'strHandleID' ];
		
	}
	
	/**
	 * Enqueues scripts by page slug and tab slug.
	 * 
	 * @since			2.1.5
	 */
	public function enqueueScripts( $arrSRCs, $arrPostTypes=array(), $arrCustomArgs=array() ) {
		
		$arrHandleIDs = array();
		foreach( ( array ) $arrSRCs as $strSRC )
			$arrHandleIDs[] = $this->enqueueScript( $strSRC, $arrPostTypes, $arrCustomArgs );
		return $arrHandleIDs;
		
	}	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>strHandleID</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>arrDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>strVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>arrTranslation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>fInFooter</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *	);</code>
	 * 
	 * @since			2.1.5			
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$strSRC				The URL of the stylesheet to enqueue, the absolute file path, or relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			array			$arrPostTypes		(optional) The post type slugs that the script should be added to. If not set, it applies to all the pages with the post type slugs.
	 * @param 			array			$arrCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $strSRC, $arrPostTypes=array(), $arrCustomArgs=array() ) {
		
		$strSRC = trim( $strSRC );
		if ( empty( $strSRC ) ) return '';
		if ( isset( $this->oProps->arrEnqueuingScripts[ md5( $strSRC ) ] ) ) return '';	// if already set
		
		$strSRC = $this->oUtil->resolveSRC( $strSRC );
		
		$strSRCHash = md5( $strSRC );	// setting the key based on the url prevents duplicate items
		$this->oProps->arrEnqueuingScripts[ $strSRCHash ] = $this->oUtil->uniteArrays( 
			( array ) $arrCustomArgs,
			array(		
				'strSRC' => $strSRC,
				'arrPostTypes' => empty( $arrPostTypes ) ? $this->oProps->arrPostTypes : $arrPostTypes,
				'strType' => 'script',
				'strHandleID' => 'script_' . $this->oProps->strClassName . '_' .  ( ++$this->oProps->intEnqueuedScriptIndex ),
			),
			AmazonAutoLinks_AdminPageFramework_Properties::$arrStructure_EnqueuingScriptsAndStyles
		);
		return $this->oProps->arrEnqueuingScripts[ $strSRCHash ][ 'strHandleID' ];
	}

	/**
	 * A helper function for the above replyToEnqueueScripts() and replyToEnqueueStyle() methods.
	 * 
	 * @since			2.1.5
	 */
	protected function enqueueSRCByConditoin( $arrEnqueueItem ) {
		
		$strCurrentPostType = isset( $_GET['post_type'] ) ? $_GET['post_type'] : ( isset( $GLOBALS['typenow'] ) ? $GLOBALS['typenow'] : null );
				
		if ( in_array( $strCurrentPostType, $arrEnqueueItem['arrPostTypes'] ) )		
			return $this->enqueueSRC( $arrEnqueueItem );
			
	}
	
	
}
endif;


if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_HeadTag_PostType' ) ) :
/**
 * Provides methods to enqueue or insert head tag elements into the head tag for the meta box class.
 * 
 * @since			2.1.5
 * @since			2.1.7			Added the replyToAddStyle() method.
 */
class AmazonAutoLinks_AdminPageFramework_HeadTag_PostType extends AmazonAutoLinks_AdminPageFramework_HeadTag_MetaBox {
	
	/**
	 * Appends the CSS rules of the framework in the head tag. 
	 * @since			2.1.7	
	 * @remark			A callback for the <em>admin_head</em> hook.
	 */ 	
	public function replyToAddStyle() {
	
		// If it's not the post type's post listing page or the taxtonomy page
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php' ) ) 
				&& ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProps->strPostType )				
			)
		) return;	
	
		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_StyleLoaded" ] ) && $GLOBALS[ "{$strRootClassName}_StyleLoaded" ] ) return;
		$GLOBALS[ "{$strRootClassName}_StyleLoaded" ] = true;
				
		$oCaller = $this->oProps->getParentObject();		
				
		// Print out the filtered styles.
		$strStyle = AmazonAutoLinks_AdminPageFramework_Properties::$strDefaultStyle . PHP_EOL . $this->oProps->strStyle;
		$strStyle = $this->oUtil->addAndApplyFilters( $oCaller, "style_{$this->oProps->strClassName}", $strStyle );
		$strStyleIE = AmazonAutoLinks_AdminPageFramework_Properties::$strDefaultStyleIE . PHP_EOL . $this->oProps->strStyleIE;
		$strStyleIE = $this->oUtil->addAndApplyFilters( $oCaller, "style_ie_{$this->oProps->strClassName}", $strStyleIE );
		if ( ! empty( $strStyle ) )
			echo 
				"<style type='text/css' id='admin-page-framework-style-post-type'>" 
					. $strStyle
				. "</style>";
		if ( ! empty( $strStyleIE ) )
			echo 
				"<!--[if IE]><style type='text/css' id='admin-page-framework-style-post-type'>" 
					. $strStyleIE
				. "</style><![endif]-->";
			
	}
	/**
	 * Appends the JavaScript script of the framework in the head tag. 
	 * @since			2.1.7
	 * @remark			A callback for the <em>admin_head</em> hook.
	 */ 
	public function replyToAddScript() {

		// If it's not the post type's post listing page
		if ( 
			! (
				in_array( $GLOBALS['pagenow'], array( 'edit.php', 'edit-tags.php' ) ) 
				&& ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->oProps->strPostType )				
			)
		) return;	
		
		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// This class may be instantiated multiple times so use a global flag.
		$strRootClassName = get_class();
		if ( isset( $GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] ) && $GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] ) return;
		$GLOBALS[ "{$strRootClassName}_ScriptLoaded" ] = true;
	
		$oCaller = $this->oProps->getParentObject();
		
		// Print out the filtered scripts.
		$strScript = $this->oUtil->addAndApplyFilters( $oCaller, "script_{$this->oProps->strClassName}", $this->oProps->strScript );
		if ( ! empty( $strScript ) )
			echo 
				"<script type='text/javascript' id='admin-page-framework-script-post-type'>"
					. $strScript
				. "</script>";	
			
	}	
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Pages' ) ) :
/**
 * Provides methods to render admin page elements.
 *
 * @abstract
 * @since			2.0.0
 * @since			2.1.0		Extends AmazonAutoLinks_AdminPageFramework_Help.
 * @extends			AmazonAutoLinks_AdminPageFramework_Help
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 * @staticvar		array		$arrPrefixes						stores the prefix strings for filter and action hooks.
 * @staticvar		array		$arrPrefixesForCallbacks			unlike $arrPrefixes, these require to set the return value.
 * @staticvar		array		$arrScreenIconIDs					stores the ID selector names for screen icons.
 * @staticvar		array		$arrPrefixes						stores the prefix strings for filter and action hooks.
 * @staticvar		array		$arrStructure_InPageTabElements		represents the array structure of an in-page tab array.
 */
abstract class AmazonAutoLinks_AdminPageFramework_Pages extends AmazonAutoLinks_AdminPageFramework_Help {
			
	/**
	 * Stores the prefixes of the filters used by this framework.
	 * 
	 * This must not use the private scope as the extended class accesses it, such as 'start_' and must use the public since another class uses this externally.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Made it public from protected since the HeadTag class accesses it.
	 * @var				array
	 * @static
	 * @access			public
	 * @internal
	 */ 
	public static $arrPrefixes = array(	
		'start_'		=> 'start_',
		'load_'			=> 'load_',
		'do_before_'	=> 'do_before_',
		'do_after_'		=> 'do_after_',
		'do_form_'		=> 'do_form_',
		'do_'			=> 'do_',
		'head_'			=> 'head_',
		'content_'		=> 'content_',
		'foot_'			=> 'foot_',
		'validation_'	=> 'validation_',
		'export_name'	=> 'export_name',
		'export_format' => 'export_format',
		'export_'		=> 'export_',
		'import_name'	=> 'import_name',
		'import_format'	=> 'import_format',
		'import_'		=> 'import_',
		'style_'		=> 'style_',
		'script_'		=> 'script_',
		'field_'		=> 'field_',
		'section_'		=> 'section_',
	);

	/**
	 * Unlike $arrPrefixes, these require to set the return value.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			protected
	 * @internal
	 */ 	
	protected static $arrPrefixesForCallbacks = array(
		'section_'		=> 'section_',
		'field_'		=> 'field_',
		'field_types_'	=> 'field_types_',
		'validation_'	=> 'validation_',
	);
	
	/**
	 * Stores the ID selector names for screen icons. <em>generic</em> is not available in WordPress v3.4.x.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			protected
	 * @internal
	 */ 	
	protected static $arrScreenIconIDs = array(
		'edit', 'post', 'index', 'media', 'upload', 'link-manager', 'link', 'link-category', 
		'edit-pages', 'page', 'edit-comments', 'themes', 'plugins', 'users', 'profile', 
		'user-edit', 'tools', 'admin', 'options-general', 'ms-admin', 'generic',
	);	

	/**
	 * Represents the array structure of an in-page tab array.
	 * 
	 * @since			2.0.0
	 * @var				array
	 * @static
	 * @access			private
	 * @internal
	 */ 	
	private static $arrStructure_InPageTabElements = array(
		'strPageSlug' => null,
		'strTabSlug' => null,
		'strTitle' => null,
		'numOrder' => null,
		'fHide'	=> null,
		'strParentTabSlug' => null,	// this needs to be set if the above fHide is true so that the plugin can mark the parent tab to be active when the hidden page is accessed.
	);
	
		
	/**
	 * Sets whether the page title is displayed or not.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->showPageTitle( false );    // disables the page title.</code>
	 * 
	 * @since			2.0.0
	 * @param			boolean			$fShow			If false, the page title will not be displayed.
	 * @remark			The user may use this method.
	 * @return			void
	 */ 
	protected function showPageTitle( $fShow=true, $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['fShowPageTitle'] = $fShow;
		else {
			$this->oProps->fShowPageTitle = $fShow;
			foreach( $this->oProps->arrPages as &$arrPage ) 
				$arrPage['fShowPageTitle'] = $fShow;
		}
	}	
	
	/**
	 * Sets whether page-heading tabs are displayed or not.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->showPageHeadingTabs( false );    // disables the page heading tabs by passing false.</code>
	 * 
	 * @since			2.0.0
	 * @param			boolean			$fShow					If false, page-heading tabs will be disabled; otherwise, enabled.
	 * @param			string			$strPageSlug			The page to apply the visibility setting. If not set, it applies to all the pages.
	 * @remark			Page-heading tabs and in-page tabs are different. The former displays page titles and the latter displays tab titles.
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */ 
	protected function showPageHeadingTabs( $fShow=true, $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['fShowPageHeadingTabs'] = $fShow;
		else {
			$this->oProps->fShowPageHeadingTabs = $fShow;
			foreach( $this->oProps->arrPages as &$arrPage ) 
				$arrPage['fShowPageHeadingTabs'] = $fShow;
		}
	}
	
	/**
	 * Sets whether in-page tabs are displayed or not.
	 * 
	 * Sometimes, it is required to disable in-page tabs in certain pages. In that case, use the second parameter.
	 * 
	 * @since			2.1.1
	 * @param			boolean			$fShow				If false, in-page tabs will be disabled.
	 * @param			string			$strPageSlug		The page to apply the visibility setting. If not set, it applies to all the pages.
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */
	protected function showInPageTabs( $fShow=true, $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['fShowInPageTabs'] = $fShow;
		else {
			$this->oProps->fShowInPageTabs = $fShow;
			foreach( $this->oProps->arrPages as &$arrPage )
				$arrPage['fShowInPageTabs'] = $fShow;
		}
	}
	
	/**
	 * Sets in-page tab's HTML tag.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setInPageTabTag( 'h2' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$strTag					The HTML tag that encloses each in-page tab title. Default: h3.
	 * @param			string			$strPageSlug			The page slug that applies the setting.	
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */ 	
	protected function setInPageTabTag( $strTag='h3', $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['strInPageTabTag'] = $strTag;
		else {
			$this->oProps->strInPageTabTag = $strTag;
			foreach( $this->oProps->arrPages as &$arrPage )
				$arrPage['strInPageTabTag'] = $strTag;
		}
	}
	
	/**
	 * Sets page-heading tab's HTML tag.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setPageHeadingTabTag( 'h2' );</code>
	 * 
	 * @since			2.1.2
	 * @param			string			$strTag					The HTML tag that encloses the page-heading tab title. Default: h2.
	 * @param			string			$strPageSlug			The page slug that applies the setting.	
	 * @remark			The user may use this method.
	 * @remark			If the second parameter is omitted, it sets the default value.
	 */
	protected function setPageHeadingTabTag( $strTag='h2', $strPageSlug='' ) {
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		if ( ! empty( $strPageSlug ) )
			$this->oProps->arrPages[ $strPageSlug ]['strPageHeadingTabTag'] = $strTag;
		else {
			$this->oProps->strPageHeadingTabTag = $strTag;
			foreach( $this->oProps->arrPages as &$arrPage )
				$arrPage[ $strPageSlug ]['strPageHeadingTabTag'] = $strTag;
		}
	}
	
	/**
	 * Renders the admin page.
	 * 
	 * @remark			This is not intended for the users to use.
	 * @since			2.0.0
	 * @access			protected
	 * @return			void
	 * @internal
	 */ 
	protected function renderPage( $strPageSlug, $strTabSlug=null ) {

		// Do actions before rendering the page. In this order, global -> page -> in-page tab
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['do_before_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );	
		?>
		<div class="wrap">
			<?php
				// Screen icon, page heading tabs(page title), and in-page tabs.
				$strHead = $this->getScreenIcon( $strPageSlug );	
				$strHead .= $this->getPageHeadingTabs( $strPageSlug, $this->oProps->strPageHeadingTabTag ); 	
				$strHead .= $this->getInPageTabs( $strPageSlug, $this->oProps->strInPageTabTag );

				// Apply filters in this order, in-page tab -> page -> global.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['head_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $strHead );
			?>
			<div class="admin-page-framework-container">
				<?php
					$this->showSettingsErrors();
						
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['do_form_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );	

					echo $this->getFormOpeningTag();	// <form ... >
					
					// Capture the output buffer
					ob_start(); // start buffer
							 					
					// Render the form elements by Settings API
					if ( $this->oProps->fEnableForm ) {
						settings_fields( $this->oProps->strOptionKey );	// this value also determines the $option_page global variable value.
						do_settings_sections( $strPageSlug ); 
					}				
					 
					$strContent = ob_get_contents(); // assign the content buffer to a variable
					ob_end_clean(); // end buffer and remove the buffer
								
					// Apply the content filters.
					echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['content_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), $strContent );
	
					// Do the page actions.
					$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['do_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );	
						
				?>
				
			<?php echo $this->getFormClosingTag( $strPageSlug, $strTabSlug );  ?>
			
			</div><!-- End admin-page-framework-container -->
				
			<?php	
				// Apply the foot filters.
				echo $this->oUtil->addAndApplyFilters( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['foot_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, false ), '' );	// empty string
			?>
		</div><!-- End Wrap -->
		<?php
		// Do actions after rendering the page.
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( self::$arrPrefixes['do_after_'], $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );
		
	}
	
	/**
	 * Displays admin notices set for the settings.
	 * 
	 * @global			$pagenow
	 * @since			2.0.0
	 * @since			2.0.1			Fixed a bug that the admin messages were displayed twice in the options-general.php page.
	 * @internal		
	 * @return			void
	 */ 
	private function showSettingsErrors() {
		
		// WordPress automatically performs the settings_errors() function in the options pages. See options-head.php.
		if ( $GLOBALS['pagenow'] == 'options-general.php' ) return;	
		
		$arrSettingsMessages = get_settings_errors( $this->oProps->strOptionKey );
		
		// If custom messages are added, remove the default one. 
		if ( count( $arrSettingsMessages ) > 1 ) 
			$this->removeDefaultSettingsNotice();
		
		settings_errors( $this->oProps->strOptionKey );	// Show the message like "The options have been updated" etc.
	
	}

	/**
	 * Removes default admin notices set for the settings.
	 * 
	 * This removes the settings messages ( admin notice ) added automatically by the framework when the form is submitted.
	 * This is used when a custom message is added manually and the default message should not be displayed.
	 * 
	 * @since			2.0.0
	 * @internal
	 */	
	protected function removeDefaultSettingsNotice() {
				
		global $wp_settings_errors;
		/*
		 * The structure of $wp_settings_errors
		 * 	array(
		 *		array(
					'setting' => $setting,
					'code' => $code,
					'message' => $message,
					'type' => $type
				),
				array( ...
			)
		 * */
		
		$arrDefaultMessages = array(
			$this->oMsg->___( 'option_cleared' ),
			$this->oMsg->___( 'option_updated' ),
		);
		
		foreach ( ( array ) $wp_settings_errors as $intIndex => $arrDetails ) {
			
			if ( $arrDetails['setting'] != $this->oProps->strOptionKey ) continue;
			
			if ( in_array( $arrDetails['message'], $arrDefaultMessages ) )
				unset( $wp_settings_errors[ $intIndex ] );
				
		}
	}
	
	/**
	 * Retrieves the form opening tag.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 
	protected function getFormOpeningTag() {
		
		if ( ! $this->oProps->fEnableForm ) return '';
		return "<form action='options.php' method='post' enctype='{$this->oProps->strFormEncType}'>";
	
	}
	
	/**
	 * Retrieves the form closing tag.
	 * 
	 * @since			2.0.0
	 * @internal
	 */ 	
	protected function getFormClosingTag( $strPageSlug, $strTabSlug ) {

		if ( ! $this->oProps->fEnableForm ) return '';	
		return "<input type='hidden' name='strPageSlug' value='{$strPageSlug}' />" . PHP_EOL
			. "<input type='hidden' name='strTabSlug' value='{$strTabSlug}' />" . PHP_EOL			
			. "</form><!-- End Form -->";
	
	}	
	
	/**
	 * Retrieves the screen icon output as HTML.
	 * 
	 * @remark			the screen object is supported in WordPress 3.3 or above.
	 * @since			2.0.0
	 */ 	
	private function getScreenIcon( $strPageSlug ) {

		// If the icon path is explicitly set, use it.
		if ( isset( $this->oProps->arrPages[ $strPageSlug ]['strURLIcon32x32'] ) ) 
			return '<div class="icon32" style="background-image: url(' . $this->oProps->arrPages[ $strPageSlug ]['strURLIcon32x32'] . ');"><br /></div>';
		
		// If the screen icon ID is explicitly set, use it.
		if ( isset( $this->oProps->arrPages[ $strPageSlug ]['strScreenIconID'] ) )
			return '<div class="icon32" id="icon-' . $this->oProps->arrPages[ $strPageSlug ]['strScreenIconID'] . '"><br /></div>';
			
		// Retrieve the screen object for the current page.
		$oScreen = get_current_screen();
		$strIconIDAttribute = $this->getScreenIDAttribute( $oScreen );

		$strClass = 'icon32';
		if ( empty( $strIconIDAttribute ) && $oScreen->post_type ) 
			$strClass .= ' ' . sanitize_html_class( 'icon32-posts-' . $oScreen->post_type );
		
		if ( empty( $strIconIDAttribute ) || $strIconIDAttribute == $this->oProps->strClassName )
			$strIconIDAttribute = 'generic';		// the default value
		
		return '<div id="icon-' . $strIconIDAttribute . '" class="' . $strClass . '"><br /></div>';
			
	}
	
	/**
	 * Retrieves the screen ID attribute from the given screen object.
	 * 
	 * @since			2.0.0
	 */ 	
	private function getScreenIDAttribute( $oScreen ) {
		
		if ( ! empty( $oScreen->parent_base ) )
			return $oScreen->parent_base;
	
		if ( 'page' == $oScreen->post_type )
			return 'edit-pages';		
			
		return esc_attr( $oScreen->base );
		
	}

	/**
	 * Retrieves the output of page heading tab navigation bar as HTML.
	 * 
	 * @since			2.0.0
	 * @return			string			the output of page heading tabs.
	 */ 		
	private function getPageHeadingTabs( $strCurrentPageSlug, $strTag='h2', $arrOutput=array() ) {
		
		// If the page title is disabled, return an empty string.
		if ( ! $this->oProps->arrPages[ $strCurrentPageSlug ][ 'fShowPageTitle' ] ) return "";

		$strTag = $this->oProps->arrPages[ $strCurrentPageSlug ][ 'strPageHeadingTabTag' ]
			? $this->oProps->arrPages[ $strCurrentPageSlug ][ 'strPageHeadingTabTag' ]
			: $strTag;
	
		// If the page heading tab visibility is disabled, return the title.
		if ( ! $this->oProps->arrPages[ $strCurrentPageSlug ][ 'fShowPageHeadingTabs' ] )
			return "<{$strTag}>" . $this->oProps->arrPages[ $strCurrentPageSlug ]['strPageTitle'] . "</{$strTag}>";		
		
		foreach( $this->oProps->arrPages as $arrSubPage ) {
			
			// For added sub-pages
			if ( isset( $arrSubPage['strPageSlug'] ) && $arrSubPage['fShowPageHeadingTab'] ) {
				// Check if the current tab number matches the iteration number. If not match, then assign blank; otherwise put the active class name.
				$strClassActive =  $strCurrentPageSlug == $arrSubPage['strPageSlug']  ? 'nav-tab-active' : '';		
				$arrOutput[] = "<a class='nav-tab {$strClassActive}' "
					. "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $arrSubPage['strPageSlug'], 'tab' => false ), $this->oProps->arrDisallowedQueryKeys ) 
					. "'>"
					. $arrSubPage['strPageTitle']
					. "</a>";	
			}
			
			// For added menu links
			if ( 
				isset( $arrSubPage['strURL'] )
				&& $arrSubPage['strType'] == 'link' 
				&& $arrSubPage['fShowPageHeadingTab']
			) 
				$arrOutput[] = "<a class='nav-tab link' "
					. "href='{$arrSubPage['strURL']}'>"
					. $arrSubPage['strMenuTitle']
					. "</a>";					
			
		}
		return "<div class='admin-page-framework-page-heading-tab'><{$strTag} class='nav-tab-wrapper'>" 
			.  implode( '', $arrOutput ) 
			. "</{$strTag}></div>";
		
	}

	/**
	 * Retrieves the output of in-page tab navigation bar as HTML.
	 * 
	 * @since			2.0.0
	 * @return			string			the output of in-page tabs.
	 */ 	
	private function getInPageTabs( $strCurrentPageSlug, $strTag='h3', $arrOutput=array() ) {
		
		// If in-page tabs are not set, return an empty string.
		if ( empty( $this->oProps->arrInPageTabs[ $strCurrentPageSlug ] ) ) return implode( '', $arrOutput );
				
		// Determine the current tab slug.
		$strCurrentTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $strCurrentPageSlug );
		$strCurrentTabSlug = $this->getParentTabSlug( $strCurrentPageSlug, $strCurrentTabSlug );
		
		$strTag = $this->oProps->arrPages[ $strCurrentPageSlug ][ 'strInPageTabTag' ]
			? $this->oProps->arrPages[ $strCurrentPageSlug ][ 'strInPageTabTag' ]
			: $strTag;
	
		// If the in-page tabs' visibility is set to false, returns the title.
		if ( ! $this->oProps->arrPages[ $strCurrentPageSlug ][ 'fShowInPageTabs' ]	)
			return isset( $this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $strCurrentTabSlug ]['strTitle'] ) 
				? "<{$strTag}>{$this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $strCurrentTabSlug ]['strTitle']}</{$strTag}>" 
				: "";
	
		// Get the actual string buffer.
		foreach( $this->oProps->arrInPageTabs[ $strCurrentPageSlug ] as $strTabSlug => $arrInPageTab ) {
					
			// If it's hidden and its parent tab is not set, skip
			if ( $arrInPageTab['fHide'] && ! isset( $arrInPageTab['strParentTabSlug'] ) ) continue;
			
			// The parent tab means the root tab when there is a hidden tab that belongs to it. Also check it the specified parent tab exists.
			$strInPageTabSlug = isset( $arrInPageTab['strParentTabSlug'], $this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $arrInPageTab['strParentTabSlug'] ] ) 
				? $arrInPageTab['strParentTabSlug'] 
				: $arrInPageTab['strTabSlug'];
				
			// Check if the current tab slug matches the iteration slug. If not match, assign blank; otherwise, put the active class name.
			$fIsActiveTab = ( $strCurrentTabSlug == $strInPageTabSlug );
			$arrOutput[ $strInPageTabSlug ] = "<a class='nav-tab " . ( $fIsActiveTab ? "nav-tab-active" : "" ) . "' "
				. "href='" . $this->oUtil->getQueryAdminURL( array( 'page' => $strCurrentPageSlug, 'tab' => $strInPageTabSlug ), $this->oProps->arrDisallowedQueryKeys ) 
				. "'>"
				. $this->oProps->arrInPageTabs[ $strCurrentPageSlug ][ $strInPageTabSlug ]['strTitle'] //	"{$arrInPageTab['strTitle']}"
				. "</a>";
		
		}		
		
		return empty( $arrOutput )
			? ""
			: "<div class='admin-page-framework-in-page-tab'><{$strTag} class='nav-tab-wrapper in-page-tab'>" 
					. implode( '', $arrOutput )
				. "</{$strTag}></div>";
			
	}

	/**
	 * Retrieves the parent tab slug from the given tab slug.
	 * 
	 * @since			2.0.0
	 * @since			2.1.2			If the parent slug has the fHide to be true, it returns an empty string.
	 * @return			string			the parent tab slug.
	 */ 	
	private function getParentTabSlug( $strPageSlug, $strTabSlug ) {
		
		$strParentTabSlug = isset( $this->oProps->arrInPageTabs[ $strPageSlug ][ $strTabSlug ]['strParentTabSlug'] ) 
			? $this->oProps->arrInPageTabs[ $strPageSlug ][ $strTabSlug ]['strParentTabSlug']
			: $strTabSlug;
		
		return isset( $this->oProps->arrInPageTabs[ $strPageSlug ][ $strParentTabSlug ]['fHide'] ) && $this->oProps->arrInPageTabs[ $strPageSlug ][ $strParentTabSlug ]['fHide']
			? ""
			: $strParentTabSlug;

	}

	/**
	 * Adds an in-page tab.
	 * 
	 * @since			2.0.0
	 * @param			string			$strPageSlug			The page slug that the tab belongs to.
	 * @param			string			$strTabTitle			The title of the tab.
	 * @param			string			$strTabSlug				The tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).
	 * @param			integer			$numOrder				( optional ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.
	 * @param			boolean			$fHide					( optional ) default: false. If this is set to false, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.
	 * @param			string			$strParentTabSlug		( optional ) this needs to be set if the above fHide is true so that the parent tab will be emphasized as active when the hidden page is accessed.
	 * @remark			Use this method to add in-page tabs to ensure the array holds all the necessary keys.
	 * @remark			In-page tabs are different from page-heading tabs which is automatically added with page titles.
	 * @return			void
	 */ 		
	protected function addInPageTab( $strPageSlug, $strTabTitle, $strTabSlug, $numOrder=null, $fHide=null, $strParentTabSlug=null ) {	
		
		$strTabSlug = $this->oUtil->sanitizeSlug( $strTabSlug );
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		$intCountElement = isset( $this->oProps->arrInPageTabs[ $strPageSlug ] ) ? count( $this->oProps->arrInPageTabs[ $strPageSlug ] ) : 0;
		if ( ! empty( $strTabSlug ) && ! empty( $strPageSlug ) ) 
			$this->oProps->arrInPageTabs[ $strPageSlug ][ $strTabSlug ] = array(
				'strPageSlug'	=> $strPageSlug,
				'strTitle'		=> trim( $strTabTitle ),
				'strTabSlug'	=> $strTabSlug,
				'numOrder'		=> is_numeric( $numOrder ) ? $numOrder : $intCountElement + 10,
				'fHide'			=> ( $fHide ),
				'strParentTabSlug' => ! empty( $strParentTabSlug ) ? $this->oUtil->sanitizeSlug( $strParentTabSlug ) : null,
			);
	
	}
	/**
	 * Adds in-page tabs.
	 *
	 * The parameters accept in-page tab arrays and they must have the following array keys.
	 * <h4>In-Page Tab Array</h4>
	 * <ul>
	 * 	<li><strong>strPageSlug</strong> - ( string ) the page slug that the tab belongs to.</li>
	 * 	<li><strong>strTabSlug</strong> -  ( string ) the tab slug. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
	 * 	<li><strong>strTitle</strong> - ( string ) the title of the tab.</li>
	 * 	<li><strong>numOrder</strong> - ( optional, integer ) the order number of the tab. The lager the number is, the lower the position it is placed in the menu.</li>
	 * 	<li><strong>fHide</strong> - ( optional, boolean ) default: false. If this is set to false, the tab title will not be displayed in the tab navigation menu; however, it is still accessible from the direct URL.</li>
	 * 	<li><strong>strParentTabSlug</strong> - ( optional, string ) this needs to be set if the above fHide is true so that the parent tab will be emphasized as active when the hidden page is accessed.</li>
	 * </ul>
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addInPageTabs(
	 *		array(
	 *			'strTabSlug' => 'firsttab',
	 *			'strTitle' => __( 'Text Fields', 'my-text-domain' ),
	 *			'strPageSlug' => 'myfirstpage'
	 *		),
	 *		array(
	 *			'strTabSlug' => 'secondtab',
	 *			'strTitle' => __( 'Selectors and Checkboxes', 'my-text-domain' ),
	 *			'strPageSlug' => 'myfirstpage'
	 *		)
	 *	);</code>
	 * 
	 * @since			2.0.0
	 * @param			array			$arrTab1			The in-page tab array.
	 * @param			array			$arrTab2			Another in-page tab array.
	 * @param			array			$_and_more			Add in-page tab arrays as many as necessary to the next parameters.
	 * @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	 * @remark			In-page tabs are different from page-heading tabs which is automatically added with page titles.	 
	 * @return			void
	 */ 			
	protected function addInPageTabs( $arrTab1, $arrTab2=null, $_and_more=null ) {
		
		foreach( func_get_args() as $arrTab ) {
			if ( ! is_array( $arrTab ) ) continue;
			$arrTab = $arrTab + self::$arrStructure_InPageTabElements;	// avoid undefined index warnings.
			$this->addInPageTab( $arrTab['strPageSlug'], $arrTab['strTitle'], $arrTab['strTabSlug'], $arrTab['numOrder'], $arrTab['fHide'], $arrTab['strParentTabSlug'] );
		}
		
	}

	/**
	 * Finalizes the in-page tab property array.
	 * 
	 * This finalizes the added in-page tabs and sets the default in-page tab for each page.
	 * Also this sorts the in-page tab property array.
	 * This must be done before registering settings sections because the default tab needs to be determined in the process.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>admin_menu</em> hook.
	 * @return			void
	 */ 		
	public function finalizeInPageTabs() {
	
		foreach( $this->oProps->arrPages as $strPageSlug => $arrPage ) {
			
			if ( ! isset( $this->oProps->arrInPageTabs[ $strPageSlug ] ) ) continue;
			
			// Apply filters to let modify the in-page tab array.
			$this->oProps->arrInPageTabs[ $strPageSlug ] = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
				$this,
				"{$this->oProps->strClassName}_{$strPageSlug}_tabs",
				$this->oProps->arrInPageTabs[ $strPageSlug ]			
			);	
			// Added in-page arrays may be missing necessary keys so merge them with the default array structure.
			foreach( $this->oProps->arrInPageTabs[ $strPageSlug ] as &$arrInPageTab ) 
				$arrInPageTab = $arrInPageTab + self::$arrStructure_InPageTabElements;
						
			// Sort the in-page tab array.
			uasort( $this->oProps->arrInPageTabs[ $strPageSlug ], array( $this->oProps, 'sortByOrder' ) );
			
			// Set the default tab for the page.
			// Read the value as reference; otherwise, a strange bug occurs. It may be due to the variable name, $arrInPageTab, is also used as reference in the above foreach.
			foreach( $this->oProps->arrInPageTabs[ $strPageSlug ] as $strTabSlug => &$arrInPageTab ) { 	
			
				if ( ! isset( $arrInPageTab['strTabSlug'] ) ) continue;	
				
				// Regardless of whether it's a hidden tab, it is stored as the default in-page tab.
				$this->oProps->arrDefaultInPageTabs[ $strPageSlug ] = $arrInPageTab['strTabSlug'];
					
				break;	// The first iteration item is the default one.
			}
		}
	}			

}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Menu' ) ) :
/**
 * Provides methods to manipulate menu items.
 *
 * @abstract
 * @since			2.0.0
 * @extends			AmazonAutoLinks_AdminPageFramework_Pages
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 * @staticvar		array	$arrBuiltInRootMenuSlugs	stores the WordPress built-in menu slugs.
 * @staticvar		array	$arrStructure_SubMenuPage	represents the structure of the sub-menu page array.
 */
abstract class AmazonAutoLinks_AdminPageFramework_Menu extends AmazonAutoLinks_AdminPageFramework_Pages {
	
	/**
	 * Used to refer the built-in root menu slugs.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds the built-in root menu slugs.
	 * @static
	 * @internal
	 */ 
	protected static $arrBuiltInRootMenuSlugs = array(
		// All keys must be lower case to support case insensitive look-ups.
		'dashboard' => 			'index.php',
		'posts' => 				'edit.php',
		'media' => 				'upload.php',
		'links' => 				'link-manager.php',
		'pages' => 				'edit.php?post_type=page',
		'comments' => 			'edit-comments.php',
		'appearance' => 		'themes.php',
		'plugins' => 			'plugins.php',
		'users' => 				'users.php',
		'tools' => 				'tools.php',
		'settings' => 			'options-general.php',
		'network admin' => 		"network_admin_menu",
	);		

	/**
	 * Represents the structure of sub-menu page array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of sub-menu page.
	 * @static
	 * @internal
	 */ 
	protected static $arrStructure_SubMenuPage = array(
		'strPageTitle' => null, 
		'strPageSlug' => null, 
		'strScreenIcon' => null,
		'strCapability' => null, 
		'numOrder' => null,
		'fShowPageHeadingTab' => true,	// if this is false, the page title won't be displayed in the page heading tab.
		'fShowInMenu' => true,	// if this is false, the menu label will not be displayed in the sidebar menu.
	);
	 
	/**
	 * Sets to which top level page is going to be adding sub-pages.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setRootMenuPage( 'Settings' );</code>
	 * <code>$this->setRootMenuPage( 
	 * 	'APF Form',
	 * 	plugins_url( 'image/screen_icon32x32.jpg', __FILE__ )
	 * );</code>
	 * 
	 * @since			2.0.0
	 * @since			2.1.6			The $strURLIcon16x16 parameter accepts a file path.
	 * @remark			Only one root page can be set per one class instance.
	 * @param			string			$strRootMenuLabel			If the method cannot find the passed string from the following listed items, it will create a top level menu item with the passed string. ( case insensitive )
	 * <blockquote>Dashboard, Posts, Media, Links, Pages, Comments, Appearance, Plugins, Users, Tools, Settings, Network Admin</blockquote>
	 * @param			string			$strURLIcon16x16			( optional ) the URL or the file path of the menu icon. The size should be 16 by 16 in pixel.
	 * @param			string			$intMenuPosition			( optional ) the position number that is passed to the <var>$position</var> parameter of the <a href="http://codex.wordpress.org/Function_Reference/add_menu_page">add_menu_page()</a> function.
	 * @return			void
	 */
	protected function setRootMenuPage( $strRootMenuLabel, $strURLIcon16x16=null, $intMenuPosition=null ) {

		$strRootMenuLabel = trim( $strRootMenuLabel );
		$strSlug = $this->isBuiltInMenuItem( $strRootMenuLabel );	// if true, this method returns the slug
		$this->oProps->arrRootMenu = array(
			'strTitle'			=> $strRootMenuLabel,
			'strPageSlug' 		=> $strSlug ? $strSlug : $this->oProps->strClassName,	
			'strURLIcon16x16'	=> $this->oUtil->resolveSRC( $strURLIcon16x16 ),
			'intPosition'		=> $intMenuPosition,
			'fCreateRoot'		=> $strSlug ? false : true,
		);	

	}
	
	/**
	 * Sets the top level menu page by page slug.
	 * 
	 * The page should be already created or scheduled to be created separately.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setRootMenuPageBySlug( 'edit.php?post_type=apf_posts' );</code>
	 * 
	 * @since			2.0.0
	 * @access			protected
	 * @remark			The user may use this method in their extended class definition.
	 * @param			string			$strRootMenuSlug			The page slug of the top-level root page.
	 * @return			void
	 */ 
	protected function setRootMenuPageBySlug( $strRootMenuSlug ) {
		
		$this->oProps->arrRootMenu['strPageSlug'] = $strRootMenuSlug;	// do not sanitize the slug here because post types includes a question mark.
		$this->oProps->arrRootMenu['fCreateRoot'] = false;		// indicates to use an existing menu item. 
		
	}
	
	/**
	 * Adds sub-menu pages.
	 * 
	 * Use addSubMenuItems() instead, which supports external links.
	 * 
	 * @since			2.0.0
	 * @internal
	 * @return			void
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 */ 
	protected function addSubMenuPages() {
		foreach ( func_get_args() as $arrSubMenuPage ) {
			$arrSubMenuPage = $arrSubMenuPage + self::$arrStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$arrSubMenuPage['strPageTitle'],
				$arrSubMenuPage['strPageSlug'],
				$arrSubMenuPage['strScreenIcon'],
				$arrSubMenuPage['strCapability'],
				$arrSubMenuPage['numOrder'],
				$arrSubMenuPage['fShowPageHeadingTab']
			);				
		}
	}
	
	/**
	 * Adds a single sub-menu page.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->addSubMenuPage( 'My Page', 'my_page', 'edit-pages' );</code>
	 * 
	 * @since			2.0.0
	 * @since			2.1.2			The key name fPageHeadingTab was changed to fShowPageHeadingTab
	 * @since			2.1.6			$strScreenIcon accepts a file path.
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 * @param			string			$strPageTitle			The title of the page.
	 * @param			string			$strPageSlug			The slug of the page.
	 * @param			string			$strScreenIcon			( optional ) Either a screen icon ID, a url of the icon, or a file path to the icon, with the size of 32 by 32 in pixel. The accepted icon IDs are as follows.
	 * <blockquote>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</blockquote>
	 * <strong>Note:</strong> the <em>generic</em> ID is available since WordPress 3.5.
	 * @param			string			$strCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the page.
	 * @param			integer			$numOrder				( optional ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.
	 * @param			boolean			$fShowPageHeadingTab	( optional ) If this is set to false, the page title won't be displayed in the page heading tab. Default: true.
	 * @param			boolean			$fShowInMenu			( optional ) If this is set to false, the page title won't be displayed in the sidebar menu while the page is still accessible. Default: true.
	 * @return			void
	 */ 
	protected function addSubMenuPage( $strPageTitle, $strPageSlug, $strScreenIcon=null, $strCapability=null, $numOrder=null, $fShowPageHeadingTab=true, $fShowInMenu=true ) {
		
		$strPageSlug = $this->oUtil->sanitizeSlug( $strPageSlug );
		$intCount = count( $this->oProps->arrPages );
		$arrPreviouslySetPage = isset( $this->oProps->arrPages[ $strPageSlug ] ) 
			? $this->oProps->arrPages[ $strPageSlug ]
			: array();
		$arrThisPage = array(  
			'strPageTitle'				=> $strPageTitle,
			'strPageSlug'				=> $strPageSlug,
			'strType'					=> 'page',	// this is used to compare with the link type.
			'strURLIcon32x32'			=> $strScreenIcon ? $this->oUtil->resolveSRC( $strScreenIcon, true ) : null,
			'strScreenIconID'			=> in_array( $strScreenIcon, self::$arrScreenIconIDs ) ? $strScreenIcon : null,
			'strCapability'				=> isset( $strCapability ) ? $strCapability : $this->oProps->strCapability,
			'numOrder'					=> is_numeric( $numOrder ) ? $numOrder : $intCount + 10,
			'fShowPageHeadingTab'		=> $fShowPageHeadingTab,
			'fShowInMenu'				=> $fShowInMenu,	// since 1.3.4			
			'fShowPageTitle'			=> $this->oProps->fShowPageTitle,			// boolean
			'fShowPageHeadingTabs'		=> $this->oProps->fShowPageHeadingTabs,		// boolean
			'fShowInPageTabs'			=> $this->oProps->fShowInPageTabs,			// boolean
			'strInPageTabTag'			=> $this->oProps->strInPageTabTag,			// string
			'strPageHeadingTabTag'		=> $this->oProps->strPageHeadingTabTag,		// string			
		);
		$this->oProps->arrPages[ $strPageSlug ] = $this->oUtil->uniteArraysRecursive( $arrThisPage, $arrPreviouslySetPage );
			
	}
	
	/**
	 * Checks if a menu item is a WordPress built-in menu item from the given menu label.
	 * 
	 * @since			2.0.0
	 * @internal
	 * @return			void|string			Returns the associated slug string, if true.
	 */ 
	protected function isBuiltInMenuItem( $strMenuLabel ) {
		
		$strMenuLabelLower = strtolower( $strMenuLabel );
		if ( array_key_exists( $strMenuLabelLower, self::$arrBuiltInRootMenuSlugs ) )
			return self::$arrBuiltInRootMenuSlugs[ $strMenuLabelLower ];
		
	}
	
	/**
	 * Registers the root menu page.
	 * 
	 * @since			2.0.0
	 */ 
	private function registerRootMenuPage() {

		$strHookName = add_menu_page(  
			$this->oProps->strClassName,						// Page title - will be invisible anyway
			$this->oProps->arrRootMenu['strTitle'],				// Menu title - should be the root page title.
			$this->oProps->strCapability,						// Capability - access right
			$this->oProps->arrRootMenu['strPageSlug'],			// Menu ID 
			'', //array( $this, $this->oProps->strClassName ), 	// Page content displaying function
			$this->oProps->arrRootMenu['strURLIcon16x16'],		// icon path
			isset( $this->oProps->arrRootMenu['intPosition'] ) ? $this->oProps->arrRootMenu['intPosition'] : null	// menu position
		);

	}
	
	/**
	 * Registers the sub-menu page.
	 * 
	 * @since			2.0.0
	 * @remark			Used in the buildMenu() method. 
	 * @remark			Within the <em>admin_menu</em> hook callback process.
	 * @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	 */ 
	private function registerSubMenuPage( $arrArgs ) {
	
		// Format the argument array since it may be added by the third party scripts via the hook.
		$arrArgs = isset( $arrArgs['strType'] ) && $arrArgs['strType'] == 'link' 
			? $arrArgs + AmazonAutoLinks_AdminPageFramework_Link::$arrStructure_SubMenuLink	// for link
			: $arrArgs + self::$arrStructure_SubMenuPage;	// for page
		
		// Variables
		$strType = $arrArgs['strType'];	// page or link
		$strTitle = $strType == 'page' ? $arrArgs['strPageTitle'] : $arrArgs['strMenuTitle'];
		$strCapability = $arrArgs['strCapability'];
			
		// Check the capability
		$strCapability = isset( $strCapability ) ? $strCapability : $this->strCapability;
		if ( ! current_user_can( $strCapability ) ) return;		
		
		// Add the sub-page to the sub-menu
		$arrResult = array();
		$strRootPageSlug = $this->oProps->arrRootMenu['strPageSlug'];
		$strMenuLabel = plugin_basename( $strRootPageSlug );	// Make it compatible with the add_submenu_page() function.
		
		// If it's a page - it's possible that the strPageSlug key is not set if the user uses a method like showPageHeadingTabs() prior to addSubMenuItam().
		if ( $strType == 'page' && isset( $arrArgs['strPageSlug'] ) ) {		
			
			$strPageSlug = $arrArgs['strPageSlug'];
			$arrResult[ $strPageSlug ] = add_submenu_page( 
				$strRootPageSlug,						// the root(parent) page slug
				$strTitle,								// page_title
				$strTitle,								// menu_title
				$strCapability,				 			// strCapability
				$strPageSlug,	// menu_slug
				// In admin.php ( line 149 of WordPress v3.6.1 ), do_action($page_hook) ( where $page_hook is $arrResult[ $strPageSlug ] )
				// will be executed and it triggers the __call magic method with the method name of "md5 class hash + _page_ + this page slug".
				array( $this, $this->oProps->strClassHash . '_page_' . $strPageSlug )
			);			
			
			add_action( "load-" . $arrResult[ $strPageSlug ] , array( $this, "load_pre_" . $strPageSlug ) );
				
			// If the visibility option is false, remove the one just added from the sub-menu array
			if ( ! $arrArgs['fShowInMenu'] ) {

				foreach( ( array ) $GLOBALS['submenu'][ $strMenuLabel ] as $intIndex => $arrSubMenu ) {
					
					if ( ! isset( $arrSubMenu[ 3 ] ) ) continue;
					
					// the array structure is defined in plugin.php - $submenu[$parent_slug][] = array ( $menu_title, $capability, $menu_slug, $page_title ) 
					if ( $arrSubMenu[0] == $strTitle && $arrSubMenu[3] == $strTitle && $arrSubMenu[2] == $strPageSlug ) {
						unset( $GLOBALS['submenu'][ $strMenuLabel ][ $intIndex ] );
						
						// The page title in the browser window title bar will miss the page title as this is left as it is.
						$this->oProps->arrHiddenPages[ $strPageSlug ] = $strTitle;
						add_filter( 'admin_title', array( $this, 'fixPageTitleForHiddenPages' ), 10, 2 );
						
						break;
					}
				}
			} 
				
		} 
		// If it's a link,
		if ( $strType == 'link' && $arrArgs['fShowInMenu'] ) {
			
			if ( ! isset( $GLOBALS['submenu'][ $strMenuLabel ] ) )
				$GLOBALS['submenu'][ $strMenuLabel ] = array();
			
			$GLOBALS['submenu'][ $strMenuLabel ][] = array ( 
				$strTitle, 
				$strCapability, 
				$arrArgs['strURL'],
			);	
		}
	
		return $arrResult;	// maybe useful to debug.

	}
	
	/**
	 * A callback function for the admin_title filter to fix the page title for hidden pages.
	 * @since			2.1.4
	 */
	public function fixPageTitleForHiddenPages( $strAdminTitle, $strPageTitle ) {

		if ( isset( $_GET['page'], $this->oProps->arrHiddenPages[ $_GET['page'] ] ) )
			return $this->oProps->arrHiddenPages[ $_GET['page'] ] . $strAdminTitle;
			
		return $strAdminTitle;
		
	}
	
	
	/**
	 * Builds menus.
	 * 
	 * @since			2.0.0
	 */
	public function buildMenus() {
		
		// If the root menu label is not set but the slug is set, 
		if ( $this->oProps->arrRootMenu['fCreateRoot'] ) 
			$this->registerRootMenuPage();
		
		// Apply filters to let other scripts add sub menu pages.
		$this->oProps->arrPages = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			$this,
			"{$this->oProps->strClassName}_pages", 
			$this->oProps->arrPages
		);
		
		// Sort the page array.
		uasort( $this->oProps->arrPages, array( $this->oProps, 'sortByOrder' ) ); 
		
		// Set the default page, the first element.
		foreach ( $this->oProps->arrPages as $arrPage ) {
			
			if ( ! isset( $arrPage['strPageSlug'] ) ) continue;
			$this->oProps->strDefaultPageSlug = $arrPage['strPageSlug'];
			break;
			
		}
		
		// Register them.
		foreach ( $this->oProps->arrPages as &$arrSubMenuItem ) 
			$this->oProps->arrRegisteredSubMenuPages = $this->registerSubMenuPage( $arrSubMenuItem );
						
		// After adding the sub menus, if the root menu is created, remove the page that is automatically created when registering the root menu.
		if ( $this->oProps->arrRootMenu['fCreateRoot'] ) 
			remove_submenu_page( $this->oProps->arrRootMenu['strPageSlug'], $this->oProps->arrRootMenu['strPageSlug'] );
		
	}	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_SettingsAPI' ) ) :
/**
 * Provides methods to add form elements with WordPress Settings API. 
 *
 * @abstract
 * @since		2.0.0
 * @extends		AmazonAutoLinks_AdminPageFramework_Menu
 * @package		Admin Page Framework
 * @subpackage	Admin Page Framework - Page
 * @staticvar	array		$arrStructure_Section				represents the structure of the form section array.
 * @staticvar	array		$arrStructure_Field					represents the structure of the form field array.
 * @var			array		$arrFieldErrors						stores the settings field errors.
 */
abstract class AmazonAutoLinks_AdminPageFramework_SettingsAPI extends AmazonAutoLinks_AdminPageFramework_Menu {
	
	/**
	 * Represents the structure of the form section array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form section.
	 * @static
	 * @internal
	 */ 	
	protected static $arrStructure_Section = array(	
		'strSectionID' => null,
		'strPageSlug' => null,
		'strTabSlug' => null,
		'strTitle' => null,
		'strDescription' => null,
		'strCapability' => null,
		'fIf' => true,	
		'numOrder' => null,	// do not set the default number here because incremented numbers will be added when registering the sections.
		'strHelp' => null,
		'strHelpAside' => null,
	);	
	
	/**
	 * Represents the structure of the form field array.
	 * 
	 * @since			2.0.0
	 * @remark			Not for the user.
	 * @var				array			Holds array structure of form field.
	 * @static
	 * @internal
	 */ 
	protected static $arrStructure_Field = array(
		'strFieldID'		=> null, 		// ( mandatory )
		'strSectionID'		=> null,		// ( mandatory )
		'strSectionTitle'	=> null,		// This will be assigned automatically in the formatting method.
		'strType'			=> null,		// ( mandatory )
		'strPageSlug'		=> null,		// This will be assigned automatically in the formatting method.
		'strTabSlug'		=> null,		// This will be assigned automatically in the formatting method.
		'strOptionKey'		=> null,		// This will be assigned automatically in the formatting method.
		'strClassName'		=> null,		// This will be assigned automatically in the formatting method.
		'strCapability'		=> null,		
		'strTitle'			=> null,
		'strTip'			=> null,
		'strDescription'	=> null,
		'strName'			=> null,		// the name attribute of the input field.
		'strError'			=> null,		// error message for the field
		'strBeforeField'	=> null,
		'strAfterField'		=> null,
		'fIf' 				=> true,
		'numOrder'			=> null,	// do not set the default number here for this key.		
		'strHelp'			=> null,	// since 2.1.0
		'strHelpAside'		=> null,	// since 2.1.0
		'fRepeatable'		=> null,	// since 2.1.3
	);	
	
	/**
	 * Stores the settings field errors. 
	 * 
	 * @since			2.0.0
	 * @var				array			Stores field errors.
	 * @internal
	 */ 
	protected $arrFieldErrors;		// Do not set a value here since it is checked to see it's null.
							
	/**
	* Sets the given message to be displayed in the next page load. 
	* 
	* This is used to inform users about the submitted input data, such as "Updated successfully." or "Problem occurred." etc. and normally used in validation callback methods.
	* 
	* <h4>Example</h4>
	* <code>if ( ! $fVerified ) {
	*		$this->setFieldErrors( $arrErrors );		
	*		$this->setSettingNotice( 'There was an error in your input.' );
	*		return $arrOldPageOptions;
	*	}</code>
	*
	* @since			2.0.0
	* @since			2.1.2			Added a check to prevent duplicate items.
	* @since			2.1.5			Added the $fOverride parameter.
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @param			string			$strMsg					the text message to be displayed.
	* @param			string			$strType				( optional ) the type of the message, either "error" or "updated"  is used.
	* @param			string			$strID					( optional ) the ID of the message. This is used in the ID attribute of the message HTML element.
	* @param			integer		$fOverride				( optional ) false: do not override when there is a message of the same id. true: override the previous one.
	* @return			void
	*/		
	protected function setSettingNotice( $strMsg, $strType='error', $strID=null, $fOverride=true ) {
		
		// Check if the same message has been added already.
		$arrWPSettingsErrors = isset( $GLOBALS['wp_settings_errors'] ) ? ( array ) $GLOBALS['wp_settings_errors'] : array();
		$strID = isset( $strID ) ? $strID : $this->oProps->strOptionKey; 	// the id attribute for the message div element.

		foreach( $arrWPSettingsErrors as $intIndex => $arrSettingsError ) {
			
			if ( $arrSettingsError['setting'] != $this->oProps->strOptionKey ) continue;
						
			// If the same message is added, no need to add another.
			if ( $arrSettingsError['message'] == $strMsg ) return;
				
			// Prevent duplicated ids.
			if ( $arrSettingsError['code'] === $strID ) {
				if ( ! $fOverride ) 
					return;
				else	// remove the item with the same id  
					unset( $arrWPSettingsErrors[ $intIndex ] );
			}
							
		}

		add_settings_error( 
			$this->oProps->strOptionKey, // the script specific ID so the other settings error won't be displayed with the settings_errors() function.
			$strID, 
			$strMsg,	// error or updated
			$strType
		);
					
	}

	/**
	* Adds the given form section items into the property. 
	* 
	* The passed section array must consist of the following keys.
	* 
	* <strong>Section Array</strong>
	* <ul>
	* <li><strong>strSectionID</strong> - ( string ) the section ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* <li><strong>strPageSlug</strong> - (  string ) the page slug that the section belongs to.</li>
	* <li><strong>strTabSlug</strong> - ( optional, string ) the tab slug that the section belongs to.</li>
	* <li><strong>strTitle</strong> - ( optional, string ) the title of the section.</li>
	* <li><strong>strCapability</strong> - ( optional, string ) the <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* <li><strong>fIf</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* <li><strong>numOrder</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* <li><strong>strHelp</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	* <li><strong>strHelpAside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	* </ul>
	* 
	* <h4>Example</h4>
	* <code>$this->addSettingSections(
	*		array(
	*			'strSectionID'		=> 'text_fields',
	*			'strPageSlug'		=> 'first_page',
	*			'strTabSlug'		=> 'textfields',
	*			'strTitle'			=> 'Text Fields',
	*			'strDescription'	=> 'These are text type fields.',
	*			'numOrder'			=> 10,
	*		),	
	*		array(
	*			'strSectionID'		=> 'selectors',
	*			'strPageSlug'		=> 'first_page',
	*			'strTabSlug'		=> 'selectors',
	*			'strTitle'			=> 'Selectors and Checkboxes',
	*			'strDescription'	=> 'These are selector type options such as dropdown lists, radio buttons, and checkboxes',
	*		)</code>
	*
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			array		$arrSection1				the section array.
	* @param			array		$arrSection2				( optional ) another section array.
	* @param			array		$_and_more					( optional ) add more section array to the next parameters as many as necessary.
	* @return			void
	*/		
	protected function addSettingSections( $arrSection1, $arrSection2=null, $_and_more=null ) {	
				
		foreach( func_get_args() as $arrSection ) 
			$this->addSettingSection( $arrSection );
			
	}
	
	/**
	 * A singular form of the adSettingSections() method which takes only a single parameter.
	 * 
	 * This is useful when adding section arrays in loops.
	 * 
	 * @since			2.1.2
	 * @access			protected
	 * @param			array		$arrSection				the section array.
	 * @remark			The user may use this method in their extended class definition.
	 * @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	 */
	protected function addSettingSection( $arrSection ) {
		
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;		
		
		if ( ! is_array( $arrSection ) ) return;

		$arrSection = $arrSection + self::$arrStructure_Section;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name, the slugs as well.
		$arrSection['strSectionID'] = $this->oUtil->sanitizeSlug( $arrSection['strSectionID'] );
		$arrSection['strPageSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strPageSlug'] );
		$arrSection['strTabSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strTabSlug'] );
		
		if ( ! isset( $arrSection['strSectionID'], $arrSection['strPageSlug'] ) ) return;	// these keys are necessary.
		
		// If the page slug does not match the current loading page, there is no need to register form sections and fields.
		if ( $GLOBALS['pagenow'] != 'options.php' && ! $strCurrentPageSlug || $strCurrentPageSlug !=  $arrSection['strPageSlug'] ) return;				

		// If the custom condition is set and it's not true, skip.
		if ( ! $arrSection['fIf'] ) return;
		
		// If the access level is set and it is not sufficient, skip.
		$arrSection['strCapability'] = isset( $arrSection['strCapability'] ) ? $arrSection['strCapability'] : $this->oProps->strCapability;
		if ( ! current_user_can( $arrSection['strCapability'] ) ) return;	// since 1.0.2.1
		
		$this->oProps->arrSections[ $arrSection['strSectionID'] ] = $arrSection;	
			
	}
	
	/**
	* Removes the given section(s) by section ID.
	* 
	* This accesses the property storing the added section arrays and removes the specified ones.
	* 
	* <h4>Example</h4>
	* <code>$this->removeSettingSections( 'text_fields', 'selectors', 'another_section', 'yet_another_section' );</code>
	* 
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			string			$strSectionID1			the section ID to remove.
	* @param			string			$strSectionID2			( optional ) another section ID to remove.
	* @param			string			$_and_more				( optional ) add more section IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	protected function removeSettingSections( $strSectionID1=null, $strSectionID2=null, $_and_more=null ) {	
		
		foreach( func_get_args() as $strSectionID ) 
			if ( isset( $this->oProps->arrSections[ $strSectionID ] ) )
				unset( $this->oProps->arrSections[ $strSectionID ] );
		
	}
	
	/**
	* Adds the given field array items into the field array property.
	* 
	* The passed field array must consist of the following keys. 
	* 
	* <h4>Field Array</h4>
	* <ul>
	* 	<li><strong>strFieldID</strong> - ( string ) the field ID. Avoid using non-alphabetic characters exept underscore and numbers.</li>
	* 	<li><strong>strSectionID</strong> - ( string ) the section ID that the field belongs to.</li>
	* 	<li><strong>strType</strong> - ( string ) the type of the field. The supported types are listed below.</li>
	* 	<li><strong>strTitle</strong> - ( optional, string ) the title of the section.</li>
	* 	<li><strong>strDescription</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	* 	<li><strong>strTip</strong> - ( optional, string ) the tip for the field which is displayed when the mouse is hovered over the field title.</li>
	* 	<li><strong>strCapability</strong> - ( optional, string ) the http://codex.wordpress.org/Roles_and_Capabilities">access level of the section. If the page visitor does not have sufficient capability, the section will be invisible to them.</li>
	* 	<li><strong>strName</strong> - ( optional, string ) the name attribute value of the input tag instead of automatically generated one.</li>
	* 	<li><strong>strError</strong> - ( optional, string ) the error message to display above the input field.</li>
	* 	<li><strong>strBeforeField</strong> - ( optional, string ) the HTML string to insert before the input field output.</li>
	* 	<li><strong>strAfterField</strong> - ( optional, string ) the HTML string to insert after the input field output.</li>
	* 	<li><strong>fIf</strong> - ( optional, boolean ) if the passed value is false, the section will not be registered.</li>
	* 	<li><strong>numOrder</strong> - ( optional, integer ) the order number of the section. The higher the number is, the lower the position it gets.</li>
	* 	<li><strong>vLabel</strong> - ( optional|mandatory, string|array ) the text label(s) associated with and displayed along with the input field. Some input types can ignore this key while some require it.</li>
	* 	<li><strong>vDefault</strong> - ( optional, string|array ) the default value(s) assigned to the input tag's value attribute.</li>
	* 	<li><strong>vValue</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>value</em> attribute to override the default or stored value.</li>
	* 	<li><strong>vDelimiter</strong> - ( optional, string|array ) the HTML string that delimits multiple elements. This is available if the <var>vLabel</var> key is passed as array. It will be enclosed in inline-block elements so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>vBeforeInputTag</strong> - ( optional, string|array ) the HTML string inserted right before the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>vAfterInputTag</strong> - ( optional, string|array ) the HTML string inserted right after the input tag. It will be enclosed in the <code>label</code> tag so the passed HTML string should not contain block elements.</li>
	* 	<li><strong>vClassAttribute</strong> - ( optional, string|array ) the value(s) assigned to the input tag's <em>class</em>.</li>
	* 	<li><strong>vLabelMinWidth</strong> - ( optional, string|array ) the inline style property of the <em>min-width</em> of the label tag for the field in pixel without the unit. Default: <code>120</code>.</li>
	* 	<li><strong>vDisable</strong> - ( optional, boolean|array ) if this is set to true, the <em>disabled</em> attribute will be inserted into the field input tag.</li>
	*	<li><strong>strHelp</strong> - ( optional, string ) the help description added to the contextual help tab.</li>
	*	<li><strong>strHelpAside</strong> - ( optional, string ) the additional help description for the side bar of the contextual help tab.</li>
	* </ul>
	* <h4>Field Types</h4>
	* <p>Each field type uses specific array keys.</p>
	* <ul>
	* 	<li><strong>text</strong> - a text input field which allows the user to type text.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>fRepeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>password</strong> - a password input field which allows the user to type text.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>fRepeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>	* 
	* 		</ul>
	* 	<li><strong>datetime, datetime-local, email, month, search, tel, time, url, week</strong> - HTML5 input fields types. Some browsers do not support these.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 		</ul>
	* 	<li><strong>number, range</strong> - HTML5 input field types. Some browsers do not support these.</li>
	* 		<ul>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 			<li><strong>vMax</strong> - ( optional, integer|array ) the number that indicates the <em>max</em> attribute of the input field.</li>
	* 			<li><strong>vMin</strong> - ( optional, integer|array ) the number that indicates the <em>min</em> attribute of the input field.</li>
	* 			<li><strong>vStep</strong> - ( optional, integer|array ) the number that indicates the <em>step</em> attribute of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>fRepeatable</strong> - [2.1.3+]( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>textarea</strong> - a textarea input field. The following array keys are supported.
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vRows</strong> - ( optional, integer|array ) the number of rows of the textarea field.</li>
	* 			<li><strong>vCols</strong> - ( optional, integer|array ) the number of cols of the textarea field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vRich</strong> - [2.1.2+]( optional, array ) to make it a rich text editor pass a non-empty value. It accept a setting array of the <code>_WP_Editors</code> class defined in the core.
	* For more information, see the argument section of <a href="http://codex.wordpress.org/Function_Reference/wp_editor" target="_blank">this page</a>.
	* 			</li>
	* 			<li><strong>fRepeatable</strong> - [2.1.3+]( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields. It's not supported for the rich editor.</li>
	*		</ul>
	* 	</li>
	* 	<li><strong>radio</strong> - a radio button input field.</li>
	* 	<li><strong>checkbox</strong> - a check box input field.</li>
	* 	<li><strong>select</strong> - a dropdown input field.</li>
	* 		<ul>
	* 			<li><strong>vMultiple</strong> - ( optional, boolean|array ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 			<li><strong>vWidth</strong> - ( optional, string|array ) the width of the dropdown list including the unit. e.g. 120px</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 		</ul>
	* 	<li><strong>size</strong> - a size input field. This is a combination of number and select fields.</li>
	* 		<ul>
	* 			<li>
	* 				<strong>vSizeUnits</strong> - ( optional, array ) defines the units to show. e.g. <code>array( 'px' => 'px', '%' => '%', 'em' => 'em'  )</code> 
	* 				Default: <code>array( 'px' => 'px', '%' => '%', 'em' => 'em', 'ex' => 'ex', 'in' => 'in', 'cm' => 'cm', 'mm' => 'mm', 'pt' => 'pt', 'pc' => 'pc' )</code>
	* 			</li>
	* 			<li><strong>vMultiple</strong> - ( optional, boolean|array ) if this is set to true, the <em>multiple</em> attribute will be inserted into the field input tag, which enables the multiple selections for the user.</li>
	* 			<li><strong>vWidth</strong> - ( optional, string|array ) the width of the dropdown list including the unit. e.g. 120px</li>
	* 			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the number input field.</li>
	* 			<li><strong>vUnitSize</strong> - [2.1.5+]( optional, integer|array ) the number that indicates the <em>size</em> attribute of the select(unit) input field.</li>
	* 			<li><strong>vMax</strong> - ( optional, integer|array ) the number that indicates the <em>max</em> attribute of the input field.</li>
	* 			<li><strong>vMin</strong> - ( optional, integer|array ) the number that indicates the <em>min</em> attribute of the input field.</li>
	* 			<li><strong>vStep</strong> - ( optional, integer|array ) the number that indicates the <em>step</em> attribute of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the <em>size</em> attribute of the input field.</li>
	* 	</ul>
	* 	<li><strong>hidden</strong> - a hidden input field.</li>
	* 	<li><strong>file</strong> - a file upload input field.</li>
	* 		<ul>
	* 			<li><strong>vAcceptAttribute</strong> - ( optional, string|array ) the accept attribute value. Default: <code>audio/*|video/*|image/*|MIME_type</code></li>
	* 		</ul>
	* 	<li><strong>submit</strong> - a submit button input field.</li>
	* 		<ul>
	* 			<li><strong>vLink</strong> - ( optional, string|array ) the url(s) linked to the submit button.</li>
	* 			<li><strong>vRedirect</strong> - ( optional, string|array ) the url(s) redirected to after submitting the input form.</li>
	* 			<li><strong>vReset</strong> - [2.1.2+] ( optional, string|array ) the option key to delete. Set 1 for the entire option.</li>
	* 		</ul>
	* 	<li><strong>import</strong> - an inport input field. This is a custom file and submit field.</li>
	* 		<ul>
	* 			<li><strong>vAcceptAttribute</strong> - ( optional, string|array ) the accept attribute value. Default: <code>audio/*|video/*|image/*|MIME_type</code></li>
	* 			<li><strong>vClassAttributeUpload</strong> - ( optional, string|array ) [2.1.5+] the class attribute for the file upload field. Default: <code>import</code></li>
	* 			<li><strong>vImportOptionKey</strong> - ( optional, string|array ) the option table key to save the importing data.</li>
	* 			<li><strong>vImportFormat</strong> - ( optional, string|array ) the import format. json, or array is supported. Default: array</li>
	* 			<li><strong>vMerge</strong> - ( optional, boolean|array ) [2.0.5+] determines whether the imported data should be merged with the existing options.</li>
	* 		</ul>
	* 	<li><strong>export</strong> - an export input field. This is a custom submit field.</li>
	* 		<ul>
	* 			<li><strong>vExportFileName</strong> - ( optional, string|array ) the file name to download.</li>
	* 			<li><strong>vExportFormat</strong> - ( optional, string|array ) the format type. array, json, or text is supported. Default: array.</li>
	* 			<li><strong>vExportData</strong> - ( optional, string|array|object ) the data to export.</li>
	* 		</ul>
	* 	<li><strong>image</strong> - an image input field. This is a custom text field with an attached JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>vImagePreview</strong> - ( optional, boolean|array ) if this is set to false, the image preview will be disabled.</li>
	* 			<li><strong>strTickBoxTitle</strong> - ( optional, string ) the text label displayed in the media uploader box's title.</li>
	* 			<li><strong>strLabelUseThis</strong> - ( optional, string ) the text label displayed in the button of the media uploader to set the image.</li>
	* 			<li><strong>fRepeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 			<li><strong>arrCaptureAttributes</strong> - [2.1.3+] ( optional, array ) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', and 'link'. Note that for external URLs, ID will not be captured. e.g. <code>'arrCaptureAttributes' => array( 'id', 'caption', 'description' )</code></li>
	* 			<li><strong>fAllowExternalSource</strong> - [2.1.3+] ( optional, boolean ) whether external URL can be set via the uploader.</li>
	* 		</ul>
	* 	<li><strong>media</strong> - [2.1.3+] a media input field. This is a custom text field with an attached JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>strTickBoxTitle</strong> - ( optional, string ) the text label displayed in the media uploader box's title.</li>
	* 			<li><strong>strLabelUseThis</strong> - ( optional, string ) the text label displayed in the button of the media uploader to set the image.</li>
	* 			<li><strong>fRepeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 			<li><strong>arrCaptureAttributes</strong> - [2.1.3+] ( optional, array ) the array of the attribute names of the image to save. If this is set, the field will be an array with the specified attributes. The supported attributes are, 'id', 'caption', and 'description'. Note that for external URLs, ID will not be captured. e.g. <code>'arrCaptureAttributes' => array( 'id', 'caption', 'description' )</code></li>
	* 			<li><strong>fAllowExternalSource</strong> - [2.1.3+] ( optional, boolean ) whether external URL can be set via the uploader.</li>
	* 		</ul>
	* 	<li><strong>color</strong> - a color picker input field. This is a custom text field with a JavaScript script.</li>
	* 		<ul>
	*			<li><strong>vReadOnly</strong> - ( optional, boolean|array ) if this is set to true, the <em>readonly</em> attribute will be inserted into the field input tag.</li>
	* 			<li><strong>vSize</strong> - ( optional, integer|array ) the number that indicates the size of the input field.</li>
	* 			<li><strong>vMaxLength</strong> - ( optional, integer|array ) the number that indicates the <em>maxlength</em> attribute of the input field.</li>
	* 			<li><strong>fRepeatable</strong> - [2.1.3+] ( optional, boolean|array ) whether the fields should be repeatable. If is true, the plus and the minus buttons appear next to each field that lets the user add/remove the fields.</li>
	* 		</ul>
	* 	<li><strong>taxonomy</strong> - a taxonomy check list. This is a set of check boxes listing a specified taxonomy. This does not accept to create multiple fields by passing an array of labels.</li>
	* 		<ul>
	*			<li><strong>vTaxonomySlug</strong> - ( optional, string|array ) the taxonomy slug to list.</li>
	*			<li><strong>strWidth</strong> - ( optional, string ) the inline style property value of <em>max-width</em> of this element. Include the unit such as px, %. Default: 100%</li>
	*			<li><strong>strHeight</strong> - ( optional, string ) the inline style property value of <em>height</em> of this element. Include the unit such as px, %. Default: 250px</li>
	* 		</ul>
	* 	<li><strong>posttype</strong> - a posttype check list. This is a set of check boxes listing post type slugs.</li>
	* 		<ul>
	* 			<li><strong>arrRemove</strong> - ( optional, array ) the post type slugs not to be listed. e.g.<code>array( 'revision', 'attachment', 'nav_menu_item' )</code></li>
	* 		</ul>

	* </ul>	
	* 
	* <h4>Example</h4>
	* <code>$this->addSettingFields(
	*		array(	// Single text field
	*			'strFieldID' => 'text',
	*			'strSectionID' => 'text_fields',
	*			'strTitle' => __( 'Text', 'admin-page-framework-demo' ),
	*			'strDescription' => __( 'Type something here.', 'admin-page-framework-demo' ),	// additional notes besides the form field
	*			'strType' => 'text',
	*			'numOrder' => 1,
	*			'vDefault' => 123456,
	*			'vSize' => 40,
	*		),	
	*		array(	// Multiple text fields
	*			'strFieldID' => 'text_multiple',
	*			'strSectionID' => 'text_fields',
	*			'strTitle' => 'Multiple Text Fields',
	*			'strDescription' => 'These are multiple text fields.',	// additional notes besides the form field
	*			'strType' => 'text',
	*			'numOrder' => 2,
	*			'vDefault' => array(
	*				'Hello World',
	*				'Foo bar',
	*				'Yes, we can.'
	*			),
	*			'vLabel' => array( 
	*				'First Item: ', 
	*				'Second Item: ', 
	*				'Third Item: ' 
	*			),
	*			'vSize' => array(
	*				30,
	*				60,
	*				90,
	*			),
	*		)
	*	);</code> 
	* 
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			array			$arrField1			the field array.
	* @param			array			$arrField2			( optional ) another field array.
	* @param			array			$_and_more			( optional ) add more field arrays to the next parameters as many as necessary.
	* @return			void
	*/		
	protected function addSettingFields( $arrField1, $arrField2=null, $_and_more=null ) {	
	
		foreach( func_get_args() as $arrField ) 
			$this->addSettingField( $arrField );

	}
	/**
	* Adds the given field array items into the field array property.
	* 
	* Itentical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* @since			2.1.2
	* @return			void
	* @remark			The user may use this method in their extended class definition.
	*/	
	protected function addSettingField( $arrField ) {
		
		if ( ! is_array( $arrField ) ) return;
		
		$arrField = $arrField + self::$arrStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
		$arrField['strSectionID'] = $this->oUtil->sanitizeSlug( $arrField['strSectionID'] );
		
		// Check the mandatory keys' values are set.
		if ( ! isset( $arrField['strFieldID'], $arrField['strSectionID'], $arrField['strType'] ) ) return;	// these keys are necessary.
		
		// If the custom condition is set and it's not true, skip.
		if ( ! $arrField['fIf'] ) return;			
		
		// If the access level is not sufficient, skip.
		$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->oProps->strCapability;
		if ( ! current_user_can( $arrField['strCapability'] ) ) return; 
								
		$this->oProps->arrFields[ $arrField['strFieldID'] ] = $arrField;		
		
	}
	
	/**
	* Removes the given field(s) by field ID.
	* 
	* This accesses the property storing the added field arrays and removes the specified ones.
	* 
	* <h4>Example</h4>
	* <code>$this->removeSettingFields( 'fieldID_A', 'fieldID_B', 'fieldID_C', 'fieldID_D' );</code>
	* 
	* @since			2.0.0
	* @access 			protected
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @remark			The actual registration will be performed in the <em>registerSettings()</em> method with the <em>admin_menu</em> hook.
	* @param			string			$strFieldID1				the field ID to remove.
	* @param			string			$strFieldID2				( optional ) another field ID to remove.
	* @param			string			$_and_more					( optional ) add more field IDs to the next parameters as many as necessary.
	* @return			void
	*/	
	protected function removeSettingFields( $strFieldID1, $strFieldID2=null, $_and_more ) {
				
		foreach( func_get_args() as $strFieldID ) 
			if ( isset( $this->oProps->arrFields[ $strFieldID ] ) )
				unset( $this->oProps->arrFields[ $strFieldID ] );

	}	
	
	/**
	 * Redirects the callback of the load-{page} action hook to the framework's callback.
	 * 
	 * @since			2.1.0
	 * @access			protected
	 * @internal
	 * @remark			This method will be triggered before the header gets sent.
	 * @return			void
	 */ 
	protected function doPageLoadCall( $strPageSlug, $strTabSlug, $arrArg ) {

		// Do actions, class name -> page -> in-page tab.
		$this->oUtil->addAndDoActions( $this, $this->oUtil->getFilterArrayByPrefix( "load_", $this->oProps->strClassName, $strPageSlug, $strTabSlug, true ) );
		
	}
			
	/**
	 * Validates the submitted user input.
	 * 
	 * @since			2.0.0
	 * @access			protected
	 * @internal
	 * @remark			This method is not intended for the users to use.
	 * @remark			the scope must be protected to be accessed from the extended class. The <em>AmazonAutoLinks_AdminPageFramework</em> class uses this method in the overloading <em>__call()</em> method.
	 * @return			array			Return the input array merged with the original saved options so that other page's data will not be lost.
	 */ 
	protected function doValidationCall( $strMethodName, $arrInput ) {
		
		$strTabSlug = isset( $_POST['strTabSlug'] ) ? $_POST['strTabSlug'] : '';	// no need to retrieve the default tab slug here because it's an embedded value that is already set in the previous page. 
		$strPageSlug = isset( $_POST['strPageSlug'] ) ? $_POST['strPageSlug'] : '';
		
		// Retrieve the submit field ID(the container that holds submit input tags) and the input ID(this determines exactly which submit button is pressed).
		$strPressedFieldID = isset( $_POST['__submit'] ) ? $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__submit'], 'field_id' ) : '';
		$strPressedInputID = isset( $_POST['__submit'] ) ? $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__submit'], 'input_id' ) : '';
		
		// Check if custom submit keys are set [part 1]
		if ( isset( $_POST['__import']['submit'], $_FILES['__import'] ) ) 
			return $this->importOptions( $this->oProps->arrOptions, $strPageSlug, $strTabSlug );
		if ( isset( $_POST['__export']['submit'] ) ) 
			die( $this->exportOptions( $this->oProps->arrOptions, $strPageSlug, $strTabSlug ) );		
		if ( isset( $_POST['__reset_confirm'] ) && $strPressedFieldName = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__reset_confirm'], 'key' ) )
			return $this->askResetOptions( $strPressedFieldName, $strPageSlug );			
		if ( isset( $_POST['__link'] ) && $strLinkURL = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__link'], 'url' ) )
			$this->oUtil->goRedirect( $strLinkURL );	// if the associated submit button for the link is pressed, the will be redirected.
		if ( isset( $_POST['__redirect'] ) && $strRedirectURL = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__redirect'], 'url' ) )
			$this->setRedirectTransients( $strRedirectURL );
				
		// Apply validation filters - validation_{page slug}_{tab slug}, validation_{page slug}, validation_{instantiated class name}
		$arrInput = $this->getFilteredOptions( $arrInput, $strPageSlug, $strTabSlug, $strPressedFieldID, $strPressedInputID );
		
		// Check if custom submit keys are set [part 2] - these should be done after applying the filters.
		if ( isset( $_POST['__reset'] ) && $strKeyToReset = $this->getPressedCustomSubmitButtonSiblingValue( $_POST['__reset'], 'key' ) )
			$arrInput = $this->resetOptions( $strKeyToReset, $arrInput );
		
		// Set the update notice
		$fEmpty = empty( $arrInput );
		$this->setSettingNotice( 
			$fEmpty ? $this->oMsg->___( 'option_cleared' ) : $this->oMsg->___( 'option_updated' ), 
			$fEmpty ? 'error' : 'updated', 
			$this->oProps->strOptionKey,	// the id
			false	// do not override
		);
		
		return $arrInput;	
		
	}
	
	/**
	 * Displays a confirmation message to the user when a reset button is pressed.
	 * 
	 * @since			2.1.2
	 */
	private function askResetOptions( $strPressedFieldName, $strPageSlug ) {
		
		// Retrieve the pressed button's associated submit field ID and its section ID.
		// $strFieldName = $this->getPressedCustomSubmitButtonFieldName( $_POST['__reset_confirm'] );
		$arrNameKeys = explode( '|', $strPressedFieldName );	
		// $strPageSlug = $arrNameKeys[ 1 ]; 
		$strSectionID = $arrNameKeys[ 2 ]; 
		$strFieldID = $arrNameKeys[ 3 ];
		
		// Set up the field error array.
		$arrErrors = array();
		$arrErrors[ $strSectionID ][ $strFieldID ] = $this->oMsg->___( 'reset_options' );
		$this->setFieldErrors( $arrErrors );
		
		// Set a flag that the confirmation is displayed
		set_transient( md5( "reset_confirm_" . $strPressedFieldName ), $strPressedFieldName, 60*2 );
		
		$this->setSettingNotice( $this->oMsg->___( 'confirm_perform_task' ) );
		
		return $this->getPageOptions( $strPageSlug ); 			
		
	}
	/**
	 * Performs reset options.
	 * 
	 * @since			2.1.2
	 * @remark			$arrInput has only the page elements that called the validation callback. In other words, it does not hold other pages' option keys.
	 */
	private function resetOptions( $strKeyToReset, $arrInput ) {
		
		if ( $strKeyToReset == 1 or $strKeyToReset === true ) {
			delete_option( $this->oProps->strOptionKey );
			$this->setSettingNotice( $this->oMsg->___( 'option_been_reset' ) );
			return array();
		}
		
		unset( $this->oProps->arrOptions[ trim( $strKeyToReset ) ] );
		unset( $arrInput[ trim( $strKeyToReset ) ] );
		update_option( $this->oProps->strOptionKey, $this->oProps->arrOptions );
		$this->setSettingNotice( $this->oMsg->___( 'specified_option_been_deleted' ) );
	
		return $arrInput;	// the returned array will be saved with the Settings API.
	}
	
	private function setRedirectTransients( $strURL ) {
		if ( empty( $strURL ) ) return;
		$strTransient = md5( trim( "redirect_{$this->oProps->strClassName}_{$_POST['strPageSlug']}" ) );
		return set_transient( $strTransient, $strURL , 60*2 );
	}
		
	/**
	 * Retrieves the target key's value associated with the given data to a custom submit button.
	 * 
	 * This method checks if the associated submit button is pressed with the input fields.
	 * 
	 * @since			2.0.0
	 * @return			mixed			Returns null if no button is found and the associated link url if found. Otherwise, the URL associated with the button.
	 */ 
	private function getPressedCustomSubmitButtonSiblingValue( $arrPostElements, $strTargetKey='url' ) {	
	
		foreach( $arrPostElements as $strFieldName => $arrSubElements ) {
			
			/*
			 * $arrSubElements['name']	- the input field name property of the submit button, delimited by pipe (|) e.g. APF_GettingStarted|first_page|submit_buttons|submit_button_link
			 * $arrSubElements['url']	- the URL to redirect to. e.g. http://www.somedomain.com
			 * */
			$arrNameKeys = explode( '|', $arrSubElements[ 'name' ] );		// the 'name' key must be set.
			
			// Count of 4 means it's a single element. Count of 5 means it's one of multiple elements.
			// The isset() checks if the associated button is actually pressed or not.
			if ( count( $arrNameKeys ) == 4 && isset( $_POST[ $arrNameKeys[0] ][ $arrNameKeys[1] ][ $arrNameKeys[2] ][ $arrNameKeys[3] ] ) )
				return $arrSubElements[ $strTargetKey ];
			if ( count( $arrNameKeys ) == 5 && isset( $_POST[ $arrNameKeys[0] ][ $arrNameKeys[1] ][ $arrNameKeys[2] ][ $arrNameKeys[3] ][ $arrNameKeys[4] ] ) )
				return $arrSubElements[ $strTargetKey ];
				
		}
		
		return null;	// not found
		
	}

	/**
	 * Processes the imported data.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Added additional filters with field id and input id.
	 */
	private function importOptions( $arrStoredOptions, $strPageSlug, $strTabSlug ) {
		
		$oImport = new AmazonAutoLinks_AdminPageFramework_ImportOptions( $_FILES['__import'], $_POST['__import'] );	
		$strPressedFieldID = $oImport->getSiblingValue( 'field_id' );
		$strPressedInputID = $oImport->getSiblingValue( 'input_id' );
		$fMerge = $oImport->getSiblingValue( 'do_merge' );
		
		// Check if there is an upload error.
		if ( $oImport->getError() > 0 ) {
			$this->setSettingNotice( $this->oMsg->___( 'import_error' ) );	
			return $arrStoredOptions;	// do not change the framework's options.
		}
		
		// Apply filters to the uploaded file's MIME type.
		$arrMIMEType = $this->oUtil->addAndApplyFilters(
			$this,
			array( "import_mime_types_{$strPageSlug}_{$strTabSlug}", "import_mime_types_{$strPageSlug}", "import_mime_types_{$this->oProps->strClassName}_{$strPressedInputID}", "import_mime_types_{$this->oProps->strClassName}_{$strPressedFieldID}", "import_mime_types_{$this->oProps->strClassName}" ),
			array( 'text/plain', 'application/octet-stream' ),	// .json file is dealt as a binary file.
			$strPressedFieldID,
			$strPressedInputID
		);		

		// Check the uploaded file MIME type.
		if ( ! in_array( $oImport->getType(), $arrMIMEType ) ) {	
			$this->setSettingNotice( $this->oMsg->___( 'uploaded_file_type_not_supported' ) );
			return $arrStoredOptions;	// do not change the framework's options.
		}
		
		// Retrieve the importing data.
		$vData = $oImport->getImportData();
		if ( $vData === false ) {
			$this->setSettingNotice( $this->oMsg->___( 'could_not_load_importing_data' ) );		
			return $arrStoredOptions;	// do not change the framework's options.
		}
		
		// Apply filters to the data format type.
		$strFormatType = $this->oUtil->addAndApplyFilters(
			$this,
			array( "import_format_{$strPageSlug}_{$strTabSlug}", "import_format_{$strPageSlug}", "import_format_{$this->oProps->strClassName}_{$strPressedInputID}", "import_format_{$this->oProps->strClassName}_{$strPressedFieldID}", "import_format_{$this->oProps->strClassName}" ),
			$oImport->getFormatType(),	// the set format type, array, json, or text.
			$strPressedFieldID,
			$strPressedInputID
		);	// import_format_{$strPageSlug}_{$strTabSlug}, import_format_{$strPageSlug}, import_format_{$strClassName}_{pressed input id}, import_format_{$strClassName}_{pressed field id}, import_format_{$strClassName}		

		// Format it.
		$oImport->formatImportData( $vData, $strFormatType );	// it is passed as reference.	
		
		// If a custom option key is set,
		// Apply filters to the importing option key.
		$strImportOptionKey = $this->oUtil->addAndApplyFilters(
			$this,
			array( "import_option_key_{$strPageSlug}_{$strTabSlug}", "import_option_key_{$strPageSlug}", "import_option_key_{$this->oProps->strClassName}_{$strPressedInputID}", "import_option_key_{$this->oProps->strClassName}_{$strPressedFieldID}", "import_option_key_{$this->oProps->strClassName}" ),
			$oImport->getSiblingValue( 'import_option_key' ),	
			$strPressedFieldID,
			$strPressedInputID
		);	// import_option_key_{$strPageSlug}_{$strTabSlug}, import_option_key_{$strPageSlug}, import_option_key_{$strClassName}_{pressed input id}, import_option_key_{$strClassName}_{pressed field id}, import_option_key_{$strClassName}			
		
		// Apply filters to the importing data.
		$vData = $this->oUtil->addAndApplyFilters(
			$this,
			array( "import_{$strPageSlug}_{$strTabSlug}", "import_{$strPageSlug}", "import_{$this->oProps->strClassName}_{$strPressedInputID}", "import_{$this->oProps->strClassName}_{$strPressedFieldID}", "import_{$this->oProps->strClassName}" ),
			$vData,
			$arrStoredOptions,
			$strPressedFieldID,
			$strPressedInputID,
			$strFormatType,
			$strImportOptionKey,
			$fMerge
		);

		// Set the update notice
		$fEmpty = empty( $vData );
		$this->setSettingNotice( 
			$fEmpty ? $this->oMsg->___( 'not_imported_data' ) : $this->oMsg->___( 'imported_data' ), 
			$fEmpty ? 'error' : 'updated',
			$this->oProps->strOptionKey,	// message id
			false	// do not override 
		);
				
		if ( $strImportOptionKey != $this->oProps->strOptionKey ) {
			update_option( $strImportOptionKey, $vData );
			return $arrStoredOptions;	// do not change the framework's options.
		}
	
		// The option data to be saved will be returned.
		return $fMerge ?
			$this->oUtil->unitArrays( $vData, $arrStoredOptions )
			: $vData;
						
	}
	private function exportOptions( $vData, $strPageSlug, $strTabSlug ) {

		$oExport = new AmazonAutoLinks_AdminPageFramework_ExportOptions( $_POST['__export'], $this->oProps->strClassName );
		$strPressedFieldID = $oExport->getSiblingValue( 'field_id' );
		$strPressedInputID = $oExport->getSiblingValue( 'input_id' );
		
		// If the data is set in transient,
		$vData = $oExport->getTransientIfSet( $vData );
	
		// Get the field ID.
		$strFieldID = $oExport->getFieldID();
	
		// Add and apply filters. - adding filters must be done in this class because the callback method belongs to this class 
		// and the magic method should be triggered.			
		$vData = $this->oUtil->addAndApplyFilters(
			$this,
			array( "export_{$strPageSlug}_{$strTabSlug}", "export_{$strPageSlug}", "export_{$this->oProps->strClassName}_{$strPressedInputID}", "export_{$this->oProps->strClassName}_{$strPressedFieldID}", "export_{$this->oProps->strClassName}" ),
			$vData,
			$strPressedFieldID,
			$strPressedInputID
		);	// export_{$strPageSlug}_{$strTabSlug}, export_{$strPageSlug}, export_{$strClassName}_{pressed input id}, export_{$strClassName}_{pressed field id}, export_{$strClassName}	
		
		$strFileName = $this->oUtil->addAndApplyFilters(
			$this,
			array( "export_name_{$strPageSlug}_{$strTabSlug}", "export_name_{$strPageSlug}", "export_name_{$this->oProps->strClassName}_{$strPressedInputID}", "export_name_{$this->oProps->strClassName}_{$strPressedFieldID}", "export_name_{$this->oProps->strClassName}" ),
			$oExport->getFileName(),
			$strPressedFieldID,
			$strPressedInputID
		);	// export_name_{$strPageSlug}_{$strTabSlug}, export_name_{$strPageSlug}, export_name_{$strClassName}_{pressed input id}, export_name_{$strClassName}_{pressed field id}, export_name_{$strClassName}	
	
		$strFormatType = $this->oUtil->addAndApplyFilters(
			$this,
			array( "export_format_{$strPageSlug}_{$strTabSlug}", "export_format_{$strPageSlug}", "export_format_{$this->oProps->strClassName}_{$strPressedInputID}", "export_format_{$this->oProps->strClassName}_{$strPressedFieldID}", "export_format_{$this->oProps->strClassName}" ),
			$oExport->getFormat(),
			$strPressedFieldID,
			$strPressedInputID
		);	// export_format_{$strPageSlug}_{$strTabSlug}, export_format_{$strPageSlug}, export_format_{$strClassName}_{pressed input id}, export_format_{$strClassName}_{pressed field id}, export_format_{$strClassName}	
							
		$oExport->doExport( $vData, $strFileName, $strFormatType );
		exit;
		
	}
	
	/**
	 * Apples validation filters to the submitted input data.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Added the $strPressedFieldID and $strPressedInputID parameters.
	 * @return			array			The filtered input array.
	 */
	private function getFilteredOptions( $arrInput, $strPageSlug, $strTabSlug, $strPressedFieldID, $strPressedInputID ) {

		$arrStoredPageOptions = $this->getPageOptions( $strPageSlug ); 			

		// for tabs
		if ( $strTabSlug && $strPageSlug )	{
			$arrRegisteredSectionKeysForThisTab = isset( $arrInput[ $strPageSlug ] ) ? array_keys( $arrInput[ $strPageSlug ] ) : array();			
			$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$strPageSlug}_{$strTabSlug}", $arrInput, $arrStoredPageOptions );	// $arrInput: new values, $arrStoredPageOptions: old values
			$arrInput = $this->oUtil->uniteArraysRecursive( $arrInput, $this->getOtherTabOptions( $strPageSlug, $arrRegisteredSectionKeysForThisTab ) );
		}
		
		// for pages	
		if ( $strPageSlug )	{
			$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$strPageSlug}", $arrInput, $arrStoredPageOptions ); // $arrInput: new values, $arrStoredPageOptions: old values
			$arrInput = $this->oUtil->uniteArraysRecursive( $arrInput, $this->getOtherPageOptions( $strPageSlug ) );
		}

		// for the input ID
		if ( $strPressedInputID )
			$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProps->strClassName}_{$strPressedInputID}", $arrInput, $this->oProps->arrOptions );
		
		// for the field ID
		if ( $strPressedFieldID )
			$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProps->strClassName}_{$strPressedFieldID}", $arrInput, $this->oProps->arrOptions );
		
		// for the class
		$arrInput = $this->oUtil->addAndApplyFilter( $this, "validation_{$this->oProps->strClassName}", $arrInput, $this->oProps->arrOptions );

		return $arrInput;
	
	}	
	
	/**
	 * Retrieves the stored options of the given page slug.
	 * 
	 * Other pages' option data will not be contained in the returning array.
	 * This is used to pass the old option array to the validation callback method.
	 * 
	 * @since			2.0.0
	 * @return			array			the stored options of the given page slug. If not found, an empty array will be returned.
	 */ 
	private function getPageOptions( $strPageSlug ) {
				
		$arrStoredPageOptions = array();
		if ( isset( $this->oProps->arrOptions[ $strPageSlug ] ) )
			$arrStoredPageOptions[ $strPageSlug ] = $this->oProps->arrOptions[ $strPageSlug ];
		
		return $arrStoredPageOptions;
		
	}
	
	/**
	 * Retrieves the stored options excluding the currently specified tab's sections and their fields.
	 * 
	 * This is used to merge the submitted form data with the previously stored option data of the form elements 
	 * that belong to the in-page tab of the given page.
	 * 
	 * @since			2.0.0
	 * @return			array			the stored options excluding the currently specified tab's sections and their fields.
	 * 	 If not found, an empty array will be returned.
	 */ 
	private function getOtherTabOptions( $strPageSlug, $arrSectionKeysForTheTab ) {
	
		$arrOtherTabOptions = array();
		if ( isset( $this->oProps->arrOptions[ $strPageSlug ] ) )
			$arrOtherTabOptions[ $strPageSlug ] = $this->oProps->arrOptions[ $strPageSlug ];
			
		// Remove the elements of the given keys so that the other stored elements will remain. 
		// They are the other form section elements which need to be returned.
		foreach( $arrSectionKeysForTheTab as $arrSectionKey ) 
			unset( $arrOtherTabOptions[ $strPageSlug ][ $arrSectionKey ] );
			
		return $arrOtherTabOptions;
		
	}
	
	/**
	 * Retrieves the stored options excluding the key of the given page slug.
	 * 
	 * This is used to merge the submitted form input data with the previously stored option data except the given page.
	 * 
	 * @since			2.0.0
	 * @return			array			the array storing the options excluding the key of the given page slug. 
	 */ 
	private function getOtherPageOptions( $strPageSlug ) {
	
		$arrOtherPageOptions = $this->oProps->arrOptions;
		if ( isset( $arrOtherPageOptions[ $strPageSlug ] ) )
			unset( $arrOtherPageOptions[ $strPageSlug ] );
		return $arrOtherPageOptions;
		
	}
	
	/**
	 * Renders the registered setting fields.
	 * 
	 * @internal
	 * @since			2.0.0
	 * @remark			the protected scope is used because it's called from an extended class.
	 * @return			void
	 */ 
	protected function renderSettingField( $strFieldID, $strPageSlug ) {
			
		// If the specified field does not exist, do nothing.
		if ( ! isset( $this->oProps->arrFields[ $strFieldID ] ) ) return;	// if it is not added, return
		$arrField = $this->oProps->arrFields[ $strFieldID ];
		
		// Retrieve the field error array.
		$this->arrFieldErrors = isset( $this->arrFieldErrors ) ? $this->arrFieldErrors : $this->getFieldErrors( $strPageSlug ); 

		// Render the form field. 		
		$strFieldType = isset( $this->oProps->arrFieldTypeDefinitions[ $arrField['strType'] ]['callRenderField'] ) && is_callable( $this->oProps->arrFieldTypeDefinitions[ $arrField['strType'] ]['callRenderField'] )
			? $arrField['strType']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).

		$oField = new AmazonAutoLinks_AdminPageFramework_InputField( $arrField, $this->oProps->arrOptions, $this->arrFieldErrors, $this->oProps->arrFieldTypeDefinitions[ $strFieldType ], $this->oMsg );
		$strFieldOutput = $oField->getInputField( $strFieldType );	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.

		// echo $this->oUtil->addAndApplyFilter(
			// $this,
			// $this->oProps->strClassName . '_' .  self::$arrPrefixesForCallbacks['field_'] . $strFieldID,	// filter: class name + _ + section_ + section id
			// $strFieldOutput,
			// $arrField // the field array
		// );	

		echo $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				$this->oProps->strClassName . '_' .  self::$arrPrefixesForCallbacks['field_'] . $strFieldID,	// this filter will be deprecated
				self::$arrPrefixesForCallbacks['field_'] . $this->oProps->strClassName . '_' . $strFieldID	// field_ + {extended class name} + _ {field id}
			),
			$strFieldOutput,
			$arrField // the field array
		);
		
	}
	
	/**
	 * Retrieves the settings error array set by the user in the validation callback.
	 * 
	 * @since				2.0.0
	 * @since				2.1.2			Added the second parameter. 
	 */
	protected function getFieldErrors( $strPageSlug, $fDelete=true ) {
		
		// If a form submit button is not pressed, there is no need to set the setting errors.
		if ( ! isset( $_GET['settings-updated'] ) ) return null;
		
		// Find the transient.
		$strTransient = md5( $this->oProps->strClassName . '_' . $strPageSlug );
		$arrFieldErrors = get_transient( $strTransient );
		if ( $fDelete )
			delete_transient( $strTransient );	
		return $arrFieldErrors;

	}
	
	/**
	 * Sets the field error array. 
	 * 
	 * This is normally used in validation callback methods. when submitted data have an issue.
	 * This method saves the given array in a temporary area( transient ) of the options database table.
	 * 
	 * <h4>Example</h4>
	 * <code>public function validation_first_page_verification( $arrInput, $arrOldPageOptions ) {	// valication_ + page slug + _ + tab slug			
	 *		$fVerified = true;
	 *		$arrErrors = array();
	 *		// Check if the submitted value meets your criteria. As an example, here a numeric value is expected.
	 *		if ( isset( $arrInput['first_page']['verification']['verify_text_field'] ) && ! is_numeric( $arrInput['first_page']['verification']['verify_text_field'] ) ) {
	 *			// Start with the section key in $arrErrors, not the key of page slug.
	 *			$arrErrors['verification']['verify_text_field'] = 'The value must be numeric: ' . $arrInput['first_page']['verification']['verify_text_field'];	
	 *			$fVerified = false;
	 *		}
	 *		// An invalid value is found.
	 *		if ( ! $fVerified ) {
	 *			// Set the error array for the input fields.
	 *			$this->setFieldErrors( $arrErrors );		
	 *			$this->setSettingNotice( 'There was an error in your input.' );
	 *			return $arrOldPageOptions;
	 *		}
	 *		return $arrInput;
	 *	}</code>
	 * 
	 * @since			2.0.0
	 * @remark			the transient name is a MD5 hash of the extended class name + _ + page slug ( the passed ID )
	 * @param			array			$arrErrors			the field error array. The structure should follow the one contained in the submitted $_POST array.
	 * @param			string			$strID				this should be the page slug of the page that has the dealing form field.
	 * @param			integer			$numSavingDuration	the transient's lifetime. 300 seconds means 5 minutes.
	 */ 
	protected function setFieldErrors( $arrErrors, $strID=null, $numSavingDuration=300 ) {
		
		$strID = isset( $strID ) ? $strID : ( isset( $_POST['strPageSlug'] ) ? $_POST['strPageSlug'] : ( isset( $_GET['page'] ) ? $_GET['page'] : $this->oProps->strClassName ) );	
		set_transient( md5( $this->oProps->strClassName . '_' . $strID ), $arrErrors, $numSavingDuration );	// store it for 5 minutes ( 60 seconds * 5 )
	
	}

	/**
	 * Renders the filtered section description.
	 * 
	 * @internal
	 * @since			2.0.0
	 * @remark			the protected scope is used because it's called from an extended class.
	 * @remark			This is the redirected callback for the section description method from __call().
	 * @return			void
	 */ 	
	protected function renderSectionDescription( $strMethodName ) {		

		$strSectionID = substr( $strMethodName, strlen( 'section_pre_' ) );	// X will be the section ID in section_pre_X
		
		if ( ! isset( $this->oProps->arrSections[ $strSectionID ] ) ) return;	// if it is not added

		// echo  $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			// $this,
			// $this->oProps->strClassName . '_' .  self::$arrPrefixesForCallbacks['section_'] . $strSectionID,	// class name + _ + section_ + section id
			// '<p>' . $this->oProps->arrSections[ $strSectionID ]['strDescription'] . '</p>',	 // the p-tagged description string
			// $this->oProps->arrSections[ $strSectionID ]['strDescription']	// the original description
		// );		
		
		echo $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				$this->oProps->strClassName . '_' .  self::$arrPrefixesForCallbacks['section_'] . $strSectionID,	// this filter will be deprecated
				self::$arrPrefixesForCallbacks['section_'] . $this->oProps->strClassName . '_' . $strSectionID	// section_ + {extended class name} + _ {section id}
			),
			'<p>' . $this->oProps->arrSections[ $strSectionID ]['strDescription'] . '</p>',	 // the p-tagged description string
			$this->oProps->arrSections[ $strSectionID ]['strDescription']	// the original description
		);		
			
	}
	
	/**
	 * Retrieves the page slug that the settings section belongs to.		
	 * 
	 * @since			2.0.0
	 * @return			string|null
	 */ 
	private function getPageSlugBySectionID( $strSectionID ) {
		return isset( $this->oProps->arrSections[ $strSectionID ]['strPageSlug'] )
			? $this->oProps->arrSections[ $strSectionID ]['strPageSlug']
			: null;			
	}
	
	/**
	 * Registers the setting sections and fields.
	 * 
	 * This methods passes the stored section and field array contents to the <em>add_settings_section()</em> and <em>add_settings_fields()</em> functions.
	 * Then perform <em>register_setting()</em>.
	 * 
	 * The filters will be applied to the section and field arrays; that means that third-party scripts can modify the arrays.
	 * Also they get sorted before being registered based on the set order.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Added the ability to define custom field types.
	 * @remark			This method is not intended to be used by the user.
	 * @remark			The callback method for the <em>admin_menu</em> hook.
	 * @return			void
	 */ 
	public function registerSettings() {
		
		// Format ( sanitize ) the section and field arrays.
		$this->oProps->arrSections = $this->formatSectionArrays( $this->oProps->arrSections );
		$this->oProps->arrFields = $this->formatFieldArrays( $this->oProps->arrFields );	// must be done after the formatSectionArrays().
				
		// If there is no section or field to add, do nothing.
		if ( 
			$GLOBALS['pagenow'] != 'options.php'
			&& ( count( $this->oProps->arrSections ) == 0 || count( $this->oProps->arrFields ) == 0 ) 
		) return;
				
		// Define field types.
		// This class adds filters for the field type definitions so that framework's default field types will be added.
		new AmazonAutoLinks_AdminPageFramework_BuiltinInputFieldTypeDefinitions( $this->oProps->arrFieldTypeDefinitions, $this->oProps->strClassName, $this->oMsg );
// var_dump( $this->oProps->arrFieldTypeDefinitions );		
		$this->oProps->arrFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			$this,
			self::$arrPrefixesForCallbacks['field_types_'] . $this->oProps->strClassName,	// 'field_types_' . {extended class name}
			$this->oProps->arrFieldTypeDefinitions
		);		
// var_dump( $this->oProps->arrFieldTypeDefinitions );
		// Register settings sections 
		uasort( $this->oProps->arrSections, array( $this->oProps, 'sortByOrder' ) ); 
		foreach( $this->oProps->arrSections as $arrSection ) {
			add_settings_section(	// Add the given section
				$arrSection['strSectionID'],	//  section ID
				"<a id='{$arrSection['strSectionID']}'></a>" . $arrSection['strTitle'],		// title - place the anchor in front of the title.
				array( $this, 'section_pre_' . $arrSection['strSectionID'] ), 				// callback function -  this will trigger the __call() magic method.
				$arrSection['strPageSlug']	// page
			);
			// For the contextual help pane,
			if ( ! empty( $arrSection['strHelp'] ) )
				$this->addHelpTab( 
					array(
						'strPageSlug'				=> $arrSection['strPageSlug'],
						'strPageTabSlug'			=> $arrSection['strTabSlug'],
						'strHelpTabTitle'			=> $arrSection['strTitle'],
						'strHelpTabID'				=> $arrSection['strSectionID'],
						'strHelpTabContent'			=> $arrSection['strHelp'],
						'strHelpTabSidebarContent'	=> $arrSection['strHelpAside'] ? $arrSection['strHelpAside'] : "",
					)
				);
				
		}
		
		// Register settings fields
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		uasort( $this->oProps->arrFields, array( $this->oProps, 'sortByOrder' ) ); 
		foreach( $this->oProps->arrFields as $arrField ) {

			add_settings_field(	// Add the given field.
				$arrField['strFieldID'],
				"<a id='{$arrField['strFieldID']}'></a><span title='{$arrField['strTip']}'>{$arrField['strTitle']}</span>",
				array( $this, 'field_pre_' . $arrField['strFieldID'] ),	// callback function - will trigger the __call() magic method.
				$this->getPageSlugBySectionID( $arrField['strSectionID'] ), // page slug
				$arrField['strSectionID'],	// section
				$arrField['strFieldID']		// arguments - pass the field ID to the callback function
			);	

			// Set relevant scripts and styles for the input field.
			$this->setFieldHeadTagElements( $arrField );
			
			// For the contextual help pane,
			if ( ! empty( $arrField['strHelp'] ) )
				$this->addHelpTab( 
					array(
						'strPageSlug'				=> $arrField['strPageSlug'],
						'strPageTabSlug'			=> $arrField['strTabSlug'],
						'strHelpTabTitle'			=> $arrField['strSectionTitle'],
						'strHelpTabID'				=> $arrField['strSectionID'],
						'strHelpTabContent'			=> "<span class='contextual-help-tab-title'>" . $arrField['strTitle'] . "</span> - " . PHP_EOL
														. $arrField['strHelp'],
						'strHelpTabSidebarContent'	=> $arrField['strHelpAside'] ? $arrField['strHelpAside'] : "",
					)
				);

		}
		
		// Set the form enabling flag so that the <form></form> tag will be inserted in the page.
		$this->oProps->fEnableForm = true;
		register_setting(	
			$this->oProps->strOptionKey,	// the option group name.	
			$this->oProps->strOptionKey,	// the option key name that will be stored in the option table in the database.
			array( $this, 'validation_pre_' . $this->oProps->strClassName )	// validation method
		); 
		
	}
		/**
		 * Sets the given field type's enqueuing scripts and styles.
		 * 
		 * A helper function for the above registerSettings() method.
		 * 
		 * @since			2.1.5
		 */
		private function setFieldHeadTagElements( $arrField ) {
			
			$strFieldType = $arrField['strType'];
			
			// Set the global flag to indicate whether the elements are already added and enqueued.
			if ( isset( $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'][ $strFieldType ] ) && $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'][ $strFieldType ] ) return;
			$GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'][ $strFieldType ] = true;

			// If the field type is not defined, return.
			if ( ! isset( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ] ) ) return;

			if ( is_callable( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callFieldLoader'] ) )
				call_user_func_array( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callFieldLoader'], array() );		
			
			if ( is_callable( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetScripts'] ) )
				$this->oProps->strScript .= call_user_func_array( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetScripts'], array() );
				
			if ( is_callable( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetStyles'] ) )
				$this->oProps->strStyle .= call_user_func_array( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetStyles'], array() );
				
			if ( is_callable( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetIEStyles'] ) )
				$this->oProps->strStyleIE .= call_user_func_array( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetIEStyles'], array() );					

				
			$this->oHeadTag->enqueueStyles( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['arrEnqueueStyles'] );
			$this->oHeadTag->enqueueScripts( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['arrEnqueueScripts'] );
					
		}
	
	
	/**
	 * Formats the given section arrays.
	 * 
	 * @since			2.0.0
	 */ 
	private function formatSectionArrays( $arrSections ) {

		// Apply filters to let other scripts to add sections.
		$arrSections = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			$this,
			"{$this->oProps->strClassName}_setting_sections",
			$arrSections
		);
		
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;
		
		// Since the section array may have been modified, sanitize the elements and 
		// apply the conditions to remove unnecessary elements and put new orders.
		$arrNewSectionArray = array();
		foreach( $arrSections as $arrSection ) {
		
			$arrSection = $arrSection + self::$arrStructure_Section;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name, the slugs as well.
			$arrSection['strSectionID'] = $this->oUtil->sanitizeSlug( $arrSection['strSectionID'] );
			$arrSection['strPageSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strPageSlug'] );
			$arrSection['strTabSlug'] = $this->oUtil->sanitizeSlug( $arrSection['strTabSlug'] );
			
			// Check the mandatory keys' values.
			if ( ! isset( $arrSection['strSectionID'], $arrSection['strPageSlug'] ) ) continue;	// these keys are necessary.
			
			// If the page slug does not match the current loading page, there is no need to register form sections and fields.
			if ( $GLOBALS['pagenow'] != 'options.php' && ! $strCurrentPageSlug || $strCurrentPageSlug !=  $arrSection['strPageSlug'] ) continue;				

			// If this section does not belong to the currently loading page tab, skip.
			if ( ! $this->isSettingSectionOfCurrentTab( $arrSection ) )  continue;
			
			// If the access level is set and it is not sufficient, skip.
			$arrSection['strCapability'] = isset( $arrSection['strCapability'] ) ? $arrSection['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrSection['strCapability'] ) ) continue;	// since 1.0.2.1
		
			// If a custom condition is set and it's not true, skip,
			if ( $arrSection['fIf'] !== true ) continue;
		
			// Set the order.
			$arrSection['numOrder']	= is_numeric( $arrSection['numOrder'] ) ? $arrSection['numOrder'] : count( $arrNewSectionArray ) + 10;
		
			// Add the section array to the returning array.
			$arrNewSectionArray[ $arrSection['strSectionID'] ] = $arrSection;
			
		}
		return $arrNewSectionArray;
		
	}
	
	/**
	 * Checks if the given section belongs to the currently loading tab.
	 * 
	 * @since			2.0.0
	 * @return			boolean			Returns true if the section belongs to the current tab page. Otherwise, false.
	 */ 	
	private function isSettingSectionOfCurrentTab( $arrSection ) {

		// Determine: 
		// 1. if the current tab matches the given tab slug. Yes -> the section should be registered.
		// 2. if the current page is the default tab. Yes -> the section should be registered.

		// If the tab slug is not specified, it means that the user wants the section to be visible in the page regardless of tabs.
		if ( ! isset( $arrSection['strTabSlug'] ) ) return true;
		
		// 1. If the checking tab slug and the current loading tab slug is the same, it should be registered.
		$strCurrentTab =  isset( $_GET['tab'] ) ? $_GET['tab'] : null;
		if ( $arrSection['strTabSlug'] == $strCurrentTab )  return true;

		// 2. If $_GET['tab'] is not set and the page slug is stored in the tab array, 
		// consider the default tab which should be loaded without the tab query value in the url
		$strPageSlug = $arrSection['strPageSlug'];
		if ( ! isset( $_GET['tab'] ) && isset( $this->oProps->arrInPageTabs[ $strPageSlug ] ) ) {
		
			$strDefaultTabSlug = isset( $this->oProps->arrDefaultInPageTabs[ $strPageSlug ] ) ? $this->oProps->arrDefaultInPageTabs[ $strPageSlug ] : '';
			if ( $strDefaultTabSlug  == $arrSection['strTabSlug'] ) return true;		// should be registered.			
				
		}
				
		// Otherwise, false.
		return false;
		
	}	
	
	/**
	 * Formats the given field arrays.
	 * 
	 * @since			2.0.0
	 */ 
	private function formatFieldArrays( $arrFields ) {
		
		// Apply filters to let other scripts to add fields.
		$arrFields = $this->oUtil->addAndApplyFilter(	// Parameters: $oCallerObject, $arrFilters, $vInput, $vArgs...
			$this,
			"{$this->oProps->strClassName}_setting_fields",
			$arrFields
		); 
		
		// Apply the conditions to remove unnecessary elements and put new orders.
		$arrNewFieldArrays = array();
		foreach( $arrFields as $arrField ) {
		
			if ( ! is_array( $arrField ) ) continue;		// the element must be an array.
			
			$arrField = $arrField + self::$arrStructure_Field;	// avoid undefined index warnings.
			
			// Sanitize the IDs since they are used as a callback method name.
			$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
			$arrField['strSectionID'] = $this->oUtil->sanitizeSlug( $arrField['strSectionID'] );
			
			// If the section that this field belongs to is not set, no need to register this field.
			// The $arrSection property must be formatted prior to perform this method.
			if ( ! isset( $this->oProps->arrSections[ $arrField['strSectionID'] ] ) ) continue;
			
			// Check the mandatory keys' values.
			if ( ! isset( $arrField['strFieldID'], $arrField['strSectionID'], $arrField['strType'] ) ) continue;	// these keys are necessary.
			
			// If the access level is not sufficient, skip.
			$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrField['strCapability'] ) ) continue; 
						
			// If the condition is not met, skip.
			if ( $arrField['fIf'] !== true ) continue;
						
			// Set the order.
			$arrField['numOrder']	= is_numeric( $arrField['numOrder'] ) ? $arrField['numOrder'] : count( $arrNewFieldArrays ) + 10;
			
			// Set the tip, option key, instantiated class name, and page slug elements.
			$arrField['strTip'] = strip_tags( isset( $arrField['strTip'] ) ? $arrField['strTip'] : $arrField['strDescription'] );
			$arrField['strOptionKey'] = $this->oProps->strOptionKey;
			$arrField['strClassName'] = $this->oProps->strClassName;
			// $arrField['strPageSlug'] = isset( $_GET['page'] ) ? $_GET['page'] : null;
			$arrField['strPageSlug'] = $this->oProps->arrSections[ $arrField['strSectionID'] ]['strPageSlug'];
			$arrField['strTabSlug'] = $this->oProps->arrSections[ $arrField['strSectionID'] ]['strTabSlug'];
			$arrField['strSectionTitle'] = $this->oProps->arrSections[ $arrField['strSectionID'] ]['strTitle'];	// used for the contextual help pane.
			
			// Add the element to the new returning array.
			$arrNewFieldArrays[ $arrField['strFieldID'] ] = $arrField;
				
		}
		return $arrNewFieldArrays;
		
	}
	
	/**
	 * Retrieves the specified field value stored in the options.
	 * 
	 * Useful when you don't know the section name but it's a bit slower than accessing the property value by specifying the section name.
	 * 
	 * @since			2.1.2
	 */
	protected function getFieldValue( $strFieldNameToFind ) {

		foreach( $this->oProps->arrOptions as $strPageSlug => $arrSections )  
			foreach( $arrSections as $strSectionName => $arrFields ) 
				foreach( $arrFields as $strFieldName => $vValue ) 
					if ( trim( $strFieldNameToFind ) == trim( $strFieldName ) )
						return $vValue;	
		
		return null;
	}

}
endif; 

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework' ) ) :
/**
 * The main class of the framework. 
 * 
 * The user should extend this class and define the set-ups in the setUp() method. Most of the public methods are for hook callbacks and the private methods are internal helper functions. So the protected methods are for the users.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor. This will be triggered in any admin page.</li>
 * 	<li><code>load_ + extended class name</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>load_ + page slug</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>load_ + page slug + _ + tab slug</code>[2.1.0+] – triggered when the framework's page is loaded before the header gets sent. This will not be triggered in the admin pages that are not registered by the framework.</li>
 * 	<li><code>do_before_ + extended class name</code> – triggered before rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_before_ + page slug</code> – triggered before rendering the page.</li>
 * 	<li><code>do_before_ + page slug + _ + tab slug</code> – triggered before rendering the page.</li>
 * 	<li><code>do_ + extended class name</code> – triggered in the middle of rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_ + page slug</code> – triggered in the middle of rendering the page.</li>
 * 	<li><code>do_ + page slug + _ + tab slug</code> – triggered in the middle of rendering the page.</li>
 * 	<li><code>do_after_ + extended class name</code> – triggered after rendering the page. It applies to all pages created by the instantiated class object.</li>
 * 	<li><code>do_after_ + page slug</code> – triggered after rendering the page.</li>
 * 	<li><code>do_after_ + page slug + _ + tab slug</code> – triggered after rendering the page.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>head_ + page slug</code> – receives the output of the top part of the page.</li>
 * 	<li><code>head_ + page slug + _ + tab slug</code> – receives the output of the top part of the page.</li>
 * 	<li><code>head_ + extended class name</code> – receives the output of the top part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>content_ + page slug</code> – receives the output of the middle part of the page including form input fields.</li>
 * 	<li><code>content_ + page slug + _ + tab slug</code> – receives the output of the middle part of the page including form input fields.</li>
 * 	<li><code>content_ + extended class name</code> – receives the output of the middle part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>foot_ + page slug</code> – receives the output of the bottom part of the page.</li>
 * 	<li><code>foot_ + page slug + _ + tab slug</code> – receives the output of the bottom part of the page.</li>
 * 	<li><code>foot_ + extended class name</code> – receives the output of the bottom part of the page, applied to all pages created by the instantiated class object.</li>
 * 	<li><code>section_ + extended class name + _ + section ID</code> – receives the description output of the given form section ID. The first parameter: output string. The second parameter: the array of option.</li> 
 * 	<li><code>field_ + extended class name + _ + field ID</code> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>validation_ + page slug + _ + tab slug</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>validation_ + page slug</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>validation_ + extended class name + _ + input id</code> – [2.1.5+] receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The input ID is the one used to the name attribute of the submit input tag. For a submit button that is inserted without using the framework's method, it will not take effect.</li>
 * 	<li><code>validation_ + extended class name + _ + field id</code> – [2.1.5+] receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database. The field ID is the one that is passed to the field array to create the submit input field.</li>
 * 	<li><code>validation_ + extended class name</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * 	<li><code>style_ + page slug + _ + tab slug</code> – receives the output of the CSS rules applied to the tab page of the slug.</li>
 * 	<li><code>style_ + page slug</code> – receives the output of the CSS rules applied to the page of the slug.</li>
 * 	<li><code>style_ + extended class name</code> – receives the output of the CSS rules applied to the pages added by the instantiated class object.</li>
 * 	<li><code>script_ + page slug + _ + tab slug</code> – receives the output of the JavaScript script applied to the tab page of the slug.</li>
 * 	<li><code>script_ + page slug</code> – receives the output of the JavaScript script applied to the page of the slug.</li>
 * 	<li><code>script_ + extended class name</code> – receives the output of the JavaScript script applied to the pages added by the instantiated class object.</li>
 * 	<li><code>export_ + page slug + _ + tab slug</code> – receives the exporting array sent from the tab page.</li>
 * 	<li><code>export_ + page slug</code> – receives the exporting array submitted from the page.</li>
 * 	<li><code>export_ + extended class name + _ + input id</code> – [2.1.5+] receives the exporting array submitted from the specific export button.</li>
 * 	<li><code>export_ + extended class name + _ + field id</code> – [2.1.5+] receives the exporting array submitted from the specific field.</li>
 * 	<li><code>export_ + extended class name</code> – receives the exporting array submitted from the plugin.</li>
 * 	<li><code>import_ + page slug + _ + tab slug</code> – receives the importing array submitted from the tab page.</li>
 * 	<li><code>import_ + page slug</code> – receives the importing array submitted from the page.</li>
 * 	<li><code>import_ + extended class name + _ + input id</code> – [2.1.5+] receives the importing array submitted from the specific import button.</li>
 * 	<li><code>import_ + extended class name + _ + field id</code> – [2.1.5+] receives the importing array submitted from the specific import field.</li>
 * 	<li><code>import_ + extended class name</code> – receives the importing array submitted from the plugin.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>
 * <h3>Examples</h3>
 * <p>If the extended class name is Sample_Admin_Pages, defining the following class method will embed a banner image in all pages created by the class.</p>
 * <code>class Sample_Admin_Pages extends AmazonAutoLinks_AdminPageFramework {
 * ...
 *     function head_Sample_Admin_Pages( $strContent ) {
 *         return '&lt;div style="float:right;"&gt;&lt;img src="' . plugins_url( 'img/banner468x60.gif', __FILE__ ) . '" /&gt;&lt;/div&gt;' 
 *             . $strContent;
 *     }
 * ...
 * }</code>
 * <p>If the created page slug is my_first_setting_page, defining the following class method will filter the middle part of the page output.</p>
 * <code>class Sample_Admin_Pages extends AmazonAutoLinks_AdminPageFramework {
 * ...
 *     function content_my_first_setting_page( $strContent ) {
 *         return $strContent . '&lt;p&gt;Hello world!&lt;/p&gt;';
 *     }
 * ...
 * }</code>
 * <h3>Timing of Hooks</h3>
 * <blockquote>------ When the class is instantiated ------
 *  
 *  start_ + extended class name
 *  load_ + extended class name
 *  load_ + page slug
 *  load_ + page slug + _ + tab slug
 *  
 *  ------ Start Rendering HTML ------
 *  
 *  &lt;head&gt;
 *      &lt;style type="text/css" name="admin-page-framework"&gt;
 *          style_ + page slug + _ + tab slug
 *          style_ + page slug
 *          style_ + extended class name
 *          script_ + page slug + _ + tab slug
 *          script_ + page slug
 *          script_ + extended class name       
 *      &lt;/style&gt;
 *  
 *  &lt;/head&gt;
 *  
 *  do_before_ + extended class name
 *  do_before_ + page slug
 *  do_before_ + page slug + _ + tab slug
 *  
 *  &lt;div class="wrap"&gt;
 *  
 *      head_ + page slug + _ + tab slug
 *      head_ + page slug
 *      head_ + extended class name                 
 *  
 *      &lt;div class="acmin-page-framework-container"&gt;
 *          &lt;form action="options.php" method="post"&gt;
 *  
 *              do_form_ + page slug + _ + tab slug
 *              do_form_ + page slug
 *              do_form_ + extended class name
 *  
 *              extended class name + _ + section_ + section ID
 *              extended class name + _ + field_ + field ID
 *  
 *              content_ + page slug + _ + tab slug
 *              content_ + page slug
 *              content_ + extended class name
 *  
 *              do_ + extended class name                   
 *              do_ + page slug
 *              do_ + page slug + _ + tab slug
 *  
 *          &lt;/form&gt;                 
 *      &lt;/div&gt;
 *  
 *          foot_ + page slug + _ + tab slug
 *          foot_ + page slug
 *          foot_ + extended class name         
 *  
 *  &lt;/div&gt;
 *  
 *  do_after_ + extended class name
 *  do_after_ + page slug
 *  do_after_ + page slug + _ + tab slug
 *  
 *  
 *  ----- After Submitting the Form ------
 *  
 *  validation_ + page slug + _ + tab slug 
 *  validation_ + page slug 
 *  validation_ + extended class name + _ + submit button input id
 *  validation_ + extended class name + _ + submit button field id
 *  validation_ + extended class name 
 *  export_ + page slug + _ + tab slug 
 *  export_ + page slug 
 *  export_ + extended class name
 *  import_ + page slug + _ + tab slug
 *  import_ + page slug
 *  import_ + extended class name</blockquote>
 * @abstract
 * @since			2.0.0
 * @use				AmazonAutoLinks_AdminPageFramework_Properties
 * @use				AmazonAutoLinks_AdminPageFramework_Debug
 * @use				AmazonAutoLinks_AdminPageFramework_Properties
 * @use				AmazonAutoLinks_AdminPageFramework_Messages
 * @use				AmazonAutoLinks_AdminPageFramework_Link
 * @use				AmazonAutoLinks_AdminPageFramework_Utilities
 * @remark			This class stems from several abstract classes.
 * @extends			AmazonAutoLinks_AdminPageFramework_SettingsAPI
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Page
 */
abstract class AmazonAutoLinks_AdminPageFramework extends AmazonAutoLinks_AdminPageFramework_SettingsAPI {
		
	/**
    * The common properties shared among sub-classes. 
	* 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AmazonAutoLinks_AdminPageFramework_Properties will be assigned in the constructor.
    */		
	protected $oProps;	
	
	/**
    * The object that provides the debug methods. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AmazonAutoLinks_AdminPageFramework_Debug will be assigned in the constructor.
    */		
	protected $oDebug;
	
	/**
    * Provides the methods for text messages of the framework. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AmazonAutoLinks_AdminPageFramework_Messages will be assigned in the constructor.
    */	
	protected $oMsg;
	
	/**
    * Provides the methods for creating HTML link elements. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AmazonAutoLinks_AdminPageFramework_Link will be assigned in the constructor.
    */		
	protected $oLink;
	
	/**
    * Provides the utility methods. 
	* @since			2.0.0
	* @access		protected
	* @var			object			an instance of AmazonAutoLinks_AdminPageFramework_Utilities will be assigned in the constructor.
    */			
	protected $oUtil;
	
	/**
	 * Provides the methods to insert head tag elements.
	 * 
	 * @since			2.1.5
	 * @access			protected
	 * @var				object			an instance of AmazonAutoLinks_AdminPageFramework_HeadTag_Pages will be assigne in the constructor.
	 */
	protected $oHeadTag;
	
	/**
	 * The constructor of the main class.
	 * 
	 * <h4>Example</h4>
	 * <code>if ( is_admin() )
	 * 		new MyAdminPageClass( 'my_custom_option_key', __FILE__ );
	 * </code>
	 * 
	 * @access			public
	 * @since			2.0.0
	 * @param			string		$strOptionKey			( optional ) specifies the option key name to store in the options table. If this is not set, the extended class name will be used.
	 * @param			string		$strCallerPath			( optional ) used to retrieve the plugin/theme details to auto-insert the information into the page footer.
	 * @param			string		$strCapability			( optional ) sets the overall access level to the admin pages created by the framework. The used capabilities are listed here( http://codex.wordpress.org/Roles_and_Capabilities ). If not set, <strong>manage_options</strong> will be assigned by default. The capability can be set per page, tab, setting section, setting field.
	 * @param			string		$strTextDomain			( optional ) the text domain( http://codex.wordpress.org/I18n_for_WordPress_Developers#Text_Domains ) used for the framework's text strings. Default: admin-page-framework.
	 * @remark			the scope is public because often <code>parent::__construct()</code> is used.
	 * @return			void		returns nothing.
	 */
	public function __construct( $strOptionKey=null, $strCallerPath=null, $strCapability=null, $strTextDomain='admin-page-framework' ){
				 
		// Variables
		$strClassName = get_class( $this );
		
		// Objects
		$this->oProps = new AmazonAutoLinks_AdminPageFramework_Properties( $this, $strClassName, $strOptionKey, $strCapability );
		$this->oMsg = AmazonAutoLinks_AdminPageFramework_Messages::instantiate( $strTextDomain );
		$this->oPageLoadStats = AmazonAutoLinks_AdminPageFramework_PageLoadStats_Page::instantiate( $this->oProps, $this->oMsg );
		$this->oUtil = new AmazonAutoLinks_AdminPageFramework_Utilities;
		$this->oDebug = new AmazonAutoLinks_AdminPageFramework_Debug;
		$this->oLink = new AmazonAutoLinks_AdminPageFramework_Link( $this->oProps, $strCallerPath, $this->oMsg );
		$this->oHeadTag = new AmazonAutoLinks_AdminPageFramework_HeadTag_Pages( $this->oProps );
								
		if ( is_admin() ) {

			// Hook the menu action - adds the menu items.
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
			
			// AmazonAutoLinks_AdminPageFramework_Menu
			add_action( 'admin_menu', array( $this, 'buildMenus' ), 98 );
			
			// AmazonAutoLinks_AdminPageFramework_Page
			add_action( 'admin_menu', array( $this, 'finalizeInPageTabs' ), 99 );	// must be called before the registerSettings() method.
			
			// AmazonAutoLinks_AdminPageFramework_SettingsAPI
			add_action( 'admin_menu', array( $this, 'registerSettings' ), 100 );
			
			// Redirect Buttons
			add_action( 'admin_init', array( $this, 'checkRedirects' ) );
						
			// The contextual help pane.
			add_action( "admin_head", array( $this, 'registerHelpTabs' ), 200 );
						
			// The capability for the settings. $this->oProps->strOptionKey is the part that is set in the settings_fields() function.
			// This prevents the "Cheatin' huh?" message.
			add_filter( "option_page_capability_{$this->oProps->strOptionKey}", array( $this->oProps, 'getCapability' ) );
						
			// For earlier loading than $this->setUp
			$this->oUtil->addAndDoAction( $this, self::$arrPrefixes['start_'] . $this->oProps->strClassName );
		
		}
	}	
		
	/**
	 * The magic method which redirects callback-function calls with the pre-defined prefixes for hooks to the appropriate methods. 
	 * 
	 * @access			public
	 * @remark			the users do not need to call or extend this method unless they know what they are doing.
	 * @param			string		$strMethodName		the called method name. 
	 * @param			array		$arrArgs			the argument array. The first element holds the parameters passed to the called method.
	 * @return			mixed		depends on the called method. If the method name matches one of the hook prefixes, the redirected methods return value will be returned. Otherwise, none.
	 * @since			2.0.0
	 */
	public function __call( $strMethodName, $arrArgs=null ) {		
				 
		// Variables
		// The currently loading in-page tab slug. Careful that not all cases $strMethodName have the page slug.
		$strPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : null;	
		$strTabSlug = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->oProps->getDefaultInPageTab( $strPageSlug );	

		// If it is a pre callback method, call the redirecting method.
		// add_settings_section() callback
		if ( substr( $strMethodName, 0, strlen( 'section_pre_' ) )	== 'section_pre_' ) return $this->renderSectionDescription( $strMethodName );  // section_pre_
		
		// add_settings_field() callback
		if ( substr( $strMethodName, 0, strlen( 'field_pre_' ) )	== 'field_pre_' ) return $this->renderSettingField( $arrArgs[ 0 ], $strPageSlug );  // field_pre_
		
		// register_setting() callback
		if ( substr( $strMethodName, 0, strlen( 'validation_pre_' ) )	== 'validation_pre_' ) return $this->doValidationCall( $strMethodName, $arrArgs[ 0 ] );  // section_pre_

		// load-{page} callback
		if ( substr( $strMethodName, 0, strlen( 'load_pre_' ) )	== 'load_pre_' ) return $this->doPageLoadCall( substr( $strMethodName, strlen( 'load_pre_' ) ), $strTabSlug, $arrArgs[ 0 ] );  // load_pre_

		// The callback of the call_page_{page slug} action hook
		if ( $strMethodName == $this->oProps->strClassHash . '_page_' . $strPageSlug )
			return $this->renderPage( $strPageSlug, $strTabSlug );	
		
		// If it's one of the framework's callback methods, do nothing.	
		if ( $this->isFrameworkCallbackMethod( $strMethodName ) )
			return isset( $arrArgs[0] ) ? $arrArgs[0] : null;	// if $arrArgs[0] is set, it's a filter, otherwise, it's an action.		

		
	}	
	
	/**
	 * Determines whether the method name matches the pre-defined hook prefixes.
	 * @access			private
	 * @since			2.0.0
	 * @remark			the users do not need to call or extend this method unless they know what they are doing.
	 * @param			string			$strMethodName			the called method name
	 * @return			boolean			If it is a framework's callback method, returns true; otherwise, false.
	 */
	private function isFrameworkCallbackMethod( $strMethodName ) {

		if ( substr( $strMethodName, 0, strlen( "{$this->oProps->strClassName}_" ) ) == "{$this->oProps->strClassName}_" )	// e.g. {instantiated class name} + _field_ + {field id}
			return true;
		
		if ( substr( $strMethodName, 0, strlen( "validation_{$this->oProps->strClassName}_" ) ) == "validation_{$this->oProps->strClassName}_" )	// e.g. validation_{instantiated class name}_ + {field id / input id}
			return true;

		if ( substr( $strMethodName, 0, strlen( "field_types_{$this->oProps->strClassName}" ) ) == "field_types_{$this->oProps->strClassName}" )	// e.g. field_types_{instantiated class name}
			return true;
			
		foreach( self::$arrPrefixes as $strPrefix ) {
			if ( substr( $strMethodName, 0, strlen( $strPrefix ) )	== $strPrefix  ) 
				return true;
		}
		return false;
	}
	
	/**
	* The method for all the necessary set-ups.
	* 
	* The users should override this method to set-up necessary settings. 
	* To perform certain tasks prior to this method, use the <em>start_ + extended class name</em> hook that is triggered at the end of the class constructor.
	* 
	* <h4>Example</h4>
	* <code>public function setUp() {
	* 	$this->setRootMenuPage( 'APF Form' ); 
	* 	$this->addSubMenuItems(
	* 		array(
	* 			'strPageTitle' => 'Form Fields',
	* 			'strPageSlug' => 'apf_form_fields',
	* 		)
	* 	);		
	* 	$this->addSettingSections(
	* 		array(
	* 			'strSectionID'		=> 'text_fields',
	* 			'strPageSlug'		=> 'apf_form_fields',
	* 			'strTitle'			=> 'Text Fields',
	* 			'strDescription'	=> 'These are text type fields.',
	* 		)
	* 	);
	* 	$this->addSettingFields(
	* 		array(	
	* 			'strFieldID' => 'text',
	* 			'strSectionID' => 'text_fields',
	* 			'strTitle' => 'Text',
	* 			'strType' => 'text',
	* 		)	
	* 	);			
	* }</code>
	* @abstract
	* @since			2.0.0
	* @remark			This is a callback for the <em>wp_loaded</em> hook. Thus, its public.
	* @remark			In v1, this is triggered with the <em>admin_menu</em> hook; however, in v2, this is triggered with the <em>wp_loaded</em> hook.
	* @access 			public
	* @return			void
	*/	
	public function setUp() {}
	
	/**
	* Adds sub-menu items on the left sidebar of the administration panel. 
	* 
	* It supports pages and links. Each of them has the specific array structure.
	* 
	* <h4>Sub-menu Page Array</h4>
	* <ul>
	* <li><strong>strPageTitle</strong> - ( string ) the page title of the page.</li>
	* <li><strong>strPageSlug</strong> - ( string ) the page slug of the page. Non-alphabetical characters should not be used including dots(.) and hyphens(-).</li>
	* <li><strong>strScreenIcon</strong> - ( optional, string ) either the ID selector name from the following list or the icon URL. The size of the icon should be 32 by 32 in pixel.
	*	<pre>edit, post, index, media, upload, link-manager, link, link-category, edit-pages, page, edit-comments, themes, plugins, users, profile, user-edit, tools, admin, options-general, ms-admin, generic</pre>
	*	<p><strong>Notes</strong>: the <em>generic</em> icon is available WordPress version 3.5 or above.</p>
	* </li>
	* <li><strong>strCapability</strong> - ( optional, string ) the access level to the created admin pages defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.</li>
	* <li><strong>numOrder</strong> - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
	* <li><strong>fShowPageHeadingTab</strong> - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
	* </ul>
	* <h4>Sub-menu Link Array</h4>
	* <ul>
	* <li><strong>strMenuTitle</strong> - ( string ) the link title.</li>
	* <li><strong>strURL</strong> - ( string ) the URL of the target link.</li>
	* <li><strong>strCapability</strong> - ( optional, string ) the access level to show the item, defined [here](http://codex.wordpress.org/Roles_and_Capabilities). If not set, the overall capability assigned in the class constructor, which is *manage_options* by default, will be used.</li>
	* <li><strong>numOrder</strong> - ( optional, integer ) the order number of the page. The lager the number is, the lower the position it is placed in the menu.</li>
	* <li><strong>fShowPageHeadingTab</strong> - ( optional, boolean ) if this is set to false, the page title won't be displayed in the page heading tab. Default: true.</li>
	* </ul>
	* 
	* <h4>Example</h4>
	* <code>$this->addSubMenuItems(
	*		array(
	*			'strPageTitle' => 'Various Form Fields',
	*			'strPageSlug' => 'first_page',
	*			'strScreenIcon' => 'options-general',
	*		),
	*		array(
	*			'strPageTitle' => 'Manage Options',
	*			'strPageSlug' => 'second_page',
	*			'strScreenIcon' => 'link-manager',
	*		),
	*		array(
	*			'strMenuTitle' => 'Google',
	*			'strURL' => 'http://www.google.com',	
	*			'fShowPageHeadingTab' => false,	// this removes the title from the page heading tabs.
	*		),
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array		$arrSubMenuItem1		a first sub-menu array.
	* @param			array		$arrSubMenuItem2		( optional ) a second sub-menu array.
	* @param			array		$_and_more				( optional ) third and add items as many as necessary with next parameters.
	* @access 			protected
	* @return			void
	*/		
	protected function addSubMenuItems( $arrSubMenuItem1, $arrSubMenuItem2=null, $_and_more=null ) {
		foreach ( func_get_args() as $arrSubMenuItem ) 
			$this->addSubMenuItem( $arrSubMenuItem );		
	}
	
	/**
	* Adds the given sub-menu item on the left sidebar of the administration panel.
	* 
	* This only adds one single item, called by the above <em>addSubMenuItem()</em> method.
	* 
	* The array structure of the parameter is documented in the <em>addSubMenuItem()</em> method section.
	* 
	* @since			2.0.0
	* @remark			The sub menu page slug should be unique because add_submenu_page() can add one callback per page slug.
	* @remark			This is not intended to be used by the user.
	* @param			array		$arrSubMenuItem			a first sub-menu array.
	* @access 			private
	* @return			void
	*/	
	private function addSubMenuItem( $arrSubMenuItem ) {
		if ( isset( $arrSubMenuItem['strURL'] ) ) {
			$arrSubMenuLink = $arrSubMenuItem + AmazonAutoLinks_AdminPageFramework_Link::$arrStructure_SubMenuLink;
			$this->oLink->addSubMenuLink(
				$arrSubMenuLink['strMenuTitle'],
				$arrSubMenuLink['strURL'],
				$arrSubMenuLink['strCapability'],
				$arrSubMenuLink['numOrder'],
				$arrSubMenuLink['fShowPageHeadingTab'],
				$arrSubMenuLink['fShowInMenu']
			);			
		}
		else { // if ( $arrSubMenuItem['strType'] == 'page' ) {
			$arrSubMenuPage = $arrSubMenuItem + self::$arrStructure_SubMenuPage;	// avoid undefined index warnings.
			$this->addSubMenuPage(
				$arrSubMenuPage['strPageTitle'],
				$arrSubMenuPage['strPageSlug'],
				$arrSubMenuPage['strScreenIcon'],
				$arrSubMenuPage['strCapability'],
				$arrSubMenuPage['numOrder'],	
				$arrSubMenuPage['fShowPageHeadingTab'],
				$arrSubMenuPage['fShowInMenu']
			);				
		}
	}

	/**
	* Adds the given link into the menu on the left sidebar of the administration panel.
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @param			string		$strMenuTitle			the menu title.
	* @param			string		$strURL					the URL linked to the menu.
	* @param			string		$strCapability			( optional ) the access level. ( http://codex.wordpress.org/Roles_and_Capabilities)
	* @param			string		$numOrder				( optional ) the order number. The larger it is, the lower the position it gets.
	* @param			string		$fShowPageHeadingTab		( optional ) if set to false, the menu title will not be listed in the tab navigation menu at the top of the page.
	* @access 			protected
	* @return			void
	*/	
	protected function addSubMenuLink( $strMenuTitle, $strURL, $strCapability=null, $numOrder=null, $fShowPageHeadingTab=true, $fShowInMenu=true ) {
		$this->oLink->addSubMenuLink( $strMenuTitle, $strURL, $strCapability, $numOrder, $fShowPageHeadingTab, $fShowInMenu );
	}

	/**
	* Adds the given link(s) into the description cell of the plugin listing table.
	* 
	* <h4>Example</h4>
	* <code>$this->addLinkToPluginDescription( 
	*		"&lt;a href='http://www.google.com'&gt;Google&lt;/a&gt;",
	*		"&lt;a href='http://www.yahoo.com'&gt;Yahoo!&lt;/a&gt;"
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			string			$strTaggedLinkHTML1			the tagged HTML link text.
	* @param			string			$strTaggedLinkHTML2			( optional ) another tagged HTML link text.
	* @param			string			$_and_more					( optional ) add more as many as want by adding items to the next parameters.
	* @access 			protected
	* @return			void
	*/		
	protected function addLinkToPluginDescription( $strTaggedLinkHTML1, $strTaggedLinkHTML2=null, $_and_more=null ) {				
		$this->oLink->addLinkToPluginDescription( func_get_args() );		
	}

	/**
	* Adds the given link(s) into the title cell of the plugin listing table.
	* 
	* <h4>Example</h4>
	* <code>$this->addLinkToPluginTitle( 
	*		"&lt;a href='http://www.wordpress.org'&gt;WordPress&lt;/a&gt;"
	*	);</code>
	* 
	* @since			2.0.0
	* @remark			The user may use this method in their extended class definition.
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			string			$strTaggedLinkHTML1			the tagged HTML link text.
	* @param			string			$strTaggedLinkHTML2			( optional ) another tagged HTML link text.
	* @param			string			$_and_more					( optional ) add more as many as want by adding items to the next parameters.
	* @access 			protected
	* @return			void
	*/	
	protected function addLinkToPluginTitle( $strTaggedLinkHTML1, $strTaggedLinkHTML2=null, $_and_more=null ) {	
		$this->oLink->addLinkToPluginTitle( func_get_args() );		
	}
	 
	/*
	 * Methods that access the properties.
	 */
	/**
	 * Sets the overall capability.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setCpability( 'read' );		// let subscribers access the pages.</code>
	 * 
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Roles_and_Capabilities
	 * @remark			The user may directly edit <code>$this->oProps->strCapability</code> instead.
	 * @param			string			$strCapability			The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> for the created pages.
	 * @return			void
	 */ 
	protected function setCapability( $strCapability ) {
		$this->oProps->strCapability = $strCapability;	
	}

	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '&lt;br /&gt;Custom Text on the left hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @remark			The user may directly edit <code>$this->oProps->arrFooterInfo['strLeft']</code> instead.
	 * @param			string			$strHTML			The HTML code to insert.
	 * @param			boolean			$fAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoLeft( $strHTML, $fAppend=true ) {
		
		$this->oProps->arrFooterInfo['strLeft'] = $fAppend 
			? $this->oProps->arrFooterInfo['strLeft'] . $strHTML
			: $strHTML;
		
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '&lt;br /&gt;Custom Text on the right hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @remark			The user may directly edit <code>$this->oProps->arrFooterInfo['strRight']</code> instead.
	 * @param			string			$strHTML			The HTML code to insert.
	 * @param			boolean			$fAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoRight( $strHTML, $fAppend=true ) {
		
		$this->oProps->arrFooterInfo['strRight'] = $fAppend 
			? $this->oProps->arrFooterInfo['strRight'] . $strHTML
			: $strHTML;
		
	}
		
	/* 
	 * Callback methods
	 */ 
	public function checkRedirects() {

		// So it's not options.php. Now check if it's one of the plugin's added page. If not, do nothing.
		if ( ! ( isset( $_GET['page'] ) ) || ! $this->oProps->isPageAdded( $_GET['page'] ) ) return; 
		
		// If the Settings API has not updated the options, do nothing.
		if ( ! ( isset( $_GET['settings-updated'] ) && ! empty( $_GET['settings-updated'] ) ) ) return;

		// Check the settings error transient.
		$arrError = $this->getFieldErrors( $_GET['page'], false );
		if ( ! empty( $arrError ) ) return;
		
		// Okay, it seems the submitted data have been updated successfully.
		$strTransient = md5( trim( "redirect_{$this->oProps->strClassName}_{$_GET['page']}" ) );
		$strURL = get_transient( $strTransient );
		if ( $strURL === false ) return;
		
		// The redirect URL seems to be set.
		delete_transient( $strTransient );	// we don't need it any more.
		
		// if the redirect page is outside the plugin admin page, delete the plugin settings admin notices as well.
		// if ( ! $this->oCore->IsPluginPage( $strURL ) ) 	
			// delete_transient( md5( 'SettingsErrors_' . $this->oCore->strClassName . '_' . $this->oCore->strPageSlug ) );
				
		// Go to the page.
		$this->oUtil->goRedirect( $strURL );
		
	}
	
	
	/**
	 * Enqueues a style by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>strHandleID</strong> - ( optional, string ) The handle ID of the stylesheet.</li>
	 * 	<li><strong>arrDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_style">codex</a>.</li>
	 * 	<li><strong>strVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>strMedia</strong> - ( optional, string ) the description of the field which is inserted into the after the input field tag.</li>
	 * </ul>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param			string			$strSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param			string			$strPageSlug		(optional) The page slug that the stylesheet should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$strTabSlug			(optional) The tab slug that the stylesheet should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$arrCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */	
	public function enqueueStyle( $strSRC, $strPageSlug='', $strTabSlug='', $arrCustomArgs=array() ) {
		return $this->oHeadTag->enqueueStyle( $strSRC, $strPageSlug, $strTabSlug, $arrCustomArgs );		
	}
	
	/**
	 * Enqueues a script by page slug and tab slug.
	 * 
	 * <h4>Custom Argument Array for the Fourth Parameter</h4>
	 * <ul>
	 * 	<li><strong>strHandleID</strong> - ( optional, string ) The handle ID of the script.</li>
	 * 	<li><strong>arrDependencies</strong> - ( optional, array ) The dependency array. For more information, see <a href="http://codex.wordpress.org/Function_Reference/wp_enqueue_script">codex</a>.</li>
	 * 	<li><strong>strVersion</strong> - ( optional, string ) The stylesheet version number.</li>
	 * 	<li><strong>arrTranslation</strong> - ( optional, array ) The translation array. The handle ID will be used for the object name.</li>
	 * 	<li><strong>fInFooter</strong> - ( optional, boolean ) Whether to enqueue the script before < / head > or before < / body > Default: <code>false</code>.</li>
	 * </ul>	 
	 * 
	 * <h4>Example</h4>
	 * <code>$this->enqueueScript(  
	 *		plugins_url( 'asset/js/test.js' , __FILE__ ),	// source url or path
	 *		'apf_read_me', 	// page slug
	 *		'', 	// tab slug
	 *		array(
	 *			'strHandleID' => 'my_script',	// this handle ID also is used as the object name for the translation array below.
	 *			'arrTranslation' => array( 
	 *				'a' => 'hello world!',
	 *				'style_handle_id' => $strStyleHandle,	// check the enqueued style handle ID here.
	 *			),
	 *		)
	 *	);</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 * @see				http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param			string			$strSRC				The URL of the stylesheet to enqueue, the absolute file path, or the relative path to the root directory of WordPress. Example: '/js/myscript.js'.
	 * @param			string			$strPageSlug		(optional) The page slug that the script should be added to. If not set, it applies to all the pages created by the framework.
	 * @param			string			$strTabSlug			(optional) The tab slug that the script should be added to. If not set, it applies to all the in-page tabs in the page.
	 * @param 			array			$arrCustomArgs		(optional) The argument array for more advanced parameters.
	 * @return			string			The script handle ID. If the passed url is not a valid url string, an empty string will be returned.
	 */
	public function enqueueScript( $strSRC, $strPageSlug='', $strTabSlug='', $arrCustomArgs=array() ) {	
		return $this->oHeadTag->enqueueScript( $strSRC, $strPageSlug, $strTabSlug, $arrCustomArgs );
	}
		
	/**
	 * Sets an admin notice.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setAdminNotice( sprintf( 'Please click <a href="%1$s">here</a> to upgrade the options.', admin_url( 'admin.php?page="my_page"' ) ), 'updated' );</code>
	 * 
	 * @remark			It should be used before the 'admin_notices' hook is triggered.
	 * @since			2.1.2
	 * @param			string			$strMessage				The message to display
	 * @param			string			$strClassSelector		( optional ) The class selector used in the message HTML element. 'error' and 'updated' are prepared by WordPress but it's not limited to them and can pass a custom name. Default: 'error'
	 * @param			string			$strID					( optional ) The ID of the message. If not set, the hash of the message will be used.
	 */
	protected function setAdminNotice( $strMessage, $strClassSelector='error', $strID='' ) {
			
		$strID = $strID ? $strID : md5( $strMessage );
		$this->oProps->arrAdminNotices[ md5( $strMessage ) ] = array(  
			'strMessage' => $strMessage,
			'strClassSelector' => $strClassSelector,
			'strID' => $strID,
		);
		add_action( 'admin_notices', array( $this, 'printAdminNotices' ) );
		
	}
	/**
	 * A helper function for the above setAdminNotice() method.
	 * @since			2.1.2
	 * @internal
	 */
	public function printAdminNotices() {
		
		foreach( $this->oProps->arrAdminNotices as $arrAdminNotice ) 
			echo "<div class='{$arrAdminNotice['strClassSelector']}' id='{$arrAdminNotice['strID']}' ><p>"
				. $arrAdminNotice['strMessage']
				. "</p></div>";
		
	}	
	
	/**
	 * Sets the disallowed query keys in the links that the framework generates.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setDisallowedQueryKeys( array( 'my-custom-admin-notice' ) );</code>
	 * 
	 * @remark			The user may use this method.
	 * @since			2.1.2
	 */
	public function setDisallowedQueryKeys( $arrQueryKeys, $fAppend=true ) {
		
		if ( ! $fAppend ) {
			$this->oProps->arrDisallowedQueryKeys = $arrQueryKeys;
			return;
		}
		
		$arrNewQueryKeys = array_merge( $arrQueryKeys, $this->oProps->arrDisallowedQueryKeys );
		$arrNewQueryKeys = array_filter( $arrNewQueryKeys );	// drop non-values
		$arrNewQueryKeys = array_unique( $arrNewQueryKeys );	// drop duplicates
		$this->oProps->arrDisallowedQueryKeys = $arrNewQueryKeys;
		
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Messages' ) ) :
/**
 * Provides methods for text messages.
 *
 * @since			2.0.0
 * @since			2.1.6			Multiple instances of this class are disallowed.
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 */
class AmazonAutoLinks_AdminPageFramework_Messages {

	/**
	 * Stores the framework's messages.
	 * 
	 * @remark			The user can modify this property directly.
	 */ 
	public $arrMessages = array();

	/**
	 * 
	 * 
	 */
	private static $oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @since			2.1.6
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $strTextDomain='admin-page-framework' ) {
		
		if ( ! isset( self::$oInstance ) && ! ( self::$oInstance instanceof AmazonAutoLinks_AdminPageFramework_Messages ) ) 
			self::$oInstance = new AmazonAutoLinks_AdminPageFramework_Messages( $strTextDomain );
		return self::$oInstance;
		
	}	
	
	public function __construct( $strTextDomain='admin-page-framework' ) {
		
		$this->strTextDomain = $strTextDomain;
		$this->arrMessages = array(
			
			// AmazonAutoLinks_AdminPageFramework
			'option_updated'		=> __( 'The options have been updated.', 'admin-page-framework' ),
			'option_cleared'		=> __( 'The options have been cleared.', 'admin-page-framework' ),
			'export_options'		=> __( 'Export Options', 'admin-page-framework' ),
			'import_options'		=> __( 'Import Options', 'admin-page-framework' ),
			'submit'				=> __( 'Submit', 'admin-page-framework' ),
			'import_error'			=> __( 'An error occurred while uploading the import file.', 'admin-page-framework' ),
			'uploaded_file_type_not_supported'	=> __( 'The uploaded file type is not supported.', 'admin-page-framework' ),
			'could_not_load_importing_data' => __( 'Could not load the importing data.', 'admin-page-framework' ),
			'imported_data'			=> __( 'The uploaded file has been imported.', 'admin-page-framework' ),
			'not_imported_data' 	=> __( 'No data could be imported.', 'admin-page-framework' ),
			'add'					=> __( 'Add', 'admin-page-framework' ),
			'remove'				=> __( 'Remove', 'admin-page-framework' ),
			'upload_image'			=> __( 'Upload Image', 'admin-page-framework' ),
			'use_this_image'		=> __( 'Use This Image', 'admin-page-framework' ),
			'reset_options'			=> __( 'Are you sure you want to reset the options?', 'admin-page-framework' ),
			'confirm_perform_task'	=> __( 'Please confirm if you want to perform the specified task.', 'admin-page-framework' ),
			'option_been_reset'		=> __( 'The options have been reset.', 'admin-page-framework' ),
			'specified_option_been_deleted'	=> __( 'The specified options have been deleted.', 'admin-page-framework' ),
			
			// AmazonAutoLinks_AdminPageFramework_PostType
			'title'			=> __( 'Title', 'admin-page-framework' ),	
			'author'		=> __( 'Author', 'admin-page-framework' ),	
			'categories'	=> __( 'Categories', 'admin-page-framework' ),
			'tags'			=> __( 'Tags', 'admin-page-framework' ),
			'comments' 		=> __( 'Comments', 'admin-page-framework' ),
			'date'			=> __( 'Date', 'admin-page-framework' ), 
			'show_all'		=> __( 'Show All', 'admin-page-framework' ),
			
			// For the meta box class
			
			// AmazonAutoLinks_AdminPageFramework_LinkBase
			'powered_by'	=> __( 'Powered by', 'admin-page-framework' ),
			
			// AmazonAutoLinks_AdminPageFramework_Link
			'settings'		=> __( 'Settings', 'admin-page-framework' ),
			
			// AmazonAutoLinks_AdminPageFramework_LinkForPostType
			'manage'		=> __( 'Manage', 'admin-page-framework' ),
			
			// AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base
			'select_image'			=> __( 'Select Image', 'admin-page-framework' ),
			'upload_file'			=> __( 'Upload File', 'admin-page-framework' ),
			'use_this_file'			=> __( 'Use This File', 'admin-page-framework' ),
			'select_file'			=> __( 'Select File', 'admin-page-framework' ),
			
			// AmazonAutoLinks_AdminPageFramework_PageLoadStats_Base
			'queries_in_seconds'	=> __( '%s queries in %s seconds.', 'admin-page-framework' ),
			'out_of_x_memory_used'	=> __( '%s out of %s MB (%s) memory used.', 'admin-page-framework' ),
			'peak_memory_usage'		=> __( 'Peak memory usage %s MB.', 'admin-page-framework' ),
			'initial_memory_usage'	=> __( 'Initial memory usage  %s MB.', 'admin-page-framework' ),
			
		);		
		
	}
	public function ___( $strKey ) {
		
		return isset( $this->arrMessages[ $strKey ] )
			? __( $this->arrMessages[ $strKey ], $this->strTextDomain )
			: '';
			
	}
	
	public function __e( $strKey ) {
		
		if ( isset( $this->arrMessages[ $strKey ] ) )
			_e( $this->arrMessages[ $strKey ], $this->strTextDomain );
			
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Properties_Base' ) ) :

/**
 * The base class for Property classes.
 * 
 * Provides the common methods  and properties for the property classes that are used by the main class, the meta box class, and the post type class.
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 */ 
abstract class AmazonAutoLinks_AdminPageFramework_Properties_Base {

	/**
	 * Stores the main (caller) object.
	 * 
	 * @since			2.1.5
	 */
	protected $oCaller;	
	
	/**
	 * Stores the script to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from each extended property class.
	 * @internal
	 */ 			
	public $strScript = '';	

	/**
	 * Stores the CSS rules to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from each extended property class.
	 * @internal
	 */ 		
	public $strStyle = '';	
	
	/**
	 * Stores the CSS rules for IE to be embedded in the head tag.
	 * 
	 * @remark			This should be an empty string by default since the related methods uses the append operator.
	 * @since			2.0.0 to 2.1.4
	 * @internal
	 */ 
	public $strStyleIE = '';	
	
	/**
	 * Stores the field type definitions.
	 * 
	 * @since			2.1.5
	 * @internal
	 */
	public $arrFieldTypeDefinitions = array();
	
	/**
	 * The default CSS rules loaded in the head tag of the created admin pages.
	 * 
	 * @since			2.0.0
	 * @var				string
	 * @static
	 * @remark			It is accessed from the main class and meta box class.
	 * @access			public	
	 * @internal	
	 */
	public static $strDefaultStyle =
		".wrap div.updated, 
		.wrap div.settings-error { 
			clear: both; 
			margin-top: 16px;
		} 		

		.contextual-help-description {
			clear: left;	
			display: block;
			margin: 1em 0;
		}
		.contextual-help-tab-title {
			font-weight: bold;
		}
		
		/* Delimiter */
		.admin-page-framework-fields .delimiter {
			display: inline;
		}
		/* Description */
		.admin-page-framework-fields .admin-page-framework-fields-description {
			/* margin-top: 0px; */
			/* margin-bottom: 0.5em; */
			margin-bottom: 0;
		}
		/* Input form elements */
		.admin-page-framework-field {
			display: inline;
			margin-top: 1px;
			margin-bottom: 1px;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container {
			margin-bottom: 0.25em;
		}
		@media only screen and ( max-width: 780px ) {	/* For WordPress v3.8 or greater */
			.admin-page-framework-field .admin-page-framework-input-label-container {
				margin-bottom: 0.5em;
			}
		}			
		.admin-page-framework-field input[type='radio'],
		.admin-page-framework-field input[type='checkbox']
		{
			margin-right: 0.5em;
		}		
		
		.admin-page-framework-field .admin-page-framework-input-label-string {
			padding-right: 1em;	/* for checkbox label strings, a right padding is needed */
		}
		.admin-page-framework-field .admin-page-framework-input-button-container {
			padding-right: 1em; 
		}
		.admin-page-framework-field-radio .admin-page-framework-input-label-container,
		.admin-page-framework-field-select .admin-page-framework-input-label-container,
		.admin-page-framework-field-checkbox .admin-page-framework-input-label-container 
		{
			padding-right: 1em;
		}

		.admin-page-framework-field .admin-page-framework-input-container {
			display: inline-block;
			vertical-align: middle; 
		}
		.admin-page-framework-field-text .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-textarea .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-color .admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field-select .admin-page-framework-field .admin-page-framework-input-label-container
		{
			vertical-align: top; 
		}
		.admin-page-framework-field-image .admin-page-framework-field .admin-page-framework-input-label-container {			
			vertical-align: middle;
		}
		.admin-page-framework-field .admin-page-framework-input-label-container,
		.admin-page-framework-field .admin-page-framework-input-label-string
		{
			display: inline-block;		
			vertical-align: middle;
		}
		.admin-page-framework-field-textarea .admin-page-framework-input-label-string {
			vertical-align: top;
			margin-top: 2px;
		}
		
		.admin-page-framework-field-posttype .admin-page-framework-field input[type='checkbox'] { 
			margin-top: 0px;
		}
		.admin-page-framework-field-posttype .admin-page-framework-field {
			display: inline-block;
		}
		.admin-page-framework-field-radio .admin-page-framework-field .admin-page-framework-input-container {
			display: inline;
		}
		
		/* Repeatable Fields */		
		.admin-page-framework-field.repeatable {
			clear: both;
			display: block;
		}
		.admin-page-framework-repeatable-field-buttons {
			float: right;
			margin-bottom: 0.5em;
		}
		.admin-page-framework-repeatable-field-buttons .repeatable-field-button {
			margin: 0 2px;
			font-weight: normal;
			vertical-align: middle;
			text-align: center;
		}

		/* Import Field */
		.admin-page-framework-field-import input {
			margin-right: 0.5em;
		}
		/* Page Load Stats */
		#admin-page-framework-page-load-stats {
			clear: both;
			display: inline-block;
			width: 100%
		}
		#admin-page-framework-page-load-stats li{
			display: inline;
			margin-right: 1em;
		}		
		
		/* To give the footer area more space */
		#wpbody-content {
			padding-bottom: 140px;
		}
		";	
		
	/**
	 * The default CSS rules for IE loaded in the head tag of the created admin pages.
	 * @since			2.1.1
	 * @since			2.1.5			Moved the contents to the taxonomy field definition so it become an empty string.
	 */
	public static $strDefaultStyleIE = '';
		
	/**
	 * Represents the structure of the array for enqueuing scripts and styles.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public static $arrStructure_EnqueuingScriptsAndStyles = array(
		'strURL' => null,
		'arrPostTypes' => array(),		// for meta box class
		'strPageSlug' => null,	
		'strTabSlug' => null,
		'strType' => null,		// script or style
		'strHandleID' => null,
		'arrDependencies' => array(),
        'strVersion' => false,		// although the type should be string, the wp_enqueue_...() functions want false as the default value.
        'arrTranslation' => array(),	// only for scripts
        'fInFooter' => false,	// only for scripts
		'strMedia' => 'all',	// only for styles		
	);
	/**
	 * Stores enqueuing script URLs and their criteria.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public $arrEnqueuingScripts = array();
	/**	
	 * Stores enqueuing style URLs and their criteria.
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */	
	public $arrEnqueuingStyles = array();
	/**
	 * Stores the index of enqueued scripts.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */
	public $intEnqueuedScriptIndex = 0;
	/**
	 * Stores the index of enqueued styles.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the script handle ID which is automatically generated.
	 * 
	 * @since			2.1.2
	 * @since			2.1.5			Moved to the base class.
	 */	
	public $intEnqueuedStyleIndex = 0;		
		
	function __construct( $oCaller ) {
		
		$this->oCaller = $oCaller;
		$GLOBALS['arrAmazonAutoLinks_AdminPageFramework'] = isset( $GLOBALS['arrAmazonAutoLinks_AdminPageFramework'] ) && is_array( $GLOBALS['arrAmazonAutoLinks_AdminPageFramework'] ) 
			? $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']
			: array();

	}
	
	/**
	 * Calculates the subtraction of two values with the array key of <em>numOrder</em>
	 * 
	 * This is used to sort arrays.
	 * 
	 * @since			2.0.0
	 * @remark			a callback method for uasort().
	 * @return			integer
	 */ 
	public function sortByOrder( $a, $b ) {	
		return $a['numOrder'] - $b['numOrder'];
	}		
	
	/**
	 * Returns the caller object.
	 * 
	 * This is used from other sub classes that need to retrieve the caller object.
	 * 
	 * @since			2.1.5
	 * @access			public	
	 * @internal
	 * @return			object			The caller class object.
	 */		
	public function getParentObject() {
		return $this->oCaller;
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_MetaBox_Properties' ) ) :
/**
 * Provides the space to store the shared properties for meta boxes.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AmazonAutoLinks_AdminPageFramework_Properties_Base
 */
class AmazonAutoLinks_AdminPageFramework_MetaBox_Properties extends AmazonAutoLinks_AdminPageFramework_Properties_Base {

	/**
	 * Stores the meta box id(slug).
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				string
	 */ 	
	public $strMetaBoxID ='';
	
	/**
	 * Stores the meta box title.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				string
	 */ 
	public $strTitle = '';

	/**
	 * Stores the post type slugs associated with the meta box.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @var				array
	 */ 	
	public $arrPostTypes = array();
	
	/**
	 * Stores the parameter value, context, for the add_meta_box() function. 
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @remark			The value can be either 'normal', 'advanced', or 'side'.
	 * @var				string
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */ 
	public $strContext = 'normal';

	/**
	 * Stores the parameter value, priority, for the add_meta_box() function. 
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @remark			The value can be either 'high', 'core', 'default' or 'low'.
	 * @var				string
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 */ 	
	public $strPriority = 'default';
	
	/**
	 * Stores the extended class name.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 */ 
	public $strClassName = '';
	
	/**
	 * Stores the capability for displayable elements.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 */ 	
	public $strCapability = 'edit_posts';
	
	/**
	 * @internal
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	*/ 		
	public $strPrefixStart = 'start_';	
	
	/**
	 * Stores the field arrays for meta box form elements.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 			
	public $arrFields = array();
	
	/**
	 * Stores option values for form fields.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */	 
	public $arrOptions = array();
	
	/**
	 * Stores the media uploader box's title.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 
	public $strThickBoxTitle = '';
	
	/**
	 * Stores the label for for the "Insert to Post" button in the media uploader box.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 	
	public $strThickBoxButtonUseThis = '';

	/**
	 * Stores text to insert into the contextual help tab.
	 * @since			2.1.0
	 */ 
	public $arrHelpTabText = array();
	
	/**
	 * Stores text to insert into the sidebar of a contextual help tab.
	 * @since			2.1.0
	 */ 
	public $arrHelpTabTextSide = array();
	
	// Default values
	/**
	 * Represents the structure of field array for meta box form fields.
	 * @since			2.0.0
	 * @since			2.1.0			Moved from the meta box class.
	 * @internal
	 */ 
	public static $arrStructure_Field = array(
		'strFieldID'		=> null,	// ( mandatory ) the field ID
		'strType'			=> null,	// ( mandatory ) the field type.
		'strTitle' 			=> null,	// the field title
		'strDescription'	=> null,	// an additional note 
		'strCapability'		=> null,	// an additional note 
		'strTip'			=> null,	// pop up text
		// 'options'			=> null,	// ? don't remember what this was for
		'vValue'			=> null,	// allows to override the stored value
		'vDefault'			=> null,	// allows to set default values.
		'strName'			=> null,	// allows to set custom field name
		'vLabel'			=> '',		// sets the label for the field. Setting a non-null value will let it parsed with the loop ( foreach ) of the input element rendering method.
		'fIf'				=> true,
		'strHelp'			=> null,	// since 2.1.0
		'strHelpAside'		=> null,	// since 2.1.0
		'fHideTitleColumn'	=> null,	// since 2.1.2
		
		// The followings may need to be uncommented.
		// 'strClassName' => null,		// This will be assigned automatically in the formatting method.
		// 'strError' => null,			// error message for the field
		// 'strBeforeField' => null,
		// 'strAfterField' => null,
		// 'numOrder' => null,			// do not set the default number here for this key.		

		'fRepeatable'		=> null,	// since 2.1.3		
	);
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_PostType_Properties' ) ) :
/**
 * Provides the space to store the shared properties for custom post types.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since			2.1.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AmazonAutoLinks_AdminPageFramework_Properties_Base
 */
class AmazonAutoLinks_AdminPageFramework_PostType_Properties extends AmazonAutoLinks_AdminPageFramework_Properties_Base {
	
	/**
	 * Stores the post type slug.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @var				string
	 * @access			public
	 */ 
	public $strPostType = '';
	
	/**
	 * Stores the post type argument.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @var				array
	 * @access			public
	 */ 
	public $arrPostTypeArgs = array();	

	/**
	 * Stores the extended class name.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @var				string
	 * @access			public
	 */ 	
	public $strClassName = '';

	/**
	 * Stores the column headers of the post listing table.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @see				http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_edit-post_type_columns
	 * @remark			This should be overriden in the constructor because it includes translated text.
	 * @internal
	 * @access			public
	 */ 	
	public $arrColumnHeaders = array(
		'cb'			=> '<input type="checkbox" />',	// Checkbox for bulk actions. 
		'title'			=> 'Title',		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
		'author'		=> 'Author',		// Post author.
		// 'categories'	=> __( 'Categories', 'admin-page-framework' ),	// Categories the post belongs to. 
		// 'tags'		=> __( 'Tags', 'admin-page-framework' ),	// Tags for the post. 
		'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
		'date'			=> 'Date', 	// The date and publish status of the post. 
	);		
	
	/**
	 * Stores the sortable column items.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 		
	public $arrColumnSortable = array(
		'title' => true,
		'date'	=> true,
	);	
	
	/**
	 * Stores the caller script path.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @var				string
	 * @access			public
	 */ 		
	public $strCallerPath = '';
	
	// Prefixes
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @internal
	 * @access			protected
	 */ 	
	public $strPrefix_Start = 'start_';
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $strPrefix_Cell = 'cell_';
	
	// Containers
	/**
	 * Stores custom taxonomy slugs.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $arrTaxonomies;		// stores the registering taxonomy info.
	
	/**
	 * Stores the taxonomy IDs as value to indicate whether the drop-down filter option should be displayed or not.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $arrTaxonomyTableFilters = array();	
	
	/**
	 * Stores removing taxonomy menus' info.
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 	
	public $arrTaxonomyRemoveSubmenuPages = array();	
	
	// Default Values
	/**
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 					
	public $fEnableAutoSave = true;	

	/**
	 * Stores the flag value which indicates whether author table filters should be enabled or not.
	 * 
	 * @since			2.0.0
	 * @since			2.1.0			Moved to AmazonAutoLinks_AdminPageFramework_PostType_Properties.
	 * @internal
	 */ 					
	public $fEnableAuthorTableFileter = false;	
		
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Properties' ) ) :
/**
 * Provides the space to store the shared properties.
 * 
 * This class stores various types of values. This is used to encapsulate properties so that it helps to avoid naming conflicts.
 * 
 * @since			2.0.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Property
 * @extends			AmazonAutoLinks_AdminPageFramework_Properties_Base
 */
class AmazonAutoLinks_AdminPageFramework_Properties extends AmazonAutoLinks_AdminPageFramework_Properties_Base {
			
	/**
	 * Stores framework's instantiated object name.
	 * 
	 * @since			2.0.0
	 */ 
	public $strClassName;	
	
	/**
	 * Stores the md5 hash string of framework's instantiated object name.
	 * @since			2.1.1
	 */
	public $strClassHash;
	
	/**
	 * Stores the access level to the root page. 
	 * 
	 * When sub pages are added and the capability value is not provided, this will be applied.
	 * 
	 * @since			2.0.0
	 */ 	
	public $strCapability = 'manage_options';	
	
	/**
	 * Stores the tag for the page heading navigation bar.
	 * @since			2.0.0
	 */ 
	public $strPageHeadingTabTag = 'h2';

	/**
	 * Stores the tag for the in-page tab navigation bar.
	 * @since			2.0.0
	 */ 
	public $strInPageTabTag = 'h3';
	
	/**
	 * Stores the default page slug.
	 * @since			2.0.0
	 */ 	
	public $strDefaultPageSlug;
		
	// Container arrays.
	/**
	 * A two-dimensional array storing registering sub-menu(page) item information with keys of the page slug.
	 * @since			2.0.0
	 */ 	
	public $arrPages = array(); 

	/**
	 * Stores the hidden page slugs.
	 * @since			2.1.4
	 */
	public $arrHiddenPages = array();
	
	/**
	 * Stores the registered sub menu pages.
	 * 
	 * Unlike the above $arrPages that holds the pages to be added, this stores the added pages. This is referred when adding a help section.
	 * 
	 * @since			2.1.0
	 */ 
	public $arrRegisteredSubMenuPages = array();
	
	/**
	 * Stores the root menu item information for one set root menu item.
	 * @since			2.0.0
	 */ 		
	public $arrRootMenu = array(
		'strTitle' => null,				// menu label that appears on the menu list
		'strPageSlug' => null,			// menu slug that identifies the menu item
		'strURLIcon16x16' => null,		// the associated icon that appears beside the label on the list
		'intPosition'	=> null,		// determines the position of the menu
		'fCreateRoot' => null,			// indicates whether the framework should create the root menu or not.
	); 
	
	/**
	 * Stores in-page tabs.
	 * @since			2.0.0
	 */ 	
	public $arrInPageTabs = array();				
	
	/**
	 * Stores the default in-page tab.
	 * @since			2.0.0
	 */ 		
	public $arrDefaultInPageTabs = array();			
		
	/**
	 * Stores link text that is scheduled to be embedded in the plugin listing table's description column cell.
	 * @since			2.0.0
	 */ 			
	public $arrPluginDescriptionLinks = array(); 

	/**
	 * Stores link text that is scheduled to be embedded in the plugin listing table's title column cell.
	 * @since			2.0.0
	 */ 			
	public $arrPluginTitleLinks = array();			
	
	/**
	 * Stores the information to insert into the page footer.
	 * @since			2.0.0
	 */ 			
	public $arrFooterInfo = array(
		'strLeft' => '',
		'strRight' => '',
	);
		
	// Settings API
	// public $arrOptions;			// Stores the framework's options. Do not even declare the property here because the __get() magic method needs to be triggered when it accessed for the first time.

	/**
	 * The instantiated class name will be assigned in the constructor if the first parameter is not set.
	 * @since			2.0.0
	 */ 				
	public $strOptionKey = '';		

	/**
	 * Stores form sections.
	 * @since			2.0.0
	 */ 					
	public $arrSections = array();
	
	/**
	 * Stores form fields
	 * @since			2.0.0
	 */ 					
	public $arrFields = array();

	/**
	 * Stores contextual help tabs.
	 * @since			2.1.0
	 */ 	
	public $arrHelpTabs = array();
	
	/**
	 * Set one of the followings: application/x-www-form-urlencoded, multipart/form-data, text/plain
	 * @since			2.0.0
	 */ 					
	public $strFormEncType = 'multipart/form-data';	
	
	/**
	 * Stores the label for for the "Insert to Post" button in the media uploader box.
	 * @since			2.0.0
	 * @internal
	 */ 	
	public $strThickBoxButtonUseThis = '';
	
	// Flags	
	/**
	 * Decides whether the setting form tag is rendered or not.	
	 * 
	 * This will be enabled when a settings section and a field is added.
	 * @since			2.0.0
	 */ 						
	public $fEnableForm = false;			
	
	/**
	 * Indicates whether the page title should be displayed.
	 * @since			2.0.0
	 */ 						
	public $fShowPageTitle = true;	
	
	/**
	 * Indicates whether the page heading tabs should be displayed.
	 * @since			2.0.0
	 * @remark			Used by the showPageHeadingTabs() method.
	 */ 	
	public $fShowPageHeadingTabs = true;

	/**
	 * Indicates whether the in-page tabs should be displayed.
	 * 
	 * This sets globally among the script using the framework. 
	 * 
	 * @since			2.1.2
	 * @remark			Used by the showInPageTabs() method.
	 */
	public $fShowInPageTabs = true;

	/**
	 * Stores the set administration notices.
	 * 
	 * The index number will be incremented as a script is enqueued regardless a previously added enqueue item has been removed or not.
	 * This is because this index number will be used for the style handle ID which is automatically generated.
	 * @since			2.1.2
	 */
	public $arrAdminNotices	= array();
	
	/**
	 * Stores the disallowed query keys in the links generated by the main class of the framework.
	 * 
	 * @remark			Currently this does not take effect on the meta box and post type classes of the framework.
	 * @since			2.1.2
	 */
	public $arrDisallowedQueryKeys	= array( 'settings-updated' );
	
	/**
	 * Construct the instance of AmazonAutoLinks_AdminPageFramework_Properties class object.
	 * 
	 * @remark			Used by the showInPageTabs() method.
	 * @since			2.0.0
	 * @since			2.1.5			The $oCaller parameter was added.
	 * @return			void
	 */ 
	public function __construct( $oCaller, $strClassName, $strOptionKey, $strCapability='manage_options' ) {
		
		parent::__construct( $oCaller );
		
		$this->strClassName = $strClassName;		
		$this->strClassHash = md5( $strClassName );
		$this->strOptionKey = $strOptionKey ? $strOptionKey : $strClassName;
		$this->strCapability = empty( $strCapability ) ? $this->strCapability : $strCapability;
		
	}
	
	/*
	 * Magic methods
	 * */
	public function &__get( $strName ) {
		
		// If $this->arrOptions is called for the first time, retrieve the option data from the database and assign to the property.
		// One this is done, calling $this->arrOptions will not trigger the __get() magic method any more.
		// Without the the ampersand in the method name, it causes a PHP warning.
		if ( $strName == 'arrOptions' ) {
			$this->arrOptions = $this->getOptions();
			return $this->arrOptions;	
		}
		
		// For regular undefined items, 
		return null;
		
	}
	
	/*
	 * Utility methods
	 * */
	
	/**
	 * Checks if the given page slug is one of the pages added by the framework.
	 * @since			2.0.0
	 * @since			2.1.0			Set the default value to the parameter and if the parameter value is empty, it applies the current $_GET['page'] value.
	 * @return			boolean			Returns true if it is of framework's added page; otherwise, false.
	 */
	public function isPageAdded( $strPageSlug='' ) {	
		
		$strPageSlug = ! empty( $strPageSlug ) ? $strPageSlug : ( isset( $_GET['page'] ) ? $_GET['page'] : '' );
		return ( array_key_exists( trim( $strPageSlug ), $this->arrPages ) )
			? true
			: false;
	}
	
	/**
	 * Retrieves the default in-page tab from the given tab slug.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5			Made it public and moved from the AmazonAutoLinks_AdminPageFramework_Pages class since this method is used by the AmazonAutoLinks_AdminPageFramework_HeadTab class as well.
	 * @internal
	 * @remark			Used in the __call() method in the main class.
	 * @return			string			The default in-page tab slug if found; otherwise, an empty string.
	 */ 		
	public function getDefaultInPageTab( $strPageSlug ) {
	
		if ( ! $strPageSlug ) return '';		
		return isset( $this->arrDefaultInPageTabs[ $strPageSlug ] ) 
			? $this->arrDefaultInPageTabs[ $strPageSlug ]
			: '';

	}	
	
	public function getOptions() {
		
		$vOptions = get_option( $this->strOptionKey );
		if ( empty( $vOptions ) )
			return array();		// casting array causes an 0 key element. So this way it can be avoided
		
		if ( is_array( $vOptions ) )	// if it's array, no problem.
			return $vOptions;
		
		return ( array ) $vOptions;	// finally cast array.
		
	}
	
	/*
	 * callback methods
	 */ 
	public function getCapability() {
		return $this->strCapability;
	}	
		
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_CustomSubmitFields' ) ) :
/**
 * Provides helper methods that deal with custom submit fields and retrieve custom key elements.
 *
 * @abstract
 * @since			2.0.0
 * @remark			The classes that extend this include ExportOptions, ImportOptions, and Redirect.
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
abstract class AmazonAutoLinks_AdminPageFramework_CustomSubmitFields {
	 
	public function __construct( $arrPostElement ) {
		
		$this->arrPostElement = $arrPostElement;	// e.g. $_POST['__import'] or $_POST['__export'] or $_POST['__redirect']
		
	}
	
	/**
	 * Retrieves the value of the specified element key.
	 * 
	 * The element key is either a single key or two keys. The two keys means that the value is stored in the second dimension.
	 * 
	 * @since			2.0.0
	 */ 
	protected function getElement( $arrElement, $arrElementKey, $strElementKey='format' ) {
			
		$strFirstDimensionKey = $arrElementKey[ 0 ];
		if ( ! isset( $arrElement[ $strFirstDimensionKey ] ) || ! is_array( $arrElement[ $strFirstDimensionKey ] ) ) return 'ERROR_A';

		/* For single element, e.g.
		 * <input type="hidden" name="__import[import_single][import_option_key]" value="APF_GettingStarted">
		 * <input type="hidden" name="__import[import_single][format]" value="array">
		 * */	
		if ( isset( $arrElement[ $strFirstDimensionKey ][ $strElementKey ] ) && ! is_array( $arrElement[ $strFirstDimensionKey ][ $strElementKey ] ) )
			return $arrElement[ $strFirstDimensionKey ][ $strElementKey ];

		/* For multiple elements, e.g.
		 * <input type="hidden" name="__import[import_multiple][import_option_key][2]" value="APF_GettingStarted.txt">
		 * <input type="hidden" name="__import[import_multiple][format][2]" value="array">
		 * */
		if ( ! isset( $arrElementKey[ 1 ] ) ) return 'ERROR_B';
		$strKey = $arrElementKey[ 1 ];
		if ( isset( $arrElement[ $strFirstDimensionKey ][ $strElementKey ][ $strKey ] ) )
			return $arrElement[ $strFirstDimensionKey ][ $strElementKey ][ $strKey ];
			
		return 'ERROR_C';	// Something wrong happened.
		
	}	
	
	/**
	 * Retrieves an array consisting of two values.
	 * 
	 * The first element is the fist dimension's key and the second element is the second dimension's key.
	 * @since			2.0.0
	 */
	protected function getElementKey( $arrElement, $strFirstDimensionKey ) {
		
		if ( ! isset( $arrElement[ $strFirstDimensionKey ] ) ) return;
		
		// Set the first element the field ID.
		$arrEkementKey = array( 0 => $strFirstDimensionKey );

		// For single export buttons, e.g. name="__import[submit][import_single]" 		
		if ( ! is_array( $arrElement[ $strFirstDimensionKey ] ) ) return $arrEkementKey;
		
		// For multiple ones, e.g. name="__import[submit][import_multiple][1]" 		
		foreach( $arrElement[ $strFirstDimensionKey ] as $k => $v ) {
			
			// Only the pressed export button's element is submitted. In other words, it is necessary to check only one item.
			$arrEkementKey[] = $k;
			return $arrEkementKey;			
				
		}		
	}
		
	public function getFieldID() {
		
		// e.g.
		// single:		name="__import[submit][import_single]"
		// multiple:	name="__import[submit][import_multiple][1]"
		
		if ( isset( $this->strFieldID ) && $this->strFieldID  ) return $this->strFieldID;
		
		// Only the pressed element will be stored in the array.
		foreach( $this->arrPostElement['submit'] as $strKey => $v ) {	// $this->arrPostElement should have been set in the constructor.
			$this->strFieldID = $strKey;
			return $this->strFieldID;
		}
	}	
		
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_ImportOptions' ) ) :
/**
 * Provides methods to import option data.
 *
 * @since			2.0.0
 * @extends			AmazonAutoLinks_AdminPageFramework_CustomSubmitFields
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AmazonAutoLinks_AdminPageFramework_ImportOptions extends AmazonAutoLinks_AdminPageFramework_CustomSubmitFields {
	
	/* Example of $_FILES for a single import field. 
		Array (
			[__import] => Array (
				[name] => Array (
				   [import_single] => APF_GettingStarted_20130709 (1).json
				)
				[type] => Array (
					[import_single] => application/octet-stream
				)
				[tmp_name] => Array (
					[import_single] => Y:\wamp\tmp\php7994.tmp
				)
				[error] => Array (
					[import_single] => 0
				)
				[size] => Array (
					[import_single] => 715
				)
			)
		)
	*/
	
	public function __construct( $arrFilesImport, $arrPostImport ) {

		// Call the parent constructor. This must be done before the getFieldID() method that uses the $arrPostElement property.
		parent::__construct( $arrPostImport );
	
		$this->arrFilesImport = $arrFilesImport;
		$this->arrPostImport = $arrPostImport;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->strFieldID = $this->getFieldID();
		$this->arrElementKey = $this->getElementKey( $arrPostImport['submit'], $this->strFieldID );
			
	}
	
	private function getElementInFilesArray( $arrFilesImport, $arrElementKey, $strElementKey='error' ) {

		$strElementKey = strtolower( $strElementKey );
		$strFieldID = $arrElementKey[ 0 ];	// or simply assigning $this->strFieldID would work as well.
		if ( ! isset( $arrFilesImport[ $strElementKey ][ $strFieldID ] ) ) return 'ERROR_A: The given key does not exist.';
	
		// For single export buttons, e.g. $_FILES[__import][ $strElementKey ][import_single] 
		if ( isset( $arrFilesImport[ $strElementKey ][ $strFieldID ] ) && ! is_array( $arrFilesImport[ $strElementKey ][ $strFieldID ] ) )
			return $arrFilesImport[ $strElementKey ][ $strFieldID ];
			
		// For multiple import buttons, e.g. $_FILES[__import][ $strElementKey ][import_multiple][2]
		if ( ! isset( $arrElementKey[ 1 ] ) ) return 'ERROR_B: the sub element is not set.';
		$strKey = $arrElementKey[ 1 ];		
		if ( isset( $arrPostImport[ $strElementKey ][ $strFieldID ][ $strKey ] ) )
			return $arrPostImport[ $strElementKey ][ $strFieldID ][ $strKey ];

		// Something wrong happened.
		return 'ERROR_C: unexpected problem occurred.';
		
	}	
		
	public function getError() {
		
		return $this->getElementInFilesArray( $this->arrFilesImport, $this->arrElementKey, 'error' );
		
	}
	public function getType() {

		return $this->getElementInFilesArray( $this->arrFilesImport, $this->arrElementKey, 'type' );
		
	}
	public function getImportData() {
		
		// Retrieve the uploaded file path.
		$strFilePath = $this->getElementInFilesArray( $this->arrFilesImport, $this->arrElementKey, 'tmp_name' );
		
		// Read the file contents.
		$vData = file_exists( $strFilePath ) ? file_get_contents( $strFilePath, true ) : false;
		
		return $vData;
		
	}
	public function formatImportData( &$vData, $strFormatType=null ) {
		
		$strFormatType = isset( $strFormatType ) ? $strFormatType : $this->getFormatType();
		switch ( strtolower( $strFormatType ) ) {
			case 'text':	// for plain text.
				return;	// do nothing
			case 'json':	// for json.
				$vData = json_decode( ( string ) $vData, true );	// the second parameter indicates to decode it as array.
				return;
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				$vData = maybe_unserialize( trim( $vData ) );
				return;
		}		
	
	}
	public function getFormatType() {
					
		$this->strFormatType = isset( $this->strFormatType ) && $this->strFormatType 
			? $this->strFormatType
			: $this->getElement( $this->arrPostImport, $this->arrElementKey, 'format' );

		return $this->strFormatType;
		
	}
	
	/**
	 * Returns the specified sibling value.
	 * 
	 * @since			2.1.5
	 */
	public function getSiblingValue( $strKey ) {
		
		return $this->getElement( $this->arrPostImport, $this->arrElementKey, $strKey );
		
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_ExportOptions' ) ) :
/**
 * Provides methods to export option data.
 *
 * @since			2.0.0
 * @extends			AmazonAutoLinks_AdminPageFramework_CustomSubmitFields
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AmazonAutoLinks_AdminPageFramework_ExportOptions extends AmazonAutoLinks_AdminPageFramework_CustomSubmitFields {

	public function __construct( $arrPostExport, $strClassName ) {
		
		// Call the parent constructor.
		parent::__construct( $arrPostExport );
		
		// Properties
		$this->arrPostExport = $arrPostExport;
		$this->strClassName = $strClassName;	// will be used in the getTransientIfSet() method.
		// $this->strPageSlug = $strPageSlug;
		// $this->strTabSlug = $strTabSlug;
		
		// Find the field ID and the element key ( for multiple export buttons )of the pressed submit ( export ) button.
		$this->strFieldID = $this->getFieldID();
		$this->arrElementKey = $this->getElementKey( $arrPostExport['submit'], $this->strFieldID );
		
		// Set the file name to download and the format type. Also find whether the exporting data is set in transient.
		$this->strFileName = $this->getElement( $arrPostExport, $this->arrElementKey, 'file_name' );
		$this->strFormatType = $this->getElement( $arrPostExport, $this->arrElementKey, 'format' );
		$this->fIsDataSet = $this->getElement( $arrPostExport, $this->arrElementKey, 'transient' );
	
	}
	
	public function getTransientIfSet( $vData ) {
		
		if ( $this->fIsDataSet ) {
			$strKey = $this->arrElementKey[1];
			$strTransient = isset( $this->arrElementKey[1] ) ? "{$this->strClassName}_{$this->strFieldID}_{$this->arrElementKey[1]}" : "{$this->strClassName}_{$this->strFieldID}";
			$tmp = get_transient( md5( $strTransient ) );
			if ( $tmp !== false ) {
				$vData = $tmp;
				delete_transient( md5( $strTransient ) );
			}
		}
		return $vData;
	}
	
	public function getFileName() {
		return $this->strFileName;
	}
	public function getFormat() {
		return $this->strFormatType;
	}
	
	/**
	 * Returns the specified sibling value.
	 * 
	 * @since			2.1.5
	 */
	public function getSiblingValue( $strKey ) {
		
		return $this->getElement( $this->arrPostExport, $this->arrElementKey, $strKey );
		
	}	

	/**
	 * Performs exporting data.
	 * 
	 * @since			2.0.0
	 */ 
	public function doExport( $vData, $strFileName=null, $strFormatType=null ) {

		/* 
		 * Sample HTML elements that triggers the method.
		 * e.g.
		 * <input type="hidden" name="__export[export_sinble][file_name]" value="APF_GettingStarted_20130708.txt">
		 * <input type="hidden" name="__export[export_sinble][format]" value="json">
		 * <input id="export_and_import_export_sinble_0" 
		 *  type="submit" 
		 *  name="__export[submit][export_sinble]" 
		 *  value="Export Options">
		*/	
		$strFileName = isset( $strFileName ) ? $strFileName : $this->strFileName;
		$strFormatType = isset( $strFormatType ) ? $strFormatType : $this->strFormatType;
							
		// Do export.
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $strFileName );
		switch ( strtolower( $strFormatType ) ) {
			case 'text':	// for plain text.
				if ( is_array( $vData ) || is_object( $vData ) ) {
					$oDebug = new AmazonAutoLinks_AdminPageFramework_Debug;
					$strData = $oDebug->getArray( $vData );
					die( $strData );
				}
				die( $vData );
			case 'json':	// for json.
				die( json_encode( ( array ) $vData ) );
			case 'array':	// for serialized PHP array.
			default:	// for anything else, 
				die( serialize( ( array ) $vData  ));
		}
	}
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_LinkBase' ) ) :
/**
 * Provides methods for HTML link elements.
 *
 * @abstract
 * @since			2.0.0
 * @extends			AmazonAutoLinks_AdminPageFramework_Utilities
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
abstract class AmazonAutoLinks_AdminPageFramework_LinkBase extends AmazonAutoLinks_AdminPageFramework_Utilities {
	
	/**
	 * @internal
	 * @since			2.0.0
	 */ 
	private static $arrStructure_CallerInfo = array(
		'strPath'			=> null,
		'strType'			=> null,
		'strName'			=> null,		
		'strURI'			=> null,
		'strVersion'		=> null,
		'strThemeURI'		=> null,
		'strScriptURI'		=> null,
		'strAuthorURI'		=> null,
		'strAuthor'			=> null,
		'strDescription'	=> null,
	);	
	
	/*
	 * Methods for getting script info.
	 */ 
	
	/**
	 * Retrieves the caller script information whether it's a theme or plugin or something else.
	 * 
	 * @since			2.0.0
	 * @remark			The information can be used to embed into the footer etc.
	 * @return			array			The information of the script.
	 */	 
	protected function getCallerInfo( $strCallerPath=null ) {
		
		$arrCallerInfo = self::$arrStructure_CallerInfo;
		$arrCallerInfo['strPath'] = $strCallerPath;
		$arrCallerInfo['strType'] = $this->getCallerType( $arrCallerInfo['strPath'] );

		if ( $arrCallerInfo['strType'] == 'unknown' ) return $arrCallerInfo;
		
		if ( $arrCallerInfo['strType'] == 'plugin' ) 
			return $this->getScriptData( $arrCallerInfo['strPath'], $arrCallerInfo['strType'] ) + $arrCallerInfo;
			
		if ( $arrCallerInfo['strType'] == 'theme' ) {
			$oTheme = wp_get_theme();	// stores the theme info object
			return array(
				'strName'			=> $oTheme->Name,
				'strVersion' 		=> $oTheme->Version,
				'strThemeURI'		=> $oTheme->get( 'ThemeURI' ),
				'strURI'			=> $oTheme->get( 'ThemeURI' ),
				'strAuthorURI'		=> $oTheme->get( 'AuthorURI' ),
				'strAuthor'			=> $oTheme->get( 'Author' ),				
			) + $arrCallerInfo;	
		}
	}

	/**
	 * Retrieves the library script info.
	 * 
	 * @since			2.1.1
	 */
	protected function getLibraryInfo() {
		return $this->getScriptData( __FILE__, 'library' ) + self::$arrStructure_CallerInfo;
	}
	
	/**
	 * Determines the script type.
	 * 
	 * It tries to find what kind of script this is, theme, plugin or something else from the given path.
	 * @since			2.0.0
	 * @return		string				Returns either 'theme', 'plugin', or 'unknown'
	 */ 
	protected function getCallerType( $strScriptPath ) {
		
		if ( preg_match( '/[\/\\\\]themes[\/\\\\]/', $strScriptPath, $m ) ) return 'theme';
		if ( preg_match( '/[\/\\\\]plugins[\/\\\\]/', $strScriptPath, $m ) ) return 'plugin';
		return 'unknown';	
	
	}
	protected function getCallerPath() {

		foreach( debug_backtrace() as $arrDebugInfo )  {			
			if ( $arrDebugInfo['file'] == __FILE__ ) continue;
			return $arrDebugInfo['file'];	// return the first found item.
		}
	}	
	
	/**
	 * Sets the default footer text on the left hand side.
	 * 
	 * @since			2.1.1
	 */
	protected function setFooterInfoLeft( $arrScriptInfo, &$strFooterInfoLeft ) {
		
		$strDescription = empty( $arrScriptInfo['strDescription'] ) 
			? ""
			: "&#13;{$arrScriptInfo['strDescription']}";
		$strVersion = empty( $arrScriptInfo['strVersion'] )
			? ""
			: "&nbsp;{$arrScriptInfo['strVersion']}";
		$strPluginInfo = empty( $arrScriptInfo['strURI'] ) 
			? $arrScriptInfo['strName'] 
			: "<a href='{$arrScriptInfo['strURI']}' target='_blank' title='{$arrScriptInfo['strName']}{$strVersion}{$strDescription}'>{$arrScriptInfo['strName']}</a>";
		$strAuthorInfo = empty( $arrScriptInfo['strAuthorURI'] )	
			? $arrScriptInfo['strAuthor'] 
			: "<a href='{$arrScriptInfo['strAuthorURI']}' target='_blank'>{$arrScriptInfo['strAuthor']}</a>";
		$strAuthorInfo = empty( $arrScriptInfo['strAuthor'] ) 
			? $strAuthorInfo 
			: ' by ' . $strAuthorInfo;
		$strFooterInfoLeft =  $strPluginInfo . $strAuthorInfo;
		
	}
	/**
	 * Sets the default footer text on the right hand side.
	 * 
	 * @since			2.1.1
	 */	
	protected function setFooterInfoRight( $arrScriptInfo, &$strFooterInfoRight ) {
	
		$strDescription = empty( $arrScriptInfo['strDescription'] ) 
			? ""
			: "&#13;{$arrScriptInfo['strDescription']}";
		$strVersion = empty( $arrScriptInfo['strVersion'] )
			? ""
			: "&nbsp;{$arrScriptInfo['strVersion']}";		
		$strLibraryInfo = empty( $arrScriptInfo['strURI'] ) 
			? $arrScriptInfo['strName'] 
			: "<a href='{$arrScriptInfo['strURI']}' target='_blank' title='{$arrScriptInfo['strName']}{$strVersion}{$strDescription}'>{$arrScriptInfo['strName']}</a>";	
	
		$strFooterInfoRight = $this->oMsg->___( 'powered_by' ) . '&nbsp;' 
			. $strLibraryInfo
			. ", <a href='http://wordpress.org' target='_blank' title='WordPress {$GLOBALS['wp_version']}'>WordPress</a>";
		
	}
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_LinkForPostType' ) ) :
/**
 * Provides methods for HTML link elements for custom post types.
 *
 * @since			2.0.0
 * @extends			AmazonAutoLinks_AdminPageFramework_Utilities
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AmazonAutoLinks_AdminPageFramework_LinkForPostType extends AmazonAutoLinks_AdminPageFramework_LinkBase {
	
	/**
	 * Stores the information to embed into the page footer.
	 * @since			2.0.0
	 * @remark			This is accessed from the AmazonAutoLinks_AdminPageFramework_PostType class.
	 */ 
	public $arrFooterInfo = array(
		'strLeft' => '',
		'strRight' => '',
	);
	
	public function __construct( $strPostTypeSlug, $strCallerPath=null, $oMsg=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->strPostTypeSlug = $strPostTypeSlug;
		$this->strCallerPath = file_exists( $strCallerPath ) ? $strCallerPath : $this->getCallerPath();
		$this->arrScriptInfo = $this->getCallerInfo( $this->strCallerPath ); 
		$this->arrLibraryInfo = $this->getLibraryInfo();
		
		$this->oMsg = $oMsg;
		
		$this->strSettingPageLinkTitle = $this->oMsg->___( 'manage' );
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		$this->setFooterInfoLeft( $this->arrScriptInfo, $this->arrFooterInfo['strLeft'] );
		$this->setFooterInfoRight( $this->arrLibraryInfo, $this->arrFooterInfo['strRight'] );
		
		// For the plugin listing page
		if ( $this->arrScriptInfo['strType'] == 'plugin' )
			add_filter( 
				'plugin_action_links_' . plugin_basename( $this->arrScriptInfo['strPath'] ),
				array( $this, 'addSettingsLinkInPluginListingPage' ), 
				20 	// set a lower priority so that the link will be embedded at the beginning ( the most left hand side ).
			);	
		
		// For post type posts listing table page ( edit.php )
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $this->strPostTypeSlug )
			add_action( 'get_edit_post_link', array( $this, 'addPostTypeQueryInEditPostLink' ), 10, 3 );
		
	}
	
	/*
	 * Callback methods
	 */ 
	/**
	 * Adds the <em>post_type</em> query key and value in the link url.
	 * 
	 * This is used to make it easier to detect if the linked page belongs to the post type created with this class.
	 * So it can be used to embed footer links.
	 * 
	 * @since			2.0.0
	 * @remark			e.g. http://.../wp-admin/post.php?post=180&action=edit -> http://.../wp-admin/post.php?post=180&action=edit&post_type=[...]
	 * @remark			A callback for the <em>get_edit_post_link</em> hook.
	 */	 
	public function addPostTypeQueryInEditPostLink( $strURL, $intPostID=null, $strContext=null ) {
		return add_query_arg( array( 'post' => $intPostID, 'action' => 'edit', 'post_type' => $this->strPostTypeSlug ), $strURL );	
	}	
	public function addSettingsLinkInPluginListingPage( $arrLinks ) {
		
		// http://.../wp-admin/edit.php?post_type=[...]
		array_unshift(	
			$arrLinks,
			"<a href='edit.php?post_type={$this->strPostTypeSlug}'>" . $this->strSettingPageLinkTitle . "</a>"
		); 
		return $arrLinks;		
		
	}
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function addInfoInFooterLeft( $strLinkHTML='' ) {
		
		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->strPostTypeSlug )
			return $strLinkHTML;	// $strLinkHTML is given by the hook.

		if ( empty( $this->arrScriptInfo['strName'] ) ) return $strLinkHTML;
					
		return $this->arrFooterInfo['strLeft'];
		
	}
	public function addInfoInFooterRight( $strLinkHTML='' ) {

		if ( ! isset( $_GET['post_type'] ) ||  $_GET['post_type'] != $this->strPostTypeSlug )
			return $strLinkHTML;	// $strLinkHTML is given by the hook.
			
		return $this->arrFooterInfo['strRight'];		
			
	}
}
endif;
 
if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Link' ) ) :
/**
 * Provides methods for HTML link elements for admin pages created by the framework, except the pages of custom post types.
 *
 * Embeds links in the footer and plugin's listing table etc.
 * 
 * @since			2.0.0
 * @extends			AmazonAutoLinks_AdminPageFramework_LinkBase
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Link
 */
class AmazonAutoLinks_AdminPageFramework_Link extends AmazonAutoLinks_AdminPageFramework_LinkBase {
	
	/**
	 * Stores the caller script path.
	 * @since			2.0.0
	 */ 
	private $strCallerPath;
	
	/**
	 * The property object, commonly shared.
	 * @since			2.0.0
	 */ 
	private $oProps;
	
	public function __construct( &$oProps, $strCallerPath=null, $oMsg=null ) {
		
		if ( ! is_admin() ) return;
		
		$this->oProps = $oProps;
		$this->strCallerPath = file_exists( $strCallerPath ) ? $strCallerPath : $this->getCallerPath();
		$this->oProps->arrScriptInfo = $this->getCallerInfo( $this->strCallerPath ); 
		$this->oProps->arrLibraryInfo = $this->getLibraryInfo();
		$this->oMsg = $oMsg;
		
		// Add script info into the footer 
		add_filter( 'update_footer', array( $this, 'addInfoInFooterRight' ), 11 );
		add_filter( 'admin_footer_text' , array( $this, 'addInfoInFooterLeft' ) );	
		$this->setFooterInfoLeft( $this->oProps->arrScriptInfo, $this->oProps->arrFooterInfo['strLeft'] );
		$this->setFooterInfoRight( $this->oProps->arrLibraryInfo, $this->oProps->arrFooterInfo['strRight'] );
	
		if ( $this->oProps->arrScriptInfo['strType'] == 'plugin' )
			add_filter( 'plugin_action_links_' . plugin_basename( $this->oProps->arrScriptInfo['strPath'] ) , array( $this, 'addSettingsLinkInPluginListingPage' ) );

	}

	
	/**	
	 * 
	 * @since			2.0.0
	 * @since			2.1.4			Changed to be static since it is used from multiple classes.
	 * @remark			The scope is public because this is accessed from an extended class.
	 */ 
	public static $arrStructure_SubMenuLink = array(		
		'strMenuTitle' => null,
		'strURL' => null,
		'strCapability' => null,
		'numOrder' => null,
		'strType' => 'link',
		'fShowPageHeadingTab' => true,
		'fShowInMenu' => true,
	);
	// public function addSubMenuLinks() {
		// foreach ( func_get_args() as $arrSubMenuLink ) {
			// $arrSubMenuLink = $arrSubMenuLink + self::$arrStructure_SubMenuLink;	// avoid undefined index warnings.
			// $this->addSubMenuLink(
				// $arrSubMenuLink['strMenuTitle'],
				// $arrSubMenuLink['strURL'],				
				// $arrSubMenuLink['strCapability'],
				// $arrSubMenuLink['numOrder']			
			// );				
		// }
	// }
	public function addSubMenuLink( $strMenuTitle, $strURL, $strCapability=null, $numOrder=null, $fShowPageHeadingTab=true, $fShowInMenu=true ) {
		
		$intCount = count( $this->oProps->arrPages );
		$this->oProps->arrPages[ $strURL ] = array(  
			'strMenuTitle'		=> $strMenuTitle,
			'strPageTitle'		=> $strMenuTitle,	// used for the page heading tabs.
			'strURL'			=> $strURL,
			'strType'			=> 'link',	// this is used to compare with the 'page' type.
			'strCapability'		=> isset( $strCapability ) ? $strCapability : $this->oProps->strCapability,
			'numOrder'			=> is_numeric( $numOrder ) ? $numOrder : $intCount + 10,
			'fShowPageHeadingTab'	=> $fShowPageHeadingTab,
			'fShowInMenu'		=> $fShowInMenu,
		);	
			
	}
			
	/*
	 * Methods for embedding links 
	 */ 	
	public function addLinkToPluginDescription( $vLinks ) {
		
		if ( !is_array( $vLinks ) )
			$this->oProps->arrPluginDescriptionLinks[] = $vLinks;
		else
			$this->oProps->arrPluginDescriptionLinks = array_merge( $this->oProps->arrPluginDescriptionLinks , $vLinks );
	
		add_filter( 'plugin_row_meta', array( $this, 'addLinkToPluginDescription_Callback' ), 10, 2 );

	}
	public function addLinkToPluginTitle( $vLinks ) {
		
		if ( !is_array( $vLinks ) )
			$this->oProps->arrPluginTitleLinks[] = $vLinks;
		else
			$this->oProps->arrPluginTitleLinks = array_merge( $this->oProps->arrPluginTitleLinks, $vLinks );
		
		add_filter( 'plugin_action_links_' . plugin_basename( $this->oProps->arrScriptInfo['strPath'] ), array( $this, 'AddLinkToPluginTitle_Callback' ) );

	}
	
	/*
	 * Callback methods
	 */ 
	
	/**
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the filter hook, <em>admin_footer_text</em>.
	 */ 
	public function addInfoInFooterLeft( $strLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProps->isPageAdded( $_GET['page'] )  ) 
			return $strLinkHTML;	// $strLinkHTML is given by the hook.
		
		if ( empty( $this->oProps->arrScriptInfo['strName'] ) ) return $strLinkHTML;
		
		return $this->oProps->arrFooterInfo['strLeft'];

	}
	public function addInfoInFooterRight( $strLinkHTML='' ) {

		if ( ! isset( $_GET['page'] ) || ! $this->oProps->isPageAdded( $_GET['page'] )  ) 
			return $strLinkHTML;	// $strLinkTHML is given by the hook.
			
		return $this->oProps->arrFooterInfo['strRight'];
			
	}
	
	public function addSettingsLinkInPluginListingPage( $arrLinks ) {
		
		// For a custom root slug,
		$strLinkURL = preg_match( '/^.+\.php/', $this->oProps->arrRootMenu['strPageSlug'] ) 
			? add_query_arg( array( 'page' => $this->oProps->strDefaultPageSlug ), admin_url( $this->oProps->arrRootMenu['strPageSlug'] ) )
			: "admin.php?page={$this->oProps->strDefaultPageSlug}";
		
		array_unshift(	
			$arrLinks,
			'<a href="' . $strLinkURL . '">' . $this->oMsg->___( 'settings' ) . '</a>'
		); 
		return $arrLinks;
		
	}	
	
	public function addLinkToPluginDescription_Callback( $arrLinks, $strFile ) {

		if ( $strFile != plugin_basename( $this->oProps->arrScriptInfo['strPath'] ) ) return $arrLinks;
		
		// Backward compatibility sanitization.
		$arrAddingLinks = array();
		foreach( $this->oProps->arrPluginDescriptionLinks as $vLinkHTML )
			if ( is_array( $vLinkHTML ) )	// should not be an array
				$arrAddingLinks = array_merge( $vLinkHTML, $arrAddingLinks );
			else
				$arrAddingLinks[] = ( string ) $vLinkHTML;
		
		return array_merge( $arrLinks, $arrAddingLinks );
		
	}			
	public function addLinkToPluginTitle_Callback( $arrLinks ) {

		// Backward compatibility sanitization.
		$arrAddingLinks = array();
		foreach( $this->oProps->arrPluginTitleLinks as $vLinkHTML )
			if ( is_array( $vLinkHTML ) )	// should not be an array
				$arrAddingLinks = array_merge( $vLinkHTML, $arrAddingLinks );
			else
				$arrAddingLinks[] = ( string ) $vLinkHTML;
		
		return array_merge( $arrLinks, $arrAddingLinks );
		
	}		
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_PageLoadStats_Base' ) ) :
/**
 * Collects data of page loads in admin pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
abstract class AmazonAutoLinks_AdminPageFramework_PageLoadStats_Base {
	
	function __construct( $oProps, $oMsg ) {
		
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			
			$this->oProps = $oProps;
			$this->oMsg = $oMsg;
			$this->nInitialMemoryUsage = memory_get_usage();
			add_action( 'admin_menu', array( $this, 'replyToSetPageLoadInfoInFooter' ), 999 );	// must be loaded after the sub pages are registered
						
		}

	}
	
	/**
	 * @remark			Should be overridden in an extended class.
	 */
	public function replyToSetPageLoadInfoInFooter() {}
		
	/**
	 * Display gathered information.
	 *
	 * @access			public
	 */
	public function replyToGetPageLoadStats( $sFooterHTML ) {
		
		// Get values we're displaying
		$nSeconds 				= timer_stop(0);
		$nQueryCount 			= get_num_queries();
		$memory_usage 			= round( $this->convert_bytes_to_hr( memory_get_usage() ), 2 );
		$memory_peak_usage 		= round( $this->convert_bytes_to_hr( memory_get_peak_usage() ), 2 );
		$memory_limit 			= round( $this->convert_bytes_to_hr( $this->let_to_num( WP_MEMORY_LIMIT ) ), 2 );
		$sInitialMemoryUsage	= round( $this->convert_bytes_to_hr( $this->nInitialMemoryUsage ), 2 );
				
		$sOutput = 
			"<div id='admin-page-framework-page-load-stats'>"
				. "<ul>"
					. "<li>" . sprintf( $this->oMsg->___( 'queries_in_seconds' ), $nQueryCount, $nSeconds ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->___( 'out_of_x_memory_used' ), $memory_usage, $memory_limit, round( ( $memory_usage / $memory_limit ), 2 ) * 100 . '%' ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->___( 'peak_memory_usage' ), $memory_peak_usage ) . "</li>"
					. "<li>" . sprintf( $this->oMsg->___( 'initial_memory_usage' ), $sInitialMemoryUsage ) . "</li>"
				. "</ul>"
			. "</div>";
		return $sFooterHTML . $sOutput;
		
	}

	/**
	 * let_to_num function.
	 *
	 * This function transforms the php.ini notation for numbers (like '2M') to an integer
	 *
	 * @access public
	 * @param $size
	 * @return int
	 * @author			Mike Jolley
	 * @see				http://mikejolley.com/projects/wp-page-load-stats/
	 */
	function let_to_num( $size ) {
		$l 		= substr( $size, -1 );
		$ret 	= substr( $size, 0, -1 );
		switch( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}
		return $ret;
	}

	/**
	 * convert_bytes_to_hr function.
	 *
	 * @access public
	 * @param mixed $bytes
	 * @author			Mike Jolley
	 * @see				http://mikejolley.com/projects/wp-page-load-stats/
	 */
	function convert_bytes_to_hr( $bytes ) {
		$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
		$log = log( $bytes, 1024 );
		$power = ( int ) $log;
		$size = pow( 1024, $log - $power );
		return $size . $units[ $power ];
	}

}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_PageLoadStats_Page' ) ) :
/**
 * Collects data of page loads of the added pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AmazonAutoLinks_AdminPageFramework_PageLoadStats_Page extends AmazonAutoLinks_AdminPageFramework_PageLoadStats_Base {
	
	private static $oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $oProps, $oMsg ) {
		
		if ( ! isset( self::$oInstance ) && ! ( self::$oInstance instanceof AmazonAutoLinks_AdminPageFramework_PageLoadStats_Page ) ) 
			self::$oInstance = new AmazonAutoLinks_AdminPageFramework_PageLoadStats_Page( $oProps, $oMsg );
		return self::$oInstance;
		
	}		
	
	/**
	 * Sets the hook if the current page is one of the framework's added pages.
	 */ 
	public function replyToSetPageLoadInfoInFooter() {
		
		// For added pages
		$strCurrentPageSlug = isset( $_GET['page'] ) ? $_GET['page'] : '';
		if ( $this->oProps->isPageAdded( $strCurrentPageSlug ) ) 
			add_filter( 'update_footer', array( $this, 'replyToGetPageLoadStats' ), 999 );
	
	}		
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_PageLoadStats_PostType' ) ) :
/**
 * Collects data of page loads of the added post type pages.
 *
 * @since			2.1.7
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AmazonAutoLinks_AdminPageFramework_PageLoadStats_PostType extends AmazonAutoLinks_AdminPageFramework_PageLoadStats_Base {
	
	private static $oInstance;
	
	/**
	 * Ensures that only one instance of this class object exists. ( no multiple instances of this object ) 
	 * 
	 * @remark			This class should be instantiated via this method.
	 */
	public static function instantiate( $oProps, $oMsg ) {
		
		if ( ! isset( self::$oInstance ) && ! ( self::$oInstance instanceof AmazonAutoLinks_AdminPageFramework_PageLoadStats_PostType ) ) 
			self::$oInstance = new AmazonAutoLinks_AdminPageFramework_PageLoadStats_PostType( $oProps, $oMsg );
		return self::$oInstance;
		
	}	

	/**
	 * Sets the hook if the current page is one of the framework's added post type pages.
	 */ 
	public function replyToSetPageLoadInfoInFooter() {

		// Some users sets $_GET['post_type'] element even in regular admin pages. In that case, do not load the style to avoid duplicates.
		if ( isset( $_GET['page'] ) && $_GET['page'] ) return;
	
		// For post type pages
		if ( isset( $_GET['post_type'], $this->oProps->strPostType ) && $_GET['post_type'] == $this->oProps->strPostType )
			add_filter( 'update_footer', array( $this, 'replyToGetPageLoadStats' ), 999 );
		
	}	
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_Debug' ) ) :
/**
 * Provides debugging methods.
 *
 * @since			2.0.0
 * @extends			n/a
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Utility
 */
class AmazonAutoLinks_AdminPageFramework_Debug {
		
	public function dumpArray( $arr, $strFilePath=null ) {
				
		echo $this->getArray( $arr, $strFilePath );
		
	}
	
	/**
	 * 
	 * @since			2.1.6			The $fEncloseInTag parameter is added.
	 */
	public function getArray( $arr, $strFilePath=null, $fEncloseInTag=true ) {
			
		if ( $strFilePath ) 
			self::logArray( $arr, $strFilePath );			
			
		// esc_html() has a bug that breaks with complex HTML code.
		$strResult = htmlspecialchars( print_r( $arr, true ) );
		return $fEncloseInTag
			? "<pre class='dump-array'>" . $strResult . "</pre>"
			: $strResult;
		
	}	
	
	/**
	 * Logs given array output into the given file.
	 * 
	 * @since			2.1.1
	 */
	static public function logArray( $arr, $strFilePath=null ) {
		
		file_put_contents( 
			$strFilePath ? $strFilePath : dirname( __FILE__ ) . '/array_log.txt', 
			date( "Y/m/d H:i:s", current_time( 'timestamp' ) ) . PHP_EOL
			. print_r( $arr, true ) . PHP_EOL . PHP_EOL
			, FILE_APPEND 
		);					
							
	}	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base' ) ) :
/**
 * The base class of field type classes that define input field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
abstract class AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base extends AmazonAutoLinks_AdminPageFramework_Utilities {
	
	protected static $arrDefaultKeys = array(
		'vValue'				=> null,				// ( array or string ) this suppress the default key value. This is useful to display the value saved in a custom place other than the framework automatically saves.
		'vDefault'				=> null,				// ( array or string )
		'fRepeatable'			=> false,
		'vClassAttribute'		=> '',					// ( array or string ) the class attribute of the input field. Do not set an empty value here, but null because the submit field type uses own default value.
		'vLabel'				=> '',					// ( array or string ) labels for some input fields. Do not set null here because it is casted as string in the field output methods, which creates an element of empty string so that it can be iterated with foreach().
		'vDelimiter'			=> '',
		'vDisable'				=> false,				// ( array or boolean ) This value indicates whether the set field is disabled or not. 
		'vReadOnly'				=> false,				// ( array or boolean ) sets the readonly attribute to text and textarea input fields.
		'vBeforeInputTag'		=> '',
		'vAfterInputTag'		=> '',				
		'vLabelMinWidth'		=> 140,
		
		// Mandatory keys.
		'strFieldID' => null,		
		
		// For the meta box class - it does not require the following keys; these are just to help to avoid undefined index warnings.
		'strPageSlug' => null,
		'strSectionID' => null,
		'strBeforeField' => null,
		'strAfterField' => null,	
	);	
	
	protected $oMsg;
	
	function __construct( $strClassName, $strFieldTypeSlug, $oMsg=null, $fAutoRegister=true ) {
			
		$this->strFieldTypeSlug = $strFieldTypeSlug;
		$this->strClassName = $strClassName;
		$this->oMsg	= $oMsg;
		
		// This automatically registers the field type. The build-in ones will be registered manually so it will be skipped.
		if ( $fAutoRegister )
			add_filter( "field_types_{$strClassName}", array( $this, 'replyToRegisterInputFieldType' ) );
	
	}	
	
	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$strClassName} filter.
	 * 
	 */
	public function replyToRegisterInputFieldType( $arrFieldDefinitions ) {
		
		$arrFieldDefinitions[ $this->strFieldTypeSlug ] = $this->getDefinitionArray();
		return $arrFieldDefinitions;
		
	}
	
	/**
	 * Returns the field type definition array.
	 * 
	 * @remark			The scope is public since AmazonAutoLinks_AdminPageFramework_CustomFieldType class allows the user to use this method.
	 * @since			2.1.5
	 */
	public function getDefinitionArray() {
		
		return array(
			'callRenderField' => array( $this, "replyToGetInputField" ),
			'callGetScripts' => array( $this, "replyToGetInputScripts" ),
			'callGetStyles' => array( $this, "replyToGetInputStyles" ),
			'callGetIEStyles' => array( $this, "replyToGetInputIEStyles" ),
			'callFieldLoader' => array( $this, "replyToFieldLoader" ),
			'arrEnqueueScripts' => $this->getEnqueuingScripts(),	// urls of the scripts
			'arrEnqueueStyles' => $this->getEnqueuingStyles(),	// urls of the styles
			'arrDefaultKeys' => $this->getDefaultKeys() + self::$arrDefaultKeys, 
		);
		
	}
	
	/*
	 * These methods should be overridden in the extended class.
	 */
	public function replytToGetInputField() { return ''; }	// should return the field output
	public function replyToGetInputScripts() { return ''; }	// should return the script
	public function replyToGetInputIEStyles() { return ''; }	// should return the style for IE
	public function replyToGetInputStyles() { return ''; }	// should return the style
	public function replyToFieldLoader() {}	// do stuff that should be done when the field type is loaded for the first time.
	protected function getEnqueuingScripts() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function getEnqueuingStyles() { return array(); }	// should return an array holding the urls of enqueuing items
	protected function getDefaultKeys() { return array(); }
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_CustomFieldType' ) ) :
/**
 * The base class for the users to create their custom field types.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
abstract class AmazonAutoLinks_AdminPageFramework_CustomFieldType extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_default' ) ) :
/**
 * Defines the default field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_default extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'vSize'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * This one is triggered when the called field type is unknown. This does not insert the input tag but just renders the value stored in the $vValue variable.
	 * 
	 * @since			2.1.5				
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		
				
		foreach( ( array ) $vValue as $strKey => $strValue ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$strTagID}_{$strKey}'>"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
							. ( ( $strLabel = $this->getCorrespondingArrayValue( $arrField['vLabel'], $strKey, $arrDefaultKeys['vLabel'] ) ) 
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>{$strLabel}</span>" 
								: "" 
							)
							. "<div class='admin-page-framework-input-container'>"
								. $strValue
							. "</div>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"		
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-default' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}

}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_text' ) ) :
/**
 * Defines the text field type.
 * 
 * Also the field types of 'password', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', and 'week' are defeined.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_text extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {

	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$strClassName} filter.
	 * 
	 * @remark			Since there are the other type slugs that are shared with the text field type, register them as well. 
	 */
	public function replyToRegisterInputFieldType( $arrFieldDefinitions ) {
		
		foreach ( array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'url', 'week', ) as $strTextTypeSlug )
			$arrFieldDefinitions[ $strTextTypeSlug ] = $this->getDefinitionArray();

		return $arrFieldDefinitions;
		
	}
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vSize'					=> 30,
			'vMaxLength'			=> 400,
		);	
	}
	/**
	 * Returns the output of the text input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];
		
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];
		$fMultiple = is_array( $arrFields );
		
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$strTagID}_{$strKey}'>"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, '' ) 
							. ( $strLabel && ! $arrField['fRepeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>"
								: "" 
							)
							. "<input id='{$strTagID}_{$strKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, '' ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, 30 ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
								. "type='{$arrField['strType']}' "	// text, password, etc.
								. "name=" . ( is_array( $arrFields ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $strKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"		
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				)
			;
				
		return "<div class='admin-page-framework-field-text' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";

	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_number' ) ) :
/**
 * Defines the number, and range field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_number extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {

	/**
	 * Registers the field type.
	 * 
	 * A callback function for the field_types_{$strClassName} filter.
	 * 
	 * @remark			Since there are the other type slugs that are shared with the text field type, register them as well. 
	 */
	public function replyToRegisterInputFieldType( $arrFieldDefinitions ) {
		
		foreach ( array( 'number', 'range' ) as $strTextTypeSlug ) 
			$arrFieldDefinitions[ $strTextTypeSlug ] = $this->getDefinitionArray();
		return $arrFieldDefinitions;
		
	}
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vMin'				=> null,
			'vMax'				=> null,
			'vStep'				=> null,
			'vSize'				=> 30,
			'vMaxLength'		=> 400,
		);	
	}
	
	/**
	 * Returns the output of the number input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {
		
		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];
		
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];
			
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$strTagID}_{$strKey}' >"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, '' ) 
							. ( $strLabel && ! $arrField['fRepeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>"
								: ""
							)
							. "<input id='{$strTagID}_{$strKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, '' ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, 30 ) . "' "
								. "type='{$arrField['strType']}' "
								. "name=" . ( is_array( $arrFields ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $vValue, $strKey, null ) . "' "
								. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
								. "min='" . $this->getCorrespondingArrayValue( $arrField['vMin'], $strKey, $arrDefaultKeys['vMin'] ) . "' "
								. "max='" . $this->getCorrespondingArrayValue( $arrField['vMax'], $strKey, $arrDefaultKeys['vMax'] ) . "' "
								. "step='" . $this->getCorrespondingArrayValue( $arrField['vStep'], $strKey, $arrDefaultKeys['vStep'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
							. "/>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);				
					
		return "<div class='admin-page-framework-field-number' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		
		
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_textarea' ) ) :
/**
 * Defines the textarea field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_textarea extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vRows'					=> 4,
			'vCols'					=> 80,
			'vRich'					=> false,
			'vMaxLength'			=> 400,
		);	
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"	/* Rich Text Editor */
			.admin-page-framework-field-textarea .wp-core-ui.wp-editor-wrap {
				margin-bottom: 0.5em;
			}		
		" . PHP_EOL;		
	}	
		
	/**
	 * Returns the output of the textarea input field.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];
		
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];			
		$fSingle = ! is_array( $arrFields );
		
		foreach( ( array ) $arrFields as $strKey => $strLabel ) {
			
			$arrRichEditorSettings = $fSingle
				? $arrField['vRich']
				: $this->getCorrespondingArrayValue( $arrField['vRich'], $strKey, null );
				
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$strTagID}_{$strKey}' >"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, '' ) 
							. ( $strLabel && ! $arrField['fRepeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>"
								: "" 
							)
							. ( ! empty( $arrRichEditorSettings ) && version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) && function_exists( 'wp_editor' )
								? wp_editor( 
									$this->getCorrespondingArrayValue( $vValue, $strKey, null ), 
									"{$strTagID}_{$strKey}",  
									$this->uniteArrays( 
										( array ) $arrRichEditorSettings,
										array(
											'wpautop' => true, // use wpautop?
											'media_buttons' => true, // show insert/upload button(s)
											'textarea_name' => is_array( $arrFields ) ? "{$strFieldName}[{$strKey}]" : $strFieldName , // set the textarea name to something different, square brackets [] can be used here
											'textarea_rows' => $this->getCorrespondingArrayValue( $arrField['vRows'], $strKey, $arrDefaultKeys['vRows'] ),
											'tabindex' => '',
											'tabfocus_elements' => ':prev,:next', // the previous and next element ID to move the focus to when pressing the Tab key in TinyMCE
											'editor_css' => '', // intended for extra styles for both visual and Text editors buttons, needs to include the <style> tags, can use "scoped".
											'editor_class' => $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, '' ), // add extra class(es) to the editor textarea
											'teeny' => false, // output the minimal editor config used in Press This
											'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
											'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
											'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()													
										)
									)
								) . $this->getScriptForRichEditor( "{$strTagID}_{$strKey}" )
								: "<textarea id='{$strTagID}_{$strKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, '' ) . "' "
									. "rows='" . $this->getCorrespondingArrayValue( $arrField['vRows'], $strKey, $arrDefaultKeys['vRows'] ) . "' "
									. "cols='" . $this->getCorrespondingArrayValue( $arrField['vCols'], $strKey, $arrDefaultKeys['vCols'] ) . "' "
									. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
									. "type='{$arrField['strType']}' "
									. "name=" . ( is_array( $arrFields ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
									. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
								. ">"
									. $this->getCorrespondingArrayValue( $vValue, $strKey, null )
								. "</textarea>"
							)
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, '' )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, '', true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		}
		
		return "<div class='admin-page-framework-field-textarea' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		

	}	
		/**
		 * A helper function for the above getTextAreaField() method.
		 * 
		 * This adds a script that forces the rich editor element to be inside the field table cell.
		 * 
		 * @since			2.1.2
		 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField.
		 */	
		private function getScriptForRichEditor( $strIDSelector ) {

			// id: wp-sample_rich_textarea_0-wrap
			return "<script type='text/javascript'>
				jQuery( '#wp-{$strIDSelector}-wrap' ).hide();
				jQuery( document ).ready( function() {
					jQuery( '#wp-{$strIDSelector}-wrap' ).appendTo( '#field-{$strIDSelector}' );
					jQuery( '#wp-{$strIDSelector}-wrap' ).show();
				})
			</script>";		
			
		}	
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_color' ) ) :
/**
 * Defines the color field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_color extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vSize'					=> 10,
			'vMaxLength'			=> 400,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 * 
	 * Loads necessary files of the color field type.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_MetaBox. Changed the name from enqueueColorFieldScript().
	 * @see				http://www.sitepoint.com/upgrading-to-the-new-wordpress-color-picker/
	 */ 
	public function replyToFieldLoader() {
		
		// If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
		if ( version_compare( $GLOBALS['wp_version'], '3.5', '>=' ) ) {
			//Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}
		//If the WordPress version is less than 3.5 load the older farbtasic color picker.
		else {
			//As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
		}	
		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"/* Color Picker */
			.repeatable .colorpicker {
				display: inline;
			}
			.admin-page-framework-field-color .wp-picker-container {
				vertical-align: middle;
			}
			.admin-page-framework-field-color .ui-widget-content {
				border: none;
				background: none;
				color: transparent;
			}
			.admin-page-framework-field-color .ui-slider-vertical {
				width: inherit;
				height: auto;
				margin-top: -11px;
			}	
			" . PHP_EOL;		
	}	
	
	/**
	 * Returns the color picker JavaScript script loaded in the head tag of the created admin pages.
	 * @since			2.0.0
	 * @since			2.1.3			Changed to define a global function literal that registers the given input field as a color picker.
	 * @since			2.1.5			Changed the name from getColorPickerScript().
	 * @var				string
	 * @remark			It is accessed from the main class and meta box class.
	 * @remark			This is made to be a method rather than a property because in the future a variable may need to be used in the script code like the above image selector script.
	 * @access			public	
	 * @internal
	 * @return			string			The image selector script.
	 */ 
	public function replyToGetInputScripts() {
		return "
			registerAPFColorPickerField = function( strInputID ) {
				'use strict';
				// This if statement checks if the color picker element exists within jQuery UI
				// If it does exist then we initialize the WordPress color picker on our text input field
				if( typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function' ){
					var myColorPickerOptions = {
						defaultColor: false,	// you can declare a default color here, or in the data-default-color attribute on the input				
						change: function(event, ui){},	// a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/			
						clear: function() {},	// a callback to fire when the input is emptied or an invalid color
						hide: true,	// hide the color picker controls on load
						palettes: true	// show a group of common colors beneath the square or, supply an array of colors to customize further
					};			
					jQuery( '#' + strInputID ).wpColorPicker( myColorPickerOptions );
				}
				else {
					// We use farbtastic if the WordPress color picker widget doesn't exist
					jQuery( '#color_' + strInputID ).farbtastic( '#' + strInputID );
				}
			}
		";		
	}	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];
	
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];		
	
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$strTagID}_{$strKey}'>"					
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
							. ( $strLabel && ! $arrField['fRepeatable']
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>"
								: "" 
							)
							. "<input id='{$strTagID}_{$strKey}' "
								. "class='input_color " . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
								. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, $arrDefaultKeys['vSize'] ) . "' "
								. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
								. "type='text' "	// text
								. "name=" . ( is_array( $arrFields ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
								. "value='" . ( $this->getCorrespondingArrayValue( $vValue, $strKey, 'transparent' ) ) . "' "
								. "color='" . ( $this->getCorrespondingArrayValue( $vValue, $strKey, 'transparent' ) ) . "' "
								. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
								. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
						. "<div class='colorpicker' id='color_{$strTagID}_{$strKey}' rel='{$strTagID}_{$strKey}'></div>"	// this div element with this class selector becomes a farbtastic color picker. ( below 3.4.x )
						. $this->getColorPickerEnablerScript( "{$strTagID}_{$strKey}" )
					. "</div>"
				. "</div>"	// admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-color' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";	
		
	}
		/**
		 * A helper function for the above getColorField() method to add a script to enable the color picker.
		 */
		private function getColorPickerEnablerScript( $strInputID ) {
			return
				"<script type='text/javascript' class='color-picker-enabler-script'>
					jQuery( document ).ready( function(){
						registerAPFColorPickerField( '{$strInputID}' );
					});
				</script>";
		}	

	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_image' ) ) :
/**
 * Defines the image field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_image extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(			
			'arrCaptureAttributes'					=> array(),	// ( array ) This is for the image and media field type. The attributes to save besides URL. e.g. ( for the image field type ) array( 'title', 'alt', 'width', 'height', 'caption', 'id', 'align', 'link' ).
			'vSize'									=> 60,
			'vMaxLength'							=> 400,
			'vImagePreview'							=> true,	// ( array or boolean )	This is for the image field type. For array, each element should contain a boolean value ( true/false ).
			'strTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'strLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'fAllowExternalSource' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		$this->enqueueMediaUploader();	
	}	
	/**
	 * Enqueues scripts and styles for the media uploader.
	 * 
	 * @remark			Used by the image and media field types.
	 * @since			2.1.5
	 */
	protected function enqueueMediaUploader() {
		
		// add_filter( 'gettext', array( $this, 'replyToReplacingThickBoxText' ) , 1, 2 );
		add_filter( 'media_upload_tabs', array( $this, 'replyToRemovingMediaLibraryTab' ) );
		
		wp_enqueue_script( 'jquery' );			
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 'thickbox' );
	
		if ( function_exists( 'wp_enqueue_media' ) ) 	// means the WordPress version is 3.5 or above
			wp_enqueue_media();	
		else		
			wp_enqueue_script( 'media-upload' );
			
	}
		/**
		 * Removes the From URL tab from the media uploader.
		 * 
		 * since			2.1.3
		 * since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_SettingsAPI. Changed the name from removeMediaLibraryTab() to replyToRemovingMediaLibraryTab().
		 * @remark			A callback for the <em>media_upload_tabs</em> hook.	
		 */
		public function replyToRemovingMediaLibraryTab( $arrTabs ) {
			
			if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $arrTabs;
			
			if ( ! $_REQUEST['enable_external_source'] )
				unset( $arrTabs['type_url'] );	// removes the From URL tab in the thick box.
			
			return $arrTabs;
			
		}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {		
		return $this->getScript_CustomMediaUploaderObject()	. PHP_EOL	
			. $this->getScript_ImageSelector( 
				"admin_page_framework", 
				$this->oMsg->___( 'upload_image' ),
				$this->oMsg->___( 'use_this_image' )
		);
	}
		/**
		 * Returns the JavaScript script that creates a custom media uploader object.
		 * 
		 * @remark			Used by the image and media field types.
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_Properties_Base.
		 */
		protected function getScript_CustomMediaUploaderObject() {
			
			 $fLoaded = isset( $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] )
				? $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] : false;
			
			if( ! function_exists( 'wp_enqueue_media' ) || $fLoaded )	// means the WordPress version is 3.4.x or below
				return "";
			
			$GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['fIsLoadedCustomMediaUploaderObject'] = true;
			
			// Global function literal
			return "
				getAPFCustomMediaUploaderSelectObject = function() {
					return wp.media.view.MediaFrame.Select.extend({

						initialize: function() {
							wp.media.view.MediaFrame.prototype.initialize.apply( this, arguments );

							_.defaults( this.options, {
								multiple:  true,
								editing:   false,
								state:    'insert'
							});

							this.createSelection();
							this.createStates();
							this.bindHandlers();
							this.createIframeStates();
						},

						createStates: function() {
							var options = this.options;

							// Add the default states.
							this.states.add([
								// Main states.
								new wp.media.controller.Library({
									id:         'insert',
									title:      'Insert Media',
									priority:   20,
									toolbar:    'main-insert',
									filterable: 'image',
									library:    wp.media.query( options.library ),
									multiple:   options.multiple ? 'reset' : false,
									editable:   true,

									// If the user isn't allowed to edit fields,
									// can they still edit it locally?
									allowLocalEdits: true,

									// Show the attachment display settings.
									displaySettings: true,
									// Update user settings when users adjust the
									// attachment display settings.
									displayUserSettings: true
								}),

								// Embed states.
								new wp.media.controller.Embed(),
							]);


							if ( wp.media.view.settings.post.featuredImageId ) {
								this.states.add( new wp.media.controller.FeaturedImage() );
							}
						},

						bindHandlers: function() {
							// from Select
							this.on( 'router:create:browse', this.createRouter, this );
							this.on( 'router:render:browse', this.browseRouter, this );
							this.on( 'content:create:browse', this.browseContent, this );
							this.on( 'content:render:upload', this.uploadContent, this );
							this.on( 'toolbar:create:select', this.createSelectToolbar, this );
							//

							this.on( 'menu:create:gallery', this.createMenu, this );
							this.on( 'toolbar:create:main-insert', this.createToolbar, this );
							this.on( 'toolbar:create:main-gallery', this.createToolbar, this );
							this.on( 'toolbar:create:featured-image', this.featuredImageToolbar, this );
							this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

							var handlers = {
									menu: {
										'default': 'mainMenu'
									},

									content: {
										'embed':          'embedContent',
										'edit-selection': 'editSelectionContent'
									},

									toolbar: {
										'main-insert':      'mainInsertToolbar'
									}
								};

							_.each( handlers, function( regionHandlers, region ) {
								_.each( regionHandlers, function( callback, handler ) {
									this.on( region + ':render:' + handler, this[ callback ], this );
								}, this );
							}, this );
						},

						// Menus
						mainMenu: function( view ) {
							view.set({
								'library-separator': new wp.media.View({
									className: 'separator',
									priority: 100
								})
							});
						},

						// Content
						embedContent: function() {
							var view = new wp.media.view.Embed({
								controller: this,
								model:      this.state()
							}).render();

							this.content.set( view );
							view.url.focus();
						},

						editSelectionContent: function() {
							var state = this.state(),
								selection = state.get('selection'),
								view;

							view = new wp.media.view.AttachmentsBrowser({
								controller: this,
								collection: selection,
								selection:  selection,
								model:      state,
								sortable:   true,
								search:     false,
								dragInfo:   true,

								AttachmentView: wp.media.view.Attachment.EditSelection
							}).render();

							view.toolbar.set( 'backToLibrary', {
								text:     'Return to Library',
								priority: -100,

								click: function() {
									this.controller.content.mode('browse');
								}
							});

							// Browse our library of attachments.
							this.content.set( view );
						},

						// Toolbars
						selectionStatusToolbar: function( view ) {
							var editable = this.state().get('editable');

							view.set( 'selection', new wp.media.view.Selection({
								controller: this,
								collection: this.state().get('selection'),
								priority:   -40,

								// If the selection is editable, pass the callback to
								// switch the content mode.
								editable: editable && function() {
									this.controller.content.mode('edit-selection');
								}
							}).render() );
						},

						mainInsertToolbar: function( view ) {
							var controller = this;

							this.selectionStatusToolbar( view );

							view.set( 'insert', {
								style:    'primary',
								priority: 80,
								text:     'Select Image',
								requires: { selection: true },

								click: function() {
									var state = controller.state(),
										selection = state.get('selection');

									controller.close();
									state.trigger( 'insert', selection ).reset();
								}
							});
						},

						featuredImageToolbar: function( toolbar ) {
							this.createSelectToolbar( toolbar, {
								text:  'Set Featured Image',
								state: this.options.state || 'upload'
							});
						},

						mainEmbedToolbar: function( toolbar ) {
							toolbar.view = new wp.media.view.Toolbar.Embed({
								controller: this,
								text: 'Insert Image'
							});
						}		
					});
				}
			";
		}	
		/**
		 * Returns the image selector JavaScript script to be loaded in the head tag of the created admin pages.
		 * @var				string
		 * @remark			It is accessed from the main class and meta box class.
		 * @remark			Moved to the base class since 2.1.0.
		 * @access			private	
		 * @internal
		 * @return			string			The image selector script.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AmazonAutoLinks_AdminPageFramework_Properties_Base class. Changed the name from getImageSelectorScript(). Changed the scope to private and not static anymore.
		 */		
		private function getScript_ImageSelector( $strReferrer, $strThickBoxTitle, $strThickBoxButtonUseThis ) {
			
			if( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_image' ).click( function() {
							pressed_id = jQuery( this ).attr( 'id' );
							field_id = pressed_id.substring( 13 );	// remove the select_image_ prefix
							var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );
							tb_show( '{$strThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$strReferrer}&amp;button_label={$strThickBoxButtonUseThis}&amp;type=image&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
						});
						
						window.original_send_to_editor = window.send_to_editor;
						window.send_to_editor = function( strRawHTML ) {

							var strHTML = '<div>' + strRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
							var src = jQuery( 'img', strHTML ).attr( 'src' );
							var alt = jQuery( 'img', strHTML ).attr( 'alt' );
							var title = jQuery( 'img', strHTML ).attr( 'title' );
							var width = jQuery( 'img', strHTML ).attr( 'width' );
							var height = jQuery( 'img', strHTML ).attr( 'height' );
							var classes = jQuery( 'img', strHTML ).attr( 'class' );
							var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : '';	// attachment ID	
							var strCaption = strRawHTML.replace( /\[(\w+).*?\](.*?)\[\/(\w+)\]/m, '$2' )
								.replace( /<a.*?>(.*?)<\/a>/m, '' );
							var align = strRawHTML.replace( /^.*?\[\w+.*?\salign=([\'\"])(.*?)[\'\"]\s.+$/mg, '$2' );	//\'\" syntax fixer
							var link = jQuery( strHTML ).find( 'a:first' ).attr( 'href' );

							// Escape the strings of some of the attributes.
							var strCaption = jQuery( '<div/>' ).text( strCaption ).html();
							var strAlt = jQuery( '<div/>' ).text( alt ).html();
							var strTitle = jQuery( '<div/>' ).text( title ).html();						
							
							// If the user wants to save relevant attributes, set them.
							jQuery( '#' + field_id ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + field_id + '_id' ).val( id );
							jQuery( '#' + field_id + '_width' ).val( width );
							jQuery( '#' + field_id + '_height' ).val( height );
							jQuery( '#' + field_id + '_caption' ).val( strCaption );
							jQuery( '#' + field_id + '_alt' ).val( strAlt );
							jQuery( '#' + field_id + '_title' ).val( strTitle );						
							jQuery( '#' + field_id + '_align' ).val( align );						
							jQuery( '#' + field_id + '_link' ).val( link );						
							
							// Update the preview
							jQuery( '#image_preview_' + field_id ).attr( 'alt', alt );
							jQuery( '#image_preview_' + field_id ).attr( 'title', strTitle );
							jQuery( '#image_preview_' + field_id ).attr( 'data-classes', classes );
							jQuery( '#image_preview_' + field_id ).attr( 'data-id', id );
							jQuery( '#image_preview_' + field_id ).attr( 'src', src );	// updates the preview image
							jQuery( '#image_preview_container_' + field_id ).css( 'display', '' );	// updates the visibility
							jQuery( '#image_preview_' + field_id ).show()	// updates the visibility
							
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
							
							// close the thickbox
							tb_remove();	

						}
					});
				";
					
			return "jQuery( document ).ready( function(){

				// Global Function Literal 
				setAPFImageUploader = function( strInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_image_' + strInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_image_' + strInputID ).click( function( e ) {
						
						window.wpActiveEditor = null;						
						e.preventDefault();
						
						// If the uploader object has already been created, reopen the dialog
						if ( custom_uploader ) {
							custom_uploader.open();
							return;
						}					
						
						// Store the original select object in a global variable
						oAPFOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;
						
						// Assign a custom select object.
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalImageUploaderSelectObject;
						var custom_uploader = wp.media({
							title: '{$strThickBoxTitle}',
							button: {
								text: '{$strThickBoxButtonUseThis}'
							},
							library     : { type : 'image' },
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						custom_uploader.on( 'close', function() {

							var state = custom_uploader.state();
							
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) 
								var image = state.props.attributes;	
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElement( strInputID, image );
							} else {
								
								var selection = custom_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( strInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + strInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var strInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( strInputIDOfNewField, attachment );
			
									}
								});				
								
							}
							
							// Restore the original select object.
							wp.media.view.MediaFrame.Select = oAPFOriginalImageUploaderSelectObject;
											
						});
						
						// Open the uploader dialog
						custom_uploader.open();											
						return false;       
					});	
				
					var setPreviewElement = function( strInputID, image ) {

						// Escape the strings of some of the attributes.
						var strCaption = jQuery( '<div/>' ).text( image.caption ).html();
						var strAlt = jQuery( '<div/>' ).text( image.alt ).html();
						var strTitle = jQuery( '<div/>' ).text( image.title ).html();
						
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( 'input#' + strInputID ).val( image.url );		// the url field is mandatory so it does not have the suffix.
						jQuery( 'input#' + strInputID + '_id' ).val( image.id );
						jQuery( 'input#' + strInputID + '_width' ).val( image.width );
						jQuery( 'input#' + strInputID + '_height' ).val( image.height );
						jQuery( 'input#' + strInputID + '_caption' ).val( strCaption );
						jQuery( 'input#' + strInputID + '_alt' ).val( strAlt );
						jQuery( 'input#' + strInputID + '_title' ).val( strTitle );
						jQuery( 'input#' + strInputID + '_align' ).val( image.align );
						jQuery( 'input#' + strInputID + '_link' ).val( image.link );
						
						// Update up the preview
						jQuery( '#image_preview_' + strInputID ).attr( 'data-id', image.id );
						jQuery( '#image_preview_' + strInputID ).attr( 'data-width', image.width );
						jQuery( '#image_preview_' + strInputID ).attr( 'data-height', image.height );
						jQuery( '#image_preview_' + strInputID ).attr( 'data-caption', strCaption );
						jQuery( '#image_preview_' + strInputID ).attr( 'alt', strAlt );
						jQuery( '#image_preview_' + strInputID ).attr( 'title', strTitle );
						jQuery( '#image_preview_' + strInputID ).attr( 'src', image.url );
						jQuery( '#image_preview_container_' + strInputID ).show();				
						
					}
				}		
			});
			";
		}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
			"/* Image Field Preview Container */
			.admin-page-framework-field .image_preview {
				border: none; 
				clear:both; 
				margin-top: 1em;
				margin-bottom: 1em;
				display: block; 
			}		
			@media only screen and ( max-width: 1200px ) {
				.admin-page-framework-field .image_preview {
					max-width: 600px;
				}
			} 
			@media only screen and ( max-width: 900px ) {
				.admin-page-framework-field .image_preview {
					max-width: 440px;
				}
			}	
			@media only screen and ( max-width: 600px ) {
				.admin-page-framework-field .image_preview {
					max-width: 300px;
				}
			}		
			@media only screen and ( max-width: 480px ) {
				.admin-page-framework-field .image_preview {
					max-width: 240px;
				}
			}
			@media only screen and ( min-width: 1200px ) {
				.admin-page-framework-field .image_preview {
					max-width: 600px;
				}
			}		 
			.admin-page-framework-field .image_preview img {		
				width: auto;
				height: auto; 
				max-width: 100%;
				display: block;
			}
		/* Image Uploader Button */
			.admin-page-framework-field-image input {
				margin-right: 0.5em;
			}
			.select_image.button.button-small {
				vertical-align: baseline;
			}			
		" . PHP_EOL;	
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];
		$fMultipleFields = is_array( $arrFields );	
		$fRepeatable = $arrField['fRepeatable'];
			
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] =
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"					
					. $this->getImageInputTags( $vValue, $arrField, $strFieldName, $strTagID, $strKey, $strLabel, $fMultipleFields, $arrDefaultKeys )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-image' id='{$strTagID}'>" 
				. implode( PHP_EOL, $arrOutput ) 
			. "</div>";		
		
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() method to return input elements.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField. Added some parameters.
		 */
		private function getImageInputTags( $vValue, $arrField, $strFieldName, $strTagID, $strKey, $strLabel, $fMultipleFields, $arrDefaultKeys ) {
			
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$intCountAttributes = count( ( array ) $arrField['arrCaptureAttributes'] );
			
			// The URL input field is mandatory as the preview element uses it.
			$arrOutputs = array(
				( $strLabel && ! $arrField['fRepeatable']
					? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>"
					: ''
				)			
				. "<input id='{$strTagID}_{$strKey}' "	// the main url element does not have the suffix of the attribute
					. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
					. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, $arrDefaultKeys['vSize'] ) . "' "
					. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
					. "type='text' "	// text
					. "name='" . ( $fMultipleFields ? "{$strFieldName}[{$strKey}]" : "{$strFieldName}" ) . ( $intCountAttributes ? "[url]" : "" ) .  "' "
					. "value='" . ( $strImageURL = $this->getImageInputValue( $vValue, $strKey, $fMultipleFields, $intCountAttributes ? 'url' : '', $arrDefaultKeys  ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $arrField['arrCaptureAttributes'] as $strAttribute )
				$arrOutputs[] = 
					"<input id='{$strTagID}_{$strKey}_{$strAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $fMultipleFields ? "{$strFieldName}[{$strKey}]" : "{$strFieldName}" ) . "[{$strAttribute}]' " 
						. "value='" . $this->getImageInputValue( $vValue, $strKey, $fMultipleFields, $strAttribute, $arrDefaultKeys ) . "' "
						. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container image-field'>"
					. "<label for='{$strTagID}_{$strKey}' >"
						. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
						. implode( PHP_EOL, $arrOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
					. "</label>"
				. "</div>"
				. ( $this->getCorrespondingArrayValue( $arrField['vImagePreview'], $strKey, true )
					? "<div id='image_preview_container_{$strTagID}_{$strKey}' "
							. "class='image_preview' "
							. "style='" . ( $strImageURL ? "" : "display : none;" ) . "'"
						. ">"
							. "<img src='{$strImageURL}' "
								. "id='image_preview_{$strTagID}_{$strKey}' "
							. "/>"
						. "</div>"
					: "" )
				. $this->getImageUploaderButtonScript( "{$strTagID}_{$strKey}", $arrField['fRepeatable'] ? true : false, $arrField['fAllowExternalSource'] ? true : false );
			
		}
		/**
		 * A helper function for the above getImageInputTags() method that retrieve the specified input field value.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField
		 */
		private function getImageInputValue( $vValue, $strKey, $fMultipleFields, $strCaptureAttribute, $arrDefaultKeys ) {	

			$vValue = $fMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $strKey, $arrDefaultKeys['vDefault'] )
				: ( isset( $vValue ) ? $vValue : $arrDefaultKeys['vDefault'] );

			return $strCaptureAttribute
				? ( isset( $vValue[ $strCaptureAttribute ] ) ? $vValue[ $strCaptureAttribute ] : "" )
				: $vValue;
			
		}
		/**
		 * A helper function for the above getImageInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField.
		 */
		private function getImageUploaderButtonScript( $strInputID, $fRpeatable, $fExternalSource ) {
			
			$strButton ="<a id='select_image_{$strInputID}' "
						. "href='#' "
						. "class='select_image button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $fExternalSource ? 1 : 0 ) . "'"
					. ">"
						. $this->oMsg->___( 'select_image' )
				."</a>";
			
			$strScript = "
				if ( jQuery( 'a#select_image_{$strInputID}' ).length == 0 ) {
					jQuery( 'input#{$strInputID}' ).after( \"{$strButton}\" );
				}			
			" . PHP_EOL;

			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$strScript .="
					jQuery( document ).ready( function(){			
						setAPFImageUploader( '{$strInputID}', '{$fRpeatable}', '{$fExternalSource}' );
					});" . PHP_EOL;	
					
			return "<script type='text/javascript'>" . $strScript . "</script>" . PHP_EOL;

		}	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_media' ) ) :
/**
 * Defines the media field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_media extends AmazonAutoLinks_AdminPageFramework_InputFieldType_image {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'arrCaptureAttributes'					=> array(),
			'vSize'									=> 60,
			'vMaxLength'							=> 400,
			'strTickBoxTitle' 						=> '',		// ( string ) This is for the image field type.
			'strLabelUseThis' 						=> '',		// ( string ) This is for the image field type.			
			'fAllowExternalSource' 					=> true,	// ( boolean ) Indicates whether the media library box has the From URL tab.
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
		$this->enqueueMediaUploader();
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return $this->getScript_CustomMediaUploaderObject()	. PHP_EOL	// defined in the parent class
			. $this->getScript_MediaUploader(
				"admin_page_framework", 
				$this->oMsg->___( 'upload_file' ),
				$this->oMsg->___( 'use_this_file' )
			);
	}	
		/**
		 * Returns the media uploader JavaScript script to be loaded in the head tag of the created admin pages.
		 * 
		 * @since			2.1.3
		 * @since			2.1.5			Moved from ... Chaned the name from getMediaUploaderScript().
		 */
		private function getScript_MediaUploader( $strReferrer, $strThickBoxTitle, $strThickBoxButtonUseThis ) {
			
			if ( ! function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.4.x or below
				return "
					jQuery( document ).ready( function(){
						jQuery( '.select_media' ).click( function() {
							pressed_id = jQuery( this ).attr( 'id' );
							field_id = pressed_id.substring( 13 );	// remove the select_file_ prefix
							var fExternalSource = jQuery( this ).attr( 'data-enable_external_source' );					
							tb_show( '{$strThickBoxTitle}', 'media-upload.php?post_id=1&amp;enable_external_source=' + fExternalSource + '&amp;referrer={$strReferrer}&amp;button_label={$strThickBoxButtonUseThis}&amp;type=media&amp;TB_iframe=true', false );
							return false;	// do not click the button after the script by returning false.
						});
						
						window.original_send_to_editor = window.send_to_editor;
						window.send_to_editor = function( strRawHTML, param ) {

							var strHTML = '<div>' + strRawHTML + '</div>';	// This is for the 'From URL' tab. Without the wrapper element. the below attr() method don't catch attributes.
							var src = jQuery( 'a', strHTML ).attr( 'href' );
							var classes = jQuery( 'a', strHTML ).attr( 'class' );
							var id = ( classes ) ? classes.replace( /(.*?)wp-image-/, '' ) : '';	// attachment ID	
						
							// If the user wants to save relavant attributes, set them.
							jQuery( '#' + field_id ).val( src );	// sets the image url in the main text field. The url field is mandatory so it does not have the suffix.
							jQuery( '#' + field_id + '_id' ).val( id );			
								
							// restore the original send_to_editor
							window.send_to_editor = window.original_send_to_editor;
							
							// close the thickbox
							tb_remove();	

						}
					});
				";
				
			return "
			jQuery( document ).ready( function(){		
				// Global Function Literal 
				setAPFMediaUploader = function( strInputID, fMultiple, fExternalSource ) {

					jQuery( '#select_media_' + strInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_media_' + strInputID ).click( function( e ) {
						
						window.wpActiveEditor = null;						
						e.preventDefault();
						
						// If the uploader object has already been created, reopen the dialog
						if ( media_uploader ) {
							media_uploader.open();
							return;
						}		
						
						// Store the original select object in a global variable
						oAPFOriginalMediaUploaderSelectObject = wp.media.view.MediaFrame.Select;
						
						// Assign a custom select object.
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalMediaUploaderSelectObject;
						var media_uploader = wp.media({
							title: '{$strThickBoxTitle}',
							button: {
								text: '{$strThickBoxButtonUseThis}'
							},
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						media_uploader.on( 'close', function() {

							var state = media_uploader.state();
							
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) 
								var image = state.props.attributes;	
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElement( strInputID, image );
							} else {
								
								var selection = media_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( strInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + strInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var strInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( strInputIDOfNewField, attachment );
			
									}
								});				
								
							}
							
							// Restore the original select object.
							wp.media.view.MediaFrame.Select = oAPFOriginalMediaUploaderSelectObject;	
							
						});
						
						// Open the uploader dialog
						media_uploader.open();											
						return false;       
					});	
				
					var setPreviewElement = function( strInputID, image ) {
									
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( '#' + strInputID ).val( image.url );		// the url field is mandatory so  it does not have the suffix.
						jQuery( '#' + strInputID + '_id' ).val( image.id );				
						jQuery( '#' + strInputID + '_caption' ).val( jQuery( '<div/>' ).text( image.caption ).html() );				
						jQuery( '#' + strInputID + '_description' ).val( jQuery( '<div/>' ).text( image.description ).html() );				
						
					}
				}		
				
			});";
		}
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return
		"/* Media Uploader Button */
			.admin-page-framework-field-media input {
				margin-right: 0.5em;
			}
			.select_media.button.button-small {
				vertical-align: baseline;
			}		
		";
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];			
		$fMultipleFields = is_array( $arrFields );	
		$fRepeatable = $arrField['fRepeatable'];			
			
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] =
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"					
					. $this->getMediaInputTags( $vValue, $arrField, $strFieldName, $strTagID, $strKey, $strLabel, $fMultipleFields, $arrDefaultKeys )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-media' id='{$strTagID}'>" 
				. implode( PHP_EOL, $arrOutput ) 
			. "</div>";		
			
	}
		/**
		 * A helper function for the above getImageField() method to return input elements.
		 * 
		 * @since			2.1.3
		 */
		private function getMediaInputTags( $vValue, $arrField, $strFieldName, $strTagID, $strKey, $strLabel, $fMultipleFields, $arrDefaultKeys ) {
	
			// If the saving extra attributes are not specified, the input field will be single only for the URL. 
			$intCountAttributes = count( ( array ) $arrField['arrCaptureAttributes'] );	
			
			// The URL input field is mandatory as the preview element uses it.
			$arrOutputs = array(
				( $strLabel && ! $arrField['fRepeatable']
					? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>" 
					: ''
				)
				. "<input id='{$strTagID}_{$strKey}' "	// the main url element does not have the suffix of the attribute
					. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
					. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, $arrDefaultKeys['vSize'] ) . "' "
					. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
					. "type='text' "	// text
					. "name='" . ( $fMultipleFields ? "{$strFieldName}[{$strKey}]" : "{$strFieldName}" ) . ( $intCountAttributes ? "[url]" : "" ) .  "' "
					. "value='" . ( $this->getMediaInputValue( $vValue, $strKey, $fMultipleFields, $intCountAttributes ? 'url' : '', $arrDefaultKeys ) ) . "' "
					. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
					. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
				. "/>"	
			);
			
			// Add the input fields for saving extra attributes. It overrides the name attribute of the default text field for URL and saves them as an array.
			foreach( ( array ) $arrField['arrCaptureAttributes'] as $strAttribute )
				$arrOutputs[] = 
					"<input id='{$strTagID}_{$strKey}_{$strAttribute}' "
						. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
						. "type='hidden' " 	// other additional attributes are hidden
						. "name='" . ( $fMultipleFields ? "{$strFieldName}[{$strKey}]" : "{$strFieldName}" ) . "[{$strAttribute}]' " 
						. "value='" . $this->getMediaInputValue( $vValue, $strKey, $fMultipleFields, $strAttribute, $arrDefaultKeys  ) . "' "
						. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
					. "/>";
			
			// Returns the outputs as well as the uploader buttons and the preview element.
			return 
				"<div class='admin-page-framework-input-label-container admin-page-framework-input-container media-field'>"
					. "<label for='{$strTagID}_{$strKey}' >"
						. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] )
						. implode( PHP_EOL, $arrOutputs ) . PHP_EOL
						. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
					. "</label>"
				. "</div>"
				. $this->getMediaUploaderButtonScript( "{$strTagID}_{$strKey}", $arrField['fRepeatable'] ? true : false, $arrField['fAllowExternalSource'] ? true : false );
			
		}
		/**
		 * A helper function for the above getMediaInputTags() method that retrieve the specified input field value.
		 * @since			2.1.3
		 */
		private function getMediaInputValue( $vValue, $strKey, $fMultipleFields, $strCaptureAttribute, $arrDefaultKeys ) {	

			$vValue = $fMultipleFields
				? $this->getCorrespondingArrayValue( $vValue, $strKey, $arrDefaultKeys['vDefault'] )
				: ( isset( $vValue ) ? $vValue : $arrDefaultKeys['vDefault'] );

			return $strCaptureAttribute
				? ( isset( $vValue[ $strCaptureAttribute ] ) ? $vValue[ $strCaptureAttribute ] : "" )
				: $vValue;
			
		}		
		/**
		 * A helper function for the above getMediaInputTags() method to add a image button script.
		 * 
		 * @since			2.1.3
		 */
		private function getMediaUploaderButtonScript( $strInputID, $fRpeatable, $fExternalSource ) {
			
			$strButton ="<a id='select_media_{$strInputID}' "
						. "href='#' "
						. "class='select_media button button-small'"
						. "data-uploader_type='" . ( function_exists( 'wp_enqueue_media' ) ? 1 : 0 ) . "'"
						. "data-enable_external_source='" . ( $fExternalSource ? 1 : 0 ) . "'"
					. ">"
						. $this->oMsg->___( 'select_file' )
				."</a>";
			
			$strScript = "
				if ( jQuery( 'a#select_media_{$strInputID}' ).length == 0 ) {
					jQuery( 'input#{$strInputID}' ).after( \"{$strButton}\" );
				}			
			" . PHP_EOL;

			if( function_exists( 'wp_enqueue_media' ) )	// means the WordPress version is 3.5 or above
				$strScript .="
					jQuery( document ).ready( function(){			
						setAPFMediaUploader( '{$strInputID}', '{$fRpeatable}', '{$fExternalSource}' );
					});" . PHP_EOL;	
					
			return "<script type='text/javascript'>" . $strScript . "</script>" . PHP_EOL;

		}	
		
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_select' ) ) :
/**
 * Defines the select field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_select extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vSize'					=> 1,
			'vMultiple'				=> false,				// ( array or boolean ) This value indicates whether the select tag should have the multiple attribute or not.
			'vWidth'				=> '',
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $arrField['vLabel'] ) ) return;	

		$fSingle = ( $this->getArrayDimension( ( array ) $arrField['vLabel'] ) == 1 );
		$arrLabels = $fSingle ? array( $arrField['vLabel'] ) : $arrField['vLabel'];
		foreach( $arrLabels as $strKey => $vLabel ) {
			
			$fMultiple = $this->getCorrespondingArrayValue( $arrField['vMultiple'], $strKey, $arrDefaultKeys['vMultiple'] );
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-select-label' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>"
						. "<label for='{$strTagID}_{$strKey}'>"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<select id='{$strTagID}_{$strKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
									. "type='{$arrField['strType']}' "
									. ( $fMultiple ? "multiple='Multiple' " : '' )
									. "name=" . ( $fSingle ? "'{$strFieldName}" : "'{$strFieldName}[{$strKey}]" ) . ( $fMultiple ? "[]' " : "' " )
									. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
									. "size=" . ( $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, $arrDefaultKeys['vSize'] ) ) . " "
									. ( ( $strWidth = $this->getCorrespondingArrayValue( $arrField['vWidth'], $strKey, $arrDefaultKeys['vWidth'] ) ) ? "style='width:{$strWidth};' " : "" )
								. ">"
									. $this->getOptionTags( $vLabel, $vValue, $strTagID, $strKey, $fSingle, $fMultiple )
								. "</select>"
							. "</span>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-select' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";				
	
	}	
	
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $strTagID parameter.
		 */ 
		private function getOptionTags( $arrLabels, $vValue, $strTagID, $strIterationID, $fSingle, $fMultiple=false ) {	

			$arrOutput = array();
			foreach ( $arrLabels as $strKey => $strLabel ) {
				$arrValue = $fSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $strIterationID, array() ) ;
				$arrOutput[] = "<option "
						. "id='{$strTagID}_{$strIterationID}_{$strKey}' "
						. "value='{$strKey}' "
						. (	$fMultiple 
							? ( in_array( $strKey, $arrValue ) ? 'selected="Selected"' : '' )
							: ( $this->getCorrespondingArrayValue( $vValue, $strIterationID, null ) == $strKey ? "selected='Selected'" : "" )
						)
					. ">"
						. $strLabel
					. "</option>";
			}
			return implode( '', $arrOutput );
		}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_radio' ) ) :
/**
 * Defines the radio field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_radio extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'vSize'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		
		
		// The value of the label key must be an array for the select type.
		if ( ! is_array( $arrField['vLabel'] ) ) return;	
		
		$fSingle = ( $this->getArrayDimension( ( array ) $arrField['vLabel'] ) == 1 );
		$arrLabels =  $fSingle ? array( $arrField['vLabel'] ) : $arrField['vLabel'];
		foreach( $arrLabels as $strKey => $vLabel )  
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. $this->getRadioTags( $arrField, $vValue, $vLabel, $strFieldName, $strTagID, $strKey, $fSingle, $arrDefaultKeys )				
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		return "<div class='admin-page-framework-field-radio' id='{$strTagID}'>" 
				. implode( '', $arrOutput )
			. "</div>";
		
	}
		/**
		 * A helper function for the <em>getRadioField()</em> method.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField. Added the $arrField, $strFieldName, $arrDefaultKeys, $strTagID, and $vValue parameter.
		 */ 
		private function getRadioTags( $arrField, $vValue, $arrLabels, $strFieldName, $strTagID, $strIterationID, $fSingle, $arrDefaultKeys ) {
			
			$arrOutput = array();
			foreach ( $arrLabels as $strKey => $strLabel ) 
				$arrOutput[] = 
					"<div class='admin-page-framework-input-label-container admin-page-framework-radio-label' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>"
						. "<label for='{$strTagID}_{$strIterationID}_{$strKey}'>"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<input "
									. "id='{$strTagID}_{$strIterationID}_{$strKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
									. "type='radio' "
									. "value='{$strKey}' "
									. "name=" . ( ! $fSingle  ? "'{$strFieldName}[{$strIterationID}]' " : "'{$strFieldName}' " )
									. ( $this->getCorrespondingArrayValue( $vValue, $strIterationID, null ) == $strKey ? 'Checked ' : '' )
									. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
								. "/>"							
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $strLabel
							. "</span>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>";

			return implode( '', $arrOutput );
		}

}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_checkbox' ) ) :
/**
 * Defines the checkbox field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_checkbox extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'vSize'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @since			2.1.5
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		

		foreach( ( array ) $arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container admin-page-framework-checkbox-label' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>"
						. "<label for='{$strTagID}_{$strKey}'>"	
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
							. "<span class='admin-page-framework-input-container'>"
								. "<input type='hidden' name=" .  ( is_array( $arrField['vLabel'] ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " ) . " value='0' />"	// the unchecked value must be set prior to the checkbox input field.
								. "<input "
									. "id='{$strTagID}_{$strKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
									. "type='{$arrField['strType']}' "	// checkbox
									. "name=" . ( is_array( $arrField['vLabel'] ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
									. "value='1' "
									. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $vValue, $strKey, null ) == 1 ? "Checked " : '' )
								. "/>"							
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $strLabel
							. "</span>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>" // end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-checkbox' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";	
	
	}

}
endif;


if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_size' ) ) :
/**
 * Defines the size field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_size extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vSizeUnits'				=> array(	// the default unit size array.
				'px'	=> 'px',	// pixel
				'%'		=> '%',		// percentage
				'em'	=> 'em',	// font size
				'ex'	=> 'ex',	// font height
				'in'	=> 'in',	// inch
				'cm'	=> 'cm',	// centimetre
				'mm'	=> 'mm',	// millimetre
				'pt'	=> 'pt',	// point
				'pc'	=> 'pc',	// pica
			),
			'vSize'						=> 10,
			'vUnitSize'					=> 1,
			'vMaxLength'				=> 400,
			'vMin'						=> null,
			'vMax'						=> null,
			'vStep'						=> null,
			'vMultiple'					=> false,
			'vWidth'					=> '',
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return
		"/* Size Field Type */
		.admin-page-framework-field-size input {
			text-align: right;
		}
		.admin-page-framework-field-size select.size-field-select {
			vertical-align: 0px;			
		}
		" . PHP_EOL;
	}
	
	/**
	 * Returns the output of the field type.
	 *
	 * Returns the size input fields. This enables for the user to set a size with a unit. This is made up of a text input field and a drop-down selector field. 
	 * Useful for theme developers.
	 * 
	 * @since			2.0.1
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField. Changed the name from getSizeField().
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
				
		$fSingle = ! is_array( $arrField['vLabel'] );
		$fIsSizeUnitForSingle = ( $this->getArrayDimension( ( array ) $arrField['vSizeUnits'] ) == 1 );
		$arrSizeUnits = isset( $arrField['vSizeUnits'] ) && is_array( $arrField['vSizeUnits'] ) && $fIsSizeUnitForSingle 
			? $arrField['vSizeUnits']
			: $arrDefaultKeys['vSizeUnits'];		
		
		foreach( ( array ) $arrField['vLabel'] as $strKey => $strLabel ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<label for='{$strTagID}_{$strKey}'>"
						. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
						. ( $strLabel 
							? "<span class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel ."</span>"
							: "" 
						)
						. "<input id='{$strTagID}_{$strKey}' "	// number field
							// . "style='text-align: right;'"
							. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
							. "size='" . $this->getCorrespondingArrayValue( $arrField['vSize'], $strKey, $arrDefaultKeys['vSize'] ) . "' "
							. "maxlength='" . $this->getCorrespondingArrayValue( $arrField['vMaxLength'], $strKey, $arrDefaultKeys['vMaxLength'] ) . "' "
							. "type='number' "	// number
							. "name=" . ( $fSingle ? "'{$strFieldName}[size]' " : "'{$strFieldName}[{$strKey}][size]' " )
							. "value='" . ( $fSingle ? $this->getCorrespondingArrayValue( $vValue['size'], $strKey, '' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $strKey, array() ), 'size', '' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
							. ( $this->getCorrespondingArrayValue( $arrField['vReadOnly'], $strKey ) ? "readonly='readonly' " : '' )
							. "min='" . $this->getCorrespondingArrayValue( $arrField['vMin'], $strKey, $arrDefaultKeys['vMin'] ) . "' "
							. "max='" . $this->getCorrespondingArrayValue( $arrField['vMax'], $strKey, $arrDefaultKeys['vMax'] ) . "' "
							. "step='" . $this->getCorrespondingArrayValue( $arrField['vStep'], $strKey, $arrDefaultKeys['vStep'] ) . "' "					
						. "/>"
					. "</label>"
						. "<select id='{$strTagID}_{$strKey}' class='size-field-select'"	// select field
							. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
							. "type='{$arrField['strType']}' "
							. ( ( $fMultipleOptions = $this->getCorrespondingArrayValue( $arrField['vMultiple'], $strKey, $arrDefaultKeys['vMultiple'] ) ) ? "multiple='Multiple' " : '' )
							. "name=" . ( $fSingle ? "'{$strFieldName}[unit]" : "'{$strFieldName}[{$strKey}][unit]" ) . ( $fMultipleOptions ? "[]' " : "' " )						
							. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
							. "size=" . ( $this->getCorrespondingArrayValue( $arrField['vUnitSize'], $strKey, $arrDefaultKeys['vUnitSize'] ) ) . " "
							. ( ( $strWidth = $this->getCorrespondingArrayValue( $arrField['vWidth'], $strKey, $arrDefaultKeys['vWidth'] ) ) ? "style='width:{$strWidth};' " : "" )
						. ">"
						. $this->getOptionTags( 
							$fSingle ? $arrSizeUnits : $this->getCorrespondingArrayValue( $arrField['vSizeUnits'], $strKey, $arrSizeUnits ),
							$fSingle ? $this->getCorrespondingArrayValue( $vValue['unit'], $strKey, 'px' ) : $this->getCorrespondingArrayValue( $this->getCorrespondingArrayValue( $vValue, $strKey, array() ), 'unit', 'px' ),
							$strTagID,
							$strKey, 
							true, 	// since the above value is directly passed, call the function as a single element.
							$fMultipleOptions 
						)
					. "</select>"
					. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);			

		return "<div class='admin-page-framework-field-size' id='{$strTagID}'>" 
			. implode( '', $arrOutput )
		. "</div>";
		
	}
		/**
		 * A helper function for the above replyToGetInputField() methods.
		 * 
		 * @since			2.0.0
		 * @since			2.0.1			Added the $vValue parameter to the second parameter. This is the result of supporting the size field type.
		 * @since			2.1.5			Added the $strTagID parameter. Moved from AdminPageFramwrodk_InputField.
		 */ 
		private function getOptionTags( $arrLabels, $vValue, $strTagID, $strIterationID, $fSingle, $fMultiple=false ) {	

			$arrOutput = array();
			foreach ( $arrLabels as $strKey => $strLabel ) {
				$arrValue = $fSingle ? ( array ) $vValue : ( array ) $this->getCorrespondingArrayValue( $vValue, $strIterationID, array() ) ;
				$arrOutput[] = "<option "
						. "id='{$strTagID}_{$strIterationID}_{$strKey}' "
						. "value='{$strKey}' "
						. (	$fMultiple 
							? ( in_array( $strKey, $arrValue ) ? 'selected="Selected"' : '' )
							: ( $this->getCorrespondingArrayValue( $vValue, $strIterationID, null ) == $strKey ? "selected='Selected'" : "" )
						)
					. ">"
						. $strLabel
					. "</option>";
			}
			return implode( '', $arrOutput );
		}

}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_hidden' ) ) :
/**
 * Defines the hidden field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_hidden extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			// 'vSize'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * @remark			The user needs to assign the value to either the vDefault key or the vValue key in order to set the hidden field. 
	 * If it's not set ( null value ), the below foreach will not iterate an element so no input field will be embedded.
	 * 
	 * @since			2.0.0
	 * @since			2.1.5				Moved from the AmazonAutoLinks_AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		
				
		foreach( ( array ) $vValue as $strKey => $strValue ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$strTagID}_{$strKey}'>"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
							. ( ( $strLabel = $this->getCorrespondingArrayValue( $arrField['vLabel'], $strKey, $arrDefaultKeys['vLabel'] ) ) 
								? "<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>{$strLabel}</span>" 
								: "" 
							)
							. "<div class='admin-page-framework-input-container'>"
								. "<input "
									. "id='{$strTagID}_{$strKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
									. "type='{$arrField['strType']}' "	// hidden
									. "name=" . ( is_array( $arrField['vLabel'] ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
									. "value='" . $strValue  . "' "
									. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
								. "/>"
							. "</div>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-hidden' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}

}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_file' ) ) :
/**
 * Defines the file field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_file extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vAcceptAttribute'				=> 'audio/*|video/*|image/*|MIME_type',
			// 'vSize'					=> 1,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		$arrFields = $arrField['fRepeatable'] ? 
			( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			: $arrField['vLabel'];		
					
		foreach( ( array ) $arrFields as $strKey => $strLabel ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container'>"
						. "<label for='{$strTagID}_{$strKey}'>"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
							. ( $strLabel && ! $arrField['fRepeatable'] ?
								"<span class='admin-page-framework-input-label-string' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>" . $strLabel . "</span>"
								: ""
							)
							. "<input "
								. "id='{$strTagID}_{$strKey}' "
								. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
								. "accept='" . $this->getCorrespondingArrayValue( $arrField['vAcceptAttribute'], $strKey, $arrDefaultKeys['vAcceptAttribute'] ) . "' "
								. "type='{$arrField['strType']}' "	// file
								. "name=" . ( is_array( $arrFields ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
								. "value='" . $this->getCorrespondingArrayValue( $arrFields, $strKey ) . "' "
								. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
							. "/>"
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
					
		return "<div class='admin-page-framework-field-file' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
	}

}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_posttype' ) ) :
/**
 * Defines the posttype field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_posttype extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'arrRemove'					=> array( 'revision', 'attachment', 'nav_menu_item' ), // for the posttype checklist field type
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * 
	 * Returns the output of post type checklist check boxes.
	 * 
	 * @remark			the posttype checklist field does not support multiple elements by passing an array of labels.
	 * @since			2.0.0
	 * 
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		
						
		foreach( ( array ) $this->getPostTypeArrayForChecklist( $arrField['arrRemove'] ) as $strKey => $strValue ) {
			$strName = "{$strFieldName}[{$strKey}]";
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					. "<div class='admin-page-framework-input-label-container' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>"
						. "<label for='{$strTagID}_{$strKey}'>"
							. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] )
							. "<span class='admin-page-framework-input-container'>"
								. "<input type='hidden' name='{$strName}' value='0' />"
								. "<input "
									. "id='{$strTagID}_{$strKey}' "
									. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
									. "type='checkbox' "
									. "name='{$strName}'"
									. "value='1' "
									. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
									. ( $this->getCorrespondingArrayValue( $vValue, $strKey, false ) == 1 ? "Checked " : '' )				
								. "/>"
							. "</span>"
							. "<span class='admin-page-framework-input-label-string'>"
								. $strValue
							. "</span>"				
							. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
						. "</label>"
					. "</div>"
				. "</div>"
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-posttype' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}	
	
		/**
		 * A helper function for the above getPosttypeChecklistField method.
		 * 
		 * @since			2.0.0
		 * @since			2.1.1			Changed the returning array to have the labels in its element values.
		 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputTag.
		 * @return			array			The array holding the elements of installed post types' labels and their slugs except the specified expluding post types.
		 */ 
		private function getPostTypeArrayForChecklist( $arrRemoveNames, $arrPostTypes=array() ) {
			
			foreach( get_post_types( '','objects' ) as $oPostType ) 
				if (  isset( $oPostType->name, $oPostType->label ) ) 
					$arrPostTypes[ $oPostType->name ] = $oPostType->label;

			return array_diff_key( $arrPostTypes, array_flip( $arrRemoveNames ) );	

		}		
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_taxonomy' ) ) :
/**
 * Defines the taxonomy field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_taxonomy extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vTaxonomySlug'					=> 'category',			// ( string ) This is for the taxonomy field type.
			'strHeight'						=> '250px',				// for the taxonomy checklist field type, since 2.1.1.
			'strWidth'						=> '100%',				// for the taxonomy checklist field type, since 2.1.1.		
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 * 
	 * Returns the JavaScript script of the taxonomy field type.
	 * 
	 * @since			2.1.1
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_Properties_Base().
	 */ 
	public function replyToGetInputScripts() {
		return "
			jQuery( document ).ready( function() {
				jQuery( '.tab-box-container' ).each( function() {
					jQuery( this ).find( '.tab-box-tab' ).each( function( i ) {
						
						if ( i == 0 )
							jQuery( this ).addClass( 'active' );
							
						jQuery( this ).click( function( e ){
								 
							// Prevents jumping to the anchor which moves the scroll bar.
							e.preventDefault();
							
							// Remove the active tab and set the clicked tab to be active.
							jQuery( this ).siblings( 'li.active' ).removeClass( 'active' );
							jQuery( this ).addClass( 'active' );
							
							// Find the element id and select the content element with it.
							var thisTab = jQuery( this ).find( 'a' ).attr( 'href' );
							active_content = jQuery( this ).closest( '.tab-box-container' ).find( thisTab ).css( 'display', 'block' ); 
							active_content.siblings().css( 'display', 'none' );
							
						});
					});			
				});
			});
		";
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 
		"/* Taxonomy Field Type */
			.admin-page-framework-field .taxonomy-checklist li { 
				margin: 8px 0 8px 20px; 
			}
			.admin-page-framework-field div.taxonomy-checklist {
				padding: 8px 0 8px 10px;
				margin-bottom: 20px;
			}
			.admin-page-framework-field .taxonomy-checklist ul {
				list-style-type: none;
				margin: 0;
			}
			.admin-page-framework-field .taxonomy-checklist ul ul {
				margin-left: 1em;
			}
			.admin-page-framework-field .taxonomy-checklist-label {
				/* margin-left: 0.5em; */
			}		
		/* Tabbed box */
			.admin-page-framework-field .tab-box-container.categorydiv {
				max-height: none;
			}
			.admin-page-framework-field .tab-box-tab-text {
				display: inline-block;
			}
			.admin-page-framework-field .tab-box-tabs {
				line-height: 12px;
				margin-bottom: 0;
			
			}
			.admin-page-framework-field .tab-box-tabs .tab-box-tab.active {
				display: inline;
				border-color: #dfdfdf #dfdfdf #fff;
				margin-bottom: 0;
				padding-bottom: 1px;
				background-color: #fff;
			}
			.admin-page-framework-field .tab-box-container { 
				position: relative; width: 100%; 

			}
			.admin-page-framework-field .tab-box-tabs li a { color: #333; text-decoration: none; }
			.admin-page-framework-field .tab-box-contents-container {  
				padding: 0 0 0 20px; 
				border: 1px solid #dfdfdf; 
				background-color: #fff;
			}
			.admin-page-framework-field .tab-box-contents { 
				overflow: hidden; 
				overflow-x: hidden; 
				position: relative; 
				top: -1px; 
				height: 300px;  
			}
			.admin-page-framework-field .tab-box-content { 
				height: 300px;
				display: none; 
				overflow: auto; 
				display: block; 
				position: relative; 
				overflow-x: hidden;
			}
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target, 
			.admin-page-framework-field .tab-box-content:target { 
				display: block; 
			}			
		" . PHP_EOL;
	}
	
	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputIEStyles() {
		return 	".tab-box-content { display: block; }
			.tab-box-contents { overflow: hidden;position: relative; }
			b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
		";	

	}	
	
	/**
	 * Returns the output of the field type.
	 * 
	 * Returns the output of taxonomy checklist check boxes.
	 * 
	 * @remark			Multiple fields are not supported.
	 * @remark			Repeater fields are not supported.
	 * @since			2.0.0
	 * @since			2.1.1			The checklist boxes are rendered in a tabbed single box.
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		
		
		$arrTabs = array();
		$arrCheckboxes = array();
		foreach( ( array ) $arrField['vTaxonomySlug'] as $strKey => $strTaxonomySlug ) {
			$strActive = isset( $strActive ) ? '' : 'active';	// inserts the active class selector into the first element.
			$arrTabs[] = 
				"<li class='tab-box-tab'>"
					. "<a href='#tab-{$strKey}'>"
						. "<span class='tab-box-tab-text'>" 
							. $this->getCorrespondingArrayValue( empty( $arrField['vLabel'] ) ? null : $arrField['vLabel'], $strKey, $this->getLabelFromTaxonomySlug( $strTaxonomySlug ) )
						. "</span>"
					."</a>"
				."</li>";
			$arrCheckboxes[] = 
				"<div id='tab-{$strKey}' class='tab-box-content' style='height: {$arrField['strHeight']};'>"
					. "<ul class='list:category taxonomychecklist form-no-clear'>"
						. wp_list_categories( array(
							'walker' => new AmazonAutoLinks_AdminPageFramework_WalkerTaxonomyChecklist,	// the walker class instance
							'name'     => is_array( $arrField['vTaxonomySlug'] ) ? "{$strFieldName}[{$strKey}]" : "{$strFieldName}",   // name of the input
							'selected' => $this->getSelectedKeyArray( $vValue, $strKey ), 		// checked items ( term IDs )	e.g.  array( 6, 10, 7, 15 ), 
							'title_li'	=> '',	// disable the Categories heading string 
							'hide_empty' => 0,	
							'echo'	=> false,	// returns the output
							'taxonomy' => $strTaxonomySlug,	// the taxonomy slug (id) such as category and post_tag 
							'strTagID' => $strTagID,
						) )					
					. "</ul>"			
					. "<!--[if IE]><b>.</b><![endif]-->"
				. "</div>";
		}
		$strTabs = "<ul class='tab-box-tabs category-tabs'>" . implode( '', $arrTabs ) . "</ul>";
		$strContents = 
			"<div class='tab-box-contents-container'>"
				. "<div class='tab-box-contents' style='height: {$arrField['strHeight']};'>"
					. implode( '', $arrCheckboxes )
				. "</div>"
			. "</div>";
			
		$strOutput = 
			"<div id='{$strTagID}' class='{$strFieldClassSelector} admin-page-framework-field-taxonomy tab-box-container categorydiv' style='max-width:{$arrField['strWidth']};'>"
				. $strTabs . PHP_EOL
				. $strContents . PHP_EOL
			. "</div>";

		return $strOutput;

	}	
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method. 
		 * 
		 * @since			2.0.0
		 * @param			array			$vValue			This can be either an one-dimensional array ( for single field ) or a two-dimensional array ( for multiple fields ).
		 * @param			string			$strKey			
		 * @return			array			Returns an array consisting of keys whose value is true.
		 */ 
		private function getSelectedKeyArray( $vValue, $strKey ) {
					
			$vValue = ( array ) $vValue;	// cast array because the initial value (null) may not be an array.
			$intArrayDimension = $this->getArrayDimension( ( array ) $vValue );
					
			if ( $intArrayDimension == 1 )
				$arrKeys = $vValue;
			else if ( $intArrayDimension == 2 )
				$arrKeys = ( array ) $this->getCorrespondingArrayValue( $vValue, $strKey, false );
				
			return array_keys( $arrKeys, true );
		
		}
	
		/**
		 * A helper function for the above getTaxonomyChecklistField() method.
		 * 
		 * @since			2.1.1
		 * 
		 */
		private function getLabelFromTaxonomySlug( $strTaxonomySlug ) {
			
			$oTaxonomy = get_taxonomy( $strTaxonomySlug );
			return isset( $oTaxonomy->label )
				? $oTaxonomy->label
				: null;
			
		}
	
}
endif;
if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_submit' ) ) :
/**
 * Defines the submit field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_submit extends AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinition_Base {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(		
			'vClassAttribute'					=> 'button button-primary',
			'vRedirect'							=> null,
			'vLink'								=> null,
			'vReset'							=> null,
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return 		
		"/* Submit Buttons */
		.admin-page-framework-field input[type='submit'] {
			margin-bottom: 0.5em;
		}" . PHP_EOL;		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5			Moved from AmazonAutoLinks_AdminPageFramework_InputField.
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		

		
		$vValue = $this->getInputFieldValueFromLabel( $arrField );
		$strFieldNameFlat = $this->getInputFieldNameFlat( $arrField );
		foreach( ( array ) $vValue as $strKey => $strValue ) {
			$strRedirectURL = $this->getCorrespondingArrayValue( $arrField['vRedirect'], $strKey, $arrDefaultKeys['vRedirect'] );
			$strLinkURL = $this->getCorrespondingArrayValue( $arrField['vLink'], $strKey, $arrDefaultKeys['vLink'] );
			$strResetKey = $this->getCorrespondingArrayValue( $arrField['vReset'], $strKey, $arrDefaultKeys['vReset'] );
			$fResetConfirmed = $this->checkConfirmationDisplayed( $strResetKey, $strFieldNameFlat ); 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__submit[{$strTagID}_{$strKey}][input_id]' "
						. "value='{$strTagID}_{$strKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__submit[{$strTagID}_{$strKey}][field_id]' "
						. "value='{$arrField['strFieldID']}' "
					. "/>"		
					. "<input type='hidden' "
						. "name='__submit[{$strTagID}_{$strKey}][name]' "
						. "value='{$strFieldNameFlat}" . ( is_array( $vValue ) ? "|{$strKey}'" : "'" )
					. "/>" 						
					// for the vRedirect key
					. ( $strRedirectURL 
						? "<input type='hidden' "
							. "name='__redirect[{$strTagID}_{$strKey}][url]' "
							. "value='" . $strRedirectURL . "' "
						. "/>" 
						. "<input type='hidden' "
							. "name='__redirect[{$strTagID}_{$strKey}][name]' "
							. "value='{$strFieldNameFlat}" . ( is_array( $vValue ) ? "|{$strKey}" : "'" )
						. "/>" 
						: "" 
					)
					// for the vLink key
					. ( $strLinkURL 
						? "<input type='hidden' "
							. "name='__link[{$strTagID}_{$strKey}][url]' "
							. "value='" . $strLinkURL . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__link[{$strTagID}_{$strKey}][name]' "
							. "value='{$strFieldNameFlat}" . ( is_array( $vValue ) ? "|{$strKey}'" : "'" )
						. "/>" 
						: "" 
					)
					// for the vReset key
					. ( $strResetKey && ! $fResetConfirmed
						? "<input type='hidden' "
							. "name='__reset_confirm[{$strTagID}_{$strKey}][key]' "
							. "value='" . $strFieldNameFlat . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset_confirm[{$strTagID}_{$strKey}][name]' "
							. "value='{$strFieldNameFlat}" . ( is_array( $vValue ) ? "|{$strKey}'" : "'" )
						. "/>" 
						: ""
					)
					. ( $strResetKey && $fResetConfirmed
						? "<input type='hidden' "
							. "name='__reset[{$strTagID}_{$strKey}][key]' "
							. "value='" . $strResetKey . "' "
						. "/>"
						. "<input type='hidden' "
							. "name='__reset[{$strTagID}_{$strKey}][name]' "
							. "value='{$strFieldNameFlat}" . ( is_array( $vValue ) ? "|{$strKey}'" : "'" )
						. "/>" 
						: ""
					)
					. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, $arrDefaultKeys['vBeforeInputTag'] ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
							. "type='{$arrField['strType']}' "	// submit
							. "name=" . ( is_array( $arrField['vLabel'] ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $strKey, $this->oMsg->___( 'submit' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
				. "</div>" // end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
				
		}
		return "<div class='admin-page-framework-field-submit' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		
	
	}
		/**
		 * A helper function for the above getSubmitField() that checks if a reset confirmation message has been displayed or not when the vReset key is set.
		 * 
		 */
		private function checkConfirmationDisplayed( $strResetKey, $strFlatFieldName ) {
				
			if ( ! $strResetKey ) return false;
			
			$fResetConfirmed =  get_transient( md5( "reset_confirm_" . $strFlatFieldName ) ) !== false 
				? true
				: false;
			
			if ( $fResetConfirmed )
				delete_transient( md5( "reset_confirm_" . $strFlatFieldName ) );
				
			return $fResetConfirmed;
			
		}

	/*
	 *	Shared Methods 
	 */
	/**
	 * Retrieves the field name attribute whose dimensional elements are delimited by the pile character.
	 * 
	 * Instead of [] enclosing array elements, it uses the pipe(|) to represent the multi dimensional array key.
	 * This is used to create a reference the submit field name to determine which button is pressed.
	 * 
	 * @remark			Used by the import and submit field types.
	 * @since			2.0.0
	 * @since			2.1.5			Made the parameter mandatory. Changed the scope to protected from private. Moved from AmazonAutoLinks_AdminPageFramework_InputField.
	 */ 
	protected function getInputFieldNameFlat( $arrField ) {	
	
		return isset( $arrField['strOptionKey'] ) // the meta box class does not use the option key
			? "{$arrField['strOptionKey']}|{$arrField['strPageSlug']}|{$arrField['strSectionID']}|{$arrField['strFieldID']}"
			: $arrField['strFieldID'];
		
	}			
	/**
	 * Retrieves the input field value from the label.
	 * 
	 * This method is similar to the above <em>getInputFieldValue()</em> but this does not check the stored option value.
	 * It uses the value set to the <var>vLabel</var> key. 
	 * This is for submit buttons including export custom field type that the label should serve as the value.
	 * 
	 * @remark			The submit, import, and export field types use this method.
	 * @since			2.0.0
	 * @since			2.1.5			Moved from AdminPageFramwrork_InputField. Changed the scope to protected from private. Removed the second parameter.
	 */ 
	protected function getInputFieldValueFromLabel( $arrField ) {	
		
		// If the value key is explicitly set, use it.
		if ( isset( $arrField['vValue'] ) ) return $arrField['vValue'];
		
		if ( isset( $arrField['vLabel'] ) ) return $arrField['vLabel'];
		
		// If the default value is set,
		if ( isset( $arrField['vDefault'] ) ) return $arrField['vDefault'];
		
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_export' ) ) :
/**
 * Defines the export field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_export extends AmazonAutoLinks_AdminPageFramework_InputFieldType_submit {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vExportData'					=> null,	// ( array or string or object ) This is for the export field type. 			
			'vExportFormat'					=> 'array',	// ( array or string )	for the export field type. Do not set a default value here. Currently array, json, and text are supported.
			'vExportFileName'				=> null,	// ( array or string )	for the export field type. Do not set a default value here.
			'vClassAttribute'				=> 'button button-primary',	// ( array or string )	
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5				Moved from the AmazonAutoLinks_AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
			// : $arrField['vLabel'];		
				
		$vValue = $this->getInputFieldValueFromLabel( $arrField );
		
		// If vValue is not an array and the export data set, set the transient. ( it means single )
		if ( isset( $arrField['vExportData'] ) && ! is_array( $vValue ) )
			set_transient( md5( "{$arrField['strClassName']}_{$arrField['strFieldID']}" ), $arrField['vExportData'], 60*2 );	// 2 minutes.
		
		foreach( ( array ) $vValue as $strKey => $strValue ) {
			
			$strExportFormat = $this->getCorrespondingArrayValue( $arrField['vExportFormat'], $strKey, $arrDefaultKeys['vExportFormat'] );
			
			// If it's one of the multiple export buttons and the export data is explictly set for the element, store it as transient in the option table.
			$fIsDataSet = false;
			if ( isset( $vValue[ $strKey ] ) && isset( $arrField['vExportData'][ $strKey ] ) ) {
				set_transient( md5( "{$arrField['strClassName']}_{$arrField['strFieldID']}_{$strKey}" ), $arrField['vExportData'][ $strKey ], 60*2 );	// 2 minutes.
				$fIsDataSet = true;
			}
			
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__export[{$arrField['strFieldID']}][input_id]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='{$strTagID}_{$strKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__export[{$arrField['strFieldID']}][field_id]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='{$arrField['strFieldID']}' "
					. "/>"					
					. "<input type='hidden' "
						. "name='__export[{$arrField['strFieldID']}][file_name]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $arrField['vExportFileName'], $strKey, $this->generateExportFileName( $arrField['strOptionKey'], $strExportFormat ) )
					. "' />"
					. "<input type='hidden' "
						. "name='__export[{$arrField['strFieldID']}][format]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $strExportFormat
					. "' />"				
					. "<input type='hidden' "
						. "name='__export[{$arrField['strFieldID']}][transient]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . ( $fIsDataSet ? 1 : 0 )
					. "' />"				
					. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>"
						. "<input "
							. "id='{$strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
							. "type='submit' "	// the export button is a custom submit button.
							// . "name=" . ( is_array( $arrField['vLabel'] ) ? "'{$strFieldName}[{$strKey}]' " : "'{$strFieldName}' " )
							. "name='__export[submit][{$arrField['strFieldID']}]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $strKey, $this->oMsg->___( 'export_options' ) ) . "' "
							. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, $arrDefaultKeys['vAfterInputTag'] )
				. "</div>" // end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);
									
		}
					
		return "<div class='admin-page-framework-field-export' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";		
	
	}
	
		/**
		 * A helper function for the above method.
		 * 
		 * @remark			Currently only array, text or json is supported.
		 * @since			2.0.0
		 * @since			2.1.5			Moved from the AmazonAutoLinks_AdminPageFramework_InputField class.
		 */ 
		private function generateExportFileName( $strOptionKey, $strExportFormat='text' ) {
				
			switch ( trim( strtolower( $strExportFormat ) ) ) {
				case 'text':	// for plain text.
					$strExt = "txt";
					break;
				case 'json':	// for json.
					$strExt = "json";
					break;
				case 'array':	// for serialized PHP arrays.
				default:	// for anything else, 
					$strExt = "txt";
					break;
			}		
				
			return $strOptionKey . '_' . date("Ymd") . '.' . $strExt;
			
		}

}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputFieldType_import' ) ) :
/**
 * Defines the import field type.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 */
class AmazonAutoLinks_AdminPageFramework_InputFieldType_import extends AmazonAutoLinks_AdminPageFramework_InputFieldType_submit {
	
	/**
	 * Returns the array of the field type specific default keys.
	 */
	protected function getDefaultKeys() { 
		return array(
			'vClassAttribute'					=> 'import button button-primary',	// ( array or string )	
			'vAcceptAttribute'					=> 'audio/*|video/*|image/*|MIME_type',
			'vClassAttributeUpload'				=> 'import',
			'vImportOptionKey'					=> null,	// ( array or string )	for the import field type. The default value is the set option key for the framework.
			'vImportFormat'						=> 'array',	// ( array or string )	for the import field type.
			'vMerge'							=> false,	// ( array or boolean ) [2.1.5+] for the import field
		);	
	}

	/**
	 * Loads the field type necessary components.
	 */ 
	public function replyToFieldLoader() {
	}	
	
	/**
	 * Returns the field type specific JavaScript script.
	 */ 
	public function replyToGetInputScripts() {
		return "";		
	}	

	/**
	 * Returns the field type specific CSS rules.
	 */ 
	public function replyToGetInputStyles() {
		return "";		
	}
	
	/**
	 * Returns the output of the field type.
	 * @since			2.1.5				Moved from the AmazonAutoLinks_AdminPageFramework_InputField class. The name was changed from getHiddenField().
	 */
	public function replyToGetInputField( $vValue, $arrField, $arrOptions, $arrErrors, $arrFieldDefinition ) {

		$arrOutput = array();
		$strFieldName = $arrField['strFieldName'];
		$strTagID = $arrField['strTagID'];
		$strFieldClassSelector = $arrField['strFieldClassSelector'];
		$arrDefaultKeys = $arrFieldDefinition['arrDefaultKeys'];	
		
		// $arrFields = $arrField['fRepeatable'] ? 
			// ( empty( $vValue ) ? array( '' ) : ( array ) $vValue )
	
		$vValue = $this->getInputFieldValueFromLabel( $arrField );
		$strFieldNameFlat = $this->getInputFieldNameFlat( $arrField );
		foreach( ( array ) $vValue as $strKey => $strValue ) 
			$arrOutput[] = 
				"<div class='{$strFieldClassSelector}' id='field-{$strTagID}_{$strKey}'>"
					// embed the field id and input id
					. "<input type='hidden' "
						. "name='__import[{$arrField['strFieldID']}][input_id]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='{$strTagID}_{$strKey}' "
					. "/>"
					. "<input type='hidden' "
						. "name='__import[{$arrField['strFieldID']}][field_id]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='{$arrField['strFieldID']}' "
					. "/>"		
					. "<input type='hidden' "
						. "name='__import[{$arrField['strFieldID']}][do_merge]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $arrField['vMerge'], $strKey, $arrDefaultKeys['vMerge'] ) . "' "
					. "/>"							
					. "<input type='hidden' "
						. "name='__import[{$arrField['strFieldID']}][import_option_key]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $arrField['vImportOptionKey'], $strKey, $arrField['strOptionKey'] )
					. "' />"
					. "<input type='hidden' "
						. "name='__import[{$arrField['strFieldID']}][format]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
						. "value='" . $this->getCorrespondingArrayValue( $arrField['vImportFormat'], $strKey, $arrDefaultKeys['vImportFormat'] )	// array, text, or json.
					. "' />"			
					. $this->getCorrespondingArrayValue( $arrField['vBeforeInputTag'], $strKey, '' ) 
					. "<span class='admin-page-framework-input-button-container admin-page-framework-input-container' style='min-width:" . $this->getCorrespondingArrayValue( $arrField['vLabelMinWidth'], $strKey, $arrDefaultKeys['vLabelMinWidth'] ) . "px;'>"
						. "<input "		// upload button
							. "id='{$strTagID}_{$strKey}_file' "
							. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttributeUpload'], $strKey, $arrDefaultKeys['vClassAttributeUpload'] ) . "' "
							. "accept='" . $this->getCorrespondingArrayValue( $arrField['vAcceptAttribute'], $strKey, $arrDefaultKeys['vAcceptAttribute'] ) . "' "
							. "type='file' "	// upload field. the file type will be stored in $_FILE
							. "name='__import[{$arrField['strFieldID']}]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
							. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )				
						. "/>"
						. "<input "		// import button
							. "id='{$strTagID}_{$strKey}' "
							. "class='" . $this->getCorrespondingArrayValue( $arrField['vClassAttribute'], $strKey, $arrDefaultKeys['vClassAttribute'] ) . "' "
							. "type='submit' "	// the import button is a custom submit button.
							. "name='__import[submit][{$arrField['strFieldID']}]" . ( is_array( $arrField['vLabel'] ) ? "[{$strKey}]' " : "' " )
							. "value='" . $this->getCorrespondingArrayValue( $vValue, $strKey, $this->oMsg->___( 'import_options' ), true ) . "' "
							. ( $this->getCorrespondingArrayValue( $arrField['vDisable'], $strKey ) ? "disabled='Disabled' " : '' )
						. "/>"
					. "</span>"
					. $this->getCorrespondingArrayValue( $arrField['vAfterInputTag'], $strKey, '' )
				. "</div>"	// end of admin-page-framework-field
				. ( ( $strDelimiter = $this->getCorrespondingArrayValue( $arrField['vDelimiter'], $strKey, $arrDefaultKeys['vDelimiter'], true ) )
					? "<div class='delimiter' id='delimiter-{$strTagID}_{$strKey}'>" . $strDelimiter . "</div>"
					: ""
				);		
					
		return "<div class='admin-page-framework-field-import' id='{$strTagID}'>" 
				. implode( '', $arrOutput ) 
			. "</div>";
		
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_BuiltinInputFieldTypeDefinitions' ) ) :
/**
 * Provides means to define custom input fields not only by the framework but also by the user.
 * 
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 * @since			2.1.5
 * @since			2.1.6			Changed the name from AmazonAutoLinks_AdminPageFramework_InputFieldTypeDefinitions
 */
class AmazonAutoLinks_AdminPageFramework_BuiltinInputFieldTypeDefinitions  {
	
	/**
	 * Holds the default input field labels
	 * 
	 * @since			2.1.5
	 */
	protected static $arrDefaultFieldTypeSlugs = array(
		'default' => array( 'default' ),	// undefined ones will be applied 
		'text' => array( 'text', 'password', 'date', 'datetime', 'datetime-local', 'email', 'month', 'search', 'tel', 'time', 'url', 'week' ),
		'number' => array( 'number', 'range' ),
		'textarea' => array( 'textarea' ),
		'radio' => array( 'radio' ),
		'checkbox' => array( 'checkbox' ),
		'select' => array( 'select' ),
		'hidden' => array( 'hidden' ),
		'file' => array( 'file' ),
		'submit' => array( 'submit' ),
		'import' => array( 'import' ),
		'export' => array( 'export' ),
		'image' => array( 'image' ),
		'media' => array( 'media' ),
		'color' => array( 'color' ),
		'taxonomy' => array( 'taxonomy' ),
		'posttype' => array( 'posttype' ),
		'size' => array( 'size' ),
	);	
	
	function __construct( &$arrFieldTypeDefinitions, $strExtendedClassName, $oMsg ) {
		foreach( self::$arrDefaultFieldTypeSlugs as $strFieldTypeSlug => $arrSlugs ) {
			$strInstantiatingClassName = "AmazonAutoLinks_AdminPageFramework_InputFieldType_{$strFieldTypeSlug}";
			if ( class_exists( $strInstantiatingClassName ) ) {
				$oFieldType = new $strInstantiatingClassName( $strExtendedClassName, $strFieldTypeSlug, $oMsg, false );	// passing false for the forth parameter disables auto-registering.
				foreach( $arrSlugs as $strSlug )
					$arrFieldTypeDefinitions[ $strSlug ] = $oFieldType->getDefinitionArray();
			}
		}
	}
}


endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_InputField' ) ) :
/**
 * Provides methods for rendering form input fields.
 *
 * @since			2.0.0
 * @since			2.0.1			Added the <em>size</em> type.
 * @extends			AmazonAutoLinks_AdminPageFramework_Utilities
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AmazonAutoLinks_AdminPageFramework_InputField extends AmazonAutoLinks_AdminPageFramework_Utilities {
		
	/**
	 * Indicates whether the creating fields are for meta box or not.
	 * @since			2.1.2
	 */
	private $fIsMetaBox = false;
		
	protected static $arrStructure_FieldDefinition = array(
		'callRenderField' => null,
		'callGetScripts' => null,
		'callGetStyles' => null,
		'callGetIEStyles' => null,
		'callFieldLoader' => null,
		'arrEnqueueScripts' => null,
		'arrEnqueueStyles' => null,
		'arrDefaultKeys' => null,
	);
	
	public function __construct( &$arrField, &$arrOptions, $arrErrors, &$arrFieldDefinition, &$oMsg ) {
			
		$this->arrField = $arrField + $arrFieldDefinition['arrDefaultKeys'] + self::$arrStructure_FieldDefinition;	// better not to merge recursively because some elements are array by default, not as multiple elements.
		$this->arrFieldDefinition = $arrFieldDefinition;
		$this->arrOptions = $arrOptions;
		$this->arrErrors = $arrErrors ? $arrErrors : array();
		$this->oMsg = $oMsg;
			
		$this->strFieldName = $this->getInputFieldName();
		$this->strTagID = $this->getInputTagID( $arrField );
		$this->vValue = $this->getInputFieldValue( $arrField, $arrOptions );
		
		// Global variable
		$GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'] = isset( $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'] )
			? $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'] 
			: array();
		
	}	
		
	private function getInputFieldName( $arrField=null ) {
		
		$arrField = isset( $arrField ) ? $arrField : $this->arrField;
		
		// If the name key is explicitly set, use it
		if ( ! empty( $arrField['strName'] ) ) return $arrField['strName'];
		
		return isset( $arrField['strOptionKey'] ) // the meta box class does not use the option key
			? "{$arrField['strOptionKey']}[{$arrField['strPageSlug']}][{$arrField['strSectionID']}][{$arrField['strFieldID']}]"
			: $arrField['strFieldID'];
		
	}

	private function getInputFieldValue( &$arrField, $arrOptions ) {	

		// If the value key is explicitly set, use it.
		if ( isset( $arrField['vValue'] ) ) return $arrField['vValue'];
		
		// Check if a previously saved option value exists or not.
		//  for regular setting pages. Meta boxes do not use these keys.
		if ( isset( $arrField['strPageSlug'], $arrField['strSectionID'] ) ) {			
		
			$vValue = $this->getInputFieldValueFromOptionTable( $arrField, $arrOptions );
			if ( $vValue != '' ) return $vValue;
			
		} 
		// For meta boxes
		else if ( isset( $_GET['action'], $_GET['post'] ) ) {

			$vValue = $this->getInputFieldValueFromPostTable( $_GET['post'], $arrField );
			if ( $vValue != '' ) return $vValue;
			
		}
		
		// If the default value is set,
		if ( isset( $arrField['vDefault'] ) ) return $arrField['vDefault'];
		
	}	
	private function getInputFieldValueFromOptionTable( &$arrField, &$arrOptions ) {
		
		if ( ! isset( $arrOptions[ $arrField['strPageSlug'] ][ $arrField['strSectionID'] ][ $arrField['strFieldID'] ] ) )
			return;
						
		$vValue = $arrOptions[ $arrField['strPageSlug'] ][ $arrField['strSectionID'] ][ $arrField['strFieldID'] ];
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$vDefault = isset( $arrField['vDefault'] ) ? $arrField['vDefault'] : array(); 
		foreach ( $vValue as $strKey => &$strElement ) 
			if ( $strElement == '' )
				$strElement = $this->getCorrespondingArrayValue( $vDefault, $strKey, '' );
		
		return $vValue;
			
		
	}	
	private function getInputFieldValueFromPostTable( $intPostID, &$arrField ) {
		
		$vValue = get_post_meta( $intPostID, $arrField['strFieldID'], true );
		
		// Check if it's not an array return it.
		if ( ! is_array( $vValue ) && ! is_object( $vValue ) ) return $vValue;
		
		// If it's an array, check if there is an empty value in each element.
		$vDefault = isset( $arrField['vDefault'] ) ? $arrField['vDefault'] : array(); 
		foreach ( $vValue as $strKey => &$strElement ) 
			if ( $strElement == '' )
				$strElement = $this->getCorrespondingArrayValue( $vDefault, $strKey, '' );
		
		return $vValue;
		
	}
		
	private function getInputTagID( $arrField )  {
		
		// For Settings API's form fields should have these key values.
		if ( isset( $arrField['strSectionID'], $arrField['strFieldID'] ) )
			return "{$arrField['strSectionID']}_{$arrField['strFieldID']}";
			
		// For meta box form fields,
		if ( isset( $arrField['strFieldID'] ) ) return $arrField['strFieldID'];
		if ( isset( $arrField['strName'] ) ) return $arrField['strName'];	// the name key is for the input name attribute but it's better than nothing.
		
		// Not Found - it's not a big deal to have an empty value for this. It's just for the anchor link.
		return '';
			
	}		
	
	
	/** 
	 * Retrieves the input field HTML output.
	 * @since			2.0.0
	 * @since			2.1.6			Moved the repeater script outside the fieldset tag.
	 */ 
	public function getInputField( $strFieldType ) {
		
		// Prepend the field error message.
		$strOutput = isset( $this->arrErrors[ $this->arrField['strSectionID'] ][ $this->arrField['strFieldID'] ] )
			? "<span style='color:red;'>*&nbsp;{$this->arrField['strError']}" . $this->arrErrors[ $this->arrField['strSectionID'] ][ $this->arrField['strFieldID'] ] . "</span><br />"
			: '';		
		
		// Prepeare the field class selector 
		$this->strFieldClassSelector = $this->arrField['fRepeatable']
			? "admin-page-framework-field repeatable"
			: "admin-page-framework-field";
			
		// Add new elements
		$this->arrField['strFieldName'] = $this->strFieldName;
		$this->arrField['strTagID'] = $this->strTagID;
		$this->arrField['strFieldClassSelector'] = $this->strFieldClassSelector;

		// Get the field output.
		$strOutput .= call_user_func_array( 
			$this->arrFieldDefinition['callRenderField'], 
			array( $this->vValue, $this->arrField, $this->arrOptions, $this->arrErrors, $this->arrFieldDefinition )
		);			
				
		// Add the description
		$strOutput .= ( isset( $this->arrField['strDescription'] ) && trim( $this->arrField['strDescription'] ) != '' ) 
			? "<p class='admin-page-framework-fields-description'><span class='description'>{$this->arrField['strDescription']}</span></p>"
			: '';
			
		// Add the repeater script
		$strOutput .= $this->arrField['fRepeatable']
			? $this->getRepeaterScript( $this->strTagID, count( ( array ) $this->vValue ) )
			: '';
			
		return $this->getRepeaterScriptGlobal( $this->strTagID )
			. "<fieldset>"
				. "<div class='admin-page-framework-fields'>"
					. $this->arrField['strBeforeField'] 
					. $strOutput
					. $this->arrField['strAfterField']
				. "</div>"
			. "</fieldset>";
		
	}
	
	/**
	 * Sets or return the flag that indicates whether the creating fields are for meta boxes or not.
	 * 
	 * If the parameter is not set, it will return the stored value. Otherwise, it will set the value.
	 * 
	 * @since			2.1.2
	 */
	public function isMetaBox( $fTrueOrFalse=null ) {
		
		if ( isset( $fTrueOrFalse ) ) 
			$this->fIsMetaBox = $fTrueOrFalse;
			
		return $this->fIsMetaBox;
		
	}
	
	/**
	 * Indicates whether the repeatable fields script is called or not.
	 * 
	 * @since			2.1.3
	 */
	private $fIsRepeatableScriptCalled = false;
	
	/**
	 * Returns the repeatable fields script.
	 * 
	 * @since			2.1.3
	 */
	private function getRepeaterScript( $strTagID, $intFieldCount ) {

		$strAdd = $this->oMsg->___( 'add' );
		$strRemove = $this->oMsg->___( 'remove' );
		$strVisibility = $intFieldCount <= 1 ? " style='display:none;'" : "";
		$strButtons = 
			"<div class='admin-page-framework-repeatable-field-buttons'>"
				. "<a class='repeatable-field-add button-secondary repeatable-field-button button button-small' href='#' title='{$strAdd}' data-id='{$strTagID}'>+</a>"
				. "<a class='repeatable-field-remove button-secondary repeatable-field-button button button-small' href='#' title='{$strRemove}' {$strVisibility} data-id='{$strTagID}'>-</a>"
			. "</div>";

		return
			"<script type='text/javascript'>
				jQuery( document ).ready( function() {
				
					// Adds the buttons
					jQuery( '#{$strTagID} .admin-page-framework-field' ).append( \"{$strButtons}\" );
					
					// Update the fields
					updateAPFRepeatableFields( '{$strTagID}' );
					
				});
			</script>";
		
	}

	/**
	 * Returns the script that will be referred multiple times.
	 * since			2.1.3
	 */
	private function getRepeaterScriptGlobal( $strID ) {

		if ( $this->fIsRepeatableScriptCalled ) return '';
		$this->fIsRepeatableScriptCalled = true;
		return 
		"<script type='text/javascript'>
			jQuery( document ).ready( function() {
				
				// Global function literals
				
				// This function modifies the ids and names of the tags of input, textarea, and relevant tags for repeatable fields.
				updateAPFIDsAndNames = function( element, fIncrementOrDecrement ) {

					var updateID = function( index, name ) {
						
						if ( typeof name === 'undefined' ) {
							return name;
						}
						return name.replace( /_((\d+))(?=(_|$))/, function ( fullMatch, n ) {						
							return '_' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
						});
						
					}
					var updateName = function( index, name ) {
						
						if ( typeof name === 'undefined' ) {
							return name;
						}
						return name.replace( /\[((\d+))(?=\])/, function ( fullMatch, n ) {				
							return '[' + ( Number(n) + ( fIncrementOrDecrement == 1 ? 1 : -1 ) );
						});
						
					}					
				
					element.attr( 'id', function( index, name ) { return updateID( index, name ) } );
					element.find( 'label' ).attr( 'for', function( index, name ) { return updateID( index, name ) } );
					element.find( 'input,textarea' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
					element.find( 'input,textarea' ).attr( 'name', function( index, name ){ return updateName( index, name ) } );
					
					// Color Pickers
					var nodeColorInput = element.find( 'input.input_color' );
					if ( nodeColorInput.length > 0 ) {
						
							var previous_id = nodeColorInput.attr( 'id' );
							
							if ( fIncrementOrDecrement > 0 ) {	// Add
					
								// For WP 3.5+
								var nodeNewColorInput = nodeColorInput.clone();	// re-clone without bind events.
								
								// For WP 3.4.x or below
								var strInputValue = nodeNewColorInput.val() ? nodeNewColorInput.val() : 'transparent';
								var strInputStyle = strInputValue != 'transparent' && nodeNewColorInput.attr( 'style' ) ? nodeNewColorInput.attr( 'style' ) : '';
								
								nodeNewColorInput.val( strInputValue );	// set the default value	
								nodeNewColorInput.attr( 'style', strInputStyle );	// remove the background color set to the input field ( for WP 3.4.x or below )						 
								
								var nodeFarbtastic = element.find( '.colorpicker' );
								var nodeNewFarbtastic = nodeFarbtastic.clone();	// re-clone without bind elements.
								
								// Remove the old elements
								nodeIris = jQuery( '#' + previous_id ).closest( '.wp-picker-container' );	
								if ( nodeIris.length > 0 ) {	// WP 3.5+
									nodeIris.remove();	
								} else {
									jQuery( '#' + previous_id ).remove();	// WP 3.4.x or below
									element.find( '.colorpicker' ).remove();	// WP 3.4.x or below
								}
							
								// Add the new elements
								element.prepend( nodeNewFarbtastic );
								element.prepend( nodeNewColorInput );
								
							}
							
							element.find( '.colorpicker' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
							element.find( '.colorpicker' ).attr( 'rel', function( index, name ){ return updateID( index, name ) } );					

							// Renew the color picker script
							var cloned_id = element.find( 'input.input_color' ).attr( 'id' );
							registerAPFColorPickerField( cloned_id );					
					
					}

					// Image uploader buttons and image preview elements
					image_uploader_button = element.find( '.select_image' );
					if ( image_uploader_button.length > 0 ) {
						var previous_id = element.find( '.image-field input' ).attr( 'id' );
						image_uploader_button.attr( 'id', function( index, name ){ return updateID( index, name ) } );
						element.find( '.image_preview' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
						element.find( '.image_preview img' ).attr( 'id', function( index, name ){ return updateID( index, name ) } );
					
						if ( jQuery( image_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
							var fExternalSource = jQuery( image_uploader_button ).attr( 'data-enable_external_source' );
							setAPFImageUploader( previous_id, true, fExternalSource );	
						}						
					}
					
					// Media uploader buttons
					media_uploader_button = element.find( '.select_media' );
					if ( media_uploader_button.length > 0 ) {
						var previous_id = element.find( '.media-field input' ).attr( 'id' );
						media_uploader_button.attr( 'id', function( index, name ){ return updateID( index, name ) } );
					
						if ( jQuery( media_uploader_button ).data( 'uploader_type' ) == '1' ) {	// for Wordpress 3.5 or above
							var fExternalSource = jQuery( media_uploader_button ).attr( 'data-enable_external_source' );
							setAPFMediaUploader( previous_id, true, fExternalSource );	
						}						
					}
					
					// Date pickers - somehow it needs to destroy the both previous one and the added one and assign the new date pickers 
					var date_picker_script = element.find( 'script.date-picker-enabler-script' );
					if ( date_picker_script.length > 0 ) {
						var previous_id = date_picker_script.attr( 'data-id' );
						date_picker_script.attr( 'data-id', function( index, name ){ return updateID( index, name ) } );

						jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker( 'destroy' ); 
						jQuery( '#' + date_picker_script.attr( 'data-id' ) ).datepicker({
							dateFormat : date_picker_script.attr( 'data-date_format' )
						});						
						jQuery( '#' + previous_id ).datepicker( 'destroy' ); //here
						jQuery( '#' + previous_id ).datepicker({
							dateFormat : date_picker_script.attr( 'data-date_format' )
						});												
					}				
									
				}
				
				// This function is called from the updateAPFRepeatableFields() and from the media uploader for multiple file selections.
				addAPFRepeatableField = function( strFieldContainerID ) {	

					var field_container = jQuery( '#' + strFieldContainerID );
					var field_delimiter_id = strFieldContainerID.replace( 'field-', 'delimiter-' );
					var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
					
					var field_new = field_container.clone( true );
					var delimiter_new = field_delimiter.clone( true );
					var target_element = ( jQuery( field_delimiter ).length ) ? field_delimiter : field_container;
			
					field_new.find( 'input,textarea' ).val( '' );	// empty the value		
					field_new.find( '.image_preview' ).hide();					// for the image field type, hide the preview element
					field_new.find( '.image_preview img' ).attr( 'src', '' );	// for the image field type, empty the src property for the image uploader field
					delimiter_new.insertAfter( target_element );	// add the delimiter
					field_new.insertAfter( target_element );		// add the cloned new field element

					// Increment the names and ids of the next following siblings.
					target_element.nextAll().each( function() {
						updateAPFIDsAndNames( jQuery( this ), true );
					});

					var remove_buttons =  field_container.closest( '.admin-page-framework-fields' ).find( '.repeatable-field-remove' );
					if ( remove_buttons.length > 1 ) 
						remove_buttons.show();				
					
					// Return the newly created element
					return field_new;
					
				}
				
				updateAPFRepeatableFields = function( strID ) {
				
					// Add button behaviour
					jQuery( '#' + strID + ' .repeatable-field-add' ).click( function() {
						
						var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
						addAPFRepeatableField( field_container.attr( 'id' ) );
						return false;
						
					});		
					
					// Remove button behaviour
					jQuery( '#' + strID + ' .repeatable-field-remove' ).click( function() {
						
						// Need to remove two elements: the field container and the delimiter element.
						var field_container = jQuery( this ).closest( '.admin-page-framework-field' );
						var field_container_id = field_container.attr( 'id' );				
						var field_delimiter_id = field_container_id.replace( 'field-', 'delimiter-' );
						var field_delimiter = field_container.siblings( '#' + field_delimiter_id );
						var target_element = ( jQuery( field_delimiter ).length ) ? field_delimiter : field_container;

						// Decrement the names and ids of the next following siblings.
						target_element.nextAll().each( function() {
							updateAPFIDsAndNames( jQuery( this ), false );	// the second parameter value indicates it's for decrement.
						});

						field_delimiter.remove();
						field_container.remove();
						
						var fieldsCount = jQuery( '#' + strID + ' .repeatable-field-remove' ).length;
						if ( fieldsCount == 1 ) {
							jQuery( '#' + strID + ' .repeatable-field-remove' ).css( 'display', 'none' );
						}
						return false;
					});
									
				}
			});
		</script>";
	}
	
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_WalkerTaxonomyChecklist' ) ) :
/**
 * Provides methods for rendering taxonomy check lists.
 * 
 * Used for the wp_list_categories() function to render category hierarchical checklist.
 * 
 * @see				Walker : wp-includes/class-wp-walker.php
 * @see				Walker_Category : wp-includes/category-template.php
 * @since			2.0.0
 * @since			2.1.5			Added the strTagID key to the argument array. Changed the format of 'id' and 'for' attribute of the input and label tags.
 * @extends			Walker_Category
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Setting
 */
class AmazonAutoLinks_AdminPageFramework_WalkerTaxonomyChecklist extends Walker_Category {
		
	function start_el( &$strOutput, $oCategory, $intDepth=0, $arrArgs=array(), $intCurrentObjectID=0 ) {
		
		/*	
		 	$arrArgs keys:
			'show_option_all' => '', 
			'show_option_none' => __('No categories'),
			'orderby' => 'name', 
			'order' => 'ASC',
			'style' => 'list',
			'show_count' => 0, 
			'hide_empty' => 1,
			'use_desc_for_title' => 1, 
			'child_of' => 0,
			'feed' => '', 
			'feed_type' => '',
			'feed_image' => '', 
			'exclude' => '',
			'exclude_tree' => '', 
			'current_category' => 0,
			'hierarchical' => true, 
			'title_li' => __( 'Categories' ),
			'echo' => 1, 
			'depth' => 0,
			'taxonomy' => 'category'	// 'post_tag' or any other registered taxonomy slug will work.

			[class] => categories
			[has_children] => 1
		*/
		
		$arrArgs = $arrArgs + array(
			'name' 		=> null,
			'disabled'	=> null,
			'selected'	=> array(),
			'strTagID'	=> null,
		);
		
		$intID = $oCategory->term_id;
		$strTaxonomy = empty( $arrArgs['taxonomy'] ) ? 'category' : $arrArgs['taxonomy'];
		$strChecked = in_array( $intID, ( array ) $arrArgs['selected'] )  ? 'Checked' : '';
		$strDisabled = $arrArgs['disabled'] ? 'disabled="Disabled"' : '';
		$strClass = 'category-list';
		$strID = "{$arrArgs['strTagID']}_{$strTaxonomy}_{$intID}";
		$strOutput .= "\n"
			. "<li id='list-{$strID}' $strClass>" 
				. "<label for='{$strID}' class='taxonomy-checklist-label'>"
					. "<input value='0' type='hidden' name='{$arrArgs['name']}[{$intID}]' />"
					. "<input id='{$strID}' value='1' type='checkbox' name='{$arrArgs['name']}[{$intID}]' {$strChecked} {$strDisabled} />"
					. esc_html( apply_filters( 'the_category', $oCategory->name ) ) 
				. "</label>";	
			// no need to close </li> since it is dealt in end_el().
			
	}
}
endif;

if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_PostType' ) ) :
/**
 * Provides methods for registering custom post types.
 * 
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>cell_ + post type + _ + column key</code> – receives the output string for the listing table of the custom post type's post. The first parameter: output string. The second parameter: the post ID.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p> 
 * 
 * @abstract
 * @since			2.0.0
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Post Type
 */
abstract class AmazonAutoLinks_AdminPageFramework_PostType {	

	// Objects
	/**
	 * @since			2.0.0
	 * @internal
	 */ 
	protected $oUtil;
	/**
	 * @since			2.0.0
	 * @internal
	 */ 	
	protected $oLink;
		
	/**
	* Constructs the class object, AmazonAutoLinks_AdminPageFramework_PostType.
	* 
	* <h4>Example</h4>
	* <code>new APF_PostType( 
	* 	'apf_posts', 	// post type slug
	* 	array(			// argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	* 		'labels' => array(
	* 			'name' => 'Admin Page Framework',
	* 			'singular_name' => 'Admin Page Framework',
	* 			'add_new' => 'Add New',
	* 			'add_new_item' => 'Add New APF Post',
	* 			'edit' => 'Edit',
	* 			'edit_item' => 'Edit APF Post',
	* 			'new_item' => 'New APF Post',
	* 			'view' => 'View',
	* 			'view_item' => 'View APF Post',
	* 			'search_items' => 'Search APF Post',
	* 			'not_found' => 'No APF Post found',
	* 			'not_found_in_trash' => 'No APF Post found in Trash',
	* 			'parent' => 'Parent APF Post'
	* 		),
	* 		'public' => true,
	* 		'menu_position' => 110,
	* 		'supports' => array( 'title' ),
	* 		'taxonomies' => array( '' ),
	* 		'menu_icon' => null,
	* 		'has_archive' => true,
	* 		'show_admin_column' => true,	// for custom taxonomies
	* 	)		
	* );</code>
	* @since			2.0.0
	* @since			2.1.6			Added the $strTextDomain parameter.
	* @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	* @param			string			$strPostType			The post type slug.
	* @param			array			$arrArgs				The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">argument array</a> passed to register_post_type().
	* @param			string			$strCallerPath			The path of the caller script. This is used to retrieve the script information to insert it into the footer. If not set, the framework tries to detect it.
	* @param			string			$strTextDomain			The text domain of the caller script.
	* @return			void
	*/
	public function __construct( $strPostType, $arrArgs=array(), $strCallerPath=null, $strTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AmazonAutoLinks_AdminPageFramework_Utilities;
		$this->oProps = new AmazonAutoLinks_AdminPageFramework_PostType_Properties( $this );
		$this->oMsg = AmazonAutoLinks_AdminPageFramework_Messages::instantiate( $strTextDomain );
		$this->oHeadTag = new AmazonAutoLinks_AdminPageFramework_HeadTag_PostType( $this->oProps );
		$this->oPageLoadStats = AmazonAutoLinks_AdminPageFramework_PageLoadStats_PostType::instantiate( $this->oProps, $this->oMsg );
		
		// Properties
		$this->oProps->strPostType = $this->oUtil->sanitizeSlug( $strPostType );
		$this->oProps->arrPostTypeArgs = $arrArgs;	// for the argument array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
		$this->oProps->strClassName = get_class( $this );
		$this->oProps->strClassHash = md5( $this->oProps->strClassName );
		$this->oProps->arrColumnHeaders = array(
			'cb'			=> '<input type="checkbox" />',		// Checkbox for bulk actions. 
			'title'			=> $this->oMsg->___( 'title' ),		// Post title. Includes "edit", "quick edit", "trash" and "view" links. If $mode (set from $_REQUEST['mode']) is 'excerpt', a post excerpt is included between the title and links.
			'author'		=> $this->oMsg->___( 'author' ), 	// Post author.
			// 'categories'	=> $this->oMsg->___( 'categories' ),	// Categories the post belongs to. 
			// 'tags'		=> $this->oMsg->___( 'tags' ), 		//	Tags for the post. 
			'comments' 		=> '<div class="comment-grey-bubble"></div>', // Number of pending comments. 
			'date'			=> $this->oMsg->___( 'date' ), 		// The date and publish status of the post. 
		);			
		$this->oProps->strCallerPath = $strCallerPath;
		
		add_action( 'init', array( $this, 'registerPostType' ), 999 );	// this is loaded in the front-end as well so should not be admin_init. Also "if ( is_admin() )" should not be used either.
		
		if ( $this->oProps->strPostType != '' && is_admin() ) {			
		
			add_action( 'admin_enqueue_scripts', array( $this, 'disableAutoSave' ) );
			
			// For table columns
			add_filter( "manage_{$this->oProps->strPostType}_posts_columns", array( $this, 'setColumnHeader' ) );
			add_filter( "manage_edit-{$this->oProps->strPostType}_sortable_columns", array( $this, 'setSortableColumns' ) );
			add_action( "manage_{$this->oProps->strPostType}_posts_custom_column", array( $this, 'setColumnCell' ), 10, 2 );
			
			// For filters
			add_action( 'restrict_manage_posts', array( $this, 'addAuthorTableFilter' ) );
			add_action( 'restrict_manage_posts', array( $this, 'addTaxonomyTableFilter' ) );
			add_filter( 'parse_query', array( $this, 'setTableFilterQuery' ) );
			
			// Style
			add_action( 'admin_head', array( $this, 'addStyle' ) );
			
			// Links
			$this->oLink = new AmazonAutoLinks_AdminPageFramework_LinkForPostType( $this->oProps->strPostType, $this->oProps->strCallerPath, $this->oMsg );
			
			add_action( 'wp_loaded', array( $this, 'setUp' ) );
		}
	
		$this->oUtil->addAndDoAction( $this, "{$this->oProps->strPrefix_Start}{$this->oProps->strClassName}" );
		
	}
	
	/*
	 * Extensible methods
	 */

	/**
	* The method for all necessary set-ups.
	* 
	* <h4>Example</h4>
	* <code>public function setUp() {
	* 		$this->setAutoSave( false );
	* 		$this->setAuthorTableFilter( true );
	* 		$this->addTaxonomy( 
	* 			'sample_taxonomy', // taxonomy slug
	* 			array(			// argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
	* 				'labels' => array(
	* 					'name' => 'Genre',
	* 					'add_new_item' => 'Add New Genre',
	* 					'new_item_name' => "New Genre"
	* 				),
	* 				'show_ui' => true,
	* 				'show_tagcloud' => false,
	* 				'hierarchical' => true,
	* 				'show_admin_column' => true,
	* 				'show_in_nav_menus' => true,
	* 				'show_table_filter' => true,	// framework specific key
	* 				'show_in_sidebar_menus' => false,	// framework specific key
	* 			)
	* 		);
	* 	}</code>
	* 
	* @abstract
	* @since			2.0.0
	* @remark			The user may override this method in their class definition.
	* @remark			A callback for the <em>wp_loaded</em> hook.
	*/
	public function setUp() {}	
	
	/**
	 * Defines the column header items in the custom post listing table.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_{post type}_post)_columns</em> hook.
	 * @remark			The user may override this method in their class definition.
	 * @return			void
	 */ 
	public function setColumnHeader( $arrColumnHeaders ) {
		return $this->oProps->arrColumnHeaders;
	}	
	
	/**
	 * Defines the sortable column items in the custom post listing table.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>manage_edit-{post type}_sortable_columns</em> hook.
	 * @remark			The user may override this method in their class definition.
	 */ 
	public function setSortableColumns( $arrColumns ) {
		return $this->oProps->arrColumnSortable;
	}
	
	/*
	 * Front-end methods
	 */
	/**
	* Enables or disables the auto-save feature in the custom post type's post submission page.
	* 
	* <h4>Example</h4>
	* <code>$this->setAutoSave( false );</code>
	* 
	* @since			2.0.0
	* @param			boolean			$fEnableAutoSave			If true, it enables the auto-save; othwerwise, it disables it.
	* return			void
	*/ 
	protected function setAutoSave( $fEnableAutoSave=True ) {
		$this->oProps->fEnableAutoSave = $fEnableAutoSave;		
	}
	
	/**
	* Adds a custom taxonomy to the class post type.
	* <h4>Example</h4>
	* <code>$this->addTaxonomy( 
	*		'sample_taxonomy', // taxonomy slug
	*		array(			// argument
	*			'labels' => array(
	*				'name' => 'Genre',
	*				'add_new_item' => 'Add New Genre',
	*				'new_item_name' => "New Genre"
	*			),
	*			'show_ui' => true,
	*			'show_tagcloud' => false,
	*			'hierarchical' => true,
	*			'show_admin_column' => true,
	*			'show_in_nav_menus' => true,
	*			'show_table_filter' => true,	// framework specific key
	*			'show_in_sidebar_menus' => false,	// framework specific key
	*		)
	*	);</code>
	* 
	* @see				http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
	* @since			2.0.0
	* @param			string			$strTaxonomySlug			The taxonomy slug.
	* @param			array			$arrArgs					The taxonomy argument array passed to the second parameter of the <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments">register_taxonomy()</a> function.
	* @return			void
	*/ 
	protected function addTaxonomy( $strTaxonomySlug, $arrArgs ) {
		
		$strTaxonomySlug = $this->oUtil->sanitizeSlug( $strTaxonomySlug );
		$this->oProps->arrTaxonomies[ $strTaxonomySlug ] = $arrArgs;	
		if ( isset( $arrArgs['show_table_filter'] ) && $arrArgs['show_table_filter'] )
			$this->oProps->arrTaxonomyTableFilters[] = $strTaxonomySlug;
		if ( isset( $arrArgs['show_in_sidebar_menus'] ) && ! $arrArgs['show_in_sidebar_menus'] )
			$this->oProps->arrTaxonomyRemoveSubmenuPages[ "edit-tags.php?taxonomy={$strTaxonomySlug}&amp;post_type={$this->oProps->strPostType}" ] = "edit.php?post_type={$this->oProps->strPostType}";
				
		if ( count( $this->oProps->arrTaxonomyTableFilters ) == 1 )
			add_action( 'init', array( $this, 'registerTaxonomies' ) );	// the hook should not be admin_init because taxonomies need to be accessed in regular pages.
		if ( count( $this->oProps->arrTaxonomyRemoveSubmenuPages ) == 1 )
			add_action( 'admin_menu', array( $this, 'removeTexonomySubmenuPages' ), 999 );		
			
	}	

	/**
	* Sets whether the author dropdown filter is enabled/disabled in the post type post list table.
	* 
	* <h4>Example</h4>
	* <code>this->setAuthorTableFilter( true );</code>
	* 
	* @since			2.0.0
	* @param			boolean			$fEnableAuthorTableFileter			If true, it enables the author filter; otherwise, it disables it.
	* @return			void
	*/ 
	protected function setAuthorTableFilter( $fEnableAuthorTableFileter=false ) {
		$this->oProps->fEnableAuthorTableFileter = $fEnableAuthorTableFileter;
	}
	
	/**
	 * Sets the post type arguments.
	 * 
	 * This is only necessary if it is not set to the constructor.
	 * 
	 * @since			2.0.0
	 * @see				http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
	 * @param			array			$arrArgs			The <a href="http://codex.wordpress.org/Function_Reference/register_post_type#Arguments">array of arguments</a> to be passed to the second parameter of the <em>register_post_type()</em> function.
	 * @return			void
	 */ 
	protected function setPostTypeArgs( $arrArgs ) {
		$this->oProps->arrPostTypeArgs = $arrArgs;
	}
	
	/**
	 * Sets the given HTML text into the footer on the left hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoLeft( '&lt;br /&gt;Custom Text on the left hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$strHTML			The HTML code to insert.
	 * @param			boolean			$fAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */	
	protected function setFooterInfoLeft( $strHTML, $fAppend=true ) {
		if ( isset( $this->oLink ) )	// check if the object is set to ensure it won't trigger a warning message in non-admin pages.
			$this->oLink->arrFooterInfo['strLeft'] = $fAppend 
				? $this->oLink->arrFooterInfo['strLeft'] . $strHTML
				: $strHTML;
	}
	
	/**
	 * Sets the given HTML text into the footer on the right hand side.
	 * 
	 * <h4>Example</h4>
	 * <code>$this->setFooterInfoRight( '&lt;br /&gt;Custom Text on the right hand side.' );</code>
	 * 
	 * @since			2.0.0
	 * @param			string			$strHTML			The HTML code to insert.
	 * @param			boolean			$fAppend			If true, the text will be appended; otherwise, it will replace the default text.
	 * @return			void
	 */		
	protected function setFooterInfoRight( $strHTML, $fAppend=true ) {
		if ( isset( $this->oLink ) )	// check if the object is set to ensure it won't trigger a warning message in non-admin pages.	
			$this->oLink->arrFooterInfo['strRight'] = $fAppend 
				? $this->oLink->arrFooterInfo['strRight'] . $strHTML
				: $strHTML;
	}

	/**
	 * Sets the given screen icon to the post type screen icon.
	 * 
	 * @since			2.1.3
	 * @since			2.1.6				The $strSRC parameter can accept file path.
	 */
	private function getStylesForPostTypeScreenIcon( $strSRC ) {
		
		$strNone = 'none';
		
		$strSRC = $this->oUtil->resolveSRC( $strSRC );
		
		return "#post-body-content {
				margin-bottom: 10px;
			}
			#edit-slug-box {
				display: {$strNone};
			}
			#icon-edit.icon32.icon32-posts-" . $this->oProps->strPostType . " {
				background: url('" . $strSRC . "') no-repeat;
				background-size: 32px 32px;
			}			
		";		
		
	}
	
	/*
	 * Callback functions
	 */
	public function addStyle() {

		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != $this->oProps->strPostType )
			return;

		// If the screen icon url is specified
		if ( isset( $this->oProps->arrPostTypeArgs['screen_icon'] ) && $this->oProps->arrPostTypeArgs['screen_icon'] )
			$this->oProps->strStyle = $this->getStylesForPostTypeScreenIcon( $this->oProps->arrPostTypeArgs['screen_icon'] );
			
		$this->oProps->strStyle = $this->oUtil->addAndApplyFilters( $this, "style_{$this->oProps->strClassName}", $this->oProps->strStyle );
		
		// Print out the filtered styles.
		if ( ! empty( $this->oProps->strStyle ) )
			echo "<style type='text/css' id='admin-page-framework-style-post-type'>" 
				. $this->oProps->strStyle
				. "</style>";			
		
	}
	
	public function registerPostType() {

		register_post_type( $this->oProps->strPostType, $this->oProps->arrPostTypeArgs );
		
		$bIsPostTypeSet = get_option( "post_type_rules_flased_{$this->oProps->strPostType}" );
		if ( $bIsPostTypeSet !== true ) {
		   flush_rewrite_rules( false );
		   update_option( "post_type_rules_flased_{$this->oProps->strPostType}", true );
		}

	}	

	public function registerTaxonomies() {
		
		foreach( $this->oProps->arrTaxonomies as $strTaxonomySlug => $arrArgs ) 
			register_taxonomy(
				$strTaxonomySlug,
				$this->oProps->strPostType,
				$arrArgs	// for the argument array keys, refer to: http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
			);	
			
	}
	
	public function removeTexonomySubmenuPages() {
		
		foreach( $this->oProps->arrTaxonomyRemoveSubmenuPages as $strSubmenuPageSlug => $strTopLevelPageSlug )
			remove_submenu_page( $strTopLevelPageSlug, $strSubmenuPageSlug );
		
	}
	
	public function disableAutoSave() {
		
		if ( $this->oProps->fEnableAutoSave ) return;
		if ( $this->oProps->strPostType != get_post_type() ) return;
		wp_dequeue_script( 'autosave' );
			
	}
	
	/**
	 * Adds a dorpdown list to filter posts by author, placed above the post type listing table.
	 */ 
	public function addAuthorTableFilter() {
		
		if ( ! $this->oProps->fEnableAuthorTableFileter ) return;
		
		if ( ! ( isset( $_GET['post_type'] ) && post_type_exists( $_GET['post_type'] ) 
			&& in_array( strtolower( $_GET['post_type'] ), array( $this->oProps->strPostType ) ) ) )
			return;
		
		wp_dropdown_users( array(
			'show_option_all'	=> 'Show all Authors',
			'show_option_none'	=> false,
			'name'			=> 'author',
			'selected'		=> ! empty( $_GET['author'] ) ? $_GET['author'] : 0,
			'include_selected'	=> false
		));
			
	}
	
	/**
	 * Adds drop-down lists to filter posts by added taxonomies, placed above the post type listing table.
	 */ 
	public function addTaxonomyTableFilter() {
		
		if ( $GLOBALS['typenow'] != $this->oProps->strPostType ) return;
		
		// If there is no post added to the post type, do nothing.
		$oPostCount = wp_count_posts( $this->oProps->strPostType );
		if ( $oPostCount->publish + $oPostCount->future + $oPostCount->draft + $oPostCount->pending + $oPostCount->private + $oPostCount->trash == 0 )
			return;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $strTaxonomySulg ) {
			
			if ( ! in_array( $strTaxonomySulg, $this->oProps->arrTaxonomyTableFilters ) ) continue;
			
			$oTaxonomy = get_taxonomy( $strTaxonomySulg );
 
			// If there is no added term, skip.
			if ( wp_count_terms( $oTaxonomy->name ) == 0 ) continue; 			

			// This function will echo the drop down list based on the passed array argument.
			wp_dropdown_categories( array(
				'show_option_all' => $this->oMsg->___( 'show_all' ) . ' ' . $oTaxonomy->label,
				'taxonomy' 	  => $strTaxonomySulg,
				'name' 		  => $oTaxonomy->name,
				'orderby' 	  => 'name',
				'selected' 	  => intval( isset( $_GET[ $strTaxonomySulg ] ) ),
				'hierarchical' 	  => $oTaxonomy->hierarchical,
				'show_count' 	  => true,
				'hide_empty' 	  => false,
				'hide_if_empty'	=> false,
				'echo'	=> true,	// this make the function print the output
			) );
			
		}
	}
	public function setTableFilterQuery( $oQuery=null ) {
		
		if ( 'edit.php' != $GLOBALS['pagenow'] ) return $oQuery;
		
		if ( ! isset( $GLOBALS['typenow'] ) ) return $oQuery;
		
		foreach ( get_object_taxonomies( $GLOBALS['typenow'] ) as $strTaxonomySlug ) {
			
			if ( ! in_array( $strTaxonomySlug, $this->oProps->arrTaxonomyTableFilters ) ) continue;
			
			$strVar = &$oQuery->query_vars[ $strTaxonomySlug ];
			if ( ! isset( $strVar ) ) continue;
			
			$oTerm = get_term_by( 'id', $strVar, $strTaxonomySlug );
			if ( is_object( $oTerm ) )
				$strVar = $oTerm->slug;

		}
		return $oQuery;
		
	}
	
	public function setColumnCell( $strColumnTitle, $intPostID ) { 
	
		// foreach ( $this->oProps->arrColumnHeaders as $strColumnHeader => $strColumnHeaderTranslated ) 
			// if ( $strColumnHeader == $strColumnTitle ) 
			
		// cell_{post type}_{custom column key}
		echo $this->oUtil->addAndApplyFilter( $this, "{$this->oProps->strPrefix_Cell}{$this->oProps->strPostType}_{$strColumnTitle}", $strCell='', $intPostID );
				  
	}
	
	/*
	 * Magic method - this prevents PHP's not-a-valid-callback errors.
	*/
	public function __call( $strMethodName, $arrArgs=null ) {	
		if ( substr( $strMethodName, 0, strlen( $this->oProps->strPrefix_Cell ) ) == $this->oProps->strPrefix_Cell ) return $arrArgs[0];
		if ( substr( $strMethodName, 0, strlen( "style_" ) )== "style_" ) return $arrArgs[0];
	}
	
}
endif;


if ( ! class_exists( 'AmazonAutoLinks_AdminPageFramework_MetaBox' ) ) :
/**
 * Provides methods for creating meta boxes.
 *
 * <h2>Hooks</h2>
 * <p>The class automatically creates WordPress action and filter hooks associated with the class methods.
 * The class methods corresponding to the name of the below actions and filters can be extended to modify the page output. Those methods are the callbacks of the filters and actions.</p>
 * <h3>Methods and Action Hooks</h3>
 * <ul>
 * 	<li><code>start_ + extended class name</code> – triggered at the end of the class constructor.</li>
 * </ul>
 * <h3>Methods and Filter Hooks</h3>
 * <ul>
 * 	<li><code>extended class name + _ + field_ + field ID</code> – receives the form input field output of the given input field ID. The first parameter: output string. The second parameter: the array of option.</li>
 * 	<li><code>style_ + extended class name</code> –  receives the output of the CSS rules applied to the pages of the associated post types with the meta box.</li>
 * 	<li><code>script_ + extended class name</code> – receives the output of the JavaScript scripts applied to the pages of the associated post types with the meta box.</li>
 * 	<li><code>validation_ + extended class name</code> – receives the form submission values as array. The first parameter: submitted input array. The second parameter: the original array stored in the database.</li>
 * </ul>
 * <h3>Remarks</h3>
 * <p>The slugs must not contain a dot(.) or a hyphen(-) since it is used in the callback method name.</p>  
 * 
 * @abstract
 * @since			2.0.0
 * @use				AmazonAutoLinks_AdminPageFramework_Utilities
 * @use				AmazonAutoLinks_AdminPageFramework_Messages
 * @use				AmazonAutoLinks_AdminPageFramework_Debug
 * @use				AmazonAutoLinks_AdminPageFramework_Properties
 * @package			Admin Page Framework
 * @subpackage		Admin Page Framework - Meta Box
 */
abstract class AmazonAutoLinks_AdminPageFramework_MetaBox extends AmazonAutoLinks_AdminPageFramework_MetaBox_Help {
	
	// Objects
	/**
	* @internal
	* @since			2.0.0
	*/ 	
	protected $oDebug;
	/**
	* @internal
	* @since			2.0.0
	*/ 		
	protected $oUtil;
	/**
	* @since			2.0.0
	* @internal
	*/ 		
	protected $oMsg;
	/**
	 * @since			2.1.5
	 * @internal
	 */
	protected $oHeadTag;
	
	/**
	 * Constructs the class object instance of AmazonAutoLinks_AdminPageFramework_MetaBox.
	 * 
	 * @see				http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters
	 * @since			2.0.0
	 * @param			string			$strMetaBoxID			The meta box ID.
	 * @param			string			$strTitle				The meta box title.
	 * @param			string|array	$vPostTypes				( optional ) The post type(s) that the meta box is associated with.
	 * @param			string			$strContext				( optional ) The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side') Default: normal.
	 * @param			string			$strPriority			( optional ) The priority within the context where the boxes should show ('high', 'core', 'default' or 'low') Default: default.
	 * @param			string			$strCapability			( optional ) The <a href="http://codex.wordpress.org/Roles_and_Capabilities">access level</a> to the meta box. Default: edit_posts.
	 * @param			string			$strTextDomain			( optional ) The text domain applied to the displayed text messages. Default: admin-page-framework.
	 * @return			void
	 */ 
	function __construct( $strMetaBoxID, $strTitle, $vPostTypes=array( 'post' ), $strContext='normal', $strPriority='default', $strCapability='edit_posts', $strTextDomain='admin-page-framework' ) {
		
		// Objects
		$this->oUtil = new AmazonAutoLinks_AdminPageFramework_Utilities;
		$this->oMsg = AmazonAutoLinks_AdminPageFramework_Messages::instantiate( $strTextDomain );
		$this->oDebug = new AmazonAutoLinks_AdminPageFramework_Debug;
		$this->oProps = new AmazonAutoLinks_AdminPageFramework_MetaBox_Properties( $this );
		$this->oHeadTag = new AmazonAutoLinks_AdminPageFramework_HeadTag_MetaBox( $this->oProps );
			
		// Properties
		$this->oProps->strMetaBoxID = $this->oUtil->sanitizeSlug( $strMetaBoxID );
		$this->oProps->strTitle = $strTitle;
		$this->oProps->arrPostTypes = is_string( $vPostTypes ) ? array( $vPostTypes ) : $vPostTypes;	
		$this->oProps->strContext = $strContext;	//  'normal', 'advanced', or 'side' 
		$this->oProps->strPriority = $strPriority;	// 	'high', 'core', 'default' or 'low'
		$this->oProps->strClassName = get_class( $this );
		$this->oProps->strClassHash = md5( $this->oProps->strClassName );
		$this->oProps->strCapability = $strCapability;
				
		if ( is_admin() ) {
			
			add_action( 'wp_loaded', array( $this, 'replyToLoadDefaultFieldTypeDefinitions' ), 10 );	// should be loaded before the setUp() method.
			add_action( 'wp_loaded', array( $this, 'setUp' ), 11 );
			
			add_action( 'add_meta_boxes', array( $this, 'addMetaBox' ) );
			add_action( 'save_post', array( $this, 'saveMetaBoxFields' ) );
						
			// the contextual help pane
			add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'registerHelpTabTextForMetaBox' ), 20 );	
	
			if ( in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php', ) ) ) 
				add_filter( 'gettext', array( $this, 'replaceThickBoxText' ) , 1, 2 );		
	
		}
		
		// Hooks
		$this->oUtil->addAndDoAction( $this, "{$this->oProps->strPrefixStart}{$this->oProps->strClassName}" );
		
	}
	
	/**
	 * Loads the default field type definition.
	 * 
	 * @since			2.1.5
	 */
	public function replyToLoadDefaultFieldTypeDefinitions() {
		
		// This class adds filters for the field type definitions so that framework's default field types will be added.
		new AmazonAutoLinks_AdminPageFramework_BuiltinInputFieldTypeDefinitions( $this->oProps->arrFieldTypeDefinitions, $this->oProps->strClassName, $this->oMsg );		
		$this->oProps->arrFieldTypeDefinitions = $this->oUtil->addAndApplyFilter(		// Parameters: $oCallerObject, $strFilter, $vInput, $vArgs...
			$this,
			'field_types_' . $this->oProps->strClassName,	// 'field_types_' . {extended class name}
			$this->oProps->arrFieldTypeDefinitions
		);				
		
	}

	
	/**
	* The method for all necessary set-ups.
	* 
	* <h4>Example</h4>
	* <code>	public function setUp() {		
	* 	$this->addSettingFields(
	* 		array(
	* 			'strFieldID'		=> 'sample_metabox_text_field',
	* 			'strTitle'			=> 'Text Input',
	* 			'strDescription'	=> 'The description for the field.',
	* 			'strType'			=> 'text',
	* 		),
	* 		array(
	* 			'strFieldID'		=> 'sample_metabox_textarea_field',
	* 			'strTitle'			=> 'Textarea',
	* 			'strDescription'	=> 'The description for the field.',
	* 			'strType'			=> 'textarea',
	* 			'vDefault'			=> 'This is a default text.',
	* 		)
	* 	);		
	* }</code>
	* 
	* @abstract
	* @since			2.0.0
	* @remark			The user may override this method.
	* @return			void
	*/	 
	public function setUp() {}
	
	/**
	* Adds the given field array items into the field array property. 
	* 
	* <h4>Example</h4>
	* <code>    $this->addSettingFields(
    *     array(
    *         'strFieldID'        => 'sample_metabox_text_field',
    *         'strTitle'          => 'Text Input',
    *         'strDescription'    => 'The description for the field.',
    *         'strType'           => 'text',
    *     ),
    *     array(
    *         'strFieldID'        => 'sample_metabox_textarea_field',
    *         'strTitle'          => 'Textarea',
    *         'strDescription'    => 'The description for the field.',
    *         'strType'           => 'textarea',
    *         'vDefault'          => 'This is a default text.',
    *     )
    * );</code>
	* 
	* @since			2.0.0
	* @remark			Accepts variadic parameters; the number of accepted parameters are not limited to three.
	* @param			array			$arrField1			The field array.
	* @param			array			$arrField2			Another field array.
	* @param			array			$_and_more			Add more fields arrays as many as necessary to the next parameters.
	* @return			void
	*/ 
	protected function addSettingFields( $arrField1, $arrField2=null, $_and_more=null ) {

		foreach( func_get_args() as $arrField ) 
			$this->addSettingField( $arrField );
		
	}	
	/**
	* Adds the given field array items into the field array property.
	* 
	* Itentical to the addSettingFields() method except that this method does not accept enumerated parameters. 
	* 
	* @since			2.1.2
	* @return			void
	* @remark			The user may use this method in their extended class definition.
	*/		
	protected function addSettingField( $arrField ) {

		if ( ! is_array( $arrField ) ) return;
		
		$arrField = $arrField + AmazonAutoLinks_AdminPageFramework_MetaBox_Properties::$arrStructure_Field;	// avoid undefined index warnings.
		
		// Sanitize the IDs since they are used as a callback method name.
		$arrField['strFieldID'] = $this->oUtil->sanitizeSlug( $arrField['strFieldID'] );
		
		// Check the mandatory keys' values are set.
		if ( ! isset( $arrField['strFieldID'], $arrField['strType'] ) ) return;	// these keys are necessary.
						
		// If a custom condition is set and it's not true, skip.
		if ( ! $arrField['fIf'] ) return;
							
		// Load head tag elements for fields.
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) )		// edit post page
			)
		) {
			// Set relevant scripts and styles for the input field.
			$this->setFieldHeadTagElements( $arrField );

		}
		
		// For the contextual help pane,
		if ( 
			in_array( $GLOBALS['pagenow'], array( 'post.php', 'post-new.php', ) ) 
			&& ( 
				( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], $this->oProps->arrPostTypes ) )
				|| ( isset( $_GET['post'], $_GET['action'] ) && in_array( get_post_type( $_GET['post'] ), $this->oProps->arrPostTypes ) )		// edit post page
			)
			&& $arrField['strHelp']
		) {
			
			$this->addHelpTextForFormFields( $arrField['strTitle'], $arrField['strHelp'], $arrField['strHelpAside'] );
							
		}
	
		$this->oProps->arrFields[ $arrField['strFieldID'] ] = $arrField;
	
	}
		/**
		 * Sets the given field type's enqueuing scripts and styles.
		 * 
		 * A helper function for the above registerSettings() method.
		 * 
		 * @since			2.1.5
		 */
		private function setFieldHeadTagElements( $arrField ) {
			
			$strFieldType = $arrField['strType'];
			
			// Set the global flag to indicate whether the elements are already added and enqueued.
			if ( isset( $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'][ $strFieldType ] ) && $GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'][ $strFieldType ] ) return;
			$GLOBALS['arrAmazonAutoLinks_AdminPageFramework']['arrFieldFlags'][ $strFieldType ] = true;

			// If the field type is not defined, return.
			if ( ! isset( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ] ) ) return;

			if ( is_callable( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callFieldLoader'] ) )
				call_user_func_array( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callFieldLoader'], array() );		
			
			if ( is_callable( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetScripts'] ) )
				$this->oProps->strScript .= call_user_func_array( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetScripts'], array() );
				
			if ( is_callable( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetStyles'] ) )
				$this->oProps->strStyle .= call_user_func_array( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetStyles'], array() );
				
			if ( is_callable( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetIEStyles'] ) )
				$this->oProps->strStyleIE .= call_user_func_array( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['callGetIEStyles'], array() );					

			$this->oHeadTag->enqueueStyles( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['arrEnqueueStyles'] );
			$this->oHeadTag->enqueueScripts( $this->oProps->arrFieldTypeDefinitions[ $strFieldType ]['arrEnqueueScripts'] );
					
		}		

	/**
	 * 
	 * since			2.1.3
	 */
	public function removeMediaLibraryTab( $arrTabs ) {
		
		if ( ! isset( $_REQUEST['enable_external_source'] ) ) return $arrTabs;
		
		if ( ! $_REQUEST['enable_external_source'] )
			unset( $arrTabs['type_url'] );	// removes the From URL tab in the thick box.
		
		return $arrTabs;
		
	}

	/**
 	 * Replaces the label text of a button used in the media uploader.
	 * @since			2.0.0
	 * @remark			A callback for the <em>gettext</em> hook.
	 */ 
	public function replaceThickBoxText( $strTranslated, $strText ) {

		// Replace the button label in the media thick box.
		if ( ! in_array( $GLOBALS['pagenow'], array( 'media-upload.php', 'async-upload.php' ) ) ) return $strTranslated;
		if ( $strText != 'Insert into Post' ) return $strTranslated;
		if ( $this->oUtil->getQueryValueInURLByKey( wp_get_referer(), 'referrer' ) != 'admin_page_framework' ) return $strTranslated;
		
		if ( isset( $_GET['button_label'] ) ) return $_GET['button_label'];

		return $this->oProps->strThickBoxButtonUseThis ?  $this->oProps->strThickBoxButtonUseThis : $this->oMsg->___( 'use_this_image' );
		
	}
	
	/**
	 * Adds the defined meta box.
	 * 
	 * @since			2.0.0
	 * @remark			uses <em>add_meta_box()</em>.
	 * @remark			A callback for the <em>add_meta_boxes</em> hook.
	 * @return			void
	 */ 
	public function addMetaBox() {
		
		foreach( $this->oProps->arrPostTypes as $strPostType ) 
			add_meta_box( 
				$this->oProps->strMetaBoxID, 		// id
				$this->oProps->strTitle, 	// title
				array( $this, 'echoMetaBoxContents' ), 	// callback
				$strPostType,		// post type
				$this->oProps->strContext, 	// context
				$this->oProps->strPriority,	// priority
				$this->oProps->arrFields	// argument
			);
			
	}	
	
	/**
	 * Echoes the meta box contents.
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>add_meta_box()</em> method.
	 * @param			object			$oPost			The object of the post associated with the meta box.
	 * @param			array			$vArgs			The array of arguments.
	 * @return			void
	 */ 
	public function echoMetaBoxContents( $oPost, $vArgs ) {	
		
		// Use nonce for verification
		$strOut = wp_nonce_field( $this->oProps->strMetaBoxID, $this->oProps->strMetaBoxID, true, false );
		
		// Begin the field table and loop
		$strOut .= '<table class="form-table">';
		$this->setOptionArray( $oPost->ID, $vArgs['args'] );
		
		foreach ( ( array ) $vArgs['args'] as $arrField ) {
			
			// Avoid undefined index warnings
			$arrField = $arrField + AmazonAutoLinks_AdminPageFramework_MetaBox_Properties::$arrStructure_Field;
			
			// get value of this field if it exists for this post
			$strStoredValue = get_post_meta( $oPost->ID, $arrField['strFieldID'], true );
			$arrField['vValue'] = $strStoredValue ? $strStoredValue : $arrField['vValue'];
			
			// Check capability. If the access level is not sufficient, skip.
			$arrField['strCapability'] = isset( $arrField['strCapability'] ) ? $arrField['strCapability'] : $this->oProps->strCapability;
			if ( ! current_user_can( $arrField['strCapability'] ) ) continue; 			
			
			// Begin a table row. 
			
			// If it's a hidden input type, do now draw a table row
			if ( $arrField['strType'] == 'hidden' ) {
				$strOut .= "<tr><td style='height: 0; padding: 0; margin: 0; line-height: 0;'>"
					. $this->getFieldOutput( $arrField )
					. "</td></tr>";
				continue;
			}
			$strOut .= "<tr>";
			if ( ! $arrField['fHideTitleColumn'] )
				$strOut .= "<th><label for='{$arrField['strFieldID']}'>"
						. "<a id='{$arrField['strFieldID']}'></a>"
						. "<span title='" . strip_tags( isset( $arrField['strTip'] ) ? $arrField['strTip'] : $arrField['strDescription'] ) . "'>"
						. $arrField['strTitle'] 
						. "</span>"
						. "</label></th>";		
			$strOut .= "<td>";
			$strOut .= $this->getFieldOutput( $arrField );
			$strOut .= "</td>";
			$strOut .= "</tr>";
			
		} // end foreach
		$strOut .= '</table>'; // end table
		echo $strOut;
		
	}
	private function setOptionArray( $intPostID, $arrFields ) {
		
		if ( ! is_array( $arrFields ) ) return;
		
		foreach( $arrFields as $intIndex => $arrField ) {
			
			// Avoid undefined index warnings
			$arrField = $arrField + AmazonAutoLinks_AdminPageFramework_MetaBox_Properties::$arrStructure_Field;

			$this->oProps->arrOptions[ $intIndex ] = get_post_meta( $intPostID, $arrField['strFieldID'], true );
			
		}
	}	
	private function getFieldOutput( $arrField ) {

		// Set the input field name which becomes the option key of the custom meta field of the post.
		$arrField['strName'] = isset( $arrField['strName'] ) ? $arrField['strName'] : $arrField['strFieldID'];

		// Render the form field. 		
		$strFieldType = isset( $this->oProps->arrFieldTypeDefinitions[ $arrField['strType'] ]['callRenderField'] ) && is_callable( $this->oProps->arrFieldTypeDefinitions[ $arrField['strType'] ]['callRenderField'] )
			? $arrField['strType']
			: 'default';	// the predefined reserved field type is applied if the parsing field type is not defined(not found).
		$oField = new AmazonAutoLinks_AdminPageFramework_InputField( $arrField, $this->oProps->arrOptions, array(), $this->oProps->arrFieldTypeDefinitions[ $strFieldType ], $this->oMsg );	// currently the error array is not supported for meta-boxes
		$oField->isMetaBox( true );
		$strFieldOutput = $oField->getInputField( $strFieldType );	// field output
		unset( $oField );	// release the object for PHP 5.2.x or below.
		
		return $this->oUtil->addAndApplyFilters(
			$this,
			array( 
				$this->oProps->strClassName . '_' . 'field_' . $arrField['strFieldID'],	// this filter will be deprecated
				'field_' . $this->oProps->strClassName . '_' . $arrField['strFieldID']	// field_ + {extended class name} + _ {field id}
			),
			$strFieldOutput,
			$arrField // the field array
		);		
		
		// return $this->oUtil->addAndApplyFilter(
			// $this,
			// $this->oProps->strClassName . '_' . 'field_' . $arrField['strFieldID'],	// filter: class name + _ + field_ + field id
			// $strFieldOutput,
			// $arrField // the field array
		// );	
				
	}
		
	/**
	 * Saves the meta box field data to the associated post. 
	 * 
	 * @since			2.0.0
	 * @remark			A callback for the <em>save_post</em> hook
	 */
	public function saveMetaBoxFields( $intPostID ) {
		
		// Bail if we're doing an auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		// If our nonce isn't there, or we can't verify it, bail
		if ( ! isset( $_POST[ $this->oProps->strMetaBoxID ] ) || ! wp_verify_nonce( $_POST[ $this->oProps->strMetaBoxID ], $this->oProps->strMetaBoxID ) ) return;
			
		// Check permissions
		if ( in_array( $_POST['post_type'], $this->oProps->arrPostTypes )   
			&& ( ( ! current_user_can( $this->oProps->strCapability, $intPostID ) ) || ( ! current_user_can( $this->oProps->strCapability, $intPostID ) ) )
		) return;

		// Compose an array consisting of the submitted registered field values.
		$arrInput = array();
		foreach( $this->oProps->arrFields as $arrField ) 
			$arrInput[ $arrField['strFieldID'] ] = isset( $_POST[ $arrField['strFieldID'] ] ) ? $_POST[ $arrField['strFieldID'] ] : null;
			
		// Prepare the old value array.
		$arrOriginal = array();
		foreach ( $arrInput as $strFieldID => $v )
			$arrOriginal[ $strFieldID ] = get_post_meta( $intPostID, $strFieldID, true );
					
		// Apply filters to the array of the submitted values.
		$arrInput = $this->oUtil->addAndApplyFilters( $this, "validation_{$this->oProps->strClassName}", $arrInput, $arrOriginal );

		// Loop through fields and save the data.
		foreach ( $arrInput as $strFieldID => $vValue ) {
			
			// $strOldValue = get_post_meta( $intPostID, $strFieldID, true );			
			$strOldValue = isset( $arrOriginal[ $strFieldID ] ) ? $arrOriginal[ $strFieldID ] : null;
			if ( ! is_null( $vValue ) && $vValue != $strOldValue ) {
				update_post_meta( $intPostID, $strFieldID, $vValue );
				continue;
			} 
			// if ( '' == $strNewValue && $strOldValue ) 
				// delete_post_meta( $intPostID, $arrField['strFieldID'], $strOldValue );
			
		} // end foreach
		
	}	
	
	/*
	 * Magic method
	*/
	function __call( $strMethodName, $arrArgs=null ) {	
		
		// the start_ action hook.
		if ( $strMethodName == $this->oProps->strPrefixStart . $this->oProps->strClassName ) return;

		// the class name + field_ field ID filter.
		if ( substr( $strMethodName, 0, strlen( 'field_' . $this->oProps->strClassName . '_' ) ) == 'field_' . $this->oProps->strClassName . '_' )
			return $arrArgs[ 0 ];
		
		// the class name + field_ field ID filter.
		if ( substr( $strMethodName, 0, strlen( $this->oProps->strClassName . '_' . 'field_' ) ) == $this->oProps->strClassName . '_' . 'field_' )
			return $arrArgs[ 0 ];

		// the field_types_ + class name filter. [2.1.5+]
		if ( substr( $strMethodName, 0, strlen( "field_types_{$this->oProps->strClassName}" ) ) == "field_types_{$this->oProps->strClassName}" )
			return $arrArgs[ 0 ];		
			
		// the script_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "script_{$this->oProps->strClassName}" ) ) == "script_{$this->oProps->strClassName}" )
			return $arrArgs[ 0 ];		
	
		// the style_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "style_{$this->oProps->strClassName}" ) ) == "style_{$this->oProps->strClassName}" )
			return $arrArgs[ 0 ];		

		// the validation_ + class name	filter.
		if ( substr( $strMethodName, 0, strlen( "validation_{$this->oProps->strClassName}" ) ) == "validation_{$this->oProps->strClassName}" )
			return $arrArgs[ 0 ];				
			
	}
}
endif;