<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */

/**
 * Provides methods for template options.
 * 
 * @since       3
 */
class AmazonAutoLinks_TemplateOption extends AmazonAutoLinks_Option_Base {
  
    /**
     * Caches the active templates.
     * 
     * @since       3    
     */
    private static $_aActiveTemplates = array();
    
    /**
     * Represents the structure of the template option array.
     * @since       3
     */
    public static $aStructure_Template = array(

        'relative_dir_path' => null,  // (string)
        'id'                => null,  // (string)
        'old_id'            => null,  // (string) v2 id (strID)
        'is_active'         => null,  // (boolean)
        'index'             => null,  // (integer)
        'name'              => null,  // (string)   will be used to list templates in options.
        
        // assigned at the load time
        'template_path'     => null,  // (string) template.php
        'dir_path'          => null,  // (string)        
        
        // for listing table
        'description'       => null,
        'version'           => null,
        'author'            => null,
        'author_uri'        => null,
                    
    );
    
    /**
     * Represents the v2 template option structure.
     * 
     */
    static public $aStructure_Template_Legacy = array(
        'strCSSPath'        => null,
        'strTemplatePath'   => null,
        'strDirPath'        => null,
        'strFunctionsPath'  => null,
        'strSettingsPath'   => null,
        'strThumbnailPath'  => null,
        'strName'           => null,
        'strID'             => null,
        'strDescription'    => null,
        'strTextDomain'     => null,
        'strDomainPath'     => null,
        'strVersion'        => null,
        'strAuthor'         => null,
        'strAuthorURI'      => null,
        'fIsActive'         => null,    
    );
    
    /**
     * Stores the self instance.
     */
    static public $oSelf;
    
    /**
     * Returns an instance of the self.
     * 
     * @remark      To support PHP 5.2, this method needs to be defined in each extended class 
     * as in static methods, it is not possible to retrieve the extended class name in a base class in PHP 5.2.x.
     * @return      object
     */
    static public function getInstance( $sOptionKey='' ) {
        
        if ( isset( self::$oSelf ) ) {
            return self::$oSelf;
        }
        $sOptionKey = $sOptionKey 
            ? $sOptionKey
            : AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ];        
        
