/**
 * @name Web Page Dumper Version Checker for Admin Notices
 * @version 1.0.0
 */
(function($){
    
  $( document ).ready( function() {

    if ( 'undefined' === typeof aalWPDVersionCheckNotice ) {
      return;
    }
    if ( ! aalWPDVersionCheckNotice.urls ) {
      return;
    }

    var _aList = aalWPDVersionCheckNotice.urls;
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
      url: aalWPDVersionCheckNotice.ajaxURL,
      data: {
        // Required
        action: aalWPDVersionCheckNotice.actionHookSuffix,  // WordPress action hook name which follows after `wp_ajax_`
        aal_nonce: aalWPDVersionCheckNotice.nonce,   // the nonce value set in template.php
        versions: oVersions
      }
    } ); // ajax
  }

}(jQuery));