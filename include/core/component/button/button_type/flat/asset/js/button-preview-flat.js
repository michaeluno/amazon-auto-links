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

    var buttonID                        = 'undefined' === typeof aalFlatButtonPreviewEventBinder.postID
      ? '___button_id___'
      : aalFlatButtonPreviewEventBinder.postID;
    var styleKeyButton         = '.amazon-auto-links-button-' + buttonID;
    var styleKeyButtonHover    = styleKeyButton + ':hover';
    var styleKeyButtonChildren = styleKeyButton + ' > *';
    var styleKeyIconLabel      = styleKeyButton + ' .button-label';
    var styleKeyIconBoth       = styleKeyButton + ' .button-icon';
    var styleKeyIconLeft       = styleKeyButton + ' .button-icon-left';
    var styleKeyIconRight      = styleKeyButton + ' .button-icon-right';
    var styleHolder            = getDefaultStyleHolder();
    var previewFrame           = $( '#button-preview-flat > iframe' ).first();
    var inputs                 = $( '.dynamic-button-field input, .dynamic-button-field select, .dynamic-button-field textarea' );
    var debouncers             = {}; // stores setTimeout IDs to debounce function calls
    var inputProcessor         = getInputProcessor();

    // When the user changes button options,
    inputs.on( 'change input', function () {

      // Parse inputs
      var _property = $( this ).data( 'property' );
      _property = typeof _property !== 'string' ? '' : _property;
      var _methodName = 'undefined' !== typeof inputProcessor[ _property ] ? _property : '_common';
      if ( ! _property.length ) {
        return;
      }
      var _self = $( this );
      clearTimeout( debouncers[ _property + $( this ).data( 'selectorSuffix' ) ] ); // do not call the function too frequent with the debounce logic
      debouncers[ _property + $( this ).data( 'selectorSuffix' ) ] = setTimeout( function(){
        inputProcessor[ _methodName ]( _self );
      }, 100 );

      // Apply the stylesheet and update field inputs
      clearTimeout( debouncers[ '_stylesheet_generator' ] ); // do not call the function too frequent with the debounce logic
      debouncers[ '_stylesheet_generator' ] = setTimeout( function () {

        // Update the framed page stylesheet
        var _style = _getCSSRulesGenerated( sortObject( styleHolder ) ) + '\n' + $( '#custom_css__0' ).val();
        _setStyleSheetInFramedPage( _style, 'button-preview-flat-framed-stylesheet', previewFrame );

        // Update the Generated CSS field
        $( '#button_css__0' ).text( _style );

        // Tell the framed window to send back proportional data so that the iframe height will be adjusted with a callback defined in this script
        previewFrame[ 0 ].contentWindow.postMessage(
          {
            message: 'hi there!',
            event:   'ReloadButtonPreview',
            nonce:   aalFlatButtonPreviewEventBinder.nonce,
          },
          location.protocol + '//' + location.host
        );
      }, 200 );

      function _setStyleSheetInFramedPage( style, attributeID, previewFrame ) {
        var _styleSheet = $( '<style>', { id: attributeID } );
        _styleSheet.text( style );
        var _prevSheet = previewFrame.contents().find( '#' + attributeID );
        if ( _prevSheet.length > 0 ) {
          _prevSheet.replaceWith( _styleSheet );
        } else {
          previewFrame.contents().find( 'head' ).first().append( _styleSheet );
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
          var _rules = selector + ' {\n';
          for ( var prop in ruleset ) {
            if ( ! ruleset.hasOwnProperty( prop ) ) {
              continue;
            }
            _rules += '    ' + prop + ': ' + ruleset[ prop ] + ';\n';
          }
          _rules += '}\n';
          return _rules;
        }
      }

      /**
       * Used to sort the style holder object.
       * @see     https://stackoverflow.com/a/29622653
       * @param   obj
       * @returns {{}}
       */
      function sortObject( obj ) {
        return Object.keys( obj ).sort().reduce( function ( result, key ) {
          result[ key ] = obj[ key ];
          return result;
        }, {} );
      }

    } );

    // When the preview iframe is loaded, call input change events to update the preview
    (function( iframe ) { // IIFE for IDE
      iframe.on( 'load', function() {

        // Set properties of the input processor that stores jQuery object of iframe elements
        inputProcessor.jqPreviewButtonIconLeft  = previewFrame.contents().find( 'body .button-icon-left' );
        inputProcessor.jqPreviewButtonIconRight = previewFrame.contents().find( 'body .button-icon-right' );
        inputProcessor.jqPreviewButtonLabel     = previewFrame.contents().find( 'body .button-label' ).first();

        // Using a debouncer as there is are cases like the frame gets loaded multiple times in a short period of time
        // Note that when the preview meta-box visibility is toggled with the screen layout option (top-right corner of the admin screen) or moved its position (sorted),
        // the frame gets reloaded and this function can be called multiple times in a single page load.
        clearTimeout( debouncers[ '_iframe_preview_loaded' ] ); // do not call the function too frequent with the debounce logic
        debouncers[ '_iframe_preview_loaded' ] = setTimeout( function () {
          triggerInputChanges( inputs );
        }, 2000 );
      });
      /// There is a case that the frame is already loaded before this is called
      if ( 'complete' === iframe[ 0 ].contentDocument.readyState ) {
        iframe.trigger( 'load' );
      }
    })( previewFrame );

    // Adjust the iframe height on proportional changes with windows messages from the framed window
    (function( iframe ) { // IIFE for IDE
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
        iframe.height( event.data.height ).css( 'height', event.data.height + 'px' );
      }, false );
    })( previewFrame );

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
        'lineHeight'                : 1.3,       // default. It may be different depending on the them
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

          // @deprecated defined in the $.on( 'load' ) callback
          // this.jqPreviewButtonLabel    = 'undefined' === typeof this.jqPreviewButtonLabel
          //   ? previewFrame.contents().find( 'body .button-label' ).first()
          //   : this.jqPreviewButtonLabel;
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
          if ( 'undefined' === typeof styleHolder[ styleKeyButton + _suffixSelector ] ) {
            styleHolder[ styleKeyButton + _suffixSelector ] = {};
          }
          styleHolder[ styleKeyButton + _suffixSelector ][ self.data( 'property' ) ] = self.val() + _suffixValue;
        }
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
      return $.extend( {}, inputProcessorBase, inputProcessorText, inputProcessorIconLeft, inputProcessorIconRight, inputProcessorBox, inputProcessorHover );

      function _getInputProcessorIcon( position, inputProcessorBase ) {
        var _pos                         = position.toLowerCase();    // left / right
        var _styleKeyIcon                = 'left' === _pos ? styleKeyIconLeft : styleKeyIconRight;
        var _iconPos                     = 'icon' + getFirstLetterCapitalized( _pos ); // iconLeft / iconRight
        var _inputProcessorIcon          = _getInputProcessorIcon();
        var _inputProcessorIconSVGFile   = _getInputProcessorIconSVGFile();
        var _inputProcessorIconImageFile = _getInputProcessorIconImageFile();
        return $.extend( {}, _inputProcessorIcon, _inputProcessorIconSVGFile, _inputProcessorIconImageFile );

        function _getInputProcessorIcon() {
          var _processor = {};
          // data-property: _icon_left_margin_type / _icon_right_margin_type
          _processor[ '_icon_' + _pos + '_margin_type' ] = function( self ) {
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
              styleHolder[ _styleKeyIcon ] = {}; // delete all the rules of the left icon

              // Even though the icon is set to off, the image field of the Image File gets shown for some reasons when opening the screen of the existing button to edit (post.php, not Add New). So hide them.
              $( self.data( 'selectors' ) ).hide();
              return;
            }
            triggerInputChanges( $( '.fields-button-icon-' + _pos + ' input, .fields-button-icon-' + _pos + ' select' ).not( self ) );
          };
          // data-property: _icon_image_type_left / _icon_image_type_right
          _processor[ '_icon_image_type_' + _pos ] = function( self ) {
            var _imageTypeHandlers = {
              'svg_file': function() {
                // delete styleHolder[ _styleKeyIcon ][ 'background-image' ]; // no need to remove as the background-image is needed for svg_file as well
                triggerInputChanges( $( '.svg-file-' + _pos + ' input, .svg-file-' + _pos + ' select' ).not( self ) );
              },
              'image_file': function() {
                delete styleHolder[ _styleKeyIcon ][ 'background-color' ];
                delete styleHolder[ _styleKeyIcon ][ '-webkit-mask-image' ];
                delete styleHolder[ _styleKeyIcon ][ 'mask-image' ];
                delete styleHolder[ _styleKeyIcon ][ '-webkit-mask-position' ];
                delete styleHolder[ _styleKeyIcon ][ 'mask-position' ];
                delete styleHolder[ _styleKeyIcon ][ '-webkit-mask-repeat' ];
                delete styleHolder[ _styleKeyIcon ][ 'mask-repeat' ];
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
              styleHolder[ _styleKeyIcon ] = {};
              return;
            }
            styleHolder[ _styleKeyIcon ][ 'display' ]             = 'inline-flex';
            var _url    = $( self ).val().trim();
            if ( ! _url.length ) {
              delete styleHolder[ _styleKeyIcon ][ 'background-image' ];
              return;
            }
            styleHolder[ _styleKeyIcon ][ 'background-image' ]    = "url('" + _url + "')";
            styleHolder[ _styleKeyIcon ][ 'background-size' ]     = 'contain';
            styleHolder[ _styleKeyIcon ][ 'background-position' ] = 'center';
            styleHolder[ _styleKeyIcon ][ 'background-repeat' ]   = 'no-repeat';
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
          };
          // data-property: _icon_svg_file_left / _icon_svg_file_right
          _processor[ '_icon_svg_file_' + _pos ] = function( self ) {
            if ( ! this.isIconEnabled( _pos ) ) {
              styleHolder[ _styleKeyIcon ] = {};
              return;
            }
            // If icon image type is not svg, remove related CSS rules
            if ( this.getIconType( _pos ) !== 'svg_file' ) {
              delete styleHolder[ _styleKeyIcon ][ 'background-color' ];
            }
            styleHolder[ _styleKeyIcon ][ 'display' ]             = 'inline-flex'; // this needs to be set regardless the url is empty or not
            var _url         = $( self ).val().trim();
            if ( ! _url.length ) {
              delete styleHolder[ _styleKeyIcon ][ 'background-color' ];
              delete styleHolder[ _styleKeyIcon ][ '-webkit-mask-image' ];
              delete styleHolder[ _styleKeyIcon ][ 'mask-image' ];
              delete styleHolder[ _styleKeyIcon ][ '-webkit-mask-position' ];
              delete styleHolder[ _styleKeyIcon ][ 'mask-position' ];
              delete styleHolder[ _styleKeyIcon ][ '-webkit-mask-repeat' ];
              delete styleHolder[ _styleKeyIcon ][ 'mask-repeat' ];
              return;
            }
            var _urlCSS = "url('" + _url + "')";
            if ( this.isSVGMaskEnabled( _pos ) ) {
              delete styleHolder[ _styleKeyIcon ][ 'background-image' ];
              styleHolder[ _styleKeyIcon ][ 'background-color' ] = $( '.dynamic-button-field input[type=text][data-property=_icon_svg_mask_' + _pos + ']' ).val();
            } else {
              delete styleHolder[ _styleKeyIcon ][ 'background-color' ];
              styleHolder[ _styleKeyIcon ][ 'background-image' ]    = _urlCSS;
            }
            styleHolder[ _styleKeyIcon ][ 'background-size' ]     = 'contain';
            styleHolder[ _styleKeyIcon ][ 'background-position' ] = 'center';
            styleHolder[ _styleKeyIcon ][ 'background-repeat' ]   = 'no-repeat';

            // For SVG color
            styleHolder[ _styleKeyIcon ][ '-webkit-mask-image' ]    = _urlCSS;
            styleHolder[ _styleKeyIcon ][ 'mask-image' ]            = _urlCSS;
            styleHolder[ _styleKeyIcon ][ '-webkit-mask-position' ] = 'center center';
            styleHolder[ _styleKeyIcon ][ 'mask-position' ]         = 'center center';
            styleHolder[ _styleKeyIcon ][ '-webkit-mask-repeat' ]   = 'no-repeat';
            styleHolder[ _styleKeyIcon ][ 'mask-repeat' ]           = 'no-repeat';            
          };
          // data-property: _icon_svg_mask_toggle_left / _icon_svg_mask_toggle_right
          _processor[ '_icon_svg_mask_toggle_' + _pos ] = function( self ) {
            if ( $( self ).is( ':checked' ) ) {
              delete styleHolder[ _styleKeyIcon ][ 'background-image' ];
              $( "input[type=text][data-property='_icon_svg_mask_" + _pos + "']" ).trigger( 'change' );
              return;
            }
            delete styleHolder[ _styleKeyIcon ][ 'background-color' ];
            $( "input[type=text][data-property='_icon_svg_file_" + _pos + "']" ).trigger( 'change' );            
          };
          // data-property: _icon_svg_mask_left / _icon_svg_mask_right
          _processor[ '_icon_svg_mask_' + _pos ] = function( self ) {
            styleHolder[ _styleKeyIcon ][ 'background-color' ] = $( self ).val();
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
      _styleHolder[ styleKeyButtonChildren ] = {
// 'position': 'absolute',
// 'top': '50%',
// '-ms-transform': 'translateY(-50%)',
// 'transform': 'translateY(-50%)',
        'align-items': 'center',
        'display': 'inline-flex',
        'vertical-align': 'middle',
      };
      _styleHolder[ styleKeyIconLabel ]      = {};
      _styleHolder[ styleKeyIconBoth ]       = {
        'margin-right':   'auto',
        'margin-left':    'auto',
        'display':        'none', // by default make them invisible. Then, with the inputs, if the icon option is turned on, override the rule
        // 'display':        'inline-block',
        // 'vertical-align': 'middle',
        'height':         'auto', // the icon size is defined with min-width and min-height and the height is set to `auto` to align the element vertically center when the icon height is shorter than the label height
      };
      _styleHolder[ styleKeyIconLeft ]       = {};
      _styleHolder[ styleKeyIconRight ]      = {};
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