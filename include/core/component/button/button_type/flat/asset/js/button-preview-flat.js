/**
 * @name    Flat Button Preview Event Binder
 * @version 1.0.0
 */
(function ( $ ) {

  /* global aalFlatButtonPreviewEventBinder, */
  $( document ).ready( function () {

    console.log( 'AAL Button Preview - Flat', aalFlatButtonPreviewEventBinder );
    if ( 'undefined' === typeof aalFlatButtonPreviewEventBinder ) {
      return;
    }

    var buttonID               = 'undefined' === typeof aalFlatButtonPreviewEventBinder.postID
      ? '___button_id___'
      : aalFlatButtonPreviewEventBinder.postID;
    var styleKeyButton         = '.amazon-auto-links-button-' + buttonID;
    var styleKeyButtonElements = '.amazon-auto-links-button-' + buttonID + ' *';
    var styleKeyButtonHover    = styleKeyButton + ':hover';
    var styleKeyButtonChildren = styleKeyButton + ' > *';
    var styleKeyButtonLabel    = styleKeyButton + ' .button-label';
    var styleKeyIconBoth       = styleKeyButton + ' .button-icon';            // the both icon containers
    var styleKeyIconIBoth      = styleKeyButton + ' .button-icon > i';        // the both icons
    var styleKeyIconLeft       = styleKeyButton + ' .button-icon-left';       // the left icon container
    var styleKeyIconILeft      = styleKeyButton + ' .button-icon-left > i';   // the left icon
    var styleKeyIconRight      = styleKeyButton + ' .button-icon-right';      // the right icon container
    var styleKeyIconIRight     = styleKeyButton + ' .button-icon-right > i';  // the right icon
    var styleHolder            = getDefaultStyleHolder();
    var jqPreviewFrame         = $( '#button-preview-flat > iframe' ).first();
    var jqInputs               = $( '.dynamic-button-field input, .dynamic-button-field select, .dynamic-button-field textarea' );
    var debouncers             = {}; // stores setTimeout IDs to debounce function calls
    var inputProcessor         = getInputProcessor();
    var jqTextAreaButtonCSS    = $( '#button_css__0' );
    var jqTextAreaCustomCSS    = $( '#custom_css__0' );
    var jqSpinner              = $( '<img>', {
      'alt': 'Loading...',
      'src': aalFlatButtonPreviewEventBinder.spinnerURL,
      'style': 'vertical-align: middle; margin: 0 auto; display: inline-block;'
    } );

    // When the user changes button options,
    $( '.dynamic-button-field' ).on( 'change input amazon-auto-links_field_type_color_cleared', 'input, select, textarea', function () {

      var _self = $( this );
      
      // Parse inputs and call the bound callback
      (function( self ) {
        var _property = $( self ).data( 'property' );
        _property = typeof _property !== 'string' ? '' : _property;
        var _methodName = 'undefined' !== typeof inputProcessor[ _property ] ? _property : '_common';
        if ( ! _property.length ) {
          return;
        }
        clearTimeout( debouncers[ _property + $( self ).data( 'selectorSuffix' ) ] ); // do not call the function too frequent with the debounce logic
        debouncers[ _property + $( self ).data( 'selectorSuffix' ) ] = setTimeout( function(){
          inputProcessor[ _methodName ]( self );
        }, 100 );
      })( _self );
    
      // Apply the stylesheet and update field inputs
      (function( self ) {

        clearTimeout( debouncers[ '_stylesheet_generator' ] ); // do not call the function too frequent with the debounce logic
        debouncers[ '_stylesheet_generator' ] = setTimeout( function () {
  
          // Update the framed page stylesheet
          var _style = _getCSSRulesGenerated( _sortObject( styleHolder ) ) + '\n' + jqTextAreaCustomCSS.val();
          _setStyleSheetInFramedPage( _style, 'button-preview-flat-framed-stylesheet', jqPreviewFrame );
  
          // Update the Generated CSS field
          jqTextAreaButtonCSS.text( _style );
  
          // Tell the framed window to send back proportional data so that the iframe height will be adjusted with a callback defined in this script
          jqPreviewFrame[ 0 ].contentWindow.postMessage(
            {
              event: 'ReloadButtonPreview',
              nonce: aalFlatButtonPreviewEventBinder.nonce,
            },
            location.protocol + '//' + location.host
          );
          
          function _setStyleSheetInFramedPage( style, attributeID, jqPreviewFrame ) {
            var _styleSheet = $( '<style>', { id: attributeID } );
            _styleSheet.text( style );
            var _prevSheet = jqPreviewFrame.contents().find( '#' + attributeID );
            if ( _prevSheet.length > 0 ) {
              _prevSheet.replaceWith( _styleSheet );
            } else {
              jqPreviewFrame.contents().find( 'head' ).first().append( _styleSheet );
            }
          }
          
          function _getCSSRulesGenerated( styleHolder ) {
            var _extraCSS = '';
            for ( var selector in styleHolder ) {
              if ( ! styleHolder.hasOwnProperty( selector ) ) {
                continue;
              }
              _extraCSS += __getRulesBySelector( selector, styleHolder[ selector ] );
            }
            return _extraCSS.trim();
            function __getRulesBySelector( selector, ruleset ) {
              if ( $.isEmptyObject( ruleset ) ) {
                return '';
              }
              var _rules = '';
              _rules += selector + ' {\n';
              for ( var prop in ruleset ) {
                if ( ! ruleset.hasOwnProperty( prop ) ) {
                  continue;
                }
                // An array can be given for multiple rules for a same property to support different browsers
                if ( ruleset[ prop ].constructor === Array ) {
                  _rules += ___getRulesExtracted( ruleset[ prop ] );
                  continue;
                }
                _rules += '    ' + prop + ': ' + ruleset[ prop ] + ';\n';
              }
              return _rules + '}\n';

              function ___getRulesExtracted( rules ) {
                if ( ! rules.length ) {
                  return;
                }
                var _rules = '';
                for( var _i = 0; _i < rules.length; _i++) {
                  _rules += '    ' + prop + ': ' + rules[ _i ] + ';\n';
                }
                return _rules;
              }
            }
          }
    
          /**
           * Used to sort the style holder object.
           * @see     https://stackoverflow.com/a/29622653
           * @param   obj
           * @returns {{}}
           */
          function _sortObject( obj ) {
            return Object.keys( obj ).sort().reduce( function ( result, key ) {
              result[ key ] = obj[ key ];
              return result;
            }, {} );
          }    
          
        }, 200 );        
        
      })( _self );
      
    } );

    // When the preview iframe is loaded, call input change events to update the preview
    (function( jqIframe ) { // IIFE for IDE

      jqIframe.before( jqSpinner );
      jqIframe.css( { 'visibility': 'hidden' } );  // when the frame loads, prevent the non-styled preview button from being displayed

      jqIframe.on( 'load', function() {

        // Set properties of the input processor that stores jQuery object of iframe elements
        inputProcessor.jqPreviewButtonIconLeft  = $( this ).contents().find( 'body .button-icon-left' );
        inputProcessor.jqPreviewButtonIconRight = $( this ).contents().find( 'body .button-icon-right' );
        inputProcessor.jqPreviewButtonLabel     = $( this ).contents().find( 'body .button-label' ).first();

        // Using a debouncer as there are cases like the frame gets loaded multiple times in a short period of time
        // Note that when the preview meta-box visibility is toggled with the screen layout option (top-right corner of the admin screen) or moved its position (sorted),
        // the frame gets reloaded and this function can be called multiple times in a single page load.
        clearTimeout( debouncers[ '_iframe_preview_loaded' ] ); // do not call the function too frequent with the debounce logic
        debouncers[ '_iframe_preview_loaded' ] = setTimeout( function () {
          triggerInputChanges( jqInputs );
        }, 2000 );

      });
      /// There is a case that the frame is already loaded before this is called
      if ( 'complete' === jqIframe[ 0 ].contentDocument.readyState ) {
        jqIframe.trigger( 'load' );
      }
    })( jqPreviewFrame );

    // Adjust the iframe height on proportional changes with windows messages from the framed window
    (function( jqIframe ) { // IIFE for IDE
      if ( ! window.addEventListener ) {  // there are browsers which don't support this
        return;
      }
      window.addEventListener('message', function( event) {
        if ( event.origin !== location.protocol + '//' + location.host ) {
          return;
        }
        if ( 'undefined' === typeof event.data.height ) {
          return;
        }
        if ( jqSpinner.is( ':visible' ) ) {
          jqSpinner.hide();
          jqIframe.css( 'visibility', 'visible' ).hide().fadeIn();
        }
        jqIframe.height( event.data.height ).css( 'height', event.data.height + 'px' );
      }, false );
    })( jqPreviewFrame );

    /* Execution ends */

    function getInputProcessor() {

      var inputProcessorBase          = {
        // Properties
        'iconLeft'                  : {
          'jqIconToggle': $( '.dynamic-button-field input[type=radio][data-property=_icon_toggle_left]' ), // stores multiple input instances are stores as radio buttons are multiple and one of them has a checked status,
          'jqIconType': $( '.dynamic-button-field input[type=radio][data-property=_icon_image_type_left]' ), // stores multiple input instances are stores as radio buttons are multiple and one of them has a checked status,
          'jqIconSVGMaskToggle': $( '.dynamic-button-field input[type=checkbox][data-property=_icon_svg_mask_toggle_left]' ),
          'jqIconSizeType': $( '.dynamic-button-field input[type=radio][data-property=_icon_size_type_left]' ),
          'jqIconSizeConstraint'  : $( '.dynamic-button-field input[type=checkbox][data-property=_icon_size_constraint_left]' ),
        },
        'iconRight'                 : {
          'jqIconToggle': $( '.dynamic-button-field input[type=radio][data-property=_icon_toggle_right]' ), // stores multiple input instances are stores as radio buttons are multiple and one of them has a checked status,
          'jqIconType': $( '.dynamic-button-field input[type=radio][data-property=_icon_image_type_right]' ), // stores multiple input instances are stores as radio buttons are multiple and one of them has a checked status,
          'jqIconSVGMaskToggle': $( '.dynamic-button-field input[type=checkbox][data-property=_icon_svg_mask_toggle_right]' ),
          'jqIconSizeType': $( '.dynamic-button-field input[type=radio][data-property=_icon_size_type_right]' ),
          'jqIconSizeConstraint' : $( '.dynamic-button-field input[type=checkbox][data-property=_icon_size_constraint_right]' ),
        },
        'jqFontSizeInput'           : $( '.dynamic-button-field input[type=number][data-property=font-size]' ), // stores the jQuery object of the font size input element
        'jqPreviewButtonIconLeft'   : undefined, // stores the jQuery object of the preview button left icon element. Not assigning a value here since iframe can have not been loaded yet.
        'jqPreviewButtonIconRight'  : undefined, // stores the jQuery object of the preview button right icon element. Not assigning a value here since iframe can have not been loaded yet.
        'jqPreviewButtonLabel'      : undefined, // stores the jQuery object of the preview button label element. Not assigning a value here since iframe can have not been loaded yet.
        'lineHeight'                : 1.3,       // default: 1.3. The line height can be different depending on the them
        'lastUpdatedLineHeight'     : 0,         // the last checked time of the line height of the iframe document

        // Methods
        'isIconEnabled': function( position ) {
          return getBoolean( this[ 'icon' + getFirstLetterCapitalized( position.toLowerCase() ) ].jqIconToggle.filter( ':checked' ).val() );
        },
        'getIconType': function( position ) {
          return this[ 'icon' + getFirstLetterCapitalized( position.toLowerCase() ) ].jqIconType.filter( ':checked' ).val();
        },
        /**
         * Gets the height of the current label element.
         */
        'getLabelHeight': function() {
          var _lineHeight  = this.getLineHeightProperty();
          var _fontSize    = this.getFontSize();
          return _fontSize && _lineHeight
            ? Math.ceil( _fontSize * _lineHeight )
            : Math.ceil( this.jqPreviewButtonLabel.height() );
        },
        /**
         * Updates the property of the line height of the button label element of the iframe document
         */
        'setLineHeightProperty': function() {
          if ( 'undefined' === typeof this.jqPreviewButtonLabel ) {
            return;
          }
          var _interval = 1000;
          if ( this.lastUpdatedLineHeight >= ( Date.now() - _interval ) ) {  // prevent from being called too frequent
            return;
          }
          this.lastUpdatedLineHeight = Date.now();
          var _element = this.jqPreviewButtonLabel.get( 0 );
          if ( 'undefined' === typeof _element ) {
            return;
          }
          this.lineHeight = _getLineHeight( _element );    // <-- this needs to be done in this function
          /**
           * Calculates the line height value for CSS as a number applied by the browser to the given element.
           * @see https://stackoverflow.com/a/4515470
           * @remark The linked method just checks the client rect height. This modifies it to calculate the CSS line-height value.
           */
          function _getLineHeight( el ) {
            var temp = document.createElement( el.nodeName ), ret;
            temp.setAttribute( "style", "margin:0; padding:0; "
              + "font-family:" + (el.style.fontFamily || "inherit") + "; "
              + "font-size:10px; " // assuming {client rect height = font size * line height}, the line height will be calculated later below by dividing the height by 10
              + "display: inline; "
              + "position:absolute; left:-999em;"
            );
            temp.innerHTML = "A";
            el.parentNode.appendChild( temp );
            ret = Math.round( ( temp.clientHeight / 10 ) * 10 ) / 10; // round to 1 decimal
            temp.parentNode.removeChild( temp );
            return ret;
          }
        },
        'getLineHeightProperty': function() {
          this.setLineHeightProperty();
          return this.lineHeight;
        },
        'getFontSize': function() {
          return parseInt( this.jqFontSizeInput.val() );
        },
        'adjustIconHeight': function( rightOrLeft ) {
          var _heightLabel = this.getLabelHeight();
          var _key = 'left' === rightOrLeft ? styleKeyIconLeft : styleKeyIconRight;
          styleHolder[ _key ][ 'min-height' ] = _heightLabel + 'px';
          styleHolder[ _key ][ 'min-width' ]  = _heightLabel + 'px';
        },
        setDimension: function( self, key, property ) {
          var _oField    = self.closest( '.amazon-auto-links-field' );
          var _valueSize = _oField.find( 'input' ).val();
          if ( ! _valueSize.length ) {
            delete styleHolder[ key ][ property ];  // if an empty value is given, do not set a rule because the use may want to remove it by giving an empty value
            return;
          }
          var _unitSize  = _oField.find( 'select' ).val();
          styleHolder[ key ][ property ] = _valueSize + _unitSize;
        },
        '_custom_css': function( self ) {}, // do nothing
        '_common': function( self ) {
          if ( ! self.data( 'property' ) ) {
            return;
          }
          var _suffixSelector = 'undefined' !== typeof self.data( 'selectorSuffix' ) && self.data( 'selectorSuffix' ).length ? ' ' + $.trim( self.data( 'selectorSuffix' ) ) : '';
          var _suffixValue    = 'undefined' !== typeof self.data( 'suffix' ) && self.data( 'suffix' ).length ? self.data( 'suffix' ) : '';
          var _value          = self.val();
          if ( self.data( 'noEmpty' ) && ! _value.length ) {
            delete styleHolder[ styleKeyButton + _suffixSelector ][ self.data( 'property' ) ];
            return;
          }
          if ( 'undefined' === typeof styleHolder[ styleKeyButton + _suffixSelector ] ) {
            styleHolder[ styleKeyButton + _suffixSelector ] = {};
          }
          styleHolder[ styleKeyButton + _suffixSelector ][ self.data( 'property' ) ] = self.val() + _suffixValue;
        }
      };
      var inputProcessorBackground    = {
        'jqBackgroundType': $( '.dynamic-button-field input[type=radio][data-property=_background_type]' ), // holds multiple elements as these are radio inputs
        'jqGradient': $( '.background-gradient' ).first(),
        '_background_type': function( self ) {
          var _val = self.val();
          if ( 'none' === _val ) {
            delete styleHolder[ styleKeyButton ][ 'background-image' ];
            delete styleHolder[ styleKeyButton ][ 'background-color' ];
            return;
          }
          if ( 'solid' === _val ) {
            $( '.dynamic-button-field input[data-property=_background_color]' ).trigger( 'change' );
            return;
          }
          if ( 'gradient' === _val ) {
            $( '.dynamic-button-field select[data-property=_background_image_gradient_direction]' ).trigger( 'change' );
          }
        },
        'getGradientRule': function( self ) {
          var _aRules    = [];
          // background-image: linear-gradient(direction, color-stop1, color-stop2, ...);
          var _direction = this.jqGradient.find( 'select[data-property=_background_image_gradient_direction]' ).val();
          _direction     = replaceAll( _direction, '_', ' ' );
          var _jqColors  = this.jqGradient.find( 'input[data-property=_background_image_gradient_colors]' );

          // Support other browsers
          var _startPos = _direction
            .replace( 'to ', '' )
            .replace( /top|bottom|left|right/gi, function( matched ){
              var _mapObj = {
                 bottom: 'top',
                 top:    'bottom',
                 left:   'right',
                 right:  'left',
              };
              return _mapObj[ matched ];
            });
          _aRules.push( _getGradient( '-webkit-linear-gradient', _startPos, _jqColors ) );  // Safari 5.1, iOS 5.0-6.1, Chrome 10-25, Android 4.0-4.3
          _aRules.push( _getGradient( '-moz-linear-gradient', _startPos, _jqColors ) );     // Firefox 3.6 - 15
          _aRules.push( _getGradient( '-o-linear-gradient', _startPos, _jqColors ) );       // Opera 11.1 - 12

          // The main rule
          _aRules.push( _getGradient( 'linear-gradient', _direction, _jqColors ) );

          return _aRules;
          function _getGradient( suffix, direction, jqColors ) {
            var _gradient  = suffix + '(' + direction;
            jqColors.each( function(){
              _gradient += ',' + $( this ).val();
            } );
            return _gradient + ')';
          }
          function replaceAll(str, find, replace) {
            return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
            function escapeRegExp(string) {
              return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
            }
          }
        },
        '_background_color': function( self ) {
          if ( this.jqBackgroundType.filter( ':checked' ).val() !== 'solid' ) {
            return;
          }
          delete styleHolder[ styleKeyButton ][ 'background-image' ];
          styleHolder[ styleKeyButton ][ 'background-color' ] = self.val();
        },
        '_background_image_gradient_direction': function( self ) {
          if ( this.jqBackgroundType.filter( ':checked' ).val() !== 'gradient' ) {
            return;
          }
          var _gradientRule = this.getGradientRule( self );
          styleHolder[ styleKeyButton ][ 'background-color' ] = this.jqGradient.find( 'input[data-property=_background_image_gradient_colors]' ).first().val(); // fallback
          styleHolder[ styleKeyButton ][ 'background-image' ] = _gradientRule;

        },
        '_background_image_gradient_colors': function( self ) {
          this._background_image_gradient_direction( self );
        },
      };
      var inputProcessorHover         = {
        '_hover_scale': function( self ) {
          if ( self.is( ':checked' ) ) {
            styleHolder[ styleKeyButtonHover ][ 'transform' ] = 'scale(1.0);';
            styleHolder[ styleKeyButton ][ 'transform' ] = 'scale(0.98)';
            return;
          }
          delete styleHolder[ styleKeyButtonHover ][ 'transform' ];
          delete styleHolder[ styleKeyButton ][ 'transform' ];
        },
        '_hover_brightness': function( self ) {
          if ( self.is( ':checked' ) ) {
            styleHolder[ styleKeyButtonHover ][ 'filter' ] = 'alpha(opacity=70)';
            styleHolder[ styleKeyButtonHover ][ 'opacity' ] = '0.7';
            return;
          }
          delete styleHolder[ styleKeyButtonHover ][ 'filter' ];
          delete styleHolder[ styleKeyButtonHover ][ 'opacity' ];
        },
      };
      var inputProcessorBox           = {
        '_width_toggle': function( self ) {
          if ( ! self.is( ':checked' ) ) {
            delete styleHolder[ styleKeyButton ][ 'width' ];
            return;
          }
          $( "input[type=number][data-property='width']" ).trigger( 'change' );
        },
        'width': function( self ) {
          this.setDimension( self, styleKeyButton, self.data( 'property' ) );
        },
        '_height_toggle': function( self ) {
          if ( ! self.is( ':checked' ) ) {
            delete styleHolder[ styleKeyButton ][ 'height' ];
            return;
          }
          $( "input[type=number][data-property='height']" ).trigger( 'change' );
        },
        'height': function ( self ) {
          this.setDimension( self, styleKeyButton, self.data( 'property' ) );
        },
        '_padding_type': function( self ) {
          if ( 'all' === self.val() ) {
            delete styleHolder[ styleKeyButton ][ 'padding-top' ];
            delete styleHolder[ styleKeyButton ][ 'padding-right' ];
            delete styleHolder[ styleKeyButton ][ 'padding-bottom' ];
            delete styleHolder[ styleKeyButton ][ 'padding-left' ];
            $( "input[type=number][data-property='padding']" ).trigger( 'change' );
            return;
          }
          // each padding
          delete styleHolder[ styleKeyButton ][ 'padding' ];
          $( "input[type=number][data-property^='padding-']" ).trigger( 'change' );
        },
      };
      var inputProcessorText          = {
        '_text_margin_type': function( self ) {
          var _suffixSelector = 'undefined' !== typeof self.data( 'selectorSuffix' ) && self.data( 'selectorSuffix' ).length ? ' ' + $.trim( self.data( 'selectorSuffix' ) ) : '';
          if ( 'all' === $( self ).val() ) {
            delete styleHolder[ styleKeyButton + _suffixSelector ][ 'margin-top' ];
            delete styleHolder[ styleKeyButton + _suffixSelector ][ 'margin-right' ];
            delete styleHolder[ styleKeyButton + _suffixSelector ][ 'margin-bottom' ];
            delete styleHolder[ styleKeyButton + _suffixSelector ][ 'margin-left' ];
            $( "input[type=number][data-property='margin'][data-selector-suffix='" + self.data( 'selectorSuffix' ) + "']" ).trigger( 'change' );
            return;
          }
          // each margin
          delete styleHolder[ styleKeyButton + _suffixSelector ][ 'margin' ];
          $( "input[type=number][data-property^='margin-'][data-selector-suffix='" + self.data( 'selectorSuffix' ) + "']" ).trigger( 'change' );
        },
        '_button_label': function( self ) {
          this.jqPreviewButtonLabel.text( $( self ).val() );
        },
        'font-size': function( self ) {
          this._common( self );
          if ( this.iconLeft.jqIconSizeConstraint.is( ':checked' ) ) {
            if ( this.isIconEnabled( 'left' ) ) {
              this.adjustIconHeight( 'left' );
            }
          }
          if ( this.iconRight.jqIconSizeConstraint.is( ':checked' ) ) {
            if ( this.isIconEnabled( 'right' ) ) {
              this.adjustIconHeight( 'right' );
            }
          }
        }
      };
      var inputProcessorIconLeft      = _getInputProcessorIcon( 'left', inputProcessorBase );
      var inputProcessorIconRight     = _getInputProcessorIcon( 'right', inputProcessorBase );
      return $.extend( {}, inputProcessorBase, inputProcessorText, inputProcessorIconLeft, inputProcessorIconRight, inputProcessorBox, inputProcessorHover, inputProcessorBackground );

      function _getInputProcessorIcon( position, inputProcessorBase ) {
        var _pos                         = position.toLowerCase();    // left / right
        var _styleKeyIcon                = 'left' === _pos ? styleKeyIconLeft : styleKeyIconRight;
        var _styleKeyIconI               = 'left' === _pos ? styleKeyIconILeft : styleKeyIconIRight;
        var _iconPos                     = 'icon' + getFirstLetterCapitalized( _pos ); // iconLeft / iconRight
        var _inputProcessorIcon          = _getInputProcessorIcon();
        var _inputProcessorIconSVGFile   = _getInputProcessorIconSVGFile();
        var _inputProcessorIconImageFile = _getInputProcessorIconImageFile();
        return $.extend( {}, _inputProcessorIcon, _inputProcessorIconSVGFile, _inputProcessorIconImageFile );

        function _getInputProcessorIcon() {
          var _processor = {};
          // data-property: _icon_padding_all_left / _icon_padding_all_right
          _processor[ '_icon_padding_all_' + _pos ] = function( self ) {
              delete styleHolder[ _styleKeyIcon ][ 'padding-top' ];
              delete styleHolder[ _styleKeyIcon ][ 'padding-right' ];
              delete styleHolder[ _styleKeyIcon ][ 'padding-bottom' ];
              delete styleHolder[ _styleKeyIcon ][ 'padding-left' ];
              if ( ! self.val().length ) {
                delete styleHolder[ _styleKeyIcon ][ 'padding' ];
                return;
              }
              styleHolder[ _styleKeyIcon ][ 'padding' ] = self.val() + 'px';
          };
          // data-property: _icon_padding_type_left / _icon_padding_type_right
          _processor[ '_icon_padding_type_' + _pos ] = function( self ) {
            if ( 'all' === $( self ).val() ) {
              delete styleHolder[ _styleKeyIcon ][ 'padding-top' ];
              delete styleHolder[ _styleKeyIcon ][ 'padding-right' ];
              delete styleHolder[ _styleKeyIcon ][ 'padding-bottom' ];
              delete styleHolder[ _styleKeyIcon ][ 'padding-left' ];
              $( "input[type=number][data-property='padding'][data-selector-suffix='" + self.data( 'selectorSuffix' ) + "']" ).trigger( 'change' );
              return;
            }
            delete styleHolder[ _styleKeyIcon ][ 'padding' ];
            $( "input[type=number][data-property^='padding-'][data-selector-suffix='" + self.data( 'selectorSuffix' ) + "']" ).trigger( 'change' );            
          };          
          // data-property: _icon_left_margin_type / _icon_right_margin_type
          _processor[ '_icon_margin_type_' + _pos ] = function( self ) {
            if ( 'all' === $( self ).val() ) {
              delete styleHolder[ _styleKeyIcon ][ 'margin-top' ];
              delete styleHolder[ _styleKeyIcon ][ 'margin-right' ];
              delete styleHolder[ _styleKeyIcon ][ 'margin-bottom' ];
              delete styleHolder[ _styleKeyIcon ][ 'margin-left' ];
              $( "input[type=number][data-property='margin'][data-selector-suffix='" + self.data( 'selectorSuffix' ) + "']" ).trigger( 'change' );
              return;
            }
            delete styleHolder[ _styleKeyIcon ][ 'margin' ];
            $( "input[type=number][data-property^='margin-'][data-selector-suffix='" + self.data( 'selectorSuffix' ) + "']" ).trigger( 'change' );            
          };
          // data-property: _icon_toggle_left / _icon_toggle_right
          _processor[ '_icon_toggle_' + _pos ] = function( self ) {
            if ( ! getBoolean( self.val() ) ) {
              styleHolder[ _styleKeyIcon ]  = {}; // delete all the rules of the left icon
              styleHolder[ _styleKeyIconI ] = {}; // delete all the rules of the left icon

              // Even though the icon is set to off, the image field of the Image File gets shown for some reasons when opening the screen of the existing button to edit (post.php, not Add New). So hide them.
              $( self.data( 'selectors' ) ).hide();
              return;
            }
            triggerInputChanges( $( '.fields-button-icon-' + _pos + ' input, .fields-button-icon-' + _pos + ' select' ).not( self ) );
          };
          // data-property: _icon_image_type_left / _icon_image_type_right
          _processor[ '_icon_image_type_' + _pos ] = function( self ) {
            var _this = this;
            var _imageTypeHandlers = {
              'svg_file': function() {
                // delete styleHolder[ _styleKeyIcon ][ 'background-image' ]; // no need to remove as the background-image is needed for svg_file as well
                triggerInputChanges( $( '.svg-file-' + _pos + ' input, .svg-file-' + _pos + ' select' ).not( self ) );
              },
              'image_file': function() {
                _this.deleteSVGRules( _pos );
                triggerInputChanges( $( '.image-file-' + _pos + ' input, .image-file-' + _pos + ' select' ).not( self ) );
              },
              '_unknown': function() {
                console.log( 'An unknown button icon image type is selected.' );
              }
            };
            var _type       = self.val();
            var _methodName = 'undefined' === typeof _imageTypeHandlers[ _type ] ? '_unknown' : _type;
            _imageTypeHandlers[ _methodName ]();
          };
          // data-property: _icon_size_constraint_left / _icon_size_constraint_right
          _processor[ '_icon_size_constraint_' + _pos ] = function( self ) {
            var _thisSection      = self.closest( '.amazon-auto-links-section' );
            var _associatedFields = _thisSection.find( '.dynamic-button-field ' + self.data( 'selectors' ) ); // the revealer-associated elements
            if ( self.is( ':checked' ) ) {
              // Set the same height and width as the button label
              this.adjustIconHeight( _pos );
              // Hide the revealer associated fields as they are shown for a moment, which doesn't look well
              this[ _iconPos ].jqIconSizeType.each( function(){
                _thisSection.find( '.dynamic-button-field ' + $( this ).data( 'selectors' ) ).hide();
              } );
              _associatedFields.hide();
              return;
            }
            // Show fields related with the revealer fields
            _associatedFields.show();
            this[ _iconPos ].jqIconSizeType.filter( ':checked' ).trigger( 'change' ); // trigger the associated revealer field change events to show/hide its related fields, plus regenerate the stylesheet
          };
          // data-property: _icon_size_type_left / _icon_size_type_right
          _processor[ '_icon_size_type_' + _pos ] = function( self ) {
            // Cover cases that even when the "icon constraint to text size" checkbox is on, the icon size type field and associated fields are still visible. So hide them.
            if ( this[ _iconPos ].jqIconSizeConstraint.is( ':checked' ) ) {
              this[ _iconPos ].jqIconSizeConstraint.trigger( 'change' ); // adjust the icon size to the font size, plus hide associated fields
              return;
            }

            // At this point, the size constraint is off, so set the user-set size to the icon
            if ( 'all' === self.val() ) {
              $( '.dynamic-button-field input[type=number][data-property=_icon_size_all_' + _pos + ']' ).first().trigger( 'change' );
``            } else { // each
              $( '.dynamic-button-field input[type=number][data-property=_icon_size_each_width_' + _pos + ']' ).trigger( 'change' );
              $( '.dynamic-button-field input[type=number][data-property=_icon_size_each_height_' + _pos + ']' ).trigger( 'change' );
            }
          };
          // data-property: _icon_size_all_left / _icon_size_all_right
          _processor[ '_icon_size_all_' + _pos ] = function( self ) {
            this.setDimension( self, _styleKeyIcon, 'min-width' );
            this.setDimension( self, _styleKeyIcon, 'min-height' );
          };
          // data-property: _icon_size_each_width_left / _icon_size_each_width_right
          _processor[ '_icon_size_each_width_' + _pos ] = function( self ) {
            this.setDimension( self, _styleKeyIcon, 'min-width' );
          };
          // data-property: _icon_size_each_height_left / _icon_size_each_height_right
          _processor[ '_icon_size_each_height_' + _pos ] = function( self ) {
            this.setDimension( self, _styleKeyIcon, 'min-height' );
          };
          return _processor;
        }
        function _getInputProcessorIconImageFile() {
          var _processor = {};
          // data-property: _icon_image_file_left / _icon_image_file_right
          _processor[ '_icon_image_file_' + _pos ] = function( self ) {
            if ( ! this.isIconEnabled( _pos ) ) {
              styleHolder[ _styleKeyIcon ]  = {};
              styleHolder[ _styleKeyIconI ] = {};
              return;
            }
            styleHolder[ _styleKeyIcon ][ 'display' ]             = 'inline-flex';
            var _url    = $( self ).val().trim();
            if ( ! _url.length ) {
              delete styleHolder[ _styleKeyIconI ][ 'background-image' ];
              return;
            }
            styleHolder[ _styleKeyIconI ][ 'background-image' ]    = "url('" + _url + "')";
            styleHolder[ _styleKeyIconI ][ 'background-size' ]     = 'contain';
            styleHolder[ _styleKeyIconI ][ 'background-position' ] = 'center';
            styleHolder[ _styleKeyIconI ][ 'background-repeat' ]   = 'no-repeat';
            // Dimensions
            var _heightLabel = Math.ceil( this.jqPreviewButtonLabel.height() );
            styleHolder[ _styleKeyIcon ][ 'min-height' ]          = _heightLabel + 'px';
            styleHolder[ _styleKeyIcon ][ 'min-width' ]           = _heightLabel + 'px';
          };
          return _processor;
        }
        function _getInputProcessorIconSVGFile() {
          var _processor = {
            'isSVGMaskEnabled': function( position ) {
              return this[ 'icon' + getFirstLetterCapitalized( position ) ].jqIconSVGMaskToggle.is( ':checked' );
            },
            'deleteSVGRules': function( position ) {
              var _styleKeyIconI = 'left' === position ? styleKeyIconILeft : styleKeyIconIRight;
              delete styleHolder[ _styleKeyIconI ][ 'background-color' ];
              delete styleHolder[ _styleKeyIconI ][ '-webkit-mask-image' ];
              delete styleHolder[ _styleKeyIconI ][ 'mask-image' ];
              delete styleHolder[ _styleKeyIconI ][ '-webkit-mask-position' ];
              delete styleHolder[ _styleKeyIconI ][ 'mask-position' ];
              delete styleHolder[ _styleKeyIconI ][ '-webkit-mask-repeat' ];
              delete styleHolder[ _styleKeyIconI ][ 'mask-repeat' ];
            },
          };
          // data-property: _icon_svg_file_left / _icon_svg_file_right
          _processor[ '_icon_svg_file_' + _pos ] = function( self ) {
            if ( ! this.isIconEnabled( _pos ) ) {
              styleHolder[ _styleKeyIcon ]  = {};
              styleHolder[ _styleKeyIconI ] = {};
              return;
            }
            // If icon image type is not svg, remove related CSS rules
            if ( this.getIconType( _pos ) !== 'svg_file' ) {
              delete styleHolder[ _styleKeyIconI ][ 'background-color' ];
            }
            styleHolder[ _styleKeyIcon ][ 'display' ]             = 'inline-flex'; // this needs to be set regardless the url is empty or not
            var _url         = $( self ).val().trim();
            if ( ! _url.length ) {
              this.deleteSVGRules( _pos );
              return;
            }
            var _urlCSS = "url('" + _url + "')";
            if ( this.isSVGMaskEnabled( _pos ) ) {
              delete styleHolder[ _styleKeyIconI ][ 'background-image' ];
              styleHolder[ _styleKeyIconI ][ 'background-color' ] = $( '.dynamic-button-field input[type=text][data-property=_icon_svg_mask_' + _pos + ']' ).val();
            } else {
              delete styleHolder[ _styleKeyIconI ][ 'background-color' ];
              styleHolder[ _styleKeyIconI ][ 'background-image' ]    = _urlCSS;
            }
            styleHolder[ _styleKeyIconI ][ 'background-size' ]     = 'contain';
            styleHolder[ _styleKeyIconI ][ 'background-position' ] = 'center';
            styleHolder[ _styleKeyIconI ][ 'background-repeat' ]   = 'no-repeat';

            // For SVG color
            styleHolder[ _styleKeyIconI ][ '-webkit-mask-image' ]    = _urlCSS;
            styleHolder[ _styleKeyIconI ][ 'mask-image' ]            = _urlCSS;
            styleHolder[ _styleKeyIconI ][ '-webkit-mask-position' ] = 'center center';
            styleHolder[ _styleKeyIconI ][ 'mask-position' ]         = 'center center';
            styleHolder[ _styleKeyIconI ][ '-webkit-mask-repeat' ]   = 'no-repeat';
            styleHolder[ _styleKeyIconI ][ 'mask-repeat' ]           = 'no-repeat';
          };
          // data-property: _icon_svg_mask_toggle_left / _icon_svg_mask_toggle_right
          _processor[ '_icon_svg_mask_toggle_' + _pos ] = function( self ) {
            if ( $( self ).is( ':checked' ) ) {
              delete styleHolder[ _styleKeyIconI ][ 'background-image' ];
              $( "input[type=text][data-property='_icon_svg_mask_" + _pos + "']" ).trigger( 'change' );
              return;
            }
            delete styleHolder[ _styleKeyIconI ][ 'background-color' ];
            $( "input[type=text][data-property='_icon_svg_file_" + _pos + "']" ).trigger( 'change' );            
          };
          // data-property: _icon_svg_mask_left / _icon_svg_mask_right
          _processor[ '_icon_svg_mask_' + _pos ] = function( self ) {
            if ( ! this.isIconEnabled( _pos ) ) {
              styleHolder[ _styleKeyIcon ]  = {};
              styleHolder[ _styleKeyIconI ] = {};
              return;
            }
            if ( this.getIconType( _pos ) !== 'svg_file' ) {
              delete styleHolder[ _styleKeyIconI ][ 'background-color' ];
              return;
            }
            styleHolder[ _styleKeyIconI ][ 'background-color' ] = $( self ).val();
          };
          return _processor;
        }

      }

    }

    function getDefaultStyleHolder() {
      var _styleHolder                       = {};
      _styleHolder[ styleKeyButton ]         = {
        'margin-right':   'auto',
        'margin-left':    'auto',
        'white-space':    'nowrap',
        'text-align':     'center',
        'display': 'inline-flex',   // to align center, there is a limit with block/inline-block with dynamic UI so inline-flex is used. flex is not used because when the width property is not given, it expands to the full width to fit the container and the button gets too wide
        'justify-content': 'space-around',
      };
      _styleHolder[ styleKeyButtonElements ] = {
        'box-sizing':     'border-box',
      };
      _styleHolder[ styleKeyButtonChildren ] = {
        'align-items':    'center',
        'display':        'inline-flex',
        'vertical-align': 'middle',
      };
      _styleHolder[ styleKeyButtonLabel ]    = {};
      _styleHolder[ styleKeyIconBoth ]       = {
        'margin-right':   'auto',
        'margin-left':    'auto',
        'display':        'none',    // by default make them invisible. Then, with the inputs, if the icon option is turned on, override the rule
        'height':         'auto',    // the icon size is defined with min-width and min-height and the height is set to `auto` to align the element vertically center when the icon height is shorter than the label height
        'border':         'solid 0', // there is no option to change the border style for icon containers unlike the Border meta-box section
      };
      _styleHolder[ styleKeyIconIBoth ]      = {
        'display':        'inline-block',
        'width':          '100%',
        'height':         '100%',
      };
      _styleHolder[ styleKeyIconLeft ]       = {};
      _styleHolder[ styleKeyIconILeft ]      = {};
      _styleHolder[ styleKeyIconRight ]      = {};
      _styleHolder[ styleKeyIconIRight ]     = {};
      _styleHolder[ styleKeyButtonHover ]    = {};
      return _styleHolder;
    }

    /**
     * Triggers the change event of the given elements.
     *
     * - Unchecked radio inputs will not be included.
     * - Revealer inputs will be triggered last.
     * @param elements
     */
    function triggerInputChanges( elements ) {
      $( elements )
        .not( 'input[type=radio]:not(:checked)' ) // exclude radio buttons that are not checked
        .not( 'input[data-reveal], select[data-reveal]' ) // exclude revealer selectors to avoid unselected hidden inputs by the revealer script triggering the change event and add their CSS rules (hidden inputs should not add their rules)
        .trigger( 'change' ); // triggering the `change` event will generate a new stylesheet
      $( elements )
        .filter( 'input[data-reveal], select[data-reveal]' )
        .not( 'input[type=radio]:not(:checked)' )
        .trigger( 'change' ); // now trigger the `change` event for revealer inputs
    }

    /* Utility methods */
    /**
     * @see     https://stackoverflow.com/a/1026087
     * @param   string
     * @returns {string}
     */
    function getFirstLetterCapitalized(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    }

    /**
     * @see     https://stackoverflow.com/a/1414175
     * @param   string
     * @returns {boolean}
     */
    function getBoolean( string ) {
      switch ( string.toLowerCase().trim() ) {
        case "true":
        case "yes":
        case "1":
          return true;

        case "false":
        case "no":
        case "0":
        case null:
          return false;

        default:
          return Boolean( string );
      }
    }
    
  } );

}( jQuery ));