        $_sClassName = __Class__;
        self::$oSelf = new $_sClassName( $sOptionKey );            
        return self::$oSelf;
        
    }
    
    /**
     * Returns the formatted options array.
     * @return  array
     */    
    protected function _getFormattedOptions( $sOptionKey ) {

        $_aOptions = parent::_getFormattedOptions( $sOptionKey );
        return $_aOptions + $this->_getDefaultTemplates();
        
    }    
        /**
         * @return      array       plugin default templates which should be activated upon installation / restoring factory default.
         */
        private function _getDefaultTemplates() {
            
            $_aDirPaths = array(
                AmazonAutoLinks_Registry::$sDirPath . '/template/category',
                AmazonAutoLinks_Registry::$sDirPath . '/template/search'
            );
            $_iIndex     = 0;
            $_aTemplates = array();
            foreach( $_aDirPaths as $_sDirPath ) {
                $_aTemplate = $this->getTemplateArrayByDirPath( $_sDirPath );
                if ( ! $_aTemplate ) {
                    continue;
                }
                $_aTemplate[ 'is_active' ] = true;
                $_aTemplate[ 'index' ] = ++$_iIndex;
                $_aTemplates[ $_aTemplate[ 'id' ] ] = $_aTemplate;
            }
            return $_aTemplates;
         
        }
    
    /**
     * Returns an array that holds arrays of activated template information.
     * 
     * @since       unknown
     * @since       3           moved from the templates class.
     * @scope       public      It is accessed from the template loader class.
     */
    public function getActiveTemplates() {
        
        if ( ! empty( self::$_aActiveTemplates ) ) {
            return self::$_aActiveTemplates;
        }
                
        $_aActiveTemplates = $this->_getActiveTemplatesExtracted( 
            $this->get()    // saved all templates
        );

        // Cache
        self::$_aActiveTemplates = $_aActiveTemplates;
        return $_aActiveTemplates;
        
    }
        /**
         * @since       3.3.0
         * @return      array
         */
        private function _getActiveTemplatesExtracted( array $aTemplates ) {
            
            // $_aActive = array();
            foreach( $aTemplates as $_sID => $_aTemplate ) {
                
                $_aTemplate = $this->_formatTemplateArray( $_aTemplate );

                // Remove inactive templates.
                if ( ! $this->getElement( $_aTemplate, 'is_active' ) ) {
                    unset( $aTemplates[ $_sID ] );
                    continue;
                }
                
                // Backward compatibility for the v2 options structure.
                // If the id is not a relative dir path,
                if ( 
                    $_sID !== $_aTemplate[ 'relative_dir_path' ] 
                ) {
                    
                    // Remove the old item.
                    unset( $aTemplates[ $_sID ] );
                    
                    // If the same ID already exists, set the old id.
                    if ( isset( $aTemplates[ $_aTemplate[ 'relative_dir_path' ] ] ) ) {
                        $aTemplates[ $_aTemplate[ 'relative_dir_path' ] ][ 'old_id' ] = $_sID;
                    } else {                    
                        $aTemplates[ $_aTemplate[ 'relative_dir_path' ] ] = $_aTemplate;
                    }
                    
                }
                
                $aTemplates[ $_aTemplate[ 'relative_dir_path' ] ][ 'is_active' ] = true;
                
            }
            return $aTemplates;
            
        }
       
        /**
         * Formats the template array.
         * 
         * Takes care of formatting change through version updates.
         * 
         * @since       3              
         * @return      array|boolean       Formatted template array. If the passed value is not an array 
         * or something wrong with the template array, false will be returned.
         */
        protected function _formatTemplateArray( $aTemplate ) {
         
            if ( ! is_array( $aTemplate ) ) { 
                return false; 
            }
            
            $aTemplate = $aTemplate + self::$aStructure_Template;
            $aTemplate = $this->_formatTemplateArrayLegacy( $aTemplate );
                       
            // Format elements
            $aTemplate[ 'relative_dir_path' ] = $this->getElement(
                $aTemplate,
                'relative_dir_path',
                str_replace( '\\', '/', untrailingslashit( $this->getRelativePath( ABSPATH, $aTemplate[ 'strDirPath' ] ) ) )
            );                        
            
            
            // Set the directory path every time the page loads. Do not store in the data base. 
            // This path is absolute so when the user moves the site, the value will be different.
            $aTemplate[ 'dir_path' ]           = $this->getElement(
                $aTemplate,
                'dir_path',
                $this->getAbsolutePathFromRelative( $aTemplate[ 'relative_dir_path' ] )
            );
            
            // Check required files. Consider the possibility that the user may directly delete the template files/folders.
            $_aRequiredFiles = array(
                $aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'style.css',
                $aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'template.php',             
            );
            if ( ! $this->doFilesExist( $_aRequiredFiles ) ) {
                return false;
            }                   
            
            // Other elements
            $aTemplate['template_path']      = $this->getElement(
                $aTemplate,
                'template_path',
                $aTemplate[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'template.php'
            );                        

            $aTemplate[ 'id' ]                = $this->getElement(
                $aTemplate,
                'id',
                $aTemplate[ 'relative_dir_path' ]
            );     
            $aTemplate[ 'old_id' ]            = $this->getElement(
                $aTemplate,
                'old_id',
                $aTemplate[ 'strID' ]
            );     
            
            // For uploaded templates
            $aTemplate[ 'name' ]              = $this->getElement(
                $aTemplate,
                'name',
                $aTemplate[ 'strName' ]
            );     
            $aTemplate[ 'description' ]       = $this->getElement(
                $aTemplate,
                'description',
                $aTemplate[ 'strDescription' ]
            );     
            $aTemplate[ 'version' ]            = $this->getElement(
                $aTemplate,
                'version',
                $aTemplate[ 'strVersion' ]
            );     
            $aTemplate[ 'author' ]             = $this->getElement(
                $aTemplate,
                'author',
                $aTemplate[ 'strAuthor' ]
            );     
            $aTemplate[ 'author_uri' ]         = $this->getElement(
                $aTemplate,
                'author_uri',
                $aTemplate[ 'strAuthorURI' ]
            );     
            $aTemplate[ 'is_active' ]          = $this->getElement(
                $aTemplate,
                'is_active',
                $aTemplate[ 'fIsActive' ]
            );     
              
            return $aTemplate;
            
        }    
            /**
             * Make the passed template array compatible with the format of v2.x or below.
             *
             * @return			array|false			The formatted template array or false if the necessary file paths do not exist.
             */
            private function _formatTemplateArrayLegacy( array $aTemplate ) {
                                
                $aTemplate = $aTemplate + self::$aStructure_Template_Legacy;                
                $aTemplate['strDirPath'] = $aTemplate['strDirPath']	// check if it's not missing
                    ? $aTemplate['strDirPath']
                    : dirname( $aTemplate['strCSSPath'] );
                            
                $aTemplate['strTemplatePath'] = $aTemplate['strTemplatePath']	// check if it's not missing
                    ? $aTemplate['strTemplatePath']
                    : dirname( $aTemplate['strCSSPath'] ) . DIRECTORY_SEPARATOR . 'template.php';
                            
                return $aTemplate;
                
            }     
 
    /**
     * Retrieves the label(name) of the template by template id
     * 
     * @remark            Used when rendering the post type table of units.
     */ 
    public function getTemplateNameByID( $sTemplateID ) {
        return $this->get(
            array( $sTemplateID, 'name' ), // dimensional keys
            '' // default
        );    
    }
 
 
    /**
     * Returns an array holding active template labels.
     * @since       3
     */
    public function getActiveTemplateLabels() {        
        $_aLabels = array();
        foreach( $this->getActiveTemplates() as $_aTemplate ) {
            $_aLabels[ $_aTemplate[ 'id' ] ] = $_aTemplate[ 'name' ];
        }
        return $_aLabels;
    }
    /**
     * 
     * @since       3
     * @return      string
     */
    public function getDefaultTemplateIDByUnitType( $sUnitType ) {
        switch ( $sUnitType ) {
            case 'email':               // 3.5.0+
            case 'contextual':          // 3.5.0+
            case 'contextual_widget':   // 3.2.1+
            case 'similarity_lookup':
            case 'item_lookup':
            case 'search':
            case 'url':                 // 3.2.0+
                $_sTemplateDirectoryName = 'search';
                break;
            case 'tag':
            case 'category':
            default:
                $_sTemplateDirectoryName = 'category';
                break;                
        }
        $_aTemplate = $this->getTemplateArrayByDirPath(
            AmazonAutoLinks_Registry::$sDirPath 
            . DIRECTORY_SEPARATOR . 'template'
            . DIRECTORY_SEPARATOR . $_sTemplateDirectoryName,
            false       // no extra info
        );
        return $_aTemplate[ 'id' ];
    }

    /**
     * Caches the uploaded templates.
     * 
     * @since       3    
     */
    private static $_aUploadedTemplates = array();
 
    /**
     * Retrieve templates and returns the template information as array.
     * 
     * This method is called for the template listing table to list available templates. So this method generates the template information dynamically.
     * This method does not deal with saved options.
     * 
     * @return      array
     */
    public function getUploadedTemplates() {
            
        if ( ! empty( self::$_aUploadedTemplates ) ) {
            return self::$_aUploadedTemplates;
        }
            
        // Construct a template array.
        $_aTemplates = array();
        $_iIndex     = 0;        
        foreach( $this->_getTemplateDirs() as $_sDirPath ) {
            
            $_aTemplate = $this->getTemplateArrayByDirPath( $_sDirPath );
            if ( ! $_aTemplate ) {
                continue;
            }
            
            // Uploaded templates are supposed to be only called in the admin template listing page.
            // So by default, these are not active.
            $_aTemplate[ 'is_active' ] = false;
            
            $_aTemplate[ 'index' ] = ++$_iIndex;
            $_aTemplates[ $_aTemplate[ 'id' ] ] = $_aTemplate;
            
        }
        
        self::$_aUploadedTemplates = $_aTemplates;
        return $_aTemplates;
        
    }
        /**
         * Returns the template array by the given directory path.
         * @since       3
         * @scope       public       The unit class also accesses this.
         */
        public function getTemplateArrayByDirPath( $sDirPath, $bExtraInfo=true ) {
                        
            $_sRelativePath = str_replace( '\\', '/', untrailingslashit( $this->getRelativePath( ABSPATH, $sDirPath ) ) );
            $_aData         = array(
                'dir_path'              => $sDirPath,
                'relative_dir_path'     => $_sRelativePath,
                'id'                    => $_sRelativePath,
                'old_id'                => md5( $sDirPath ),            
                    
                // Backward compatibility
                'strDirPath'            => $sDirPath,
                'strID'                 => md5( $sDirPath ),
            );
            
            if ( $bExtraInfo ) {               
                $_aData[ 'thumbnail_path' ] = $this->_getScreenshotPath( $_aData[ 'dir_path' ] );
                return $this->_formatTemplateArray( 
                    $this->getTemplateData( $_aData[ 'dir_path' ] . DIRECTORY_SEPARATOR . 'style.css' )
                    + $_aData 
                );
            }
            return $this->_formatTemplateArray( $_aData );
        }        
            /**
             * @return  string|null
             */
            protected function _getScreenshotPath( $sDirPath ) {
                foreach( array( 'jpg', 'jpeg', 'png', 'gif' ) as $sExt ) {
                    if ( file_exists( $sDirPath . DIRECTORY_SEPARATOR . 'screenshot.' . $sExt ) ) { 
                        return $sDirPath . DIRECTORY_SEPARATOR . 'screenshot.' . $sExt;
                    }
                }
                return null;
            }           
    
        /**
         * Stores the read template directory paths.
         * @since       3    
         */
        static private $_aTemplateDirs = array();
        
        /**
         * Returns an array holding the template directories.
         * 
         * @since       3
         * @return      array       Contains list of template directory paths.
         */
        private function _getTemplateDirs() {
                
            if ( ! empty( self::$_aTemplateDirs ) ) {
                return self::$_aTemplateDirs;
            }
            foreach( $this->_getTemplateContainerDirs() as $__sTemplateDirPath ) {
                    
                if ( ! @file_exists( $__sTemplateDirPath  ) ) { 
                    continue; 
                }
                $__aFoundDirs = glob( $__sTemplateDirPath . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR );
                if ( is_array( $__aFoundDirs ) ) {    // glob can return false
                    self::$_aTemplateDirs = array_merge( 
                        $__aFoundDirs, 
                        self::$_aTemplateDirs 
                    );
                }
                                
            }
            self::$_aTemplateDirs = array_unique( self::$_aTemplateDirs );
            self::$_aTemplateDirs = ( array ) apply_filters( 'aal_filter_template_directories', self::$_aTemplateDirs );
            self::$_aTemplateDirs = array_filter( self::$_aTemplateDirs );    // drops elements of empty values.
            self::$_aTemplateDirs = array_unique( self::$_aTemplateDirs );
            return self::$_aTemplateDirs;
        
        }    
            /**
             * Returns the template container directories.
             * @since       3
             */
            private function _getTemplateContainerDirs() {
                
                $_aTemplateContainerDirs    = array();
                $_aTemplateContainerDirs[]  = AmazonAutoLinks_Registry::$sDirPath . DIRECTORY_SEPARATOR . 'template';
                $_aTemplateContainerDirs[]  = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'amazon-auto-links';
                $_aTemplateContainerDirs    = apply_filters( 'aal_filter_template_container_directories', $_aTemplateContainerDirs );
                $_aTemplateContainerDirs    = array_filter( $_aTemplateContainerDirs );    // drop elements of empty values.
                return array_unique( $_aTemplateContainerDirs );
                
            }       
    
 
    /**
     * A helper function for the getUploadedTemplates() method.
     * 
     * Used when rendering the template listing table.
     * An alternative to get_plugin_data() as some users change the location of the wp-admin directory.
     * 
     * @return      array       an array of template detail information from the given file path.
     * */
    protected function getTemplateData( $sCSSPath )    {

        return file_exists( $sCSSPath )
            ? get_file_data( 
                $sCSSPath, 
                array(
                    'name'           => 'Template Name',
                    'template_uri'   => 'Template URI',
                    'version'        => 'Version',
                    'description'    => 'Description',
                    'author'         => 'Author',
                    'author_uri'     => 'Author URI',
                ),
                '' // context - do not set any
            )
            : array();
        
    }                     
        
}