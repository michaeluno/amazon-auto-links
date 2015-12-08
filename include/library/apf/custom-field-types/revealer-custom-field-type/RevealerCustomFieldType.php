<?php
/**
 * Admin Page Framework - Field Type Pack
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2014-2015 Michael Uno
 * 
 */

if ( ! class_exists( 'AmazonAutoLinks_RevealerCustomFieldType' ) ) :

/**
 * Defines the revealer field type.
 * 
 * This field type allows the user to hide and reveal chosen HTML elements.
 * 
 * <h3>Field Type Specific Arguments</h3>
 * <ul>
 *  <li>`select_type` - (string) The selector type such as drop-down list, check boxes, or radio buttons.. Accepted values are `select`, `radio`, or `checkbox`.</li>
 *  <li>`attributes` - (array) The array that defines the HTML attributes of the field elements.
 *      <ul>
 *          <li>`select` - (array) The attributes applied the select tag.</li>
 *          <li>`optgroup` - (array) The attributes applied the optgroup tag.</li>
 *          <li>`option` - (array) The attributes applied the option tag.</li>
 *      </ul>
 *  </li>
 *  <li>`label` - (array) Specifies the element to toggle the visibility. Set the jQuery selector of the element to the array key and it will be toggled when the user selects it.</li>
 *  <li>`selectors` - (array) Specifies the selectors of the target element to level in each key of the `label` argument. If this argument is set, the above label key will not be used.</li>
 * </ul>
 * 
 * <h3>Example</h3>
 * <code>
 *  array(
 *      'field_id'      => 'revealer_field_by_id',
 *      'type'          => 'revealer',     
 *      'title'         => __( 'Reveal Hidden Fields', 'amazon-auto-links-field-type-pack' ),
 *      'default'       => 'undefined',
 *      'label'         => array( // the keys represent the selector to reveal, in this case, their tag id : #fieldrow-{section id}_{field id}
 *          'undefined' => __( '-- Select a Field --', 'amazon-auto-links-field-type-pack' ),     
 *          '#fieldrow-revealer_revealer_field_option_a' => __( 'Option A', 'amazon-auto-links-field-type-pack' ),     
 *          '#fieldrow-revealer_revealer_field_option_b, #fieldrow-revealer_revealer_field_option_c' => __( 'Option B and C', 'amazon-auto-links-field-type-pack' ),
 *          '#fieldrow-revealer_another_revealer_field' => __( 'Another Revealer', 'amazon-auto-links-field-type-pack' ),
 *      ),
 *      'description'   => __( 'Specify the selectors to reveal in the <code>label</code> argument keys in the field definition array.', 'amazon-auto-links-field-type-pack' ),
 *  ),
 *  array(
 *      'field_id'      => 'revealer_field_option_a',
 *      'type'          => 'textarea',     
 *      'default'       => __( 'Hi there!', 'amazon-auto-links-field-type-pack' ),
 *      'hidden'        => true,
 *  ),
 *  array(
 *      'field_id'      => 'revealer_field_option_b',     
 *      'type'          => 'password',     
 *      'description'   => __( 'Type a password.', 'amazon-auto-links-field-type-pack' ),     
 *      'hidden'        => true,
 *  ),
 *  array(
 *      'field_id'      => 'revealer_field_option_c',
 *      'type'          => 'text',     
 *      'description'   => __( 'Type text.', 'amazon-auto-links-field-type-pack' ),     
 *      'hidden'        => true,
 *  ),
 *  array(
 *      'field_id'      => 'another_revealer_field',
 *      'type'          => 'revealer',  
 *      'select_type'   => 'radio',
 *      'title'         => __( 'Another Hidden Field', 'amazon-auto-links-field-type-pack' ),
 *      'label'         => array( // the keys represent the selector to reveal, in this case, their tag id : #fieldrow-{field id}
 *          '.revealer_field_option_d' => __( 'Option D', 'amazon-auto-links-field-type-pack' ),     
 *          '.revealer_field_option_e' => __( 'Option E', 'amazon-auto-links-field-type-pack' ),
 *          '.revealer_field_option_f' => __( 'Option F', 'amazon-auto-links-field-type-pack' ),
 *      ),
 *      'hidden'        => true,
 *      'default'       => '.revealer_field_option_e',
 *      'delimiter'     => '<br /><br />',
 *      // Sub-fields
 *      array(
 *          'type'          => 'textarea',     
 *          'class'         => array(
 *              'field' => 'revealer_field_option_d',
 *          ),
 *          'label'         => '',
 *          'default'       => '',
 *          'delimiter'     => '',
 *      ),        
 *      array(
 *          'type'          => 'radio',
 *          'label'         => array(
 *              'a' => __( 'A', 'amazon-auto-links-field-type-pack' ),
 *              'b' => __( 'B', 'amazon-auto-links-field-type-pack' ),
 *              'c' => __( 'C', 'amazon-auto-links-field-type-pack' ),
 *          ),
 *          'default'       => 'a',
 *          'class'         => array(
 *              'field' => 'revealer_field_option_e',
 *          ),
 *          'delimiter'     => '',
 *      ),                        
 *      array(
 *          'type'          => 'select',     
 *          'label'         => array(
 *              'i'     => __( 'i', 'amazon-auto-links-field-type-pack' ),
 *              'ii'    => __( 'ii', 'amazon-auto-links-field-type-pack' ),
 *              'iii'   => __( 'iii', 'amazon-auto-links-field-type-pack' ),
 *          ),                
 *          'default'       => 'ii',
 *          'class'         => array(
 *              'field' => 'revealer_field_option_f',
 *          ),
 *          'delimiter'     => '',
 *      ),   
 *      
 *  ), 
 * </code>
 * 
 * @since       1.0.0
 * @package     AmazonAutoLinks_AdminPageFrameworkFieldTypePack
 * @subpackage  CustomFieldType
 * @version     1.0.1
 */
