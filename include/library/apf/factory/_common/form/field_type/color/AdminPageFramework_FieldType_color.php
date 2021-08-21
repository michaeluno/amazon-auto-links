<?php 
/**
	Admin Page Framework v3.9.0b07 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_FieldType_color extends AmazonAutoLinks_AdminPageFramework_FieldType {
    public $aFieldTypeSlugs = array('color');
    protected $aDefaultKeys = array('attributes' => array('size' => 10, 'maxlength' => 400, 'value' => 'transparent',),);
    protected function setUp() {
        if (version_compare($GLOBALS['wp_version'], '3.5', '>=')) {
            $this->___enqueueWPColorPicker();
        } else {
            wp_enqueue_style('farbtastic');
            wp_enqueue_script('farbtastic');
        }
    }
    private function ___enqueueWPColorPicker() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        if (!is_admin()) {
            wp_enqueue_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
            wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false, 1);
            wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array('clear' => __('Clear'), 'defaultString' => __('Default'), 'pick' => __('Select Color'), 'current' => __('Current Color'),));
        }
    }
    protected function getStyles() {
        return ".repeatable .colorpicker {display: inline;}.amazon-auto-links-field-color .wp-picker-container {vertical-align: middle;}.amazon-auto-links-field-color .ui-widget-content {border: none;background: none;color: transparent;}.amazon-auto-links-field-color .ui-slider-vertical {width: inherit;height: auto;margin-top: -11px;}.amazon-auto-links-field-color .amazon-auto-links-repeatable-field-buttons {margin-top: 0;}.amazon-auto-links-field-color .wp-color-result {margin: 3px;}";
    }
    protected function getScripts() {
        $_aJSArray = json_encode($this->aFieldTypeSlugs);
        $_sDoubleQuote = '\"';
        return <<<JAVASCRIPTS
registerAmazonAutoLinks_AdminPageFrameworkColorPickerField = function( osTragetInput, aOptions ) {
    
    var osTargetInput   = 'string' === typeof osTragetInput 
        ? '#' + osTragetInput 
        : osTragetInput;
    var sInputID        = 'string' === typeof osTragetInput 
        ? osTragetInput 
        : osTragetInput.attr( 'id' );

    // Only for the iris color picker.
    var _aDefaults = {
        defaultColor: false, // you can declare a default color here, or in the data-default-color attribute on the input     
        change: function( event, ui ){
            jQuery( this ).trigger( 
                'amazon-auto-links_field_type_color_changed',
                [ jQuery( this ), sInputID ]
            ); 
        }, // a callback to fire whenever the color changes to a valid color. reference : http://automattic.github.io/Iris/     
        clear: function( event, ui ) {
            jQuery( this ).trigger(
                'amazon-auto-links_field_type_color_cleared',
                [ jQuery( '#' + sInputID ), sInputID ]
            );            
        }, // a callback to fire when the input is emptied or an invalid color
        hide: true, // hide the color picker controls on load
        palettes: true // show a group of common colors beneath the square or, supply an array of colors to customize further                
    };
    var _aColorPickerOptions = jQuery.extend( {}, _aDefaults, aOptions );
        
    'use strict';
    /* This if-statement checks if the color picker element exists within jQuery UI
     If it does exist, then we initialize the WordPress color picker on our text input field */
    if( 'object' === typeof jQuery.wp && 'function' === typeof jQuery.wp.wpColorPicker ){
        jQuery( osTargetInput ).wpColorPicker( _aColorPickerOptions );
    }
    else {
        /* We use farbtastic if the WordPress color picker widget doesn't exist */
        jQuery( '#color_' + sInputID ).farbtastic( osTargetInput );
    }
}

/* The below function will be triggered when a new repeatable field is added. Since the APF repeater script does not
    renew the color piker element (while it does on the input tag value), the renewal task must be dealt here separately. */
jQuery( document ).ready( function(){
        
    jQuery().registerAmazonAutoLinks_AdminPageFrameworkCallbacks( {     
        /**
         * Called when a field of this field type gets repeated.
         */
        repeated_field: function( oCloned, aModel ) {
                        
            oCloned.find( 'input.input_color' ).each( function( iIterationIndex ) {
                
                var _oNewColorInput = jQuery( this );
                var _oIris          = _oNewColorInput.closest( '.wp-picker-container' );
                // WP 3.5+
                if ( _oIris.length > 0 ) { 
                    // unbind the existing color picker script in case there is.
                    var _oNewColorInput = _oNewColorInput.clone(); 
                }                    
                var _sInputID       = _oNewColorInput.attr( 'id' );
                
                // Reset the value of the color picker.
                var _sInputValue    = _oNewColorInput.val() 
                    ? _oNewColorInput.val() 
                    : _oNewColorInput.attr( 'data-default' );
                var _sInputStyle = _sInputValue !== 'transparent' && _oNewColorInput.attr( 'style' )
                    ? _oNewColorInput.attr( 'style' ) 
                    : '';
                _oNewColorInput.val( _sInputValue ); // set the default value    
                _oNewColorInput.attr( 'style', _sInputStyle ); // remove the background color set to the input field ( for WP 3.4.x or below )  

                // Replace the old color picker elements with the new one.
                // WP 3.5+
                if ( _oIris.length > 0 ) { 
                    jQuery( _oIris ).replaceWith( _oNewColorInput );
                } 
                // WP 3.4.x -     
                else { 
                    oCloned.find( '.colorpicker' )
                        .replaceWith( '<div class=\"colorpicker\" id=\"color_' + _sInputID + '\"></div>' );
                }

                // Bind the color picker event.
                registerAmazonAutoLinks_AdminPageFrameworkColorPickerField( _oNewColorInput );                
            
            } );                   
        },    
    },
    {$_aJSArray}
    );
});
JAVASCRIPTS;
        
    }
    protected function getField($aField) {
        $aField['value'] = is_null($aField['value']) ? 'transparent' : $aField['value'];
        $aField['attributes'] = $this->_getInputAttributes($aField);
        return $aField['before_label'] . "<div class='amazon-auto-links-input-label-container'>" . "<label for='{$aField['input_id']}'>" . $aField['before_input'] . ($aField['label'] && !$aField['repeatable'] ? "<span " . $this->getLabelContainerAttributes($aField, 'amazon-auto-links-input-label-string') . ">" . $aField['label'] . "</span>" : "") . "<input " . $this->getAttributes($aField['attributes']) . " />" . $aField['after_input'] . "<div class='repeatable-field-buttons'></div>" . "</label>" . "<div class='colorpicker' id='color_{$aField['input_id']}'></div>" . $this->_getColorPickerEnablerScript("{$aField['input_id']}") . "</div>" . $aField['after_label'];
    }
    private function _getInputAttributes(array $aField) {
        return array('color' => $aField['value'], 'value' => $aField['value'], 'data-default' => isset($aField['default']) ? $aField['default'] : 'transparent', 'type' => 'text', 'class' => trim('input_color ' . $aField['attributes']['class']),) + $aField['attributes'];
    }
    private function _getColorPickerEnablerScript($sInputID) {
        $_sScript = <<<JAVASCRIPTS
jQuery( document ).ready( function(){
    registerAmazonAutoLinks_AdminPageFrameworkColorPickerField( '{$sInputID}' );
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='color-picker-enabler-script'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
    }
    }
    