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
            $_aAllowedHTMLTags = $_oUtil->getStringIntoArray(
                str_replace(
                    PHP_EOL,            // search
                    ',',                // replace
                    $_oOption->get(     // subject
                        'form_options',     // first dimensional key
                        'allowed_html_tags' // second dimensional key
                    )
                ), 
                ',' 
            );
            $_aAllowedAttributes = $_oUtil->getStringIntoArray(
                str_replace(
                    PHP_EOL,            // search
                    ',',                // replace
                    $_oOption->get(     // subject
                        'form_options',     // first dimensional key
                        'allowed_attributes' // second dimensional key
                    )
                ), 
                ',' 
            );
            
            $aInputs[ 'item_format' ]  = $_oUtil->escapeKSESFilter( $aInputs[ 'item_format' ], $_aAllowedHTMLTags, array(), array(), $_aAllowedAttributes );
            $aInputs[ 'image_format' ] = $_oUtil->escapeKSESFilter( $aInputs[ 'image_format' ], $_aAllowedHTMLTags, array(), array(), $_aAllowedAttributes );
            $aInputs[ 'title_format' ] = $_oUtil->escapeKSESFilter( $aInputs[ 'title_format' ], $_aAllowedHTMLTags, array(), array(), $_aAllowedAttributes );
            remove_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
            
            return $aInputs;
            
        }    
            /**
             * @return      array
             */
            public function replyToAddAllowedInlineCSSProperties( $aProperty ) {
                
                $_oOption = AmazonAutoLinks_Option::getInstance();    
                $_oUtil   = new AmazonAutoLinks_WPUtility;
                $_aAllowedCSSProperties = $_oUtil->getStringIntoArray(
                    str_replace(
                        PHP_EOL,            // search
                        ',',                // replace
                        $_oOption->get(     // subject
                            'form_options',     // first dimensional key
                            'allowed_inline_css_properties' // second dimensional key
                        )
                    ),
                    ',' // delimiter
                ) + array(
                    'max-width', 'min-width', 'max-height', 'min-height'
                );

                $_aResult = array_unique( 
                    array_merge( 
                        $aProperty, 
                        $_aAllowedCSSProperties 
                    ) 
                );

                return $_aResult;                  
                
            }        
      
}