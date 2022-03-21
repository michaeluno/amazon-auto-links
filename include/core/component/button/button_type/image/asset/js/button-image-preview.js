/**
 * @name Image Button Preview Event Binder
 * @version 1.0.0
 */
(function ( $ ) {
  $( document ).ready( function () {

    var buttonID        = 'undefined' === typeof aalImageButtonPreviewEventBinder.postID
      ? '___button_id___'
      : aalImageButtonPreviewEventBinder.postID;
    var pseudoImg       = $( '<img>' );
    var styleHolder     = {};
    styleHolder[ '.amazon-auto-links-button-' + buttonID ] = {
      'display': 'block',
      'margin-right': 'auto',
      'margin-left': 'auto',
    };
    styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img' ] = {
      'height': 'unset',
      'max-width': '100%',
      'max-height': '100%',
      'margin-right': 'auto',
      'margin-left': 'auto',
      'display': 'block'
    };
    styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img:hover' ] = {};

    var previewFrame    = $( '#button-image-preview > iframe' ).first();
    var inputs          = $( '.dynamic-button-field input, .dynamic-button-field select, .dynamic-button-field textarea' );
    var debounce;
    var inputProcessor  = {
      '_width_toggle': function( self ) {
        if ( ! $( self ).is( ':checked' ) ) {
          delete styleHolder[ '.amazon-auto-links-button-' + buttonID ][ 'width' ];
          return;
        }
        $( "input[type=number][data-property='width']" ).trigger( 'change' );
      },
      'width': function( self ) {
        var _oField = self.closest( '.amazon-auto-links-field' );
        styleHolder[ '.amazon-auto-links-button-' + buttonID ][ self.data( 'property' ) ] = _oField.find( 'input' ).val() + _oField.find( 'select' ).val();
      },
      '_height_toggle': function( self ) {
        if ( ! $( self ).is( ':checked' ) ) {
          delete styleHolder[ '.amazon-auto-links-button-' + buttonID ][ 'height' ];
          return;
        }
        $( "input[type=number][data-property='height']" ).trigger( 'change' );
      },
      'height': function ( self ) {
        this.width( self );
      },
      '_hover_scale': function( self ) {
        if ( self.is( ':checked' ) ) {
          styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img:hover' ][ 'transform' ] = 'scale(1.0);';
          styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img' ][ 'transform' ] = 'scale(0.98)';
          return;
        }
        delete styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img:hover' ][ 'transform' ];
        delete styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img' ][ 'transform' ];
      },
      '_hover_brightness': function( self ) {
        if ( self.is( ':checked' ) ) {
          styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img:hover' ][ 'filter' ] = 'alpha(opacity=70)';
          styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img:hover' ][ 'opacity' ] = '0.7';
          return;
        }
        delete styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img:hover' ][ 'filter' ];
        delete styleHolder[ '.amazon-auto-links-button-' + buttonID + ' > img:hover' ][ 'opacity' ];
      },
      'background-image': function( self ) {
        var _bUnchanged = !! self.attr( 'data-unchanged' ); // The first time of call is triggered programmatically. In that case, don't update the width and height options
        self.removeAttr( 'data-unchanged' );

        var _url = self.val();
        pseudoImg.attr( 'src', _url );
        var _imgTemp    = new Image();
        _imgTemp.onload = function(){
          // Adjust the preview frame height
          previewFrame.css( {
            'height':  _imgTemp.height + 'px'  // the frame width is always 100% so set only the height
          });
          // Update the Width and Height options with the image dimensions
          if ( ! _bUnchanged ) {
            $( '#_dimensions_width__0_size' ).val( _imgTemp.width );
            $( '#_dimensions_width__0_unit' ).val( 'px' );
            $( '#_dimensions_width_toggle__0_' ).trigger( 'change' );
            $( '#_dimensions_height__0_size' ).val( _imgTemp.height );
            $( '#_dimensions_height__0_unit' ).val( 'px' );
            $( '#_dimensions_height_toggle__0_' ).trigger( 'change' );
          }
        }
        _imgTemp.src = _url;  // this triggers the onload function above
      },
      '_common': function( self ) {
        if ( ! self.data( 'property' ) ) {
          return;
        }
        styleHolder[ '.amazon-auto-links-button-' + buttonID ][ self.data( 'property' ) ] = self.val();
      }
    };

    // When the preview iframe is loaded,
    previewFrame.on( 'load', function() {
      inputs
        .not( 'input[type=radio]:not(:checked)' ) // exclude radio buttons that are not checked
        .not( 'input[data-reveal], select[data-reveal]' ) // exclude revealer selectors to avoid unselected hidden inputs by the revealer script triggering the change event and add their CSS rules (hidden inputs should not add their rules)
        .trigger( 'change' ); // triggering the `change` event will generate a new stylesheet
      inputs
        .filter( 'input[data-reveal], select[data-reveal]' )
        .not( 'input[type=radio]:not(:checked)' )
        .trigger( 'change' ); // now trigger the `change` event for revealer inputs
    });

    // When the user changes button options,
    inputs.on( 'change input', function () {

      // Parse inputs
      var _property = $( this ).data( 'property' );
      _property = typeof _property !== 'string' ? '' : _property;
      var _methodName = 'undefined' !== typeof inputProcessor[ _property ] ? _property : '_common';
      inputProcessor[ _methodName ]( $( this ) );

      // Adjust the iframe size
      previewFrame.css({
          'max-height':  $( '#_height__0_size' ).val() + $( '#_height__0_unit' ).val(),
      });

      // Apply the stylesheet and update field inputs
      clearTimeout( debounce ); // do not call the function too frequent with the debounce logic
      debounce = setTimeout( function () {

        // Update the framed page stylesheet
        var _style = _getCSSRulesGenerated( styleHolder ) + '\n' + $( '#custom_css__0' ).val();
        _setStyleSheetInFramedPage( _style, 'button-preview-image-framed-stylesheet', previewFrame, pseudoImg );

        // Update the Generated CSS field
        $( '#button_css__0' ).text( _style );

      }, 100 );

    } );

    function _setStyleSheetInFramedPage( style, attributeID, previewFrame, pseudoImg ) {
      var _styleSheet = $( '<style>', { id: attributeID } );
      _styleSheet.text( style );
      var _prevSheet = previewFrame.contents().find( '#' + attributeID );
      if ( _prevSheet.length > 0 ) {
        _prevSheet.replaceWith( _styleSheet );
      } else {
        previewFrame.contents().find( 'head' ).first().append( _styleSheet );
      }
      previewFrame.contents().find( 'body .amazon-auto-links-button img' ).attr( 'src', pseudoImg.attr( 'src' ) );
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

  } );

}( jQuery ));