class AmazonAutoLinks_RevealerCustomFieldType extends AmazonAutoLinks_AdminPageFramework_FieldType {
        
    /**
     * Defines the field type slugs used for this field type.
     */
    public $aFieldTypeSlugs = array( 'revealer', );
    
    /**
     * Defines the default key-values of this field type. 
     * 
     * @remark            $_aDefaultKeys holds shared default key-values defined in the base class.
     */
    protected $aDefaultKeys = array(
        'select_type'   => 'select',        // accepts 'radio', 'checkbox'
        'is_multiple'   => false,
        'selectors'     => array(),
        'attributes'    => array(
            'select'    => array(
                'size'          => 1,
                'autofocusNew'  => null,
                'multiple'      => null,    // set 'multiple' for multiple selections. If 'is_multiple' is set, it takes the precedence.
                'required'      => null,        
            ),
            'optgroup'  => array(),
            'option'    => array(),
        ),        
    );
    
    /**
     * Indicates whether the JavaScirpt script is inserted or not.
     */
    private static $_bIsLoaded = false;
    
    /**
     * Loads the field type necessary components.
     */ 
    protected function setUp() {
                
        if ( ! self::$_bIsLoaded ) {
            wp_enqueue_script( 'jquery' );
            self::$_bIsLoaded = add_action( 'admin_print_footer_scripts', array( $this, '_replyToAddRevealerjQueryPlugin' ) );
        }
        
        $this->_checkFrameworkVersion();
        
    }    
    
        /**
         * @return      void
         */
        private function _checkFrameworkVersion() {
                        
            // Requires Admin Page Framework 3.7.1+
            if ( 
                method_exists( $this, 'getFrameworkVersion' ) 
                && version_compare( '3.7.1', $this->_getSuffixRemoved( $this->getFrameworkVersion(), '.dev'  ), '<=' ) 
            ) {
                return;
            }
            
            trigger_error(
                $this->getFrameworkName() . ': ' 
                . sprintf( 
                    __( 'This revealer field type version requires Admin Page Framework %1$s to function properly.', 'amazon-auto-links-field-type-pack' )
                    . ' ' . __( 'You are using the framework version %2$s.', 'amazon-auto-links-field-type-pack' ),
                    '3.7.1',
                    $this->getFrameworkVersion()
                ),
                E_USER_WARNING 
            );                
            
        }
        /**
         * @return  string
         */
        private function _getSuffixRemoved( $sString, $sSuffix ) {

            $_iLength = strlen( $sSuffix );
            if ( substr( $sString, $_iLength * -1 ) !== $sSuffix ) {
                return $sString;
            } 
            return substr( $sString, 0, $_iLength * - 1 );

        }        

