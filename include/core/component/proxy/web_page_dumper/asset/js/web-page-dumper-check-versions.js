/**
 * @name Web Page Dumper Version Checker
 * @version 1.0.0
 */
(function($){
    
  $( document ).ready( function() {
        
    if ( 'undefined' === typeof aalWebPageDumperVersionChecker ) {
        return;
    }

    var _list = $( '.list-web-page-dumper' ).val();
    if ( ! _list ) {
        return;
    }
    var _aList     = _list.split( /\r?\n/ );
    var _oVersions = {};
    $.each( _aList, function( _index, _url ){
      $.ajax({
        type: 'get',
        dataType: 'text',
        url: _url.replace( /\/$/, '' ) + '/version',
        complete: function( response ) {
          _oVersions[ _url ] = 'undefined' === typeof response.responseText
            ? ''
            : response.responseText;
          if ( Object.keys( _oVersions ).length === _aList.length ) {
            doAjaxToStoreVersions( _oVersions );
          }
        }
      } );
    } );

  });

  function doAjaxToStoreVersions( oVersions ) {
    $.ajax( {
      type: 'post',
      dataType: 'json',
      url: aalWebPageDumperVersionChecker.ajaxURL,
      data: {
        // Required
        action: aalWebPageDumperVersionChecker.actionHookSuffix,  // WordPress action hook name which follows after `wp_ajax_`
        aal_nonce: aalWebPageDumperVersionChecker.nonce,   // the nonce value set in template.php
        versions: oVersions
      },
      success: function ( response ) {
        if ( response.success ) {
            return;
        }
        $( '.web-page-dumper-update-required-table' ).replaceWith( response.result );
        $( '.web-page-dumper-update-required-fieldrow' ).fadeIn();
      }
    } ); // ajax

  }
}(jQuery));
