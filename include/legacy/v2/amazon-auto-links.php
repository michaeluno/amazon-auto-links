<?php
/*
	Plugin Name:    Amazon Auto Links
	Plugin URI:     http://en.michaeluno.jp/amazon-auto-links
	Description:    Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
	Author:         Michael Uno (miunosoft)
	Author URI:     http://michaeluno.jp
	Requirements:   WordPress >= 3.3 and PHP >= 5.2.4
	Version:        3
*/

/**
 * Provides the basic information about the plugin.
 * 
 * @since       2.0.6
 */
class AmazonAutoLinks_Commons_Base {
 
	const Version        = '3';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
	const Name           = 'Amazon Auto Links';
	const Description    = 'Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.';
	const URI            = 'http://en.michaeluno.jp/amazon-auto-links';
	const Author         = 'miunosoft (Michael Uno)';
	const AuthorURI      = 'http://en.michaeluno.jp/';
	const Copyright      = 'Copyright (c) 2013-2014, Michael Uno';
	const License        = 'GPL v2 or later';
	const Contributors   = '';
 
}

// Do not load if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    return; 
}

/**
 * Provides the common data shared among plugin files.
 * 
 * To use the class, first call the setUp() method, which sets up the necessary properties.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @authorurl	http://michaeluno.jp
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since		2.0.0
*/
final class AmazonAutoLinks_Commons extends AmazonAutoLinks_Commons_Base {
    
	const TextDomain                = 'amazon-auto-links';
	const TextDomainPath            = './language';
	const ShortCode                 = 'amazon_auto_links';
	const TagSlug                   = 'amazon_auto_links_tag';
	const AdminOptionKey            = 'amazon_auto_links_admin';
	const TransientPrefix           = 'AAL';
	const PostTypeSlug              = 'amazon_auto_links';
	const PostTypeSlugAutoInsert    = 'aal_auto_insert';	// amazon_auto_links_auto_insert fails creating the post type.
	const PageSettingsSlug          = 'aal_settings';	// this is to be referred by Pro and third party extension.
	const SectionID_License         = 'pro_license';
	const FieldID_LicenseKey        = 'pro_license_key';
	
    static public $sFilePath;   // 2.0.6+
    static public $sDirPath;    // 2.0.6+
    static public $sPluginPath;
    static public $sPluginDirPath;
    static public $sPluginName;     
    static public $sPluginVersion;       
    static public $sPluginDescription;   
    static public $sPluginAuthor;      
    static public $sPluginAuthorURI;     
    static public $sPluginStoreURI;      
    static public $sPluginTextDomain;    
    static public $sPluginDomainPath;            
    static public $strPluginFilePath;  
    static public $strPluginDirPath;     	
    static public $strPluginName;     
    static public $strPluginVersion;     
    static public $strPluginDescription;
    static public $strPluginAuthor;  
    static public $strPluginAuthorURI;
    static public $strPluginTextDomain;
    static public $strPluginDomainPath; 
    static public $strPluginNetwork;   
    static public $strPluginSiteWide; 
    static public $strPluginStoreURI; 
    
	static function setUp( $sPluginFilePath=null ) {
		
        self::$sFilePath            = $sPluginFilePath ? $sPluginFilePath : __FILE__;             // 2.0.6+
        self::$sDirPath             = dirname( self::$sFilePath );  // 2.0.6+
        
		// These static properties are for backward compatibility.
        self::$sPluginPath          = self::$sFilePath;             // backward compat
        self::$sPluginDirPath       = self::$sDirPath;              // backward compat
        self::$sPluginName          = self::Name;
        self::$sPluginVersion       = self::Version;
        self::$sPluginDescription   = self::Description;
        self::$sPluginAuthor        = self::Author;
        self::$sPluginAuthorURI     = self::AuthorURI;
        self::$sPluginStoreURI      = 'http://michaeluno.jp';
        self::$sPluginTextDomain    = self::TextDomain;
        self::$sPluginDomainPath    = self::TextDomainPath;
	
        // Backward compatibility - will be deprecated
		self::$strPluginFilePath    = self::$sFilePath;
		self::$strPluginDirPath     = self::$sDirPath;
		// self::$strPluginURI         = plugins_url( '', self::$strPluginFilePath );   // @deprecated
        self::$strPluginName        = self::Name;
        self::$strPluginVersion     = self::Version;
        self::$strPluginDescription = self::Description;
        self::$strPluginAuthor      = self::Author;
        self::$strPluginAuthorURI   = self::AuthorURI;
        self::$strPluginTextDomain  = self::TextDomain;
        self::$strPluginDomainPath  = self::TextDomainPath;
        self::$strPluginNetwork     = '';
        self::$strPluginSiteWide    = 'Site Wide Only';
        self::$strPluginStoreURI    = 'http://michaeluno.jp';
    
	}	
	
	public static function getPluginURL( $sRelativePath='' ) {
		return plugins_url( $sRelativePath, self::$strPluginFilePath );
	}

	
}

// Run the bootstrap
AmazonAutoLinks_Commons::setUp( __FILE__ );
include( AmazonAutoLinks_Commons::$sDirPath . '/include/class/boot/AmazonAutoLinks_AutoLoad.php' );
include( AmazonAutoLinks_Commons::$sDirPath . '/include/class/boot/AmazonAutoLinks_Bootstrap.php' );
include( AmazonAutoLinks_Commons::$sDirPath . '/include/class/boot/AmazonAutoLinks_RegisterClasses.php' );

new AmazonAutoLinks_Bootstrap( __FILE__ );