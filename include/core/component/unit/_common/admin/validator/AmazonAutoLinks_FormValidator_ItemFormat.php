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
     * @param array $aInputs
     * @param array $aOldInputs
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

        $this->aInputs[ 'custom_text' ] = wpautop( $this->getElement( $this->aInputs, array( 'custom_text' ), '' ) );  // 3.4.0
        return $this->___getOutputFormatsSanitized( $this->aInputs, $this->aOldInputs );

    }

        /**
         * @param array $aInputs
         * @param array $aOldInputs
         *
         * @return      array
         * @since       3.2.4
         */
        private function ___getOutputFormatsSanitized( $aInputs, $aOldInputs ) {
            
            $_oOption = AmazonAutoLinks_Option::getInstance();    

            add_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
            $_aAllowedHTMLTags   = $_oOption->getAllowedHTMLTags();

            // For backward compatibility for v3 or below
            $aInputs = $this->___getInputEscapedForKSES( $aInputs, $_aAllowedHTMLTags );

            // The option added since v4.0.0 in place of `item_format`.
            $aInputs[ 'output_formats' ] = $this->___getInputEscapedForKSES(
                $this->getElementAsArray( $aInputs, array( 'output_formats' ) ),
                $_aAllowedHTMLTags
            );

            remove_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
            return $aInputs;
            
        }

            /**
             * @param  array $aOutputFormat
             * @param  array $aAllowedHTMLTags
             * @return array Sanitized array with escaped form values.
             * @return array
             */
            private function ___getInputEscapedForKSES( array $aOutputFormat, array $aAllowedHTMLTags ) {
                $_sKeysToCheck = array( 'item_format', 'image_format', 'title_format', 'unit_format' );
                foreach( $_sKeysToCheck as $_sLegacyKey ) {
                    if ( ! isset( $aOutputFormat[ $_sLegacyKey ] ) ) {
                        continue;
                    }
                    $aOutputFormat[ $_sLegacyKey ] = $this->getEscapedWithKSES(
                        $aOutputFormat[ $_sLegacyKey ],
                        $aAllowedHTMLTags
                    );
                }
                return $aOutputFormat;
            }
            /**
             * @return      array
             */
            public function replyToAddAllowedInlineCSSProperties( $aProperty ) {
                
                $_oOption = AmazonAutoLinks_Option::getInstance();
                $_aAllowedCSSProperties = $this->getStringIntoArray(
                    str_replace(
                        PHP_EOL,            // search
                        ',',                // replace
                        $_oOption->get(     // subject
                            'security',     // first dimensional key
                            'allowed_inline_css_properties' // second dimensional key
                        )
                    ),
                    ',' // delimiter
                ) + array(
                    'max-width', 'min-width', 'max-height', 'min-height'
                );
                return array_unique( array_merge( $aProperty, $_aAllowedCSSProperties ) );

            }        
      
}