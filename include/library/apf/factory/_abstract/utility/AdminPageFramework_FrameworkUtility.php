<?php
class AmazonAutoLinks_AdminPageFramework_FrameworkUtility extends AmazonAutoLinks_AdminPageFramework_WPUtility {
    static public function getFrameworkVersion($bTrimDevVer = false) {
        $_sVersion = AmazonAutoLinks_AdminPageFramework_Registry::getVersion();
        return $bTrimDevVer ? self::getSuffixRemoved($_sVersion, '.dev') : $_sVersion;
    }
    static public function getFrameworkName() {
        return AmazonAutoLinks_AdminPageFramework_Registry::NAME;
    }
}