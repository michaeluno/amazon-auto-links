(function($){
    
    oPreviewButton = {};
    oPreviewButton.styles = [];
    oPreviewButton.styles_markup = '';
    oPreviewButton.styles_hover_markup = '';
    oPreviewButton.aPixelProperties = [ 
        'font-size', 
        'border-radius', 
        'padding', 
        'padding-top', 
        'padding-right', 
        'padding-bottom', 
        'padding-left',
        'width'
    ];
    oPreviewButton.gradient_properties = [
        'bg-start-gradient', 
        'bg-end-gradient'
    ];
    oPreviewButton.input_focus = '';
    
    /**
     * Update the array that stores all css values
     */
    oPreviewButton.updateStyles = function(){
        oPreviewButton.prepareStyles();
        oPreviewButton.generateStyleMarkUp();
        oPreviewButton.printStyles();
    }

    /**
     * Prepares the raw style data for css presentation (removes, combines, etc..)
     */
    oPreviewButton.prepareStyles = function(){
        
        oPreviewButton.styles = {
            'margin-left': 'auto',
            'margin-right': 'auto',
            'text-align':'center',
            'white-space': 'nowrap',
        };
        oPreviewButton.styles_markup        = '';
        oPreviewButton.styles_hover_markup  = '';

        $( '#post' )
            .find( 'input[data-property][type="text"], input[data-property][type="number"], input[data-property][type="radio"], select[data-property]' )
            .not( ':disabled' )
            .each( function(){
                
                var _sValue    = $( this ).val();
                if ( ! _sValue ) {
                    return true;
                }
                var _sCSSProperty = $( this ).attr( 'data-property' );
                oPreviewButton.styles[ _sCSSProperty ] = _sValue;
            });

          // remove the text data
          $( '.amazon-auto-links-button' ).html( oPreviewButton.styles[ 'text' ] );
          delete oPreviewButton.styles[ 'text' ];

          // combine padding if all are present
          var padding_top, padding_right, padding_bottom, padding_left;
          if((padding_top = oPreviewButton.styles['padding-top']) &&
             (padding_right = oPreviewButton.styles['padding-right']) &&
             (padding_bottom = oPreviewButton.styles['padding-bottom']) &&
             (padding_left = oPreviewButton.styles['padding-left'])){
            oPreviewButton.styles['padding'] = padding_top + 'px ' + padding_right + 'px ' + padding_bottom + 'px ' + padding_left;
            delete oPreviewButton.styles['padding-top'];
            delete oPreviewButton.styles['padding-right'];
            delete oPreviewButton.styles['padding-bottom'];
            delete oPreviewButton.styles['padding-left'];
          }

          // combine border styles
          var border_style, border_color, border_width;
          if((border_style = oPreviewButton.styles['border-style']) &&
             (border_color = oPreviewButton.styles['border-color']) &&
             (border_width = oPreviewButton.styles['border-width'])){
            oPreviewButton.styles['border'] = border_style + ' ' + border_color + ' ' + border_width + 'px';
            delete oPreviewButton.styles['border-style'];
            delete oPreviewButton.styles['border-color'];
            delete oPreviewButton.styles['border-width'];
          }

          // combine border-top styles
          var border_top_style, border_top_color, border_top_width;
          if((border_top_style = oPreviewButton.styles['border-top-style']) &&
             (border_top_color = oPreviewButton.styles['border-top-color']) &&
             (border_top_width = oPreviewButton.styles['border-top-width'])){
            oPreviewButton.styles['border-top'] = border_top_style + ' ' + border_top_color + ' ' + border_top_width + 'px';
            delete oPreviewButton.styles['border-top-style'];
            delete oPreviewButton.styles['border-top-color'];
            delete oPreviewButton.styles['border-top-width'];
          }

          // combine border-right styles
          var border_right_style, border_right_color, border_right_width;
          if((border_right_style = oPreviewButton.styles['border-right-style']) &&
             (border_right_color = oPreviewButton.styles['border-right-color']) &&
             (border_right_width = oPreviewButton.styles['border-right-width'])){
            oPreviewButton.styles['border-right'] = border_right_style + ' ' + border_right_color + ' ' + border_right_width + 'px';
            delete oPreviewButton.styles['border-right-style'];
            delete oPreviewButton.styles['border-right-color'];
            delete oPreviewButton.styles['border-right-width'];
          }

          // combine border-bottom styles
          var border_bottom_style, border_bottom_color, border_bottom_width;
          if((border_bottom_style = oPreviewButton.styles['border-bottom-style']) &&
             (border_bottom_color = oPreviewButton.styles['border-bottom-color']) &&
             (border_bottom_width = oPreviewButton.styles['border-bottom-width'])){
            oPreviewButton.styles['border-bottom'] = border_bottom_style + ' ' + border_bottom_color + ' ' + border_bottom_width + 'px';
            delete oPreviewButton.styles['border-bottom-style'];
            delete oPreviewButton.styles['border-bottom-color'];
            delete oPreviewButton.styles['border-bottom-width'];
          }

          // combine border-left styles
          var border_left_style, border_left_color, border_left_width;
          if((border_left_style = oPreviewButton.styles['border-left-style']) &&
             (border_left_color = oPreviewButton.styles['border-left-color']) &&
             (border_left_width = oPreviewButton.styles['border-left-width'])){
            oPreviewButton.styles['border-left'] = border_left_style + ' ' + border_left_color + ' ' + border_left_width + 'px';
            delete oPreviewButton.styles['border-left-style'];
            delete oPreviewButton.styles['border-left-color'];
            delete oPreviewButton.styles['border-left-width'];
          }
    }

    /**
     * Populates the oPreviewButton.styles_markup property with the printable string
     */
    oPreviewButton.generateStyleMarkUp = function(){

          // gradients
          var gradient_start, gradient_end;
          if((gradient_start = oPreviewButton.styles['bg-start-gradient']) &&
             (gradient_end = oPreviewButton.styles['bg-end-gradient'])){
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine( 'background', gradient_start );
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine( 'background-image', '-webkit-linear-gradient(top, ' + gradient_start + ', ' + gradient_end + ')' );
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine( 'background-image', '-moz-linear-gradient(top, ' + gradient_start + ', ' + gradient_end + ')' );
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine( 'background-image', '-ms-linear-gradient(top, ' + gradient_start + ', ' + gradient_end + ')' );
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine( 'background-image', '-o-linear-gradient(top, ' + gradient_start + ', ' + gradient_end + ')' );
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine( 'background-image', 'linear-gradient(to bottom, ' + gradient_start + ', ' + gradient_end + ')' );
            delete oPreviewButton.styles[ 'bg-start-gradient' ];
            delete oPreviewButton.styles[ 'bg-end-gradient' ];
            delete oPreviewButton.styles[ 'bg-color' ];
            
            delete oPreviewButton.styles[ 'background-gradient' ];
            delete oPreviewButton.styles[ 'background-solid' ];
                 
          }

          // gradient hovers
          var gradient_hover_start, gradient_hover_end;
          if((gradient_hover_start = oPreviewButton.styles['bg-start-gradient-hover']) &&
             (gradient_hover_end = oPreviewButton.styles['bg-end-gradient-hover'])){
            oPreviewButton.styles_hover_markup += oPreviewButton.getStyleByLine('background', gradient_hover_start);
            oPreviewButton.styles_hover_markup += oPreviewButton.getStyleByLine('background-image', '-webkit-linear-gradient(top, ' + gradient_hover_start + ', ' + gradient_hover_end + ')');
            oPreviewButton.styles_hover_markup += oPreviewButton.getStyleByLine('background-image', '-moz-linear-gradient(top, ' + gradient_hover_start + ', ' + gradient_hover_end + ')');
            oPreviewButton.styles_hover_markup += oPreviewButton.getStyleByLine('background-image', '-ms-linear-gradient(top, ' + gradient_hover_start + ', ' + gradient_hover_end + ')');
            oPreviewButton.styles_hover_markup += oPreviewButton.getStyleByLine('background-image', '-o-linear-gradient(top, ' + gradient_hover_start + ', ' + gradient_hover_end + ')');
            oPreviewButton.styles_hover_markup += oPreviewButton.getStyleByLine('background-image', 'linear-gradient(to bottom, ' + gradient_hover_start + ', ' + gradient_hover_end + ')');
            delete oPreviewButton.styles[ 'bg-start-gradient-hover' ];
            delete oPreviewButton.styles[ 'bg-end-gradient-hover' ];
            delete oPreviewButton.styles[ 'background-hover' ];
            
            
            delete oPreviewButton.styles[ 'background-gradient-hover' ];
            delete oPreviewButton.styles[ 'background-solid-hover' ];
          }

          // border radius
          var border_radius;
          if((border_radius = oPreviewButton.styles['border-radius'])){
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine('-webkit-border-radius', border_radius);
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine('-moz-border-radius', border_radius);
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine('border-radius', border_radius);
            delete oPreviewButton.styles['border-radius'];
          }

          // text shadow
          var text_shadow_color, text_shadow_x, text_shadow_y, text_shadow_blur;
          if((text_shadow_color = oPreviewButton.styles['text-shadow-color']) &&
             (text_shadow_x = oPreviewButton.styles['text-shadow-x']) &&
             (text_shadow_y = oPreviewButton.styles['text-shadow-y']) &&
             (text_shadow_blur = oPreviewButton.styles['text-shadow-blur'])){
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine('text-shadow', text_shadow_x + 'px ' + text_shadow_y + 'px ' + text_shadow_blur + 'px ' + text_shadow_color);
            delete oPreviewButton.styles['text-shadow-color'];
            delete oPreviewButton.styles['text-shadow-x'];
            delete oPreviewButton.styles['text-shadow-y'];
            delete oPreviewButton.styles['text-shadow-blur'];
          }

          // box shadow
          var box_shadow_color, box_shadow_x, box_shadow_y, box_shadow_blur;
          if((box_shadow_color = oPreviewButton.styles['box-shadow-color']) &&
            (box_shadow_x = oPreviewButton.styles['box-shadow-x']) &&
            (box_shadow_y = oPreviewButton.styles['box-shadow-y']) &&
            (box_shadow_blur = oPreviewButton.styles['box-shadow-blur'])){
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine('-webkit-box-shadow', box_shadow_x + 'px ' + box_shadow_y + 'px ' + box_shadow_blur + 'px ' + box_shadow_color);
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine('-moz-box-shadow', box_shadow_x + 'px ' + box_shadow_y + 'px ' + box_shadow_blur + 'px ' + box_shadow_color);
            oPreviewButton.styles_markup += oPreviewButton.getStyleByLine('box-shadow', box_shadow_x + 'px ' + box_shadow_y + 'px ' + box_shadow_blur + 'px ' + box_shadow_color);
            delete oPreviewButton.styles['box-shadow-color'];
            delete oPreviewButton.styles['box-shadow-x'];
            delete oPreviewButton.styles['box-shadow-y'];
            delete oPreviewButton.styles['box-shadow-blur'];
          }
            
            /**
             * For unknown properties.
             */
            $.each( oPreviewButton.styles, function( sCSSProperty, sCSSValue ){

                if ( 'background-hover' === sCSSProperty  ) {
                    oPreviewButton.styles_hover_markup = oPreviewButton.getStyleByLine( 'background', sCSSValue );
                } else{
                    oPreviewButton.styles_markup += oPreviewButton.getStyleByLine( sCSSProperty, sCSSValue );
                }
                
            });

          // remove text-decoration
          oPreviewButton.styles_markup += oPreviewButton.getStyleByLine( 'text-decoration', 'none' );

          // wrap the style markups in proper css calls
          var _isButtonID = aal_button_script_preview_updator[ 'post_id' ];          
          oPreviewButton.styles_markup = '.amazon-auto-links-button.amazon-auto-links-button-' + _isButtonID + ' {\n' + oPreviewButton.styles_markup + '}';
          oPreviewButton.styles_hover_markup += oPreviewButton.getStyleByLine( 'text-decoration', 'none' );
          oPreviewButton.styles_hover_markup = '\n\n.amazon-auto-links-button.amazon-auto-links-button-' + _isButtonID + ':hover {\n'
            + oPreviewButton.styles_hover_markup 
            + '}';
    }

    /**
     * Update the output of the css styles.
     */
    oPreviewButton.printStyles = function(){
        
        var _sOutput = oPreviewButton.styles_markup 
            + oPreviewButton.styles_hover_markup;
        var _sStyleTag = '<style id="amazon-auto-links-button-style" type="text/css">' 
                + _sOutput 
            + '</style>';
        $( '#amazon-auto-links-button-style' ).replaceWith( _sStyleTag );
        $( 'textarea#button_css__0' ).text( _sOutput );

    }

    /**
     * Renders an individual style line
     */
    oPreviewButton.getStyleByLine = function( sCSSProperty, sCSSValue ){
        
        if ( 'undefined' === sCSSProperty  ) {
            return '';
        }
        
        // check if "px" should appended to the style
        var _sPx = $.inArray( sCSSProperty, oPreviewButton.aPixelProperties ) > -1 
            ? 'px' 
            : '';
        var _sTab = '    ';
        return _sTab + sCSSProperty + ': ' + sCSSValue + _sPx + ';\n';
        
    }
    
    // Initial button update.
    $( document ).ready( function() {
        oPreviewButton.updateStyles();  
        
// console.log( 'started' );
// console.log( aal_button_script_preview_updator );

    });

}( jQuery ));