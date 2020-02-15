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
        return $this->___getItemFormatsSanitized( $this->aInputs, $this->aOldInputs );
    }

        /**
         * @param array $aInputs
         * @param array $aOldInputs
         *
         * @return      array
         * @since       3.2.4
         */
        private function ___getItemFormatsSanitized( $aInputs, $aOldInputs ) {
            
            $_oOption = AmazonAutoLinks_Option::getInstance();    

            add_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
            $_aAllowedHTMLTags = $this->getStringIntoArray(
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
            $_aAllowedAttributes = $this->getStringIntoArray(
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

            // For backward compatibility for v3 or below
            $aInputs = $this->___getInputEscapedForKSES( $aInputs, $_aAllowedHTMLTags, $_aAllowedAttributes );

            // The option added since v4.0.0 in place of `item_format`.
            $aInputs[ 'output_formats' ] = $this->___getInputEscapedForKSES(
                $this->getElementAsArray( $aInputs, array( 'output_formats' ) ),
                $_aAllowedHTMLTags,
                $_aAllowedAttributes
            );

            remove_filter( 'safe_style_css', array( $this, 'replyToAddAllowedInlineCSSProperties' ) );
            return $aInputs;
            
        }

            /**
             * @param array $aOutputFormat
             * @param array $aAllowedHTMLTags
             * @param array $aAllowedAttributes
             *
             * @return array    Sanitized array with escaped form values.
             */
            private function ___getInputEscapedForKSES( array $aOutputFormat, array $aAllowedHTMLTags, array $aAllowedAttributes ) {
                $_sKeysToCheck = array( 'item_format', 'image_format', 'title_format' );
                foreach( $_sKeysToCheck as $_sLegacyKey ) {
                    if ( ! isset( $aInputs[ $_sLegacyKey ] ) ) {
                        continue;
                    }
                    $aOutputFormat[ $_sLegacyKey ] = $this->escapeKSESFilter(
                        $aOutputFormat[ $_sLegacyKey ],
                        $aAllowedHTMLTags,
                        array(),
                        array(),
                        $aAllowedAttributes
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
                            'form_options',     // first dimensional key
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