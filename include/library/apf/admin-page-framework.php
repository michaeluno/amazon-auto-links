<?php
abstract class AmazonAutoLinks_AdminPageFramework_Registry_Base {
    const VERSION = '3.7.2b02';
    const NAME = 'Admin Page Framework';
    const DESCRIPTION = 'Facilitates WordPress plugin and theme development.';
    const URI = 'http://en.michaeluno.jp/admin-page-framework';
    const AUTHOR = 'Michael Uno';
    const AUTHOR_URI = 'http://en.michaeluno.jp/';
    const COPYRIGHT = 'Copyright (c) 2013-2015, Michael Uno';
    const LICENSE = 'MIT <http://opensource.org/licenses/MIT>';
    const CONTRIBUTORS = '';
}
final class AmazonAutoLinks_AdminPageFramework_Registry extends AmazonAutoLinks_AdminPageFramework_Registry_Base {
    const TEXT_DOMAIN = 'admin-page-framework';
    const TEXT_DOMAIN_PATH = '/language';
    static public $bIsMinifiedVersion = true;
    static public $bIsDevelopmentVersion = true;
    static public $sAutoLoaderPath;
    static public $sIncludeClassListPath;
    static public $aClassFiles = array();
    static public $sFilePath = '';
    static public $sDirPath = '';
    static public $sFileURI = '';
    static public function setUp($sFilePath = __FILE__) {
        self::$sFilePath = $sFilePath;
        self::$sDirPath = dirname(self::$sFilePath);
        self::$sFileURI = plugins_url('', self::$sFilePath);
        self::$sIncludeClassListPath = self::$sDirPath . '/admin-page-framework-include-class-list.php';
        self::$aClassFiles = self::_getClassFilePathList(self::$sIncludeClassListPath);
        self::$sAutoLoaderPath = isset(self::$aClassFiles['AmazonAutoLinks_AdminPageFramework_RegisterClasses']) ? self::$aClassFiles['AmazonAutoLinks_AdminPageFramework_RegisterClasses'] : '';
        self::$bIsMinifiedVersion = class_exists('AmazonAutoLinks_AdminPageFramework_MinifiedVersionHeader');
    }
    static private function _getClassFilePathList($sInclusionClassListPath) {
        $aClassFiles = array();
        include ($sInclusionClassListPath);
        return $aClassFiles;
    }
    static public function getVersion() {
        if (!isset(self::$sAutoLoaderPath)) {
            trigger_error('Admin Page Framework: ' . ' : ' . sprintf(__('The method is called too early. Perform <code>%2$s</code> earlier.', 'admin-page-framework'), __METHOD__, 'setUp()'), E_USER_WARNING);
            return self::VERSION;
        }
        $_aMinifiedVesionSuffix = array(0 => '', 1 => '.min',);
        $_aDevelopmentVersionSuffix = array(0 => '', 1 => '.dev',);
        return self::VERSION . $_aMinifiedVesionSuffix[( int )self::$bIsMinifiedVersion] . $_aDevelopmentVersionSuffix[( int )self::$bIsDevelopmentVersion];
    }
    static public function getInfo() {
        $_oReflection = new ReflectionClass(__CLASS__);
        return $_oReflection->getConstants() + $_oReflection->getStaticProperties();
    }
}
final class AmazonAutoLinks_AdminPageFramework_Bootstrap {
    public function __construct($sLibraryPath = __FILE__) {
        if (!$this->_isLoadable()) {
            return;
        }
        AmazonAutoLinks_AdminPageFramework_Registry::setUp($sLibraryPath);
        if (AmazonAutoLinks_AdminPageFramework_Registry::$bIsMinifiedVersion) {
            return;
        }
        include (AmazonAutoLinks_AdminPageFramework_Registry::$sAutoLoaderPath);
        new AmazonAutoLinks_AdminPageFramework_RegisterClasses(empty(AmazonAutoLinks_AdminPageFramework_Registry::$aClassFiles) ? AmazonAutoLinks_AdminPageFramework_Registry::$sDirPath : '', array('exclude_class_names' => array('AmazonAutoLinks_AdminPageFramework_MinifiedVersionHeader', 'AmazonAutoLinks_AdminPageFramework_BeautifiedVersionHeader',),), AmazonAutoLinks_AdminPageFramework_Registry::$aClassFiles);
        AmazonAutoLinks_AdminPageFramework_Registry::$bIsDevelopmentVersion = class_exists('AmazonAutoLinks_AdminPageFramework_InclusionClassFilesHeader');
    }
    private function _isLoadable() {
        if (isset(self::$sAutoLoaderPath)) {
            return false;
        }
        return defined('ABSPATH');
    }
}
new AmazonAutoLinks_AdminPageFramework_Bootstrap(__FILE__);