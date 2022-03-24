/**
 * @name    Button Preview Meta Box
 * @version 1.0.0
 *
 * When the preview meta-box goes off screen, stick it to the screen
 */
(function ( $ ) {

  var previewMetaBox = $( '.iframe-button-preview-container' ).closest( '.postbox' );
  var hasMadeSticky  = false;

  $( window ).on( 'scroll', function(){
    if ( hasMadeSticky ) {
      return;
    }
    if ( isOffScreen( previewMetaBox[ 0 ] ) ) {
      hasMadeSticky = false;
    }
    makeSticky();
  } );

  $( document ).ready( function() {

    // If side meta box exists, with the 2-column layout
    if ( $( '#post-body.columns-2' ).length ) {
      var _heightMainMetaBoxContainer = $( '#postbox-container-2' ).height();
      if ( $( '#postbox-container-1' ).height() < _heightMainMetaBoxContainer ) {
        // Make the side meta box container height same as the main container
        // so that the preview box will not go away (it will be gone if the side container is too short)
        $( '#side-sortables' ).height( _heightMainMetaBoxContainer );
      }
    }

    // The initial set-up
    if ( isOffScreen( previewMetaBox[ 0 ] ) ) {
      makeSticky();
    }

  } );

  /**
   * Makes the button preview meta box sticky to the screen.
   */
  function makeSticky() {
    if ( hasMadeSticky ) {
      return;
    }
    previewMetaBox.css({
      'top': Math.ceil( $( '#wpadminbar' ).height() ) + 20, // the admin bar height + margin
      'z-index': 99999,
    });
    // For a fallback rule with the same property name, we cannot use .css() method.
    // @see https://stackoverflow.com/a/32348558
    var _styleInline = [
      'position: -webkit-sticky',
      'position: sticky',
    ].join( ';' );
    previewMetaBox.attr('style', previewMetaBox.attr( 'style' ) + _styleInline );
    hasMadeSticky = true;
  }

  /**
   * @see     https://stackoverflow.com/a/8897628
   * @param   el
   * @returns {boolean}
   */
  function isOffScreen( el ) {
    var rect = el.getBoundingClientRect();
    return (
      (rect.x + rect.width) < 0
      || (rect.y + rect.height) < 0
      || (rect.x > window.innerWidth || rect.y > window.innerHeight)
    );
  }

}( jQuery ));