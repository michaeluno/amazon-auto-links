<?php
/**
    
    Handles templates that display fetched data.
    
    @package     Amazon Auto Links
    @copyright   Copyright (c) 2013, Michael Uno
    @authorurl    http://michaeluno.jp
    @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
    @since        2.0.0
    @filters 
    - aal_filter_template_container_directories    : applies to the loading template container directories
    - aal_filter_template_directories                : applies to the loading template directories
*/

abstract class AmazonAutoLinks_Templates_ {

    // public $arrTemplateDirs = array();    // stores the template directory paths where the plugin loads templates.
    // public $arrTemplates = array(); // stores each template information.
    
    public static $arrStructure_Template = array(
        'strCSSPath'        => null,
        'strTemplatePath'    => null,
        'strDirPath'        => null,
        'strFunctionsPath'    => null,
        'strSettingsPath'    => null,
        'strThumbnailPath'    => null,
        'strName'            => null,
        'strID'                => null,
        'strDescription'    => null,
        'strTextDomain'        => null,
        'strDomainPath'        => null,
        'strVersion'        => null,
        'strAuthor'            => null,
        'strAuthorURI'        => null,
        'fIsActive'            => null,
    );
    
    /**
     * Returns the default template.
     * 
     * @remark            The category template is the default one.
     */
    public function getPluginDefaultTemplate( $strDirBaseName='category', $fEncloseInArray=false ) {
    
        $arrTemplate = $this->getTemplateArray( 
            AmazonAutoLinks_Commons::$strPluginDirPath 
            . DIRECTORY_SEPARATOR . 'template'
            . DIRECTORY_SEPARATOR . $strDirBaseName
        );
        return $fEncloseInArray 
            ? array( $arrTemplate['strID'] => $arrTemplate ) 
            : $arrTemplate;
    
    }
        
    /**
     * Returns the plugin's default template's label.
     * 
     * 
     */
    public function getPluginDefaultTemplateID( $strType='category' ) {
        
        switch ( $strType ) {
            case 'similarity_lookup':
            case 'item_lookup':
            case 'search':
                $strDirBaseName = 'search';
                break;
            case 'tag':
            case 'category':
            default:
                $strDirBaseName = 'category';
                break;
        }        
        
        $arrTemplate = $this->getPluginDefaultTemplate( $strDirBaseName, false );
        return $arrTemplate['strID'];        
        
    }
    
    /**
     * Returns the labels used for select type input fields.
     * 
     * @remark            Used with the template meta box's options.
     */
    public function getTemplateArrayForSelectLabel( $arrTemplates=null ) {
        
        if ( ! $arrTemplates ) {
            $arrTemplates = $this->getActiveTemplates();
        }
            
        $arrLabels = array();
        foreach ( $arrTemplates as $strID => $arrTemplate ) 
            if ( isset( $arrTemplate['strName'] ) ) 
                $arrLabels[ $strID ] = $arrTemplate['strName'];
                
        return $arrLabels;        
        
    }
    
    /**
     * Retrieves the label(name) of the template by template id
     * 
     * @remark            Used when rendering the post type table of units.
     */
    public function getTemplateNameByID( $strTemplateID ) {
    
        $arrTemplates = $this->getActiveTemplates();
        
        if ( isset( $arrTemplates[ $strTemplateID ]['strName'] ) )
            return $arrTemplates[ $strTemplateID ]['strName'];
        
        return '';
    
    }
    
