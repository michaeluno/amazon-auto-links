<?php
/**
 * Checks the specified requirements and if it fails, it deactivates the plugin.
 * 
 * @package         Amazon Auto Links
 * @copyright       Copyright (c) 2013, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since            2.0.0
 * @remark            The AmazonAutoLinks_Commons class needs to be set up to use this class.
*/

final class AmazonAutoLinks_Requirements {

    // Properties
    private $strAdminNotice = '';    // admin notice
    private $bSufficient = true;    // tells whether it suffices for all the requirements.
    private $bDeactivate = true;    // indicates whether automatically deactivate the plugin if the verification fails.
    private $arrParams = array();
    private $arrDefaultParams = array(
        'php' => array(
            'version' => '5.2.4',
            'error' => 'The plugin requires the PHP version %1$s or higher.',
        ),
        'wordpress' => array(
            'version' => '3.3',
            'error' => 'The plugin requires the WordPress version %1$s or higher.',
        ),
        'functions' => array(
            // e.g. 'echo' => 'The plugin requires the %1$s function.',
            // e.g. 'mblang' => 'The plugin requires the mbstring extension.',
        ),
        'classes' => array(
            // e.g. 'DOMDocument' => 'The plugin requires the DOMXML extension.',
        ),
        'constants'    => array(
            // e.g. 'THEADDONFILE' => 'The plugin requires the ... addon to be installed.',
        ),
    );
    private $strPluginFilePath;
    
    function __construct( $strPluginFilePath, $arrParams=array(), $bDeactivate=True, $strHook='' ) {
        
        // avoid undefined index warnings.
        $this->arrParams = $arrParams + $this->arrDefaultParams;    
        $this->arrParams['php'] = $this->arrParams['php'] + $this->arrDefaultParams['php'];
        $this->arrParams['wordpress'] = $this->arrParams['wordpress'] + $this->arrDefaultParams['wordpress'];

        $this->strPluginFilePath = $strPluginFilePath;
        // $this->arrScriptInfo = debug_backtrace();
        $this->bDeactivate = $bDeactivate;
        
        $this->strAdminNotice = '<strong>' . AmazonAutoLinks_Commons::$strPluginName . '</strong><br />';
;
        if ( ! empty( $strHook ) ) 
            add_action( $strHook, array( $this, 'checkRequirements' ) );
        else if ( $strHook === '' )        
            $this->checkRequirements();
        else if ( is_null( $strHook ) )
            return $this;    // do nothing if it's null
            
    }

    public function checkRequirements() {
        /*
         * Do not call this method with register_activation_hook(). For some reasons, it won't trigger the deactivate_plugins() function.
         * */
             
        if ( ! $this->isSufficientPHPVersion( $this->arrParams['php']['version'] ) ) {
            $this->bSufficient = False;
            $this->strAdminNotice .= sprintf( $this->arrParams['php']['error'], $this->arrParams['php']['version'] ) . '<br />';
        }

        if ( ! $this->isSufficientWordPressVersion( $this->arrParams['wordpress']['version'] ) ) {
            $this->bSufficient = False;
            $this->strAdminNotice .= sprintf( $this->arrParams['wordpress']['error'], $this->arrParams['wordpress']['version'] ) . '<br />';
        }
        
        // 'The plugin requires the PHP <a href="http://www.php.net/manual/en/mbstring.installation.php">mb string extension</a> installed on the server.
        if ( count( $arrNonFoundFuncs = $this->checkFunctions( $this->arrParams['functions'] ) ) > 0 ) {
            $this->bSufficient = False;
            foreach ( $arrNonFoundFuncs as $i => $strError ) 
                $this->strAdminNotice .= $strError . '<br />';
                
        }
        if ( count( $arrNonFoundClasses = $this->checkClasses( $this->arrParams['classes'] ) ) > 0 ) {
            $this->bSufficient = False;
            foreach ( $arrNonFoundClasses as $i => $strError ) 
                $this->strAdminNotice .= $strError . '<br />';
        }
        if ( count( $arrNonFoundConstants = $this->checkConstants( $this->arrParams['constants'] ) ) > 0 ) {
            $this->bSufficient = False;
            foreach ( $arrNonFoundConstants as $i => $strError ) 
                $this->strAdminNotice .= $strError . '<br />';
        }
    
        if ( ! $this->bSufficient ) {

            add_action( 'admin_notices', array( $this, 'showAdminNotice' ) );    
            if ( $this->bDeactivate ) {
                $this->includeOnce( ABSPATH . '/wp-admin/includes/plugin.php' );
                deactivate_plugins( $this->strPluginFilePath );
            }

        }
    }
            
    private function includeOnce( $strPath ) {
        
        if ( ! file_exists( $strPath ) ) return;
        include_once( $strPath );
        
    }    
    
    public function showAdminNotice() {

        $strMsg = $this->bDeactivate ? '<strong>' . __( 'Deactivating the plugin.', 'amazon-auto-links' ) . '</strong>' : '';
        echo '<div class="error"><p>' 
            . $this->strAdminNotice     // it ends with <br />
            . $strMsg
            . '</p></div>';
        
    }
    
    private function isSufficientPHPVersion( $strPHPver ) {
        
        if ( version_compare( phpversion(), $strPHPver, ">=" ) ) return true;
            
    }
    private function isSufficientWordPressVersion( $strWPver ) {
        
        global $wp_version;
        if ( version_compare( $wp_version, $strWPver, ">=" ) ) return true;
        
    }
    private function checkClasses( $arrClasses ) {
        
        $arrClasses = $arrClasses ? $arrClasses : $this->arrParams['classes'];
        $arrNonExistentClasses = array();
        foreach( $arrClasses as $strClass => $strError ) 
            if ( ! class_exists( $strClass ) )
                $arrNonExistentClasses[] = sprintf( $strError, $strClass );
        return $arrNonExistentClasses;
        
    }
    private function checkFunctions( $arrFuncs ) {
        
        // returns non-existent functions as array.
        $arrFuncs = $arrFuncs ? $arrFuncs : $this->arrParams['functions'];
        $arrNonExistentFuncs = array();
        foreach( $arrFuncs as $strFunc => $strError ) 
            if ( ! function_exists( $strFunc ) ) 
                $arrNonExistentFuncs[] = sprintf( $strError, $strFunc );
                
        return $arrNonExistentFuncs;
        
    }    
    private function checkConstants( $arrConstants ) {
        
        // returns non-existent constants as array.
        $arrConstants = $arrConstants ? $arrConstants : $this->arrParams['constants'];
        $arrNonExistentConstants = array();
        foreach( $arrConstants as $strConstant => $strError ) 
            if ( ! defined( $strConstant ) ) 
                $arrNonExistentConstants[] = sprintf( $strError, $strConstant );
        return $arrNonExistentConstants;
        
    }    
    
}