/**
 * Extends the core wp-pointer tooltip jQuery widget to add additional features.
 */

/**
 * Displays tips for fields in a tooltip.
 *
 * It provides the $[ 'amazon-auto-links-tooltip' ]( options ) jQuery plugin method.
 * The name uses hyphens for the user's text domain to be replaced. So the compiled framework script will be entirely ported with without the keyword of `amazon-auto-links`.
 *
 * To use the method,
 * ```
 * $( '.my-tooltip' ).aalSurveyTooltip( 'Hi, this is a tooltip content!' );
 * ```
 * Or have a text message enclosed in an element with a class `amazon-auto-links-tooltip-survey-content`.
 * ```
 *    <span class="my-tooltip dashicons dashicons-editor-help">
 *      <span class="amazon-auto-links-tooltip-survey-content">
 *        Some text
 *      </span>
 *    </span>
 * ```
 * Then, call it like
 * ```
 * $( '.my-tooltip' ).aalSurveyTooltip();
 * ```
 * Or
 * ```
 *    <span class="my-tooltip dashicons dashicons-editor-help" data-tooltip-content="Hello"></span>
 * ```
 *
 * If it the script is loaded, elements with the .amazon-auto-links-from-tooltip class will be automatically parsed.
 * So not to call the `.aalSurveyTooltip()` method, just create an element with the selector
 * and it should automatically have a tooltip.
 *
 *
 * When the framework file is compiled, replace the keyword `amazon-auto-links` with your text domain.
 */
(function($){

  // Initialize
  // $( document ).ready( function() {
  //   $( '.amazon-auto-links-tooltip-survey' ).aalSurveyTooltip();
  // } );
  //
  $.fn.aalSurveyTooltip = function( options ) {
    if ( 'string' === typeof options ) {
      options = {
        content: isHTML( options ) ? options : "<span>" + options + "</span>"
      }
    }
    initialize( this, options )
  };

  function initialize( target, options ) {

    var _this = $( target );
    var _content = $( target ).attr( 'data-tooltip-content' );
    _content = _content ? _content : undefined;

    // Format options
    options = 'undefined' === typeof options ? {} : options;
    options = $.extend( {}, {
      pointerClass: 'amazon-auto-links-tooltip-survey-balloon',
      width: $( target ).data( 'width' ) || options.width || 340,
      shown: false,        // initial visibility
      content: _content,
      oneOff: false,
      // whether to close the tooltip automatically when the mouse leaves. do not turn on when the oneOff is on, it will disappear immediately
      autoClose: true,
      noArrow: false,
    }, options );
    options.pointerClass += options.noArrow ? ' no-arrow' : '';

    // Disable the CSS default tooltip
    $( _this ).removeClass( 'no-js' );

    if ( options.shown ) {
      handleTooltip( target, options );
    }

    var _pointerTooltip = $( _this );
    if ( ! options.oneOff ) {
      _pointerTooltip.on( 'mouseover touchend', options, handleTooltipCallback );
    }

  }

  function handleTooltipCallback( event ) {
    handleTooltip( this, event.data );
  }
  function handleTooltip( self, options ) {

    var _body      = $( 'body' );
    var _width     = options.width;
    var _content   = 'undefined' !== typeof options.content
      ? $( isHTML( options.content ) ? options.content : "<span>" + options.content + "</span>" )
      : $( self ).find( '.amazon-auto-links-tooltip-survey-content' ).clone();
    var _offscreen  = $( self ).offset().left + $( self ).width() + _width > _body.offset().left + _body.width();

    // Open the tooltip
    var _options    = $.extend( true, {}, options, {
      pointerWidth: _width,
      content: function() {
        return _content.html();
      },
      // @deprecated not look well
      // position: {
        // edge: _offscreen ? 'top' : 'left',
        // align: _offscreen ? 'center' : 'left',
        // within: _offscreen ? _body : $( self ).closest( '.amazon-auto-links-field, .amazon-auto-links-fieldrow, .amazon-auto-links-section' ),
      // },
      buttons: function() {},
      close: function() {},
    }, options );
    _options.pointerClass += _offscreen ? ' offscreen' : '';

    $( self ).aalPointer( _options )
      .aalPointer( 'open' );

    // Handle toolitip closing
    var _self    = self;
    if ( options.autoClose ) {
      handleAutoClose( _self, _content );
    } else {
      setTimeout( function() {
        handleCloseOnEmptySpace( _self, _content, options );
      }, 200 );
    }
    /// For mobile devices
    setTimeout( function() {
      handleCloseOnMobile( _self, _content );
    }, 200 );

  }

  function handleCloseOnEmptySpace( self, content, options ) {

    var _class = options.pointerClass.split( ' ' )[ 0 ];
    var _emptySpace = $( 'body' ).not( '.' + _class );
    _emptySpace.on( 'click', ':not(.' + _class + ')', _closeTooltipOnEmptySpace );
    function _closeTooltipOnEmptySpace( event ) {
      if ( $( this ).closest( '.' + _class ).length ) {
        return;
      }
      _emptySpace.off( 'click', _closeTooltipOnEmptySpace );
      $( self ).aalPointer( 'close' );
      content.remove();
    }
  }

  function handleCloseOnMobile( self, content ) {
    var _body = $( 'body' );
    _body.on( 'touchstart', _closeTooltipMobile );
    function _closeTooltipMobile( event ) {
      _body.off( 'touchstart', _closeTooltipMobile );
      $( self ).aalPointer( 'close' );
      content.remove();
    }
  }
  function handleAutoClose( self, content ) {
      /// For non-mobile devices
      $( self ).add( '.amazon-auto-links-tooltip-survey-balloon' ).on( 'mouseleave', function( event ){
        var _selfMouseLeave = this;
        // Set a timeout for the tooltip to close, allowing us to clear this trigger if the mouse comes back over
        var _timeoutId = setTimeout(function(){
          $( self ).aalPointer( 'close' );
          content.remove();
          $( self ).off( 'mouseleave' );
          $( _selfMouseLeave ).off( 'mouseleave' );
          $( self ).off( 'mouseenter' );
          $( _selfMouseLeave ).off( 'mouseenter' );
        }, 1000 );
        $( self ).data( 'timeoutId', _timeoutId );

      } );
      $( self ).add( '.amazon-auto-links-tooltip-survey-balloon' ).on( 'mouseenter', function(){
        clearTimeout( $( self ).data('timeoutId' ) );
      });
  }

  function isHTML(str) {
    var a = document.createElement('div');
    a.innerHTML = str;
    for (var c = a.childNodes, i = c.length; i--; ) {
      if ( 1 === c[ i ].nodeType) {
        return true;
      }
    }
    return false;
  }
  
}(jQuery));