    /**
     * Returns an array holding the urls of enqueuing scripts.
     */
    protected function getEnqueuingScripts() { 
        return array(
            // array( 'src'    => dirname( __FILE__ ) . '/js/jquery.knob.js', 'dependencies'    => array( 'jquery' ) ),
        );
    }
    
    /**
     * Returns an array holding the urls of enqueuing styles.
     */
    protected function getEnqueuingStyles() { 
        return array();
    }            


    /**
     * Returns the field type specific JavaScript script.
     */ 
    protected function getScripts() { 
        return "";
    }

    /**
     * Returns IE specific CSS rules.
     */
    protected function getIEStyles() { return ''; }

    /**
     * Returns the field type specific CSS rules.
     */ 
    protected function getStyles() {
        return "";
    }

    
    /**
     * Returns the output of the geometry custom field type.
     * 
     */
    /**
     * Returns the output of the field type.
     */
    protected function getField( $aField ) { 
                
        $_aOutput   = array();        
        $aField     = $this->_sanitizeInnerFieldArray( $aField );
        $_aOutput[] = $this->geFieldOutput( $aField );
        $_aOutput[] = $this->_getRevealerScript( $aField[ 'input_id' ] );
        switch( $aField[ 'select_type' ] ) {
            default:
            case 'select':
            case 'radio':                          
                $_aOutput[] = $this->_getConcealerScript( $aField[ 'input_id' ], $aField[ 'label' ], $aField[ 'value' ] );
                break;
                
            case 'checkbox':
                $_aSelections = is_array( $aField[ 'value' ] )
                    ? array_keys( array_filter( $aField[ 'value' ] ) )
                    : $aField[ 'label' ];                  
                $_aOutput[] = $this->_getConcealerScript( $aField[ 'input_id' ], $aField[ 'label' ], $_aSelections );
                break;
  
        }
        return implode( PHP_EOL, $_aOutput );
        
    }
        
        /**
         * Sanitize (re-format) the field definition array to get the field output by the select type.
         * 
         * @since       3.4.0
         */
        private function _sanitizeInnerFieldArray( array $aField ) {
            
            // The revealer field type has its own description element.
            unset( $aField[ 'description' ] );
            
            // The revealer script of checkboxes needs the reference of the selector to reveal. 
            // For radio and select input types, the key of the label array can be used but for the checkbox input type, 
            // the value attribute needs to be always 1 (for cases of key of zero '0') so the selector needs to be separately stored.
            $_aSelectors = $this->getAsArray( $aField[ 'selectors' ] );
            switch( $aField[ 'select_type' ] ) {
                default:
                case 'select':
                    foreach( $this->getAsArray( $aField[ 'label' ] ) as $_sKey => $_sLabel ) {
                        // If the user sets the 'selectors' argument, its value will be used; otherwise, the label key will be used.
                        $_sSelector = $this->getElement( $_aSelectors, array( $_sKey ), $_sKey );
                        $aField[ 'attributes' ][ 'option' ][ $_sKey ] = array(
                                'data-reveal'   => $_sSelector,
                            ) 
                            + $this->getElementAsArray( $aField[ 'attributes' ], array( 'option', $_sKey ) );
                    }                
                    break;
                case 'radio': 
                case 'checkbox':
                    
                    foreach( $this->getAsArray( $aField[ 'label' ] ) as $_sKey => $_sLabel ) {
                        // If the user sets the 'selectors' argument, its value will be used; otherwise, the label key will be used.
                        $_sSelector = $this->getElement( $_aSelectors, array( $_sKey ), $_sKey );
                        $aField[ 'attributes' ][ $_sKey ] = array(
                                'data-reveal'   => $_sSelector,
                            ) 
                            + $this->getElementAsArray( $aField[ 'attributes' ], $_sKey );
                    }
                    break;
      
            }

            // Set the select_type to the type argument.
            return array( 
                    'type' => $aField[ 'select_type' ] 
                ) + $aField;
            
        }
        
