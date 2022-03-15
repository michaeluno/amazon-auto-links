/**
 * @name    Button Preview in Unit Definition Page
 * @version 1.1.0
 */
(function($){

  /* global aalButtonPreview, aalSetButtonPreviewIframeStyle */
  if ( 'undefined' === typeof aalButtonPreview ) {
    console.log( 'AAL Debug (Button Preview in Unit Definition):', 'The script translation data are not loaded.' );
    return false;
  }

  debugLog( aalButtonPreview );

  var _spinner = $( '<img>', {
    'alt': 'Loading...',
    'src': aalButtonPreview.spinnerURL,
    'style': 'vertical-align: middle; margin: 0 0.6em;'
  } ).hide();

  /**
   * Global function called from an iframe document.
   *
   * This is needed to get the button preview element dimensions in the framed page
   * especially when the iframe in the main page is invisible which makes it not possible to get dimensions of the previewed button.
   * @remark  it seems whenever the iframe contents are modified, the child script calls this function.
   */
  aalSetButtonPreviewIframeStyle = function ( iWidth, iHeight, isButtonID ) {
    $( '#iframe-button-preview-' + isButtonID ).width( iWidth + 10 ).height( iHeight + 10 )
      .css( {
        'width': iWidth + 'px',
        'height': iHeight + 'px',
        'visibility': 'visible',
        'min-width': '116px',
      } );
    _spinner.hide();
  };

  $( document ).ready( function(){
    $( '.button-select-row select' ).trigger( 'change' ); // load the first iframe item
  } );
  
  $( document ).on( 'change', '.button-select-row select', function () {

    // Create an iframe element if not exists.
    var _frameContainer = $( this ).closest( '.amazon-auto-links-fields' ).find( '.iframe-button-preview-container' );
    var _idFrame        = 'iframe-button-preview-' + $( this ).val();
    var _thisFrame      = $( '#' + _idFrame );
    var _title          = 'Iframe Button Preview: ' + $( this ).val();
    var _bNew           = ! _thisFrame.length;
    if ( _bNew ) {
      var _src = aalButtonPreview.frameSRC.replace( '___button_id___', $( this ).val() );
      var _buttonLabel = _getButtonLabelByID( $( this ).val(), this );
      _src += 'undefined' === _buttonLabel ? '' : '&button-label=' + _buttonLabel;
      _thisFrame = $( '<iframe>', {
        'id': 'iframe-button-preview-' + $( this ).val(),
        'data-button-id': $( this ).val(),
        'class': 'frame-button-preview',
        'frameborder': 0,
        'border': 0,
        'style': 'height:60px; border:none; overflow:hidden; margin: 0 auto; display: block; visibility: hidden;',
        'width': '200',
        'height': '60',
        'scrolling': 'no',
        'src': _src,
        'title': _title, // without the `title` attribute, Chrome complains
      } );
      _frameContainer.append( _thisFrame );
      $( this ).after( _spinner.show() );
    }
    _thisFrame.siblings().not( _thisFrame.show() ).hide();

    /**
     * This MUST be called after _thisFrame.show() as width and height need to hold a value (invisible elements don't have them)
     * This is needed for this case flow:
     *  1. User change the button selection from button B to button A,
     *  2. check Override Label and type something new
     *  3. The user changes back to button B.
     * Without this, the updated label entered in the step 2 above will not be reflected
     */
    if ( ! _bNew ) {
      _setButtonLabelByID( $( this ).val(), this );
    }

  } );
    function _getButtonLabelByID( iButtonID, self ) {
      if ( ! $( 'input.override-button-label[type=checkbox]' ).is( ':checked' ) ) {
        return aalButtonPreview.activeButtons[ iButtonID ];
      }
      // Otherwise, the user wants to override the label.
      return $( self ).closest( '.amazon-auto-links-section-table' ).find( 'input.button-label[type=text]' ).first().val();
    }

  /**
   * When the Override Button Label option is toggled, update the label.
   */
  $( document ).on( 'change', 'input.override-button-label[type=checkbox]', function(){
    var _iCurrentButtonID = $( '.button-select-row select' ).val();
    _setButtonLabelByID( _iCurrentButtonID, this );
  } );
  
    function _setButtonLabelByID( iButtonID, element ) {
      var _thisFrame = $( '#iframe-button-preview-' + iButtonID );
      if ( ! _thisFrame.length ) {
        return;
      }
      _thisFrame.contents().find( '.amazon-auto-links-button[data-type!=image]' )
        .text(
          $( element ).closest( '.amazon-auto-links-section-table' ).find( 'input.override-button-label[type=checkbox]' ).is( ':checked' )
            ? $( element ).closest( '.amazon-auto-links-section-table' ).find( 'input.button-label[type=text]' ).first().val() // Override
            : aalButtonPreview.activeButtons[ iButtonID ] // Revert the label by using the default label
        );
      _thisFrame.get( 0 ).contentWindow.setParentButtonPreviewIframeStyle();  // Update the frame dimension
    }

  // Override the button label when the Button Label field is entered.
  $( document ).on( 'input', 'input.button-label[type=text]', function(){
    if ( ! $( this ).closest( '.amazon-auto-links-section-table' )
      .find( 'input.override-button-label[type=checkbox]' ).is( ':checked' ) ) {
      return;
    }
    var _select = $( this ).closest( '.amazon-auto-links-section-table' ).find( '.button-select-row select' );
    // Update the button labels of all the iframe instances
    for ( var _buttonID in aalButtonPreview.activeButtons ) {
      if ( ! aalButtonPreview.activeButtons.hasOwnProperty( _buttonID ) ) {
        continue;
      }
      _setButtonLabelByID( _buttonID, _select );
    }
  } );

  function debugLog( ...args ) {
    if ( ! parseInt( aalButtonPreview.debugMode ) ) {
      return;
    }
    console.log( 'AAL Debug (Button Preview in Unit Definition):', ...args );
  }

}( jQuery ));