    /**
     * Formats the paths for v3 while the user does not upgrade the options.
     * 
     * @since       3.0.2
     * @return      array
     */
    public function getActiveTemplates( $bUseProperty=true ) {
        $_aActiveTempaltes = $this->_getActiveTemplates( $bUseProperty ); 

        foreach( $_aActiveTempaltes as &$_aTemplate ) {
            if ( ! in_array( $_aTemplate[ 'strName' ], array( 'Category', 'Search' ) ) ) {
                continue;
            }
            $_aTemplate[ 'strCSSPath' ]       = $this->_getV3Path( $_aTemplate[ 'strCSSPath' ] );
            $_aTemplate[ 'strTemplatePath' ]  = $this->_getV3Path( $_aTemplate[ 'strTemplatePath' ] );
            $_aTemplate[ 'strDirPath' ]       = $this->_getV3Path( $_aTemplate[ 'strDirPath' ] );
            $_aTemplate[ 'strFunctionsPath' ] = $this->_getV3Path( $_aTemplate[ 'strFunctionsPath' ] );
            $_aTemplate[ 'strSettingsPath' ]  = $this->_getV3Path( $_aTemplate[ 'strSettingsPath' ] );
            $_aTemplate[ 'strThumbnailPath' ] = $this->_getV3Path( $_aTemplate[ 'strThumbnailPath' ] );
        }
        return $_aActiveTempaltes;
        
    }
        /**
         * @return      string
         * @since       3.0.2
         */
        private function _getV3Path( $sV2Path ) {
            return preg_replace(
                '#(\\\\|/)template(\\\\|/)#', // needle pattern - forward/back slash 
                '$1include$1v2$1template$1', // replacement
                $sV2Path // subject haystack
            );
        }
    
    /**
     * Returns an array that holds arrays of activated template information stored in the option array.
     * 
     * @param       boolean            $fUseProperty            If this is true, it checks the previously set data and if there are, use them.
     * @return      array
     */
    public function _getActiveTemplates( $fUseProperty=true ) {

        $_oOption = $GLOBALS[ 'oAmazonAutoLinks_Option' ];
    
        if ( $fUseProperty && isset( $_oOption->arrOptions['arrTemplates'] ) && ! empty( $_oOption->arrOptions['arrTemplates'] ) ) {
            return $_oOption->arrOptions['arrTemplates'];
        }
            
        // The saved active templates.
        $_aActiveTempletes = $_oOption->arrOptions['arrTemplates'];
                        
        // Check if they exist - moving the site may cause an issue that files don't exist anymore.
        foreach( $_aActiveTempletes as $_sDirSlug => &$_aActiveTemplete ) {
        
            if ( ! is_array( $_aActiveTemplete ) || $_sDirSlug == '' ) {
                unset( $_aActiveTempletes[ $_sDirSlug ] );
                continue;
            }
            
            $_aActiveTemplete = $_aActiveTemplete + self::$arrStructure_Template;    
            
            // Check mandatory files. Consider that the user may directly delete the template files/folders.
            if ( ! $this->doFilesExist( array( $_aActiveTemplete['strCSSPath'], $_aActiveTemplete['strTemplatePath'], ) ) ) {
                unset( $_aActiveTempletes[ $_sDirSlug ] );
                continue;
            }
                        
        }
        
        // Store the data in a property so that it can be reused.
        $_oOption->arrOptions['arrTemplates'] = empty( $_aActiveTempletes ) 
            ? $this->getPluginDefaultTemplate( 'category', true ) + $this->getPluginDefaultTemplate( 'search', true )
            : $_aActiveTempletes;
        
        foreach( $_oOption->arrOptions['arrTemplates'] as $_sID => &$_aTemplate ) {
            $_aTemplate['fIsActive'] = true;
        }
        
        // If there are no active templates, the default one was just added. So save it.
        if ( empty( $_aActiveTempletes ) ) {
            $_oOption->save();
        }
            
        return $_oOption->arrOptions['arrTemplates'];
        
    }
    
    /**
     * Checks multiple file existence.
     * 
     */
    private function doFilesExist( $arrFilePaths ) {
        
        foreach( $arrFilePaths as $strFilePath )
            if ( empty( $strFilePath ) || ! file_exists( $strFilePath ) )
                return false;
                
        return true;
        
    }
        
