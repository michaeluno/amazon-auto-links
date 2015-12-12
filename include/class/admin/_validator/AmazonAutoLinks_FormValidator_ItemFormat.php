<?php
/**
 * Provides methods to validate form field values.
 * 
 * @since           3.2.4
 */
class AmazonAutoLinks_FormValidator_ItemFormat extends AmazonAutoLinks_PluginUtility {

    public $aInputs     = array();
    public $aOldInputs  = array();

    /**
     * Sets up properties.
     * 
     */
    public function __construct( $aInputs, $aOldInputs ) {
        $this->aInputs      = $aInputs;
        $this->aOldInputs   = $aOldInputs;
    }

    /**
     * Returns the validated values.
     * 
     * @return      array
     */    
    public function get() {
        
        return $this->_getItemFormatsSanitized( $this->aInputs, $this->aOldInputs );
        
    }
        /**
         * @return      array
         * @since       3.2.4
         */
        private function _getItemFormatsSanitized( $aInputs, $aOldInputs ) {
            
            $_oOption = AmazonAutoLinks_Option::getInstance();    
            $_oUtil   = new AmazonAutoLinks_WPUtility;
            
            add_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
            $_aAllowedHTMLTags = $_oUtil->convertStringToArray(
                $_oOption->get( 
                    'form_options', // first dimensional key
                    'allowed_html_tags' // second dimensional key
                ), 
                ',' 
            );
            
            if ( ! $_oOption->isAdvancedAllowed() ) {
                $_aItemFormat   = AmazonAutoLinks_UnitOption_Base::getDefaultItemFormat();
                $aInputs[ 'item_format' ] = $_oUtil->getElement(
                    $aOldInputs,
                    'item_format',
                    $_aItemFormat[ 'item_format' ]
                );
                $aInputs[ 'image_format' ] = $_oUtil->getElement(
                    $aOldInputs,
                    'image_format',
                    $_aItemFormat[ 'image_format' ]
                );
                $aInputs[ 'title_format' ] = $_oUtil->getElement(
                    $aOldInputs,
                    'title_format',
                    $_aItemFormat[ 'title_format' ]
                );                
            }
            $aInputs[ 'item_format' ]  = $_oUtil->escapeKSESFilter( $aInputs[ 'item_format' ], $_aAllowedHTMLTags );
            $aInputs[ 'image_format' ] = $_oUtil->escapeKSESFilter( $aInputs[ 'image_format' ], $_aAllowedHTMLTags );
            $aInputs[ 'title_format' ] = $_oUtil->escapeKSESFilter( $aInputs[ 'title_format' ], $_aAllowedHTMLTags );
            remove_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
            
            return $aInputs;
            
        }    
            /**
             * @return      array
             */
            public function replyToAddAllowedInlineCSSProperties( $aProperty ) {
                $aProperty[] = 'max-width';
                $aProperty[] = 'min-width';
                $aProperty[] = 'max-height';
                $aProperty[] = 'min-height';
                return $aProperty;
            }        
      
}