        private function _getRevealerScript( $sInputID ) {
            return 
                "<script type='text/javascript' >"
                    . '/* <![CDATA[ */ '
                    . "jQuery( document ).ready( function(){
                        jQuery('*[data-id=\"{$sInputID}\"]').setAmazonAutoLinks_AdminPageFrameworkRevealer();
                    });"
                    . ' /* ]]> */'
                . "</script>";    
        }        
        private function _getConcealerScript( $sSelectorID, $aLabels, $asCurrentSelection ) {
            
            $aLabels            = $this->getAsArray( $aLabels );
            $_aCurrentSelection = $this->getAsArray( $asCurrentSelection );
            unset( $_aCurrentSelection[ 'undefined' ] );    // an internal reserved key    
            if( ( $_sKey = array_search( 'undefined' , $_aCurrentSelection) ) !== false ) {
                unset( $_aCurrentSelection[ $_sKey ] );
            }            
            $_sCurrentSelection = json_encode( $_aCurrentSelection );            
            
            unset( $aLabels[ 'undefined' ] );
            $aLabels    = array_keys( $aLabels );
            $_sLabels   = json_encode( $aLabels );    // encode it to be usable in JavaScript
            return 
                "<script type='text/javascript' class='amazon-auto-links-revealer-field-type-concealer-script'>"
                    . '/* <![CDATA[ */ '
                    . "jQuery( document ).ready( function(){

                        jQuery.each( {$_sLabels}, function( iIndex, sValue ) {

                            /* If it is the selected item, show it */
                            if ( jQuery.inArray( sValue, {$_sCurrentSelection} ) !== -1 ) { 
                                jQuery( sValue ).fadeIn();
                                return true;    // continue
                            }
                            
                            jQuery( sValue ).hide();
                                
                        });
                        jQuery( 'select[data-id=\"{$sSelectorID}\"], input:checked[type=radio][data-id=\"{$sSelectorID}\"], input:checked[type=checkbox][data-id=\"{$sSelectorID}\"]' )
                            .trigger( 'change' );
                    });"
                    . ' /* ]]> */'
                . "</script>";
                
        }

    /**
     * Adds the revealer jQuery plugin.
     * @since            3.0.0
     */
    public function _replyToAddRevealerjQueryPlugin() {
                
        $_sScript = "
        ( function ( $ ) {
            
            /**
             * Binds the revealer event to the element.
             */
            $.fn.setAmazonAutoLinks_AdminPageFrameworkRevealer = function() {

                var _sLastRevealedSelector;
                this.change( function() {
                    
                    var _sTargetSelector        = $( this ).is( 'select' )
                        ? $( this ).children( 'option:selected' ).data( 'reveal' )
                        : $( this ).data( 'reveal' );
                    
                    // For checkboxes       
                    if ( $( this ).is(':checkbox') ) {
                        var _oElementToReveal       = $( _sTargetSelector );
                        if ( $( this ).is( ':checked' ) ) {
                            _oElementToReveal.fadeIn();
                        } else {
                            _oElementToReveal.hide();    
                        }                      
                        return;
                    }
                    
                    // For other types (select and radio).
                    // var _sTargetSelector        = $( this ).val();
                    var _oElementToReveal       = $( _sTargetSelector );

                    // Hide the previously hidden element.
                    $( _sLastRevealedSelector ).hide();    
                                        
                    // Store the last revealed item in the local and the outer local variables.
                    _sLastRevealedSelector = _sTargetSelector;
                    
                    if ( 'undefined' === _sTargetSelector ) { 
                        return; 
                    }
                    _oElementToReveal.fadeIn();                                       
                    
                });
                
            };
                        
        }( jQuery ));";
        
        echo "<script type='text/javascript' class='amazon-auto-links-revealer-jQuery-plugin'>"
                . '/* <![CDATA[ */ '
                . $_sScript
                . ' /* ]]> */'
            . "</script>";
        
    }        
    
}
endif;