    /**
     * Returns uploaded templates and returns them as array.
     */
    public function getUploadedTemplates() {
    
        // Set up the template array.
        $arrTemplateContainerDirs = array();
        $arrTemplateContainerDirs[] = AmazonAutoLinks_Commons::$strPluginDirPath . DIRECTORY_SEPARATOR . 'template';
        $arrTemplateContainerDirs[] = get_template_directory() . DIRECTORY_SEPARATOR . 'amazon-auto-links';
        $arrTemplateContainerDirs = apply_filters( 'aal_filter_template_container_directories', $arrTemplateContainerDirs );
        $arrTemplateContainerDirs = array_unique( $arrTemplateContainerDirs );

        // Load templates in the given container directories.
        $arrTemplateDirs = array();
        foreach( ( array ) $arrTemplateContainerDirs as $strTemplateDirPath ) 
            if ( file_exists( $strTemplateDirPath  ) ) 
                $arrTemplateDirs = array_merge( glob( $strTemplateDirPath . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR ), $arrTemplateDirs );
        $arrTemplateDirs = array_unique( $arrTemplateDirs );
        $arrTemplateDirs = apply_filters( 'aal_filter_template_directories', $arrTemplateDirs );
        $arrTemplateDirs = array_unique( $arrTemplateDirs );
        
        // Format the template array now that all the uploaded template directories are retrieved.
        $arrTemplates = array();    
        foreach ( $arrTemplateDirs as $strDirPath ) 
            if ( $arrTemplateArray = $this->getTemplateArray( $strDirPath ) ) 
                $arrTemplates[  md5( AmazonAutoLinks_Utilities::getRelativePath( ABSPATH, $strDirPath ) ) ] = $arrTemplateArray;
                    
        return $arrTemplates;
        
    }

    /**
     * Generates a template array by the given template directory path.
     * 
     * @remark            The directory must have necessary template files including style.css and tmplate.php
     * @access            public            This is access by the pro classes.
     */
    public function getTemplateArray( $strDirPath ) {
        
        // Check mandatory files.
        if ( 
            ! $this->doFilesExist(
                array( 
                    $strDirPath . DIRECTORY_SEPARATOR . 'style.css', 
                    $strDirPath . DIRECTORY_SEPARATOR . 'template.php' 
                )
            ) 
        )    
            return;
        
        return array(
                'strCSSPath' => $strDirPath . DIRECTORY_SEPARATOR. 'style.css',    // mandatory
                'strTemplatePath' => $strDirPath . DIRECTORY_SEPARATOR . 'template.php',    // mandatory
                'strDirPath' => $strDirPath,
                'strFunctionsPath' => file_exists( $strDirPath . DIRECTORY_SEPARATOR . 'functions.php' ) ? $strDirPath . DIRECTORY_SEPARATOR . 'functions.php' : null,    // optional.
                'strSettingsPath' => file_exists( $strDirPath . DIRECTORY_SEPARATOR . 'settings.php' ) ? $strDirPath . DIRECTORY_SEPARATOR . 'settings.php' : null,        // optional.
                'strThumbnailPath' => $this->getScreenshotPath( $strDirPath ),    // note that it's not a url.
                'strID' => md5( AmazonAutoLinks_Utilities::getRelativePath( ABSPATH, $strDirPath ) ),
            ) 
            + $this->getTemplateData( $strDirPath . DIRECTORY_SEPARATOR . 'style.css' ) 
            + self::$arrStructure_Template;
        
    }
        protected function getScreenshotPath( $strDirPath ) {
            foreach( array( 'jpg', 'jpeg', 'png', 'gif' ) as $strExt ) 
                if ( file_exists( $strDirPath . DIRECTORY_SEPARATOR . 'screenshot.' . $strExt ) )
                    return $strDirPath . DIRECTORY_SEPARATOR . 'screenshot.' . $strExt;
        }    
    
    /*
     * Event methods 
     * */
    /**
     * Outputs the stylesheet of the given template ID and then exits.
     * 
     * @remark            This is called from the event class. 
     */
    public function loadStyle( $strTemplateID ) {
                        
        $arrTemplate = isset( $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['arrTemplates'][ trim( $strTemplateID ) ] )
            ? $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['arrTemplates'][ trim( $strTemplateID ) ]
            : $this->getPluginDefaultTemplate( 'category', false );
            
        if ( ! file_exists( $arrTemplate['strCSSPath'] ) )
            die( __( '/* The CSS file does not exist. */', 'amazon-auto-links' ) );    // the file must exist.
        
        header( "Content-Type: text/css" ); 
        header( "X-Content-Type-Options: nosniff" );    // for IE 8 or greater.
        die( file_get_contents( $arrTemplate['strCSSPath'] ) );        // echo the contents and exit.        
        
    }
    
    /**
     * Includes activated templates' functions.php files.
     * 
     * @remark            This is called from the initial loader class.
     * 
     */     
    public function loadFunctionsOfActiveTemplates() {
        
        foreach( $this->getActiveTemplates() as $arrTemplate ) {
            
            if ( ! isset( $arrTemplate['strFunctionsPath'], $arrTemplate['strTemplatePath'], $arrTemplate['strCSSPath'] ) ) continue;
            if ( ! $arrTemplate['strCSSPath']  ) continue;

            $strFunctionsPath = file_exists( $arrTemplate['strFunctionsPath'] )
                ? $arrTemplate['strFunctionsPath']
                : ( file_exists( dirname( $arrTemplate['strCSSPath'] ) . DIRECTORY_SEPARATOR . 'functions.php' )
                    ? dirname( $arrTemplate['strCSSPath'] ) . DIRECTORY_SEPARATOR . 'functions.php'
                    : null
                );
            if ( $strFunctionsPath )
                include_once( $strFunctionsPath );
                        
        }
        
    }
    
    /**
     * Includes activated templates' settings.php files.
     * 
     * @remark            This is called from the initial loader class.
     * 
     */ 
    public function loadSettingsOfActiveTemplates() {
        
        if ( ! is_admin() ) {
            return;
        }
        
        foreach( $this->getActiveTemplates() as $arrTemplate ) {

            if ( 
                ! $this->doFilesExist(
                    array( 
                        $arrTemplate['strCSSPath'],
                        $arrTemplate['strTemplatePath'],
                    )
                ) 
            )    continue;

            
            $strSettingsPath = $arrTemplate['strSettingsPath'] 
                ? $arrTemplate['strSettingsPath']
                : ( file_exists( dirname( $arrTemplate['strCSSPath'] ) . '/settings.php' )
                    ? dirname( $arrTemplate['strCSSPath'] ) . '/settings.php'
                    : null
                );
            if ( $strSettingsPath ) {
                include_once( $strSettingsPath );
            }
                        
        }
    }
    
    /**
     * Enqueues activated templates' CSS file.
     * 
     */
    public function enqueueActiveTemplateStyles() {
        
        // This must be called after the option object has been established.
        foreach( $this->getActiveTemplates() as $arrTemplate ) {
            
            if ( 
                ! $this->doFilesExist(
                    array( 
                        $arrTemplate['strCSSPath'],
                        $arrTemplate['strTemplatePath'],
                    )
                ) 
            ) {
                continue;
            }
            
            wp_register_style( "amazon-auto-links-{$arrTemplate['strID']}", AmazonAutoLinks_WPUtilities::getSRCFromPath( $arrTemplate['strCSSPath'] ) );
            // wp_register_style( "amazon-auto-links-{$arrTemplate['strID']}", site_url() . "?amazon_auto_links_style={$arrTemplate['strID']}" );
            wp_enqueue_style( "amazon-auto-links-{$arrTemplate['strID']}" );        
            
        }
        
    }
    
    
    /**
     * A helper function for the getUploadedTemplates() method.
     * 
     * This is used when rendering the template listing table.
     * */
    protected function getTemplateData( $strPath, $strType='theme' )    {
    
        // Returns an array of template detail information from the given file path.    
        // An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
        $arrData = get_file_data( 
            $strPath, 
            array(
                'strName' => 'Template Name',
                'strTemplateURI' => 'Template URI',
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
            $strType    // 'plugin' or 'theme'
        );                
        return $arrData;
        